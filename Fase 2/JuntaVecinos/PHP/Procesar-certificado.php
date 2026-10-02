<?php
session_start();
require_once 'Config.php';

if (!isset($_SESSION['id_vecino'])) {
    header('Location: ../HTML/Acceso.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_vecino = $_SESSION['id_vecino'];
    $tipo = $_POST['tipo'];
    $motivo = trim($_POST['motivo']);

    $stmt = $conexion->prepare("INSERT INTO certificados (id_vecino, tipo, motivo) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $id_vecino, $tipo, $motivo);
    $stmt->execute();
    $stmt->close();

    header('Location: ../HTML/Certificados.php');
    exit;
}
?>