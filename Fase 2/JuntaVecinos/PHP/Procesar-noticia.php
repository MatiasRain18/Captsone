<?php
session_start();
require_once 'Config.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'directorio') {
    header('Location: ../HTML/Acceso.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    $tipo = $_POST['tipo'];
    $rutDestino = trim($_POST['rut_destino']);
    $idVecinoDestino = null;
    $idJuntaSesion = $_SESSION['id_junta'];

    if ($tipo === 'especifico' && $rutDestino !== '') {
        $buscar = $conexion->prepare("SELECT id_vecino FROM vecinos WHERE rut = ?");
        $buscar->bind_param("s", $rutDestino);
        $buscar->execute();
        $res = $buscar->get_result();
        if ($res->num_rows === 1) {
            $idVecinoDestino = $res->fetch_assoc()['id_vecino'];
        } else {
            die('No se encontró ningún vecino con ese RUT. <a href="../HTML/GestionNoticias.php">Volver</a>');
        }
    }

    $stmt = $conexion->prepare("INSERT INTO noticias (titulo, contenido, tipo, id_vecino_destino, id_junta) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssii", $titulo, $contenido, $tipo, $idVecinoDestino, $idJuntaSesion);
    $stmt->execute();
    $stmt->close();

    header('Location: ../HTML/GestionNoticias.php');
    exit;
}
?>