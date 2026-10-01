<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rut = trim($_POST['rut']);
    $clave = $_POST['clave'];

    $stmt = $conexion->prepare("SELECT id_vecino, nombre, clave, rol, estado, es_socio_activo, id_junta FROM vecinos WHERE rut = ?");
    $stmt->bind_param("s", $rut);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $vecino = $resultado->fetch_assoc();

        if (password_verify($clave, $vecino['clave'])) {

            if ($vecino['estado'] === 'pendiente') {
                echo 'Tu inscripción todavía está pendiente de aprobación por tu directiva vecinal. <a href="../HTML/Acceso.html">Volver</a>';
                exit;
            }

            if ($vecino['estado'] === 'rechazado') {
                echo 'Tu inscripción fue rechazada. Contacta a tu junta de vecinos para más información. <a href="../HTML/Acceso.html">Volver</a>';
                exit;
            }

            $_SESSION['id_vecino'] = $vecino['id_vecino'];
            $_SESSION['nombre'] = $vecino['nombre'];
            $_SESSION['rol'] = $vecino['rol'];
            $_SESSION['es_socio_activo'] = $vecino['es_socio_activo'];
            $_SESSION['id_junta'] = $vecino['id_junta'];

            if ($vecino['rol'] === 'directorio') {
                header('Location: ../HTML/PanelDirectorio.html');
            } else {
                header('Location: ../HTML/Panel.html');
            }
            exit;

        } else {
            echo 'RUT o clave incorrectos. <a href="../HTML/Acceso.html">Volver</a>';
        }
    } else {
        echo 'RUT o clave incorrectos. <a href="../HTML/Acceso.html">Volver</a>';
    }

    $stmt->close();
}
?>