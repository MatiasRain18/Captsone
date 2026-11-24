<?php
// Conexión a la base de datos.
// En XAMPP no hay variables de entorno, así que se usan los valores de siempre (root sin clave).
// En Docker, docker-compose.yml entrega DB_HOST, DB_USER, DB_PASS y DB_NAME.
$host = getenv('DB_HOST') ?: 'localhost';
$usuario = getenv('DB_USER') ?: 'root';
$clave = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$basedatos = getenv('DB_NAME') ?: 'unidad_territorial';

// Hora de Chile para todas las fechas que muestra el sistema
date_default_timezone_set('America/Santiago');

mysqli_report(MYSQLI_REPORT_OFF);
$conexion = new mysqli($host, $usuario, $clave, $basedatos);

if ($conexion->connect_error) {
    http_response_code(500);
    die('No se pudo conectar a la base de datos. Revisa que MySQL esté encendido en XAMPP o que el contenedor de la base de datos esté corriendo en Docker.');
}

$conexion->set_charset('utf8mb4');

require_once __DIR__ . '/Funciones.php';
