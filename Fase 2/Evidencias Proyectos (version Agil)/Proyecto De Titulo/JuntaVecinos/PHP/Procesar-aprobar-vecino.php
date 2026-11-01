<?php
require_once 'Config.php';
require_once 'Verificar-sesion.php';
require_once 'Enviar-correo.php';

$usuario = verificarSesion($conexion, 'directorio');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/GestionVecinos.php');
    exit;
}

$idVecino = intval($_POST['id_vecino'] ?? 0);
$accion = $_POST['accion'] ?? '';

if (!in_array($accion, ['aprobar', 'rechazar'], true)) {
    responder(false, 'Acción no válida.');
}

// Solo inscripciones pendientes de la MISMA junta del directorio
$stmt = $conexion->prepare("SELECT nombre, correo FROM vecinos WHERE id_vecino = ? AND id_junta = ? AND estado = 'pendiente'");
$stmt->bind_param("ii", $idVecino, $usuario['id_junta']);
$stmt->execute();
$vecino = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$vecino) {
    responder(false, 'Esa inscripción ya fue revisada o no pertenece a tu junta.', ['recargar' => true]);
}

$nuevoEstado = ($accion === 'aprobar') ? 'aprobado' : 'rechazado';
$stmt = $conexion->prepare("UPDATE vecinos SET estado = ? WHERE id_vecino = ?");
$stmt->bind_param("si", $nuevoEstado, $idVecino);
$stmt->execute();
$stmt->close();

// Aviso por correo al vecino
$nombre = e($vecino['nombre']);
$junta = e($usuario['junta']);
if ($nuevoEstado === 'aprobado') {
    $asunto = 'Tu inscripción en la ' . $usuario['junta'] . ' fue aprobada';
    $cuerpo = "<p>Hola $nombre,</p>
        <p>El directorio de la <strong>$junta</strong> aprobó tu inscripción.</p>
        <p>Ya puedes ingresar al Sistema Unidad Territorial con tu RUT y la clave que creaste.</p>";
} else {
    $asunto = 'Tu inscripción en la ' . $usuario['junta'] . ' fue rechazada';
    $cuerpo = "<p>Hola $nombre,</p>
        <p>El directorio de la <strong>$junta</strong> rechazó tu inscripción.</p>
        <p>Si crees que es un error, comunícate directamente con tu junta de vecinos.</p>";
}
enviarCorreo($vecino['correo'], $vecino['nombre'], $asunto, plantillaCorreo($usuario['junta'], $cuerpo));

$texto = ($nuevoEstado === 'aprobado')
    ? $vecino['nombre'] . ' ya puede ingresar al sistema.'
    : 'Se rechazó la inscripción de ' . $vecino['nombre'] . '.';
responder(true, $texto, ['recargar' => true]);
