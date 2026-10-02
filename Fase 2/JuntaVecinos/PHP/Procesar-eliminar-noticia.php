<?php
session_start();
require_once 'Config.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'directorio') {
    header('Location: ../HTML/Acceso.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_noticia = intval($_POST['id_noticia']);

    $stmt = $conexion->prepare("DELETE FROM noticias WHERE id_noticia = ?");
    $stmt->bind_param("i", $id_noticia);
    $stmt->execute();
    $stmt->close();
}

header('Location: ../HTML/GestionNoticias.php');
exit;
?>