<?php
require_once '../PHP/Config.php';
require_once '../PHP/Verificar-sesion.php';

$usuario = verificarSesion($conexion);
$esSocio = (int) $usuario['es_socio_activo'];

// Generales para todos, de grupo solo para socios activos, y específicas solo para el destinatario
$stmt = $conexion->prepare("SELECT titulo, contenido, tipo, fecha_publicacion FROM noticias WHERE id_junta = ? AND (tipo = 'general' OR (tipo = 'grupo' AND ? = 1) OR (tipo = 'especifico' AND id_vecino_destino = ?)) ORDER BY fecha_publicacion DESC");
$stmt->bind_param("iii", $usuario['id_junta'], $esSocio, $usuario['id_vecino']);
$stmt->execute();
$noticias = $stmt->get_result();

$etiquetaTipo = ['general' => ['General', 'sello-info'], 'grupo' => ['Socios activos', 'sello-grupo'], 'especifico' => ['Solo para ti', 'sello-espera']];

$tituloPagina = 'Noticias y avisos';
$paginaActiva = 'Noticias.php';
require '../PHP/Encabezado.php';
?>

<header class="pagina-cabecera">
  <h1>Noticias y avisos</h1>
  <p>Comunicados de tu junta de vecinos. Algunos son para todos, otros para los socios activos y otros solo para ti.</p>
</header>

<section class="lista" style="max-width: 820px;">
  <?php if ($noticias->num_rows === 0): ?>
    <div class="vacio">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      <h3>Todavía no hay noticias publicadas</h3>
      <p>Cuando tu junta de vecinos publique algo, aparecerá aquí.</p>
    </div>
  <?php else: ?>
    <?php while ($n = $noticias->fetch_assoc()): ?>
      <article class="noticia <?php echo e($n['tipo']); ?>">
        <div class="noticia-cabecera">
          <h3><?php echo e($n['titulo']); ?></h3>
          <span class="sello <?php echo $etiquetaTipo[$n['tipo']][1]; ?>"><?php echo $etiquetaTipo[$n['tipo']][0]; ?></span>
        </div>
        <p><?php echo e($n['contenido']); ?></p>
        <time datetime="<?php echo e($n['fecha_publicacion']); ?>">Publicado el <?php echo fechaLarga($n['fecha_publicacion']); ?></time>
      </article>
    <?php endwhile; ?>
  <?php endif; ?>
</section>

<?php require '../PHP/Pie.php'; ?>
