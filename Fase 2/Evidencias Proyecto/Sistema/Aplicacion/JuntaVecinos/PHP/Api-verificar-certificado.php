<?php
// AJAX (página pública Verificar.php): revisa si un código de certificado es auténtico.
// No necesita sesión: cualquier institución que reciba el PDF puede comprobarlo.
require_once 'Config.php';
header('Content-Type: application/json; charset=utf-8');

$codigo = strtoupper(preg_replace('/[^0-9A-Za-z]/', '', $_GET['codigo'] ?? ''));

if (strlen($codigo) !== 12) {
    echo json_encode(['valido' => false, 'mensaje' => 'El código tiene 12 caracteres, por ejemplo: 8E2C-62AE-411C.'], JSON_UNESCAPED_UNICODE);
    exit;
}
$codigo = implode('-', str_split($codigo, 4));

$stmt = $conexion->prepare("
    SELECT c.id_certificado, c.tipo, c.fecha_respuesta, c.fecha_solicitud,
           v.nombre, v.rut, v.dv, j.nombre AS junta, j.comuna
    FROM certificados c
    INNER JOIN vecinos v ON c.id_vecino = v.id_vecino
    INNER JOIN juntas j ON v.id_junta = j.id_junta
    WHERE c.codigo_validacion = ? AND c.estado = 'aprobado'
");
$stmt->bind_param("s", $codigo);
$stmt->execute();
$c = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$c) {
    echo json_encode(['valido' => false, 'mensaje' => 'No existe ningún certificado aprobado con el código ' . $codigo . '. El documento podría no ser auténtico.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Por privacidad se ocultan los 3 dígitos del medio del RUT
$rutCompleto = formatearRut($c['rut'], $c['dv']);
$rutOculto = preg_replace('/\.(\d{3})-/', '.***-', $rutCompleto);

echo json_encode([
    'valido' => true,
    'mensaje' => 'Certificado auténtico.',
    'certificado' => [
        'codigo' => $codigo,
        'folio' => str_pad($c['id_certificado'], 6, '0', STR_PAD_LEFT),
        'tipo' => ($c['tipo'] === 'residencia') ? 'Certificado de residencia' : 'Certificado de vecino vigente',
        'nombre' => $c['nombre'],
        'rut' => $rutOculto,
        'junta' => $c['junta'],
        'comuna' => $c['comuna'],
        'fecha' => fechaLarga($c['fecha_respuesta'] ?? $c['fecha_solicitud']),
    ],
], JSON_UNESCAPED_UNICODE);
