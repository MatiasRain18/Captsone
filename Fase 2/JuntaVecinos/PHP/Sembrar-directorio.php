<?php
require_once 'Config.php';

$nombre = 'SuperAdmin';
$rut = '99.999.999-9';
$direccion = 'Cerro Navia 6730';
$junta = 'Las Lumas';
$correo = 'MatiasCheuquecoy@gmail.com';
$claveTexto = 'Matias21';
$idJunta = 1;
$claveHash = password_hash($claveTexto, PASSWORD_DEFAULT);

$stmt = $conexion->prepare("INSERT INTO vecinos (nombre, rut, direccion, junta, correo, clave, id_junta, rol, estado) VALUES (?, ?, ?, ?, ?, ?, ?, 'directorio', 'aprobado')");
$stmt->bind_param("ssssssi", $nombre, $rut, $direccion, $junta, $correo, $claveHash, $idJunta);

if ($stmt->execute()) {
    echo 'Cuenta de directorio creada correctamente, asignada a la junta con id_junta = ' . $idJunta . '. Puedes borrar este archivo ahora para mayor seguridad.';
} else {
    echo 'Error: ' . $stmt->error;
}
?>