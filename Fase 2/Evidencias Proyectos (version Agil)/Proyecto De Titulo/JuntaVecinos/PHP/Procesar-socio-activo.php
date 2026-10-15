<?php
require_once 'Config.php';
require_once 'Verificar-sesion.php';

$usuario = verificarSesion($conexion, 'directorio');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/GestionVecinos.php');
    exit;
}

$idVecino = intval($_POST['id_vecino'] ?? 0);
$accion = $_POST['accion'] ?? '';

if (!in_array($accion, ['marcar', 'quitar'], true)) {
    responder(false, 'Acción no válida.');
}
$nuevoValor = ($accion === 'marcar') ? 1 : 0;

// Solo vecinos aprobados de la MISMA junta
$stmt = $conexion->prepare("UPDATE vecinos SET es_socio_activo = ? WHERE id_vecino = ? AND id_junta = ? AND rol = 'vecino' AND estado = 'aprobado'");
$stmt->bind_param("iii", $nuevoValor, $idVecino, $usuario['id_junta']);
$stmt->execute();
$cambio = $stmt->affected_rows;
$stmt->close();

if ($cambio === 0) {
    responder(false, 'No se pudo cambiar: el vecino no pertenece a tu junta o ya tenía ese estado.', ['recargar' => true]);
}

responder(true, $nuevoValor ? 'Vecino marcado como socio activo.' : 'Se quitó la condición de socio activo.', ['recargar' => true]);
