<?php
session_start();
require_once 'Config.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'directorio') {
    header('Location: ../HTML/Acceso.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_proyecto = intval($_POST['id_proyecto']);
    $nuevoEstado = ($_POST['accion'] === 'cerrar') ? 'cerrado' : 'abierto';

    $stmt = $conexion->prepare("UPDATE proyectos SET estado = ? WHERE id_proyecto = ?");
    $stmt->bind_param("si", $nuevoEstado, $id_proyecto);
    $stmt->execute();
    $stmt->close();
}

header('Location: ../HTML/GestionProyectos.php');
exit;
?>