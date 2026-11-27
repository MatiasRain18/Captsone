<?php
// Este php revisa que haya una sesión iniciada y que la cuenta siga aprobada.
// En cada página se vuelven a leer los datos desde la base de datos, así si el
// directorio da de baja a alguien o lo marca como socio activo, se nota al tiro.

function verificarSesion($conexion, $rolRequerido = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['id_vecino'])) {
        salirDeSesion('Tu sesión expiró. Vuelve a iniciar sesión.');
    }

    $stmt = $conexion->prepare("
        SELECT v.id_vecino, v.nombre, v.rol, v.cargo, v.estado, v.es_socio_activo, v.id_junta,
               j.nombre AS junta, j.comuna
        FROM vecinos v
        INNER JOIN juntas j ON v.id_junta = j.id_junta
        WHERE v.id_vecino = ?
    ");
    $stmt->bind_param("i", $_SESSION['id_vecino']);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Si la cuenta ya no está aprobada (baja, rechazada), se cierra la sesión
    if (!$usuario || $usuario['estado'] !== 'aprobado') {
        session_unset();
        session_destroy();
        salirDeSesion('Tu cuenta ya no tiene acceso al sistema. Comunícate con tu junta de vecinos.');
    }

    // Se actualizan los datos de la sesión con lo que dice la base de datos
    $_SESSION['nombre'] = $usuario['nombre'];
    $_SESSION['rol'] = $usuario['rol'];
    $_SESSION['cargo'] = $usuario['cargo'];
    $_SESSION['es_socio_activo'] = (int) $usuario['es_socio_activo'];
    $_SESSION['id_junta'] = (int) $usuario['id_junta'];

    // Páginas solo para el directorio
    if ($rolRequerido === 'directorio' && $usuario['rol'] !== 'directorio') {
        if (esAjax()) {
            responder(false, 'Esta acción es solo para el directorio de la junta.');
        }
        header('Location: ../HTML/Panel.php');
        exit;
    }

    return $usuario;
}

// Sin sesión: AJAX recibe un JSON que lo manda al login; una página normal se redirige
function salirDeSesion($mensaje) {
    if (esAjax()) {
        responder(false, $mensaje, ['redirigir' => 'Acceso.html']);
    }
    header('Location: ../HTML/Acceso.html');
    exit;
}
