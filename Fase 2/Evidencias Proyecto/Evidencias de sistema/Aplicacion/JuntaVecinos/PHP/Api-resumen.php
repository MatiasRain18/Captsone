<?php
// AJAX (paneles): cuenta lo pendiente para mostrarlo en las tarjetas del panel
require_once 'Config.php';
require_once 'Verificar-sesion.php';

$usuario = verificarSesion($conexion);
header('Content-Type: application/json; charset=utf-8');

$idJunta = $usuario['id_junta'];
$idVecino = $usuario['id_vecino'];

// Ejecuta una consulta que devuelve un solo número
function contar($conexion, $sql, $tipos, ...$valores) {
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param($tipos, ...$valores);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_row();
    $stmt->close();
    return (int) $fila[0];
}

if ($usuario['rol'] === 'directorio') {
    $datos = [
        'vecinos' => contar($conexion, "SELECT COUNT(*) FROM vecinos WHERE id_junta = ? AND estado = 'pendiente'", "i", $idJunta),
        'certificados' => contar($conexion, "SELECT COUNT(*) FROM certificados c INNER JOIN vecinos v ON c.id_vecino = v.id_vecino WHERE v.id_junta = ? AND c.estado = 'pendiente'", "i", $idJunta),
        'espacios' => contar($conexion, "SELECT COUNT(*) FROM reservas_espacios r INNER JOIN vecinos v ON r.id_vecino = v.id_vecino WHERE v.id_junta = ? AND r.estado = 'pendiente'", "i", $idJunta),
        'proyectos' => contar($conexion, "SELECT COUNT(*) FROM proyectos WHERE id_junta = ? AND estado = 'abierto' AND fecha_limite >= CURDATE()", "i", $idJunta),
        'postulaciones' => contar($conexion, "SELECT COUNT(*) FROM postulaciones po INNER JOIN proyectos p ON po.id_proyecto = p.id_proyecto WHERE p.id_junta = ? AND po.estado = 'pendiente'", "i", $idJunta),
        'noticias' => contar($conexion, "SELECT COUNT(*) FROM noticias WHERE id_junta = ?", "i", $idJunta),
        'directiva' => contar($conexion, "SELECT 3 - COUNT(*) FROM vecinos WHERE id_junta = ? AND cargo IS NOT NULL AND estado = 'aprobado'", "i", $idJunta),
        'socios' => contar($conexion, "SELECT COUNT(*) FROM vecinos WHERE id_junta = ? AND rol = 'vecino' AND estado = 'aprobado' AND es_socio_activo = 1", "i", $idJunta),
        'aprobados' => contar($conexion, "SELECT COUNT(*) FROM vecinos WHERE id_junta = ? AND rol = 'vecino' AND estado = 'aprobado'", "i", $idJunta),
    ];
} else {
    $esSocio = (int) $usuario['es_socio_activo'];
    $datos = [
        'certificados' => contar($conexion, "SELECT COUNT(*) FROM certificados WHERE id_vecino = ? AND estado = 'aprobado'", "i", $idVecino),
        'proyectos' => contar($conexion, "SELECT COUNT(*) FROM proyectos p WHERE p.id_junta = ? AND p.estado = 'abierto' AND p.fecha_limite >= CURDATE() AND NOT EXISTS (SELECT 1 FROM postulaciones WHERE id_proyecto = p.id_proyecto AND id_vecino = ?)", "ii", $idJunta, $idVecino),
        'noticias' => contar($conexion, "SELECT COUNT(*) FROM noticias WHERE id_junta = ? AND fecha_publicacion >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND (tipo = 'general' OR (tipo = 'grupo' AND ? = 1) OR (tipo = 'especifico' AND id_vecino_destino = ?))", "iii", $idJunta, $esSocio, $idVecino),
        'espacios' => contar($conexion, "SELECT COUNT(*) FROM actividades a WHERE a.id_junta = ? AND a.fecha >= CURDATE() AND (SELECT COUNT(*) FROM inscripciones_actividades WHERE id_actividad = a.id_actividad) < a.cupo_maximo", "i", $idJunta),
    ];
}

echo json_encode(['ok' => true, 'datos' => $datos]);
