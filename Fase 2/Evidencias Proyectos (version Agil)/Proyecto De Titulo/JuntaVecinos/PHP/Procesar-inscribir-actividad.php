<?php
require_once 'Config.php';
require_once 'Verificar-sesion.php';

$usuario = verificarSesion($conexion);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/Espacios.php');
    exit;
}

$idActividad = intval($_POST['id_actividad'] ?? 0);

// La actividad debe ser de la junta del vecino y no haber pasado
$stmt = $conexion->prepare("
    SELECT a.nombre, a.cupo_maximo, a.fecha,
           (SELECT COUNT(*) FROM inscripciones_actividades WHERE id_actividad = a.id_actividad) AS ocupados
    FROM actividades a
    WHERE a.id_actividad = ? AND a.id_junta = ?
");
$stmt->bind_param("ii", $idActividad, $usuario['id_junta']);
$stmt->execute();
$actividad = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$actividad) {
    responder(false, 'Esa actividad no existe en tu junta de vecinos.');
}
if ($actividad['fecha'] < date('Y-m-d')) {
    responder(false, 'Esa actividad ya se realizó.', ['recargar' => true]);
}
if ($actividad['ocupados'] >= $actividad['cupo_maximo']) {
    responder(false, 'Lo sentimos, ya no quedan cupos para esta actividad.', ['recargar' => true]);
}

$stmt = $conexion->prepare("INSERT IGNORE INTO inscripciones_actividades (id_actividad, id_vecino) VALUES (?, ?)");
$stmt->bind_param("ii", $idActividad, $usuario['id_vecino']);
$stmt->execute();
$nueva = $stmt->affected_rows > 0;
$stmt->close();

if (!$nueva) {
    responder(false, 'Ya estabas inscrito en esta actividad.', ['recargar' => true]);
}

responder(true, 'Quedaste inscrito en "' . $actividad['nombre'] . '".', ['recargar' => true]);
