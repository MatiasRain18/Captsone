<?php
session_start();
require_once 'Config.php';

if (!isset($_SESSION['id_vecino'])) {
    header('Location: ../HTML/Acceso.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_vecino = $_SESSION['id_vecino'];
    $id_actividad = intval($_POST['id_actividad']);

    $stmtCupo = $conexion->prepare("SELECT cupo_maximo, (SELECT COUNT(*) FROM inscripciones_actividades WHERE id_actividad = ?) AS ocupados FROM actividades WHERE id_actividad = ?");
    $stmtCupo->bind_param("ii", $id_actividad, $id_actividad);
    $stmtCupo->execute();
    $resultado = $stmtCupo->get_result()->fetch_assoc();

    if ($resultado && $resultado['ocupados'] < $resultado['cupo_maximo']) {
        $stmt = $conexion->prepare("INSERT INTO inscripciones_actividades (id_actividad, id_vecino) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_actividad, $id_vecino);
        $stmt->execute();
        $stmt->close();
    }

    header('Location: ../HTML/Espacios.php');
    exit;
}
?>