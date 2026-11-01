<?php
require_once 'Config.php';
require_once 'Verificar-sesion.php';

$usuario = verificarSesion($conexion);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/Proyectos.php');
    exit;
}

if ((int) $usuario['es_socio_activo'] !== 1) {
    responder(false, 'Solo los socios activos pueden postular a proyectos.', ['volver' => 'Proyectos.php']);
}

$idProyecto = intval($_POST['id_proyecto'] ?? 0);
$motivacion = mb_substr(campo('motivacion'), 0, 400);

// El proyecto debe ser de la junta del vecino, estar abierto y con el plazo vigente
$stmt = $conexion->prepare("SELECT nombre FROM proyectos WHERE id_proyecto = ? AND id_junta = ? AND estado = 'abierto' AND fecha_limite >= CURDATE()");
$stmt->bind_param("ii", $idProyecto, $usuario['id_junta']);
$stmt->execute();
$proyecto = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$proyecto) {
    responder(false, 'Este proyecto ya no recibe postulaciones.', ['volver' => 'Proyectos.php']);
}

$stmt = $conexion->prepare("SELECT id_postulacion FROM postulaciones WHERE id_proyecto = ? AND id_vecino = ?");
$stmt->bind_param("ii", $idProyecto, $usuario['id_vecino']);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    responder(false, 'Ya postulaste a este proyecto.', ['volver' => 'Proyectos.php']);
}
$stmt->close();

$stmt = $conexion->prepare("INSERT INTO postulaciones (id_proyecto, id_vecino, motivacion) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $idProyecto, $usuario['id_vecino'], $motivacion);
$stmt->execute();
$stmt->close();

responder(true, 'Tu postulación a "' . $proyecto['nombre'] . '" fue enviada. Te avisaremos por correo cuando el directorio la responda.', ['redirigir' => 'Proyectos.php']);
