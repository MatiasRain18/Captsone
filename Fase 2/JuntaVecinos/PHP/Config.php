<?php
$host = 'localhost';
$usuario = 'root';
$clave = '';
$basedatos = 'unidad_territorial';

$conexion = new mysqli($host, $usuario, $clave, $basedatos);

if ($conexion->connect_error) {
    die('Error de conexión: ' . $conexion->connect_error);
}

$conexion->set_charset('utf8mb4');
?>