<?php
// Conexión a la base de datos (XAMPP: usuario root sin clave)
$host = 'localhost';
$usuario = 'root';
$clave = '';
$basedatos = 'unidad_territorial';

// Hora de Chile para todas las fechas que muestra el sistema
date_default_timezone_set('America/Santiago');

mysqli_report(MYSQLI_REPORT_OFF);
$conexion = new mysqli($host, $usuario, $clave, $basedatos);

if ($conexion->connect_error) {
    http_response_code(500);
    die('No se pudo conectar a la base de datos. Revisa que MySQL esté encendido en XAMPP.');
}

$conexion->set_charset('utf8mb4');

require_once __DIR__ . '/Funciones.php';
