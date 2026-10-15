<?php
require_once 'Config.php';
require_once 'Verificar-sesion.php';

// Solo el directorio puede dar de baja o reactivar vecinos
$usuario = verificarSesion($conexion, 'directorio');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/GestionVecinos.php');
    exit;
}

$idVecino = intval($_POST['id_vecino'] ?? 0);
$accion = $_POST['accion'] ?? '';

if ($accion === 'baja') {
    $estadoActual = 'aprobado';
    $estadoNuevo = 'baja';
} elseif ($accion === 'reactivar') {
    $estadoActual = 'baja';
    $estadoNuevo = 'aprobado';
} else {
    responder(false, 'Acción no válida.');
}

// Reglas de seguridad:
// - Solo vecinos de la MISMA junta del directorio
// - Solo cuentas con rol 'vecino' (no se puede dar de baja a un directorio, ni a sí mismo)
$stmt = $conexion->prepare("UPDATE vecinos SET estado = ? WHERE id_vecino = ? AND id_junta = ? AND rol = 'vecino' AND estado = ?");
$stmt->bind_param("siis", $estadoNuevo, $idVecino, $usuario['id_junta'], $estadoActual);
$stmt->execute();
$cambio = $stmt->affected_rows;
$stmt->close();

if ($cambio === 0) {
    responder(false, 'No se pudo hacer el cambio. Actualiza la página e inténtalo de nuevo.', ['recargar' => true]);
}

responder(true, ($accion === 'baja') ? 'El vecino fue dado de baja. Ya no puede ingresar al sistema.' : 'El vecino fue reactivado y puede volver a ingresar.', ['recargar' => true]);
