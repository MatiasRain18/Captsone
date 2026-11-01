<?php
// AJAX (registro): revisa mientras la persona escribe si el RUT es válido y si ya tiene cuenta
require_once 'Config.php';
header('Content-Type: application/json; charset=utf-8');

$rut = separarRut($_GET['rut'] ?? '');

if (!rutValido($rut)) {
    echo json_encode(['valido' => false, 'disponible' => false, 'mensaje' => 'El RUT no es válido. Revisa el dígito verificador.']);
    exit;
}

$stmt = $conexion->prepare("SELECT id_vecino FROM vecinos WHERE rut = ?");
$stmt->bind_param("i", $rut['cuerpo']);
$stmt->execute();
$existe = $stmt->get_result()->num_rows > 0;
$stmt->close();

echo json_encode([
    'valido' => true,
    'disponible' => !$existe,
    'mensaje' => $existe ? 'Ya existe una cuenta con este RUT.' : 'RUT válido y disponible.',
], JSON_UNESCAPED_UNICODE);
