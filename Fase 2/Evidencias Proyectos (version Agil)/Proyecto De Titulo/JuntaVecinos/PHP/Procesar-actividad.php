<?php
require_once 'Config.php';
require_once 'Verificar-sesion.php';

$usuario = verificarSesion($conexion, 'directorio');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/GestionEspacios.php');
    exit;
}

$nombre = mb_substr(campo('nombre'), 0, 150);
$descripcion = mb_substr(campo('descripcion'), 0, 300);
$fecha = $_POST['fecha'] ?? '';
$cupoMaximo = intval($_POST['cupo_maximo'] ?? 0);

if ($nombre === '') {
    responder(false, 'Escribe el nombre de la actividad.');
}
if (!fechaValida($fecha) || $fecha < date('Y-m-d')) {
    responder(false, 'La fecha de la actividad debe ser de hoy en adelante.');
}
if ($cupoMaximo < 1 || $cupoMaximo > 1000) {
    responder(false, 'El cupo debe ser un número entre 1 y 1000.');
}

$stmt = $conexion->prepare("INSERT INTO actividades (id_junta, nombre, descripcion, fecha, cupo_maximo) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isssi", $usuario['id_junta'], $nombre, $descripcion, $fecha, $cupoMaximo);
$stmt->execute();
$stmt->close();

responder(true, 'Actividad "' . $nombre . '" creada.', ['recargar' => true, 'limpiar' => true]);
