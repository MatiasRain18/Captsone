<?php
session_start();
require_once 'Config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/Registro.php');
    exit;
}

$nombre = campo('nombre');
$rut = separarRut($_POST['rut'] ?? '');
$direccion = campo('direccion');
$idJunta = intval($_POST['id_junta'] ?? 0);
$correo = campo('correo');
$clave = $_POST['clave'] ?? '';
$claveConfirmar = $_POST['claveConfirmar'] ?? '';
$mayorDe14 = isset($_POST['mayor_14']);

$volver = ['volver' => 'Registro.php'];

// Validaciones en el servidor (aunque el navegador ya las revisa, aquí no se pueden saltar)
if ($nombre === '' || $direccion === '' || $correo === '' || $clave === '') {
    responder(false, 'Completa todos los campos del formulario.', $volver);
}
if (!rutValido($rut)) {
    responder(false, 'El RUT no es válido. Revisa el número y el dígito verificador.', $volver);
}
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    responder(false, 'El correo electrónico no es válido.', $volver);
}
if (strlen($clave) < 8) {
    responder(false, 'La clave debe tener al menos 8 caracteres.', $volver);
}
if ($clave !== $claveConfirmar) {
    responder(false, 'Las claves no coinciden.', $volver);
}
if (!$mayorDe14) {
    responder(false, 'Para inscribirte en una junta de vecinos debes tener 14 años o más.', $volver);
}

$stmt = $conexion->prepare("SELECT id_junta FROM juntas WHERE id_junta = ?");
$stmt->bind_param("i", $idJunta);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    responder(false, 'Selecciona tu junta de vecinos de la lista.', $volver);
}
$stmt->close();

// Mensajes claros si el RUT o el correo ya existen
$stmt = $conexion->prepare("SELECT id_vecino FROM vecinos WHERE rut = ?");
$stmt->bind_param("i", $rut['cuerpo']);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    responder(false, 'Ya existe una cuenta con ese RUT. Si es tuya, inicia sesión.', $volver);
}
$stmt->close();

$stmt = $conexion->prepare("SELECT id_vecino FROM vecinos WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    responder(false, 'Ese correo ya está registrado en otra cuenta. Usa otro correo.', $volver);
}
$stmt->close();

$claveHash = password_hash($clave, PASSWORD_DEFAULT);

$stmt = $conexion->prepare("INSERT INTO vecinos (nombre, rut, dv, direccion, correo, clave, id_junta) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sissssi", $nombre, $rut['cuerpo'], $rut['dv'], $direccion, $correo, $claveHash, $idJunta);

if (!$stmt->execute()) {
    responder(false, 'No se pudo guardar tu inscripción. Inténtalo de nuevo en unos minutos.', $volver);
}
$stmt->close();

responder(true, 'Tu solicitud fue enviada. El directorio de tu junta la revisará y te avisaremos por correo cuando sea aprobada.', [
    'registrado' => true,
    'volver' => 'Acceso.html',
]);
