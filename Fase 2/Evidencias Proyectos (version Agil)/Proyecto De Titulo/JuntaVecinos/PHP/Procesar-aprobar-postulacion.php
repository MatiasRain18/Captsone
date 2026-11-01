<?php
require_once 'Config.php';
require_once 'Verificar-sesion.php';
require_once 'Enviar-correo.php';

// El directorio aprueba o rechaza la postulación de un socio a un proyecto vecinal
$usuario = verificarSesion($conexion, 'directorio');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/GestionProyectos.php');
    exit;
}

$idPostulacion = intval($_POST['id_postulacion'] ?? 0);
$accion = $_POST['accion'] ?? '';

if (!in_array($accion, ['aprobar', 'rechazar'], true)) {
    responder(false, 'Acción no válida.');
}
$nuevoEstado = ($accion === 'aprobar') ? 'aprobado' : 'rechazado';

// Solo postulaciones pendientes de proyectos de la MISMA junta del directorio
$stmt = $conexion->prepare("
    SELECT p.nombre AS proyecto, v.nombre, v.correo
    FROM postulaciones po
    INNER JOIN proyectos p ON po.id_proyecto = p.id_proyecto
    INNER JOIN vecinos v ON po.id_vecino = v.id_vecino
    WHERE po.id_postulacion = ? AND p.id_junta = ? AND po.estado = 'pendiente'
");
$stmt->bind_param("ii", $idPostulacion, $usuario['id_junta']);
$stmt->execute();
$postulacion = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$postulacion) {
    responder(false, 'Esa postulación ya fue revisada o no pertenece a tu junta.', ['recargar' => true]);
}

$stmt = $conexion->prepare("UPDATE postulaciones SET estado = ?, fecha_respuesta = NOW() WHERE id_postulacion = ?");
$stmt->bind_param("si", $nuevoEstado, $idPostulacion);
$stmt->execute();
$stmt->close();

// Aviso por correo al vecino
$nombre = e($postulacion['nombre']);
$proyecto = e($postulacion['proyecto']);
$junta = e($usuario['junta']);

if ($nuevoEstado === 'aprobado') {
    $asunto = 'Tu postulación al proyecto "' . $postulacion['proyecto'] . '" fue aprobada';
    $cuerpo = "<p>Hola $nombre,</p>
        <p>El directorio de la <strong>$junta</strong> <strong>aprobó</strong> tu postulación al proyecto vecinal <strong>$proyecto</strong>.</p>
        <p>Pronto el directorio se comunicará contigo con los siguientes pasos.</p>";
} else {
    $asunto = 'Tu postulación al proyecto "' . $postulacion['proyecto'] . '" fue rechazada';
    $cuerpo = "<p>Hola $nombre,</p>
        <p>El directorio de la <strong>$junta</strong> <strong>rechazó</strong> tu postulación al proyecto vecinal <strong>$proyecto</strong>.</p>
        <p>Si tienes dudas, comunícate con el directorio de tu junta de vecinos.</p>";
}
$correoEnviado = enviarCorreo($postulacion['correo'], $postulacion['nombre'], $asunto, plantillaCorreo($usuario['junta'], $cuerpo));

$texto = ($nuevoEstado === 'aprobado')
    ? 'Postulación de ' . $postulacion['nombre'] . ' aprobada.'
    : 'Postulación de ' . $postulacion['nombre'] . ' rechazada.';
$texto .= $correoEnviado ? ' Se le avisó por correo.' : ' (No se pudo enviar el correo, pero el cambio quedó guardado).';
responder(true, $texto, ['recargar' => true]);
