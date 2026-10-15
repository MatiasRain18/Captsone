<?php
require_once '../PHP/Config.php';
require_once '../PHP/Verificar-sesion.php';

$usuario = verificarSesion($conexion);

// Si un integrante del directorio entra aquí, se le lleva a su panel
if ($usuario['rol'] === 'directorio') {
    header('Location: PanelDirectorio.php');
    exit;
}

$esSocio = (int) $usuario['es_socio_activo'];

// Últimos 3 avisos para este vecino
$stmt = $conexion->prepare("SELECT titulo, contenido, tipo, fecha_publicacion FROM noticias WHERE id_junta = ? AND (tipo = 'general' OR (tipo = 'grupo' AND ? = 1) OR (tipo = 'especifico' AND id_vecino_destino = ?)) ORDER BY fecha_publicacion DESC LIMIT 3");
$stmt->bind_param("iii", $usuario['id_junta'], $esSocio, $usuario['id_vecino']);
$stmt->execute();
$avisos = $stmt->get_result();

$primerNombre = explode(' ', $usuario['nombre'])[0];
$etiquetaTipo = ['general' => ['General', 'sello-info'], 'grupo' => ['Socios activos', 'sello-grupo'], 'especifico' => ['Solo para ti', 'sello-espera']];

$tituloPagina = 'Mi panel';
$paginaActiva = 'Panel.php';
require '../PHP/Encabezado.php';
?>

<header class="pagina-cabecera">
  <p class="saludo-junta"><?php echo e($usuario['junta']); ?></p>
  <h1>Hola, <?php echo e($primerNombre); ?></h1>
  <p>¿Qué necesitas hacer hoy? Elige una opción para empezar.</p>
</header>

<section class="bloque">
  <div class="accesos">
    <a href="Certificados.php" class="acceso">
      <span class="icono-fila"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
      <h3>Pedir un certificado</h3>
      <p>Residencia o vecino vigente, en PDF.</p>
      <span class="dato" data-dato="certificados" data-cero="Aún no tienes certificados aprobados" data-uno="1 certificado listo para descargar" data-varios="{n} certificados listos para descargar"></span>
    </a>
    <a href="Proyectos.php" class="acceso">
      <span class="icono-fila"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></span>
      <h3>Proyectos vecinales</h3>
      <p><?php echo $esSocio ? 'Postula a los proyectos de tu junta.' : 'Mira los proyectos de tu junta.'; ?></p>
      <span class="dato" data-dato="proyectos" data-alerta data-cero="No hay proyectos nuevos" data-uno="1 proyecto abierto" data-varios="{n} proyectos abiertos"></span>
    </a>
    <a href="Noticias.php" class="acceso">
      <span class="icono-fila"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></span>
      <h3>Noticias y avisos</h3>
      <p>Comunicados de tu directiva.</p>
      <span class="dato" data-dato="noticias" data-cero="Sin avisos este mes" data-uno="1 aviso este mes" data-varios="{n} avisos este mes"></span>
    </a>
    <a href="Espacios.php" class="acceso">
      <span class="icono-fila"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
      <h3>Espacios y actividades</h3>
      <p>Reserva la cancha, la sala o la plaza.</p>
      <span class="dato" data-dato="espacios" data-alerta data-cero="Sin actividades con cupo" data-uno="1 actividad con cupos" data-varios="{n} actividades con cupos"></span>
    </a>
  </div>
</section>

<section class="bloque">
  <div class="bloque-titulo">
    <h2>Últimos avisos</h2>
    <a href="Noticias.php">Ver todos</a>
  </div>
  <div class="lista">
    <?php if ($avisos->num_rows === 0): ?>
      <div class="vacio">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        <h3>Todavía no hay avisos</h3>
        <p>Cuando tu directiva publique algo, aparecerá aquí.</p>
      </div>
    <?php else: ?>
      <?php while ($n = $avisos->fetch_assoc()): ?>
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
  </div>
</section>

<?php require '../PHP/Pie.php'; ?>
