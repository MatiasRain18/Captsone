<?php
session_start();
require_once 'Config.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'directorio') {
    header('Location: ../HTML/Acceso.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_vecino = intval($_POST['id_vecino']);
    $accion = $_POST['accion'];

    $nuevoEstado = ($accion === 'aprobar') ? 'aprobado' : 'rechazado';

    $stmt = $conexion->prepare("UPDATE vecinos SET estado = ? WHERE id_vecino = ?");
    $stmt->bind_param("si", $nuevoEstado, $id_vecino);
    $stmt->execute();
    $stmt->close();
}

header('Location: ../HTML/GestionVecinos.php');
exit;
?>