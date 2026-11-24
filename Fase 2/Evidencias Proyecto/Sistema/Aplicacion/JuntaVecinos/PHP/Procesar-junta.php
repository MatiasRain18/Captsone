<?php
require_once 'Config.php';
require_once 'Verificar-sesion.php';

$usuario = verificarSesion($conexion, 'directorio');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/GestionJuntas.php');
    exit;
}

$nombreJunta = mb_substr(campo('nombre_junta'), 0, 150);
$comuna = mb_substr(campo('comuna'), 0, 100);
$direccionSede = mb_substr(campo('direccion_sede'), 0, 200);
$correoJunta = campo('correo_junta');

if ($nombreJunta === '' || $comuna === '' || $direccionSede === '') {
    responder(false, 'Completa el nombre, la comuna y la dirección de la sede.');
}
if (!filter_var($correoJunta, FILTER_VALIDATE_EMAIL)) {
    responder(false, 'El correo de contacto de la junta no es válido.');
}

// Una junta de vecinos debe tener directiva completa: presidente, secretario y tesorero
$cargos = ['presidente', 'secretario', 'tesorero'];
$directiva = [];
$rutsUsados = [];
$correosUsados = [];

foreach ($cargos as $cargo) {
    $nombreCargo = nombreCargo($cargo);
    $persona = [
        'cargo' => $cargo,
        'nombre' => mb_substr(campo('nombre_' . $cargo), 0, 150),
        'rut' => separarRut($_POST['rut_' . $cargo] ?? ''),
        'correo' => campo('correo_' . $cargo),
        'clave' => $_POST['clave_' . $cargo] ?? '',
    ];

    if ($persona['nombre'] === '') {
        responder(false, "Escribe el nombre del $nombreCargo.");
    }
    if (!rutValido($persona['rut'])) {
        responder(false, "El RUT del $nombreCargo no es válido.");
    }
    if (!filter_var($persona['correo'], FILTER_VALIDATE_EMAIL)) {
        responder(false, "El correo del $nombreCargo no es válido.");
    }
    if (strlen($persona['clave']) < 8) {
        responder(false, "La clave del $nombreCargo debe tener al menos 8 caracteres.");
    }
    if (in_array($persona['rut']['cuerpo'], $rutsUsados, true)) {
        responder(false, 'Cada cargo debe ser una persona distinta: hay RUT repetidos.');
    }
    if (in_array(strtolower($persona['correo']), $correosUsados, true)) {
        responder(false, 'Cada cargo debe tener un correo distinto.');
    }

    // El RUT y el correo no pueden existir en otra cuenta
    $stmt = $conexion->prepare("SELECT id_vecino FROM vecinos WHERE rut = ? OR correo = ?");
    $stmt->bind_param("is", $persona['rut']['cuerpo'], $persona['correo']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        responder(false, "El RUT o el correo del $nombreCargo ya tienen una cuenta en el sistema.");
    }
    $stmt->close();

    $rutsUsados[] = $persona['rut']['cuerpo'];
    $correosUsados[] = strtolower($persona['correo']);
    $directiva[] = $persona;
}

// Todo se guarda junto: si algo falla, no queda una junta a medias
$conexion->begin_transaction();
try {
    $stmt = $conexion->prepare("INSERT INTO juntas (nombre, comuna, direccion_sede, correo_contacto) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombreJunta, $comuna, $direccionSede, $correoJunta);
    if (!$stmt->execute()) {
        throw new Exception('junta');
    }
    $idJuntaNueva = $conexion->insert_id;
    $stmt->close();

    $stmt = $conexion->prepare("INSERT INTO vecinos (nombre, rut, dv, direccion, correo, clave, id_junta, rol, cargo, estado) VALUES (?, ?, ?, ?, ?, ?, ?, 'directorio', ?, 'aprobado')");
    foreach ($directiva as $p) {
        $claveHash = password_hash($p['clave'], PASSWORD_DEFAULT);
        $stmt->bind_param("sissssis", $p['nombre'], $p['rut']['cuerpo'], $p['rut']['dv'], $direccionSede, $p['correo'], $claveHash, $idJuntaNueva, $p['cargo']);
        if (!$stmt->execute()) {
            throw new Exception('directiva');
        }
    }
    $stmt->close();

    $conexion->commit();
} catch (Exception $ex) {
    $conexion->rollback();
    responder(false, 'No se pudo crear la junta. Revisa los datos e inténtalo de nuevo.');
}

responder(true, 'La ' . $nombreJunta . ' fue creada con su directiva completa.', ['recargar' => true, 'limpiar' => true]);
