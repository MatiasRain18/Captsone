<?php
require_once 'Config.php';
require_once 'Verificar-sesion.php';
require_once 'Enviar-correo.php';

$usuario = verificarSesion($conexion, 'directorio');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/GestionCertificados.php');
    exit;
}

$idCertificado = intval($_POST['id_certificado'] ?? 0);
$accion = $_POST['accion'] ?? '';

if (!in_array($accion, ['aprobar', 'rechazar'], true)) {
    responder(false, 'Acción no válida.');
}
$nuevoEstado = ($accion === 'aprobar') ? 'aprobado' : 'rechazado';

// Solo certificados pendientes de vecinos de la MISMA junta del directorio
$stmt = $conexion->prepare("
    SELECT c.tipo, v.nombre, v.correo
    FROM certificados c
    INNER JOIN vecinos v ON c.id_vecino = v.id_vecino
    WHERE c.id_certificado = ? AND v.id_junta = ? AND c.estado = 'pendiente'
");
$stmt->bind_param("ii", $idCertificado, $usuario['id_junta']);
$stmt->execute();
$cert = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cert) {
    responder(false, 'Esa solicitud ya fue revisada o no pertenece a tu junta.', ['recargar' => true]);
}

// Al aprobar se crea el código de validación que va impreso en el PDF
// (12 caracteres al azar, ej: 8E2C-62AE-411C) y que se revisa en Verificar.php
$codigo = null;
if ($nuevoEstado === 'aprobado') {
    $codigo = implode('-', str_split(strtoupper(bin2hex(random_bytes(6))), 4));
}

$stmt = $conexion->prepare("UPDATE certificados SET estado = ?, codigo_validacion = ?, fecha_respuesta = NOW() WHERE id_certificado = ?");
$stmt->bind_param("ssi", $nuevoEstado, $codigo, $idCertificado);
$stmt->execute();
$stmt->close();

// Aviso por correo al vecino
$tipoTexto = ($cert['tipo'] === 'residencia') ? 'Certificado de Residencia' : 'Certificado de Vecino Vigente';
$nombre = e($cert['nombre']);
$junta = e($usuario['junta']);

if ($nuevoEstado === 'aprobado') {
    $asunto = "Tu $tipoTexto fue aprobado";
    $cuerpo = "<p>Hola $nombre,</p>
        <p>Tu solicitud de <strong>$tipoTexto</strong> fue <strong>aprobada</strong> por el directorio de la $junta.</p>
        <p>Ya puedes descargarlo en PDF ingresando al Sistema Unidad Territorial, en la sección <strong>Certificados</strong>.</p>
        <p>Código de validación: <strong>$codigo</strong></p>";
} else {
    $asunto = "Tu solicitud de $tipoTexto fue rechazada";
    $cuerpo = "<p>Hola $nombre,</p>
        <p>Tu solicitud de <strong>$tipoTexto</strong> fue <strong>rechazada</strong> por el directorio de la $junta.</p>
        <p>Si tienes dudas, comunícate con el directorio de tu junta de vecinos.</p>";
}
$correoEnviado = enviarCorreo($cert['correo'], $cert['nombre'], $asunto, plantillaCorreo($usuario['junta'], $cuerpo));

$texto = ($nuevoEstado === 'aprobado') ? 'Certificado aprobado.' : 'Solicitud rechazada.';
$texto .= $correoEnviado ? ' Se avisó al vecino por correo.' : ' (No se pudo enviar el correo, pero el cambio quedó guardado).';
responder(true, $texto, ['recargar' => true]);
