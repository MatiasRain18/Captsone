<?php
require_once 'Config.php';
require_once 'Verificar-sesion.php';

$usuario = verificarSesion($conexion, 'directorio');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/GestionNoticias.php');
    exit;
}

$idNoticia = intval($_POST['id_noticia'] ?? 0);

// Solo se pueden borrar noticias de la MISMA junta
$stmt = $conexion->prepare("DELETE FROM noticias WHERE id_noticia = ? AND id_junta = ?");
$stmt->bind_param("ii", $idNoticia, $usuario['id_junta']);
$stmt->execute();
$borradas = $stmt->affected_rows;
$stmt->close();

if ($borradas === 0) {
    responder(false, 'Esa noticia ya no existe o no es de tu junta.', ['recargar' => true]);
}

responder(true, 'Noticia eliminada.', ['recargar' => true]);
