<?php
require_once 'Config.php';
require_once 'Verificar-sesion.php';

$usuario = verificarSesion($conexion, 'directorio');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/GestionProyectos.php');
    exit;
}

$nombre = mb_substr(campo('nombre'), 0, 150);
$descripcion = mb_substr(campo('descripcion'), 0, 400);
$fechaLimite = $_POST['fecha_limite'] ?? '';

if ($nombre === '' || $descripcion === '') {
    responder(false, 'Escribe el nombre y la descripción del proyecto.');
}
if (!fechaValida($fechaLimite) || $fechaLimite < date('Y-m-d')) {
    responder(false, 'La fecha límite debe ser de hoy en adelante.');
}

$stmt = $conexion->prepare("INSERT INTO proyectos (id_junta, nombre, descripcion, fecha_limite) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $usuario['id_junta'], $nombre, $descripcion, $fechaLimite);
$stmt->execute();
$stmt->close();

responder(true, 'Proyecto "' . $nombre . '" publicado. Los socios activos ya pueden postular.', ['recargar' => true, 'limpiar' => true]);
