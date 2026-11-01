<?php
require_once '../PHP/Config.php';
require_once '../PHP/Verificar-sesion.php';

$usuario = verificarSesion($conexion, 'directorio');

$stmt = $conexion->prepare("
    SELECT c.id_certificado, c.tipo, c.motivo, c.fecha_solicitud, v.nombre, v.rut, v.dv, v.es_socio_activo
    FROM certificados c
    INNER JOIN vecinos v ON c.id_vecino = v.id_vecino
    WHERE c.estado = 'pendiente' AND v.id_junta = ?
    ORDER BY c.fecha_solicitud ASC
");
$stmt->bind_param("i", $usuario['id_junta']);
$stmt->execute();
$pendientes = $stmt->get_result();

// Últimos certificados respondidos (historial)
$stmt = $conexion->prepare("
    SELECT c.tipo, c.estado, c.codigo_validacion, c.fecha_respuesta, v.nombre
    FROM certificados c
    INNER JOIN vecinos v ON c.id_vecino = v.id_vecino
    WHERE c.estado <> 'pendiente' AND v.id_junta = ?
    ORDER BY c.fecha_respuesta DESC, c.id_certificado DESC
    LIMIT 8
");
$stmt->bind_param("i", $usuario['id_junta']);
$stmt->execute();
$historial = $stmt->get_result();

$etiquetaTipo = ['residencia' => 'Certificado de residencia', 'socio_activo' => 'Certificado de vecino vigente'];

$tituloPagina = 'Certificados';
$paginaActiva = 'GestionCertificados.php';
require '../PHP/Encabezado.php';
?>

<header class="pagina-cabecera">
  <h1>Certificados</h1>
  <p>Revisa cada solicitud. Al aprobarla se genera un código de validación y el vecino recibe un correo para descargar su PDF.</p>
</header>

<section class="bloque" aria-labelledby="tituloPendientes">
  <div class="bloque-titulo"><h2 id="tituloPendientes">Solicitudes por revisar</h2></div>
  <div class="lista" data-zona="pendientes">
    <?php if ($pendientes->num_rows === 0): ?>
      <div class="vacio">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="8 12.5 11 15.5 16 9.5"/></svg>
        <h3>No hay certificados pendientes</h3>
        <p>Todas las solicitudes ya fueron revisadas.</p>
      </div>
    <?php else: ?>
      <?php while ($c = $pendientes->fetch_assoc()): ?>
        <article class="fila">
          <span class="avatar" aria-hidden="true"><?php echo e(iniciales($c['nombre'])); ?></span>
          <div class="fila-info">
            <h3><?php echo $etiquetaTipo[$c['tipo']]; ?></h3>
            <p><?php echo e($c['nombre']); ?>, RUT <?php echo formatearRut($c['rut'], $c['dv']); ?></p>
            <p class="meta">Pedido el <?php echo fechaLarga($c['fecha_solicitud']); ?><?php echo $c['motivo'] ? '. Motivo: ' . e($c['motivo']) : ''; ?></p>
          </div>
          <form class="fila-acciones" action="../PHP/Procesar-aprobar-certificado.php" method="POST" data-ajax>
            <input type="hidden" name="id_certificado" value="<?php echo (int) $c['id_certificado']; ?>">
            <button type="submit" name="accion" value="aprobar" class="btn btn-primary">Aprobar</button>
            <button type="submit" name="accion" value="rechazar" class="btn btn-peligro" data-confirmar="¿Rechazar esta solicitud de <?php echo e($c['nombre']); ?>? Se le avisará por correo." data-peligro="Rechazar">Rechazar</button>
          </form>
        </article>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</section>

<section class="bloque" aria-labelledby="tituloHistorial">
  <div class="bloque-titulo"><h2 id="tituloHistorial">Últimas respuestas</h2></div>
  <div class="lista" data-zona="historial">
    <?php if ($historial->num_rows === 0): ?>
      <div class="vacio"><h3>Aún no has respondido certificados</h3></div>
    <?php else: ?>
      <?php while ($h = $historial->fetch_assoc()): ?>
        <article class="fila">
          <div class="fila-info">
            <h3><?php echo e($h['nombre']); ?></h3>
            <p><?php echo $etiquetaTipo[$h['tipo']]; ?><?php echo $h['fecha_respuesta'] ? ', respondido el ' . fechaLarga($h['fecha_respuesta']) : ''; ?></p>
            <?php if ($h['codigo_validacion']): ?><p class="meta">Código <?php echo e($h['codigo_validacion']); ?></p><?php endif; ?>
          </div>
          <span class="sello <?php echo $h['estado'] === 'aprobado' ? 'sello-ok' : 'sello-error'; ?>"><?php echo $h['estado'] === 'aprobado' ? 'Aprobado' : 'Rechazado'; ?></span>
        </article>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</section>

<?php require '../PHP/Pie.php'; ?>
