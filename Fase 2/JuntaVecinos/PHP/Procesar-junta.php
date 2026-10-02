<?php
session_start();
require_once 'Config.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'directorio') {
    header('Location: ../HTML/Acceso.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreJunta = trim($_POST['nombre_junta']);
    $comuna = trim($_POST['comuna']);
    $direccionSede = trim($_POST['direccion_sede']);
    $correoJunta = trim($_POST['correo_junta']);

    $nombreDirector = trim($_POST['nombre_director']);
    $rutDirector = trim($_POST['rut_director']);
    $correoDirector = trim($_POST['correo_director']);
    $claveDirector = $_POST['clave_director'];

    $stmtJunta = $conexion->prepare("INSERT INTO juntas (nombre, comuna, direccion_sede, correo_contacto) VALUES (?, ?, ?, ?)");
    $stmtJunta->bind_param("ssss", $nombreJunta, $comuna, $direccionSede, $correoJunta);

    if ($stmtJunta->execute()) {
        $idJuntaNueva = $conexion->insert_id;
        $claveHash = password_hash($claveDirector, PASSWORD_DEFAULT);

        $stmtDirector = $conexion->prepare("INSERT INTO vecinos (nombre, rut, direccion, correo, clave, id_junta, rol, estado) VALUES (?, ?, ?, ?, ?, ?, 'directorio', 'aprobado')");
        $stmtDirector->bind_param("sssssi", $nombreDirector, $rutDirector, $direccionSede, $correoDirector, $claveHash, $idJuntaNueva);

        if ($stmtDirector->execute()) {
            header('Location: ../HTML/GestionJuntas.php');
            exit;
        } else {
            echo 'La junta se creó, pero hubo un error al crear el director: ' . $stmtDirector->error . '<br>Es posible que el RUT o correo ya estén registrados. <a href="../HTML/GestionJuntas.php">Volver</a>';
        }
        $stmtDirector->close();
    } else {
        echo 'Error al crear la junta: ' . $stmtJunta->error . '<a href="../HTML/GestionJuntas.php">Volver</a>';
    }

    $stmtJunta->close();
}
?>