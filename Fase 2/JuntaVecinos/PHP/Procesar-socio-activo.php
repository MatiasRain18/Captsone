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
    $nuevoValor = ($accion === 'marcar') ? 1 : 0;

    $stmt = $conexion->prepare("UPDATE vecinos SET es_socio_activo = ? WHERE id_vecino = ?");
    $stmt->bind_param("ii", $nuevoValor, $id_vecino);
    $stmt->execute();
    $stmt->close();
}

header('Location: ../HTML/GestionVecinos.php');
exit;
?>