<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $rut = trim($_POST['rut']);
    $direccion = trim($_POST['direccion']);
    $idJunta = intval($_POST['id_junta']);
    $correo = trim($_POST['correo']);
    $clave = $_POST['clave'];
    $claveConfirmar = $_POST['claveConfirmar'];

    if ($clave !== $claveConfirmar) {
        die('Las claves no coinciden. <a href="../HTML/Registro.php">Volver</a>');
    }

    $claveHash = password_hash($clave, PASSWORD_DEFAULT);

    $stmt = $conexion->prepare("INSERT INTO vecinos (nombre, rut, direccion, correo, clave, id_junta) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $nombre, $rut, $direccion, $correo, $claveHash, $idJunta);

    if ($stmt->execute()) {
        header('Location: ../HTML/Acceso.html?registro=ok');
        exit;
    } else {
        echo 'Error al registrar: ' . $stmt->error . '<br>Es posible que el RUT o correo ya estén registrados. <a href="../HTML/Registro.php">Volver</a>';
    }

    $stmt->close();
}
?>