<?php
require_once 'Config.php';
require_once 'Verificar-sesion.php';

$usuario = verificarSesion($conexion, 'directorio');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/GestionEspacios.php');
    exit;
}

$idReserva = intval($_POST['id_reserva'] ?? 0);
$accion = $_POST['accion'] ?? '';

if (!in_array($accion, ['aprobar', 'rechazar'], true)) {
    responder(false, 'Acción no válida.');
}
$nuevoEstado = ($accion === 'aprobar') ? 'aprobado' : 'rechazado';

// Solo reservas pendientes de vecinos de la MISMA junta
$stmt = $conexion->prepare("
    UPDATE reservas_espacios r
    INNER JOIN vecinos v ON r.id_vecino = v.id_vecino
    SET r.estado = ?
    WHERE r.id_reserva = ? AND v.id_junta = ? AND r.estado = 'pendiente'
");
$stmt->bind_param("sii", $nuevoEstado, $idReserva, $usuario['id_junta']);
$stmt->execute();
$cambio = $stmt->affected_rows;
$stmt->close();

if ($cambio === 0) {
    responder(false, 'Esa reserva ya fue revisada o no pertenece a tu junta.', ['recargar' => true]);
}

responder(true, ($nuevoEstado === 'aprobado') ? 'Reserva aprobada.' : 'Reserva rechazada.', ['recargar' => true]);
