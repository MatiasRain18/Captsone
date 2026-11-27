<?php
require_once 'Config.php';
require_once 'Verificar-sesion.php';

$usuario = verificarSesion($conexion, 'directorio');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/GestionProyectos.php');
    exit;
}

$idProyecto = intval($_POST['id_proyecto'] ?? 0);
$accion = $_POST['accion'] ?? '';

if (!in_array($accion, ['cerrar', 'abrir'], true)) {
    responder(false, 'Acción no válida.');
}
$nuevoEstado = ($accion === 'cerrar') ? 'cerrado' : 'abierto';

// Solo proyectos de la MISMA junta
$stmt = $conexion->prepare("UPDATE proyectos SET estado = ? WHERE id_proyecto = ? AND id_junta = ?");
$stmt->bind_param("sii", $nuevoEstado, $idProyecto, $usuario['id_junta']);
$stmt->execute();
$stmt->close();

responder(true, ($nuevoEstado === 'cerrado') ? 'Proyecto cerrado. Ya no recibe postulaciones.' : 'Proyecto reabierto.', ['recargar' => true]);
