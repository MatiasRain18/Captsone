<?php
require_once '../PHP/Config.php';
require_once '../PHP/Verificar-sesion.php';

$usuario = verificarSesion($conexion, 'directorio');

$stmt = $conexion->prepare("
    SELECT n.id_noticia, n.titulo, n.contenido, n.tipo, n.fecha_publicacion, v.nombre AS destinatario
    FROM noticias n
    LEFT JOIN vecinos v ON n.id_vecino_destino = v.id_vecino
    WHERE n.id_junta = ?
    ORDER BY n.fecha_publicacion DESC
");
$stmt->bind_param("i", $usuario['id_junta']);
$stmt->execute();
$noticias = $stmt->get_result();

$etiquetaTipo = ['general' => ['Todos los vecinos', 'sello-info'], 'grupo' => ['Socios activos', 'sello-grupo'], 'especifico' => ['Un vecino', 'sello-espera']];

$tituloPagina = 'Noticias';
$paginaActiva = 'GestionNoticias.php';
require '../PHP/Encabezado.php';
?>

<header class="pagina-cabecera">
  <h1>Noticias</h1>
  <p>Publica avisos para todos los vecinos, solo para los socios activos o para un vecino en particular de tu junta.</p>
</header>

<div class="dos-columnas">
  <section class="tarjeta" aria-labelledby="tituloPublicar">
    <h2 id="tituloPublicar">Publicar noticia</h2>
    <p class="intro">Elige bien a quién va dirigida.</p>

    <form action="../PHP/Procesar-noticia.php" method="POST" novalidate data-ajax data-validar>
      <div class="field">
        <label for="titulo">Título</label>
        <input type="text" id="titulo" name="titulo" maxlength="150" placeholder="Ej: Corte de agua programado" required>
      </div>

      <div class="field">
        <label for="contenido">Mensaje</label>
        <textarea id="contenido" name="contenido" maxlength="500" placeholder="Escribe el mensaje completo" required></textarea>
      </div>

      <div class="field">
        <label for="tipo">¿Para quién es?</label>
        <select id="tipo" name="tipo" required data-mostrar-si="campoRutDestino">
          <option value="">Selecciona una opción</option>
          <option value="general">Todos los vecinos de la junta</option>
          <option value="grupo">Solo socios activos</option>
          <option value="especifico">Un vecino específico</option>
        </select>
      </div>

      <div class="field" id="campoRutDestino" hidden>
        <label for="rut_destino">RUT del vecino</label>
        <input type="text" id="rut_destino" name="rut_destino" placeholder="Ej: 12.345.678-5" autocomplete="off" data-rut data-sin-dv data-buscar-vecino="ayudaDestino">
        <p class="ayuda-vivo" id="ayudaDestino" aria-live="polite"></p>
        <p class="field-hint">Solo puedes enviar mensajes a vecinos aprobados de tu propia junta.</p>
      </div>

      <button type="submit" class="btn btn-primary btn-large">Publicar noticia</button>
    </form>
  </section>

  <section class="bloque" aria-labelledby="tituloPublicadas">
    <div class="bloque-titulo"><h2 id="tituloPublicadas">Noticias publicadas</h2></div>
    <div class="lista" data-zona="noticias">
      <?php if ($noticias->num_rows === 0): ?>
        <div class="vacio">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          <h3>Todavía no has publicado noticias</h3>
          <p>Usa el formulario para publicar la primera.</p>
        </div>
      <?php else: ?>
        <?php while ($n = $noticias->fetch_assoc()): ?>
          <article class="noticia <?php echo e($n['tipo']); ?>">
            <div class="noticia-cabecera">
              <h3><?php echo e($n['titulo']); ?></h3>
              <span class="sello <?php echo $etiquetaTipo[$n['tipo']][1]; ?>"><?php echo $n['tipo'] === 'especifico' && $n['destinatario'] ? 'Para ' . e($n['destinatario']) : $etiquetaTipo[$n['tipo']][0]; ?></span>
            </div>
            <p><?php echo e($n['contenido']); ?></p>
            <div class="noticia-pie">
              <time datetime="<?php echo e($n['fecha_publicacion']); ?>">Publicada el <?php echo fechaLarga($n['fecha_publicacion']); ?></time>
              <form action="../PHP/Procesar-eliminar-noticia.php" method="POST" data-ajax>
                <input type="hidden" name="id_noticia" value="<?php echo (int) $n['id_noticia']; ?>">
                <button type="submit" class="btn btn-peligro btn-sm" data-confirmar="¿Eliminar la noticia &quot;<?php echo e($n['titulo']); ?>&quot;? Los vecinos ya no la verán." data-peligro="Eliminar">Eliminar</button>
              </form>
            </div>
          </article>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>
  </section>
</div>

<?php require '../PHP/Pie.php'; ?>
