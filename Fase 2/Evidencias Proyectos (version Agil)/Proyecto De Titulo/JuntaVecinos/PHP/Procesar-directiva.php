<?php
require_once 'Config.php';
require_once 'Verificar-sesion.php';

// Asigna o quita los cargos de secretario(a) y tesorero(a) dentro de la propia junta
$usuario = verificarSesion($conexion, 'directorio');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/GestionDirectiva.php');
    exit;
}

$accion = $_POST['accion'] ?? '';
$cargo = $_POST['cargo'] ?? '';
$idJunta = $usuario['id_junta'];

// El cargo de presidente no se cambia desde aquí (es quien creó la junta)
if (!in_array($cargo, ['secretario', 'tesorero'], true)) {
    responder(false, 'Solo se pueden asignar los cargos de secretario(a) y tesorero(a).');
}

if ($accion === 'asignar') {
    $idVecino = intval($_POST['id_vecino'] ?? 0);

    // El cargo debe estar libre
    $stmt = $conexion->prepare("SELECT id_vecino FROM vecinos WHERE id_junta = ? AND cargo = ?");
    $stmt->bind_param("is", $idJunta, $cargo);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        responder(false, 'Ese cargo ya está ocupado. Primero quita a la persona actual.', ['recargar' => true]);
    }
    $stmt->close();

    // Solo un vecino aprobado de la MISMA junta puede asumir el cargo
    $stmt = $conexion->prepare("UPDATE vecinos SET rol = 'directorio', cargo = ? WHERE id_vecino = ? AND id_junta = ? AND rol = 'vecino' AND estado = 'aprobado'");
    $stmt->bind_param("sii", $cargo, $idVecino, $idJunta);
    $stmt->execute();
    $cambio = $stmt->affected_rows;
    $stmt->close();

    if ($cambio === 0) {
        responder(false, 'Elige un vecino aprobado de tu junta.');
    }
    responder(true, 'Cargo de ' . nombreCargo($cargo) . ' asignado. Desde ahora esa persona entra al panel del directorio.', ['recargar' => true]);
}

if ($accion === 'quitar') {
    // Vuelve a ser vecino (no se puede quitar el cargo a uno mismo)
    $stmt = $conexion->prepare("UPDATE vecinos SET rol = 'vecino', cargo = NULL WHERE id_junta = ? AND cargo = ? AND id_vecino <> ?");
    $stmt->bind_param("isi", $idJunta, $cargo, $usuario['id_vecino']);
    $stmt->execute();
    $cambio = $stmt->affected_rows;
    $stmt->close();

    if ($cambio === 0) {
        responder(false, 'No puedes quitarte tu propio cargo. Pídeselo a otro integrante del directorio.');
    }
    responder(true, 'Se quitó el cargo de ' . nombreCargo($cargo) . '. La persona sigue como vecino de la junta.', ['recargar' => true]);
}

responder(false, 'Acción no válida.');
