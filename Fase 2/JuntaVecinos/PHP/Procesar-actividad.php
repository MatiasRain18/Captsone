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
    $fecha = $_POST['fecha'];
    $cupoMaximo = intval($_POST['cupo_maximo']);

    $stmt = $conexion->prepare("INSERT INTO actividades (id_junta, nombre, descripcion, fecha, cupo_maximo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssi", $idJuntaSesion, $nombre, $descripcion, $fecha, $cupoMaximo);
    $stmt->execute();
    $stmt->close();

    header('Location: ../HTML/GestionEspacios.php');
    exit;
}
?>