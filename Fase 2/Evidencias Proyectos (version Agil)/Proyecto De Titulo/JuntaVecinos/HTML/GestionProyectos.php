<?php
require_once '../PHP/Config.php';
require_once '../PHP/Verificar-sesion.php';

$usuario = verificarSesion($conexion, 'directorio');

$stmt = $conexion->prepare("
    SELECT p.id_proyecto, p.nombre, p.descripcion, p.fecha_limite, p.estado,
           (p.fecha_limite < CURDATE()) AS vencido,
           (SELECT COUNT(*) FROM postulaciones WHERE id_proyecto = p.id_proyecto) AS postulantes
    FROM proyectos p
    WHERE p.id_junta = ?
    ORDER BY (p.estado = 'abierto' AND p.fecha_limite >= CURDATE()) DESC, p.fecha_limite ASC
");
$stmt->bind_param("i", $usuario['id_junta']);
$stmt->execute();
$proyectos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Postulaciones que esperan respuesta del directorio
$stmt = $conexion->prepare("
    SELECT po.id_postulacion, po.motivacion, po.fecha_postulacion,
           p.nombre AS proyecto, v.nombre, v.rut, v.dv
    FROM postulaciones po
    INNER JOIN proyectos p ON po.id_proyecto = p.id_proyecto
    INNER JOIN vecinos v ON po.id_vecino = v.id_vecino
    WHERE p.id_junta = ? AND po.estado = 'pendiente'
    ORDER BY po.fecha_postulacion ASC
");
$stmt->bind_param("i", $usuario['id_junta']);
$stmt->execute();
$pendientes = $stmt->get_result();

// Postulantes de cada proyecto, con su estado (para la lista desplegable)
$stmt = $conexion->prepare("
    SELECT po.id_proyecto, v.nombre, po.motivacion, po.estado
    FROM postulaciones po
    INNER JOIN vecinos v ON po.id_vecino = v.id_vecino
    INNER JOIN proyectos p ON po.id_proyecto = p.id_proyecto
    WHERE p.id_junta = ?
    ORDER BY po.fecha_postulacion ASC
");
$stmt->bind_param("i", $usuario['id_junta']);
$stmt->execute();
$postulantesPorProyecto = [];
foreach ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $fila) {
    $postulantesPorProyecto[$fila['id_proyecto']][] = $fila;
}

$etiquetaPostulacion = ['pendiente' => ['En revisión', 'sello-espera'], 'aprobado' => ['Aprobada', 'sello-ok'], 'rechazado' => ['Rechazada', 'sello-error']];

$tituloPagina = 'Proyectos';
$paginaActiva = 'GestionProyectos.php';
require '../PHP/Encabezado.php';
?>

<header class="pagina-cabecera">
  <h1>Proyectos vecinales</h1>
  <p>Publica proyectos para que los socios activos postulen y responde sus postulaciones. Cuando pasa la fecha límite, el proyecto se cierra solo.</p>
</header>

<section class="bloque" aria-labelledby="tituloPostulaciones">
  <div class="bloque-titulo">
    <h2 id="tituloPostulaciones">Postulaciones por revisar</h2>
    <p>Aprueba o rechaza a los socios que postularon. Se les avisa por correo.</p>
  </div>
  <div class="lista" data-zona="postulaciones">
    <?php if ($pendientes->num_rows === 0): ?>
      <div class="vacio">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="8 12.5 11 15.5 16 9.5"/></svg>
        <h3>No hay postulaciones pendientes</h3>
        <p>Cuando un socio postule a un proyecto, aparecerá aquí.</p>
      </div>
    <?php else: ?>
      <?php while ($po = $pendientes->fetch_assoc()): ?>
        <article class="fila">
          <span class="avatar" aria-hidden="true"><?php echo e(iniciales($po['nombre'])); ?></span>
          <div class="fila-info">
            <h3><?php echo e($po['nombre']); ?></h3>
            <p>Postula a <strong><?php echo e($po['proyecto']); ?></strong>, RUT <?php echo formatearRut($po['rut'], $po['dv']); ?></p>
            <?php if ($po['motivacion']): ?><p class="cita">"<?php echo e($po['motivacion']); ?>"</p><?php endif; ?>
            <p class="meta">Postuló el <?php echo fechaLarga($po['fecha_postulacion']); ?></p>
          </div>
          <form class="fila-acciones" action="../PHP/Procesar-aprobar-postulacion.php" method="POST" data-ajax>
            <input type="hidden" name="id_postulacion" value="<?php echo (int) $po['id_postulacion']; ?>">
            <button type="submit" name="accion" value="aprobar" class="btn btn-primary">Aprobar</button>
            <button type="submit" name="accion" value="rechazar" class="btn btn-peligro" data-confirmar="¿Rechazar la postulación de <?php echo e($po['nombre']); ?> al proyecto &quot;<?php echo e($po['proyecto']); ?>&quot;? Se le avisará por correo." data-peligro="Rechazar">Rechazar</button>
          </form>
        </article>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</section>

<div class="dos-columnas">
  <section class="tarjeta" aria-labelledby="tituloNuevo">
    <h2 id="tituloNuevo">Publicar proyecto</h2>
    <p class="intro">Solo los socios activos podrán postular.</p>

    <form action="../PHP/Procesar-proyecto.php" method="POST" novalidate data-ajax data-validar>
      <div class="field">
        <label for="nombre">Nombre del proyecto</label>
        <input type="text" id="nombre" name="nombre" maxlength="150" placeholder="Ej: Mejoramiento de la plaza central" required>
      </div>

      <div class="field">
        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" maxlength="400" placeholder="¿En qué consiste y a quién beneficia?" required></textarea>
      </div>

      <div class="field">
        <label for="fecha_limite">Fecha límite de postulación</label>
        <input type="date" id="fecha_limite" name="fecha_limite" data-desde-hoy required>
      </div>

      <button type="submit" class="btn btn-primary btn-large">Publicar proyecto</button>
    </form>
  </section>

  <section class="bloque" aria-labelledby="tituloPublicados">
    <div class="bloque-titulo"><h2 id="tituloPublicados">Proyectos publicados</h2></div>
    <div class="lista" data-zona="proyectos">
      <?php if (!$proyectos): ?>
        <div class="vacio">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
          <h3>Todavía no has publicado ningún proyecto</h3>
          <p>Usa el formulario para publicar el primero.</p>
        </div>
      <?php else: ?>
        <?php foreach ($proyectos as $p): ?>
          <?php $abierto = $p['estado'] === 'abierto' && !$p['vencido']; $lista = $postulantesPorProyecto[$p['id_proyecto']] ?? []; ?>
          <article class="fila <?php echo $abierto ? '' : 'atenuada'; ?>">
            <div class="fila-info">
              <h3><?php echo e($p['nombre']); ?></h3>
              <p><?php echo e($p['descripcion']); ?></p>
              <p class="meta"><?php echo $p['vencido'] ? 'Plazo vencido el ' : 'Cierra el '; ?><?php echo fechaLarga($p['fecha_limite']); ?></p>
              <?php if ($lista): ?>
                <details class="desplegable">
                  <summary><?php echo count($lista); ?> postulante<?php echo count($lista) === 1 ? '' : 's'; ?></summary>
                  <ul>
                    <?php foreach ($lista as $po): ?>
                      <li><strong><?php echo e($po['nombre']); ?></strong><?php echo $po['motivacion'] ? ': "' . e($po['motivacion']) . '"' : ''; ?> <span class="sello <?php echo $etiquetaPostulacion[$po['estado']][1]; ?>"><?php echo $etiquetaPostulacion[$po['estado']][0]; ?></span></li>
                    <?php endforeach; ?>
                  </ul>
                </details>
              <?php else: ?>
                <p class="meta">Sin postulantes todavía.</p>
              <?php endif; ?>
            </div>
            <div class="fila-acciones">
              <?php if ($p['vencido']): ?>
                <span class="sello sello-neutro">Plazo vencido</span>
              <?php else: ?>
                <span class="sello <?php echo $abierto ? 'sello-ok' : 'sello-error'; ?>"><?php echo $abierto ? 'Abierto' : 'Cerrado'; ?></span>
                <form action="../PHP/Procesar-cerrar-proyecto.php" method="POST" data-ajax>
                  <input type="hidden" name="id_proyecto" value="<?php echo (int) $p['id_proyecto']; ?>">
                  <?php if ($abierto): ?>
                    <button type="submit" name="accion" value="cerrar" class="btn btn-secundario btn-sm" data-confirmar="¿Cerrar las postulaciones de este proyecto antes de la fecha límite?">Cerrar</button>
                  <?php else: ?>
                    <button type="submit" name="accion" value="abrir" class="btn btn-secundario btn-sm">Reabrir</button>
                  <?php endif; ?>
                </form>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</div>

<?php require '../PHP/Pie.php'; ?>
