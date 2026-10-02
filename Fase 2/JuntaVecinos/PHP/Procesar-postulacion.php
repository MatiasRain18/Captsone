<?php
session_start();
require_once 'Config.php';

if (!isset($_SESSION['id_vecino']) || ($_SESSION['es_socio_activo'] ?? 0) != 1) {
    header('Location: ../HTML/Acceso.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_vecino = $_SESSION['id_vecino'];
    $id_proyecto = intval($_POST['id_proyecto']);
    $motivacion = trim($_POST['motivacion']);

    $stmtRevisar = $conexion->prepare("SELECT id_postulacion FROM postulaciones WHERE id_proyecto = ? AND id_vecino = ?");
    $stmtRevisar->bind_param("ii", $id_proyecto, $id_vecino);
    $stmtRevisar->execute();
    $yaExiste = $stmtRevisar->get_result()->num_rows > 0;

    if (!$yaExiste) {
        $stmt = $conexion->prepare("INSERT INTO postulaciones (id_proyecto, id_vecino, motivacion) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $id_proyecto, $id_vecino, $motivacion);
        $stmt->execute();
        $stmt->close();
    }

    header('Location: ../HTML/Proyectos.php');
    exit;
}
?>