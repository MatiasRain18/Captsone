<?php
session_start();
require_once 'Config.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'directorio') {
    header('Location: ../HTML/Acceso.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idJuntaSesion = $_SESSION['id_junta'];
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $fechaLimite = $_POST['fecha_limite'];

    $stmt = $conexion->prepare("INSERT INTO proyectos (id_junta, nombre, descripcion, fecha_limite) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $idJuntaSesion, $nombre, $descripcion, $fechaLimite);
    $stmt->execute();
    $stmt->close();

    header('Location: ../HTML/GestionProyectos.php');
    exit;
}
?>