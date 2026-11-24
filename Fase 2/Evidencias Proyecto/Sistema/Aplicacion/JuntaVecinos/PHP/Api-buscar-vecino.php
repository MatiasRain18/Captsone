<?php
// AJAX (noticias): busca el RUT SOLO dentro de la junta del directorio.
// Así el directorio ve al tiro si el vecino es de su junta antes de enviarle un mensaje.
require_once 'Config.php';
require_once 'Verificar-sesion.php';

$usuario = verificarSesion($conexion, 'directorio');
header('Content-Type: application/json; charset=utf-8');

$rut = separarRut($_GET['rut'] ?? '');
if ($rut === null) {
    echo json_encode(['encontrado' => false, 'mensaje' => 'Escribe el RUT completo con su dígito verificador.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = $conexion->prepare("SELECT nombre FROM vecinos WHERE rut = ? AND dv = ? AND id_junta = ? AND estado = 'aprobado'");
$stmt->bind_param("isi", $rut['cuerpo'], $rut['dv'], $usuario['id_junta']);
$stmt->execute();
$vecino = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($vecino) {
    echo json_encode(['encontrado' => true, 'mensaje' => $vecino['nombre'] . ' — vecino de tu junta.'], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['encontrado' => false, 'mensaje' => 'Este RUT no corresponde a ningún vecino de tu junta. No se le puede enviar el mensaje.'], JSON_UNESCAPED_UNICODE);
}
