<?php
require_once 'Config.php';
require_once 'Verificar-sesion.php';

$usuario = verificarSesion($conexion, 'directorio');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/GestionNoticias.php');
    exit;
}

$titulo = mb_substr(campo('titulo'), 0, 150);
$contenido = mb_substr(campo('contenido'), 0, 500);
$tipo = $_POST['tipo'] ?? '';
$idVecinoDestino = null;

if ($titulo === '' || $contenido === '') {
    responder(false, 'Escribe el título y el contenido de la noticia.');
}
if (!in_array($tipo, ['general', 'grupo', 'especifico'], true)) {
    responder(false, 'Elige a quién va dirigida la noticia.');
}

if ($tipo === 'especifico') {
    $rut = separarRut($_POST['rut_destino'] ?? '');
    if ($rut === null) {
        responder(false, 'Escribe el RUT del vecino al que le quieres enviar la noticia.');
    }

    // El vecino debe ser de la MISMA junta del directorio.
    // No se pueden enviar mensajes a personas de otra junta de vecinos.
    $stmt = $conexion->prepare("SELECT id_vecino FROM vecinos WHERE rut = ? AND dv = ? AND id_junta = ? AND estado = 'aprobado'");
    $stmt->bind_param("isi", $rut['cuerpo'], $rut['dv'], $usuario['id_junta']);
    $stmt->execute();
    $destino = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$destino) {
        responder(false, 'No hay ningún vecino con ese RUT en tu junta. Solo puedes enviar mensajes a vecinos aprobados de tu propia junta de vecinos.');
    }
    $idVecinoDestino = $destino['id_vecino'];
}

$stmt = $conexion->prepare("INSERT INTO noticias (titulo, contenido, tipo, id_vecino_destino, id_junta) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssii", $titulo, $contenido, $tipo, $idVecinoDestino, $usuario['id_junta']);
$stmt->execute();
$stmt->close();

responder(true, 'Noticia publicada.', ['recargar' => true, 'limpiar' => true]);
