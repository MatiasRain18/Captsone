<?php
session_start();
require_once 'Config.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'directorio') {
    header('Location: ../HTML/Acceso.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_certificado = intval($_POST['id_certificado']);
    $accion = $_POST['accion'];
    $nuevoEstado = ($accion === 'aprobar') ? 'aprobado' : 'rechazado';

    $stmt = $conexion->prepare("UPDATE certificados SET estado = ? WHERE id_certificado = ?");
    $stmt->bind_param("si", $nuevoEstado, $id_certificado);
    $stmt->execute();
    $stmt->close();
}

header('Location: ../HTML/GestionCertificados.php');
exit;
?>