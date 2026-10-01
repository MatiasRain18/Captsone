<?php
session_start();
require_once 'Config.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'directorio') {
    header('Location: ../HTML/Acceso.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_reserva = intval($_POST['id_reserva']);
    $accion = $_POST['accion'];
    $nuevoEstado = ($accion === 'aprobar') ? 'aprobado' : 'rechazado';

    $stmt = $conexion->prepare("UPDATE reservas_espacios SET estado = ? WHERE id_reserva = ?");
    $stmt->bind_param("si", $nuevoEstado, $id_reserva);
    $stmt->execute();
    $stmt->close();
}

header('Location: ../HTML/GestionEspacios.php');
exit;
?>