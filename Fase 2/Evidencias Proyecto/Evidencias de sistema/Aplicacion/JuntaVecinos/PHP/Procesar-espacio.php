<?php
require_once 'Config.php';
require_once 'Verificar-sesion.php';

$usuario = verificarSesion($conexion);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/Espacios.php');
    exit;
}

$espacio = $_POST['espacio'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$horario = mb_substr(campo('horario'), 0, 50);
$actividad = mb_substr(campo('actividad'), 0, 200);

if (!in_array($espacio, ['cancha', 'sala', 'plaza'], true)) {
    responder(false, 'Elige el espacio que quieres reservar.');
}
if (!fechaValida($fecha) || $fecha < date('Y-m-d')) {
    responder(false, 'Elige una fecha de hoy en adelante.');
}
if ($horario === '' || $actividad === '') {
    responder(false, 'Escribe el horario y la actividad que vas a realizar.');
}

$stmt = $conexion->prepare("INSERT INTO reservas_espacios (id_vecino, espacio, fecha, horario, actividad) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $usuario['id_vecino'], $espacio, $fecha, $horario, $actividad);
$stmt->execute();
$stmt->close();

responder(true, 'Solicitud de reserva enviada. El directorio la revisará pronto.', ['recargar' => true]);
