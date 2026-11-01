<?php
require_once 'Config.php';
require_once 'Verificar-sesion.php';

$usuario = verificarSesion($conexion);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/Certificados.php');
    exit;
}

$tipo = $_POST['tipo'] ?? '';
$motivo = mb_substr(campo('motivo'), 0, 250);

if (!in_array($tipo, ['residencia', 'socio_activo'], true)) {
    responder(false, 'Elige el tipo de certificado que necesitas.');
}

// El certificado de socio activo solo lo pueden pedir los socios activos
if ($tipo === 'socio_activo' && (int) $usuario['es_socio_activo'] !== 1) {
    responder(false, 'El certificado de vecino vigente es solo para socios activos. Si crees que deberías serlo, contacta a tu directorio.');
}

// No se permiten dos solicitudes pendientes del mismo tipo
$stmt = $conexion->prepare("SELECT id_certificado FROM certificados WHERE id_vecino = ? AND tipo = ? AND estado = 'pendiente'");
$stmt->bind_param("is", $usuario['id_vecino'], $tipo);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    responder(false, 'Ya tienes una solicitud pendiente de este certificado. Espera la respuesta del directorio.');
}
$stmt->close();

$stmt = $conexion->prepare("INSERT INTO certificados (id_vecino, tipo, motivo) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $usuario['id_vecino'], $tipo, $motivo);
$stmt->execute();
$stmt->close();

responder(true, 'Solicitud enviada. Te avisaremos por correo cuando el directorio la responda.', ['recargar' => true]);
