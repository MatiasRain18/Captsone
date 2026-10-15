<?php
require_once '../PHP/Config.php';
require_once '../PHP/Verificar-sesion.php';

$usuario = verificarSesion($conexion);
$esSocio = (int) $usuario['es_socio_activo'];

// Un proyecto está abierto si el directorio no lo cerró Y su fecha límite no ha pasado
$stmt = $conexion->prepare("
    SELECT p.id_proyecto, p.nombre, p.descripcion, p.fecha_limite,
           (p.estado = 'abierto' AND p.fecha_limite >= CURDATE()) AS abierto,
           (SELECT estado FROM postulaciones WHERE id_proyecto = p.id_proyecto AND id_vecino = ? LIMIT 1) AS mi_postulacion
    FROM proyectos p
    WHERE p.id_junta = ?
    ORDER BY abierto DESC, p.fecha_limite ASC
");
$stmt->bind_param("ii", $usuario['id_vecino'], $usuario['id_junta']);
$stmt->execute();
$proyectos = $stmt->get_result();

$tituloPagina = 'Proyectos vecinales';
$paginaActiva = 'Proyectos.php';
require '../PHP/Encabezado.php';
?>

<header class="pagina-cabecera">
  <h1>Proyectos vecinales</h1>
  <p>Proyectos que publica el directorio de tu junta. Los socios activos pueden postular mientras el plazo esté abierto.</p>
</header>

<?php if ($esSocio !== 1): ?>
  <div class="aviso aviso-info">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="11" x2="12" y2="16"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
    <div>
      <h3>Solo los socios activos pueden postular</h3>
      <p>Puedes ver los proyectos, pero para postular el directorio debe marcarte como socio activo. Si crees que deberías serlo, contacta a tu junta de vecinos.</p>
    </div>
  </div>
<?php endif; ?>

<section class="lista" data-zona="proyectos">
  <?php if ($proyectos->num_rows === 0): ?>
    <div class="vacio">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
      <h3>Todavía no hay proyectos publicados</h3>
      <p>Cuando el directorio publique uno, aparecerá aquí.</p>
    </div>
  <?php else: ?>
    <?php while ($p = $proyectos->fetch_assoc()): ?>
      <article class="fila <?php echo $p['abierto'] ? '' : 'atenuada'; ?>">
        <span class="icono-fila fecha" aria-hidden="true">
          <b><?php echo date('d', strtotime($p['fecha_limite'])); ?></b>
          <span><?php echo ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'][date('n', strtotime($p['fecha_limite'])) - 1]; ?></span>
        </span>
        <div class="fila-info">
          <h3><?php echo e($p['nombre']); ?></h3>
          <p><?php echo e($p['descripcion']); ?></p>
          <p class="meta"><?php echo $p['abierto'] ? 'Postulaciones hasta el ' : 'Plazo cerrado el '; ?><?php echo fechaLarga($p['fecha_limite']); ?></p>
        </div>
        <div class="fila-acciones">
          <?php if ($p['mi_postulacion'] === 'pendiente'): ?>
            <span class="sello sello-espera">Postulación en revisión</span>
          <?php elseif ($p['mi_postulacion'] === 'aprobado'): ?>
            <span class="sello sello-ok">Postulación aprobada</span>
          <?php elseif ($p['mi_postulacion'] === 'rechazado'): ?>
            <span class="sello sello-error">Postulación rechazada</span>
          <?php elseif (!$p['abierto']): ?>
            <span class="sello sello-neutro">Cerrado</span>
          <?php elseif ($esSocio === 1): ?>
            <a href="Postulacion.php?id_proyecto=<?php echo (int) $p['id_proyecto']; ?>" class="btn btn-primary">Postular</a>
          <?php else: ?>
            <span class="sello sello-espera">Solo socios activos</span>
          <?php endif; ?>
        </div>
      </article>
    <?php endwhile; ?>
  <?php endif; ?>
</section>

<?php require '../PHP/Pie.php'; ?>
