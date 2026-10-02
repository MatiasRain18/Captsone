<?php
session_start();
require_once 'Config.php';

if (!isset($_SESSION['id_vecino'])) {
    header('Location: ../HTML/Acceso.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_vecino = $_SESSION['id_vecino'];
    $espacio = $_POST['espacio'];
    $fecha = $_POST['fecha'];
    $horario = trim($_POST['horario']);
    $actividad = trim($_POST['actividad']);

    $stmt = $conexion->prepare("INSERT INTO reservas_espacios (id_vecino, espacio, fecha, horario, actividad) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $id_vecino, $espacio, $fecha, $horario, $actividad);
    $stmt->execute();
    $stmt->close();

    header('Location: ../HTML/Espacios.php');
    exit;
}
?>