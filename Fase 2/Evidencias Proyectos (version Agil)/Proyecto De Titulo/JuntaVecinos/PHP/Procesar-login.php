<?php
session_start();
require_once 'Config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/Acceso.html');
    exit;
}

$rut = separarRut($_POST['rut'] ?? '');
$clave = $_POST['clave'] ?? '';

if ($rut === null || $clave === '') {
    responder(false, 'Escribe tu RUT y tu clave para ingresar.', ['volver' => 'Acceso.html']);
}

// El RUT se busca por número y dígito verificador (en la base de datos van separados)
$stmt = $conexion->prepare("SELECT id_vecino, nombre, clave, rol, cargo, estado, es_socio_activo, id_junta FROM vecinos WHERE rut = ? AND dv = ?");
$stmt->bind_param("is", $rut['cuerpo'], $rut['dv']);
$stmt->execute();
$vecino = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Mismo mensaje si el RUT no existe o la clave está mal (no se revela cuál de los dos falló)
if (!$vecino || !password_verify($clave, $vecino['clave'])) {
    responder(false, 'El RUT o la clave no son correctos. Revisa los datos e inténtalo de nuevo.', ['volver' => 'Acceso.html']);
}

if ($vecino['estado'] === 'pendiente') {
    responder(false, 'Tu inscripción todavía está pendiente. El directorio de tu junta debe aprobarla antes de que puedas ingresar.', ['volver' => 'Acceso.html']);
}
if ($vecino['estado'] === 'rechazado') {
    responder(false, 'Tu inscripción fue rechazada. Comunícate con tu junta de vecinos para más información.', ['volver' => 'Acceso.html']);
}
if ($vecino['estado'] === 'baja') {
    responder(false, 'Tu cuenta fue dada de baja por el directorio de tu junta. Si crees que es un error, comunícate con ellos.', ['volver' => 'Acceso.html']);
}

// Nueva sesión (evita que alguien reutilice un identificador de sesión antiguo)
session_regenerate_id(true);
$_SESSION['id_vecino'] = $vecino['id_vecino'];
$_SESSION['nombre'] = $vecino['nombre'];
$_SESSION['rol'] = $vecino['rol'];
$_SESSION['cargo'] = $vecino['cargo'];
$_SESSION['es_socio_activo'] = (int) $vecino['es_socio_activo'];
$_SESSION['id_junta'] = (int) $vecino['id_junta'];

$destino = ($vecino['rol'] === 'directorio') ? 'PanelDirectorio.php' : 'Panel.php';
$primerNombre = explode(' ', $vecino['nombre'])[0];
responder(true, '¡Hola, ' . $primerNombre . '! Entrando al sistema…', ['redirigir' => $destino]);
