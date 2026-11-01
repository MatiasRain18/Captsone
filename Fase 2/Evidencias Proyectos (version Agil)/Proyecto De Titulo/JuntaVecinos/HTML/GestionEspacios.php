<?php
require_once '../PHP/Config.php';
require_once '../PHP/Verificar-sesion.php';

$usuario = verificarSesion($conexion, 'directorio');
$idJunta = $usuario['id_junta'];

// Reservas pendientes. "choque" indica si ya hay otra reserva APROBADA del mismo espacio ese día
$stmt = $conexion->prepare("
    SELECT r.id_reserva, r.espacio, r.fecha, r.horario, r.actividad, v.nombre, v.rut, v.dv,
           EXISTS(
               SELECT 1 FROM reservas_espacios r2
               INNER JOIN vecinos v2 ON r2.id_vecino = v2.id_vecino
               WHERE r2.espacio = r.espacio AND r2.fecha = r.fecha AND r2.estado = 'aprobado' AND v2.id_junta = v.id_junta
           ) AS choque
    FROM reservas_espacios r
    INNER JOIN vecinos v ON r.id_vecino = v.id_vecino
    WHERE r.estado = 'pendiente' AND v.id_junta = ?
    ORDER BY r.fecha ASC
");
$stmt->bind_param("i", $idJunta);
$stmt->execute();
$pendientes = $stmt->get_result();

// Próximas reservas aprobadas (calendario simple)
$stmt = $conexion->prepare("
    SELECT r.espacio, r.fecha, r.horario, r.actividad, v.nombre
    FROM reservas_espacios r
    INNER JOIN vecinos v ON r.id_vecino = v.id_vecino
    WHERE r.estado = 'aprobado' AND v.id_junta = ? AND r.fecha >= CURDATE()
    ORDER BY r.fecha ASC
    LIMIT 10
");
$stmt->bind_param("i", $idJunta);
$stmt->execute();
$aprobadas = $stmt->get_result();

$stmt = $conexion->prepare("
    SELECT a.nombre, a.descripcion, a.fecha, a.cupo_maximo, (a.fecha < CURDATE()) AS realizada,
           (SELECT COUNT(*) FROM inscripciones_actividades WHERE id_actividad = a.id_actividad) AS ocupados
    FROM actividades a
    WHERE a.id_junta = ?
    ORDER BY realizada ASC, a.fecha ASC
");
$stmt->bind_param("i", $idJunta);
$stmt->execute();
$actividades = $stmt->get_result();

$etiquetaEspacio = ['cancha' => 'Cancha multicancha', 'sala' => 'Sala de reuniones', 'plaza' => 'Plaza central'];
$mesCorto = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];

$tituloPagina = 'Espacios y actividades';
$paginaActiva = 'GestionEspacios.php';
require '../PHP/Encabezado.php';
?>

<header class="pagina-cabecera">
  <h1>Espacios y actividades</h1>
  <p>Responde las solicitudes de la cancha, la sala y la plaza, y crea actividades con cupos para los vecinos.</p>
</header>

<section class="bloque" aria-labelledby="tituloReservas">
  <div class="bloque-titulo"><h2 id="tituloReservas">Reservas por revisar</h2></div>
  <div class="lista" data-zona="reservas">
    <?php if ($pendientes->num_rows === 0): ?>
      <div class="vacio">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="8 12.5 11 15.5 16 9.5"/></svg>
        <h3>No hay reservas pendientes</h3>
        <p>Todas las solicitudes ya fueron revisadas.</p>
      </div>
    <?php else: ?>
      <?php while ($r = $pendientes->fetch_assoc()): ?>
        <article class="fila">
          <span class="icono-fila fecha" aria-hidden="true">
            <b><?php echo date('d', strtotime($r['fecha'])); ?></b>
            <span><?php echo $mesCorto[date('n', strtotime($r['fecha'])) - 1]; ?></span>
          </span>
          <div class="fila-info">
            <h3><?php echo $etiquetaEspacio[$r['espacio']]; ?>, <?php echo e($r['horario']); ?></h3>
            <p><?php echo e($r['nombre']); ?>, RUT <?php echo formatearRut($r['rut'], $r['dv']); ?></p>
            <p class="meta">Actividad: <?php echo e($r['actividad']); ?></p>
            <?php if ($r['choque']): ?>
              <p class="meta" style="color: var(--espera); font-weight: 700;">Ojo: ya hay otra reserva aprobada de este espacio el mismo día. Revisa que los horarios no choquen.</p>
            <?php endif; ?>
          </div>
          <form class="fila-acciones" action="../PHP/Procesar-aprobar-espacio.php" method="POST" data-ajax>
            <input type="hidden" name="id_reserva" value="<?php echo (int) $r['id_reserva']; ?>">
            <button type="submit" name="accion" value="aprobar" class="btn btn-primary">Aprobar</button>
            <button type="submit" name="accion" value="rechazar" class="btn btn-peligro" data-confirmar="¿Rechazar la reserva de <?php echo e($r['nombre']); ?>?" data-peligro="Rechazar">Rechazar</button>
          </form>
        </article>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</section>

<section class="bloque" aria-labelledby="tituloAgenda">
  <div class="bloque-titulo"><h2 id="tituloAgenda">Próximas reservas aprobadas</h2></div>
  <div class="lista" data-zona="agenda">
    <?php if ($aprobadas->num_rows === 0): ?>
      <div class="vacio"><h3>No hay reservas aprobadas próximas</h3></div>
    <?php else: ?>
      <?php while ($r = $aprobadas->fetch_assoc()): ?>
        <article class="fila">
          <span class="icono-fila fecha" aria-hidden="true">
            <b><?php echo date('d', strtotime($r['fecha'])); ?></b>
            <span><?php echo $mesCorto[date('n', strtotime($r['fecha'])) - 1]; ?></span>
          </span>
          <div class="fila-info">
            <h3><?php echo $etiquetaEspacio[$r['espacio']]; ?>, <?php echo e($r['horario']); ?></h3>
            <p><?php echo e($r['nombre']); ?>: <?php echo e($r['actividad']); ?></p>
          </div>
          <span class="sello sello-ok">Aprobada</span>
        </article>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</section>

<div class="dos-columnas" id="nuevaActividad">
  <section class="tarjeta" aria-labelledby="tituloNueva">
    <h2 id="tituloNueva">Nueva actividad</h2>
    <p class="intro">Talleres, operativos o eventos con cupos limitados.</p>

    <form action="../PHP/Procesar-actividad.php" method="POST" novalidate data-ajax data-validar>
      <div class="field">
        <label for="nombre">Nombre de la actividad</label>
        <input type="text" id="nombre" name="nombre" maxlength="150" placeholder="Ej: Taller de manualidades" required>
      </div>

      <div class="field">
        <label for="descripcion">Descripción <span class="opcional">(opcional)</span></label>
        <input type="text" id="descripcion" name="descripcion" maxlength="300" placeholder="Dónde, a qué hora y qué llevar">
      </div>

      <div class="campos-2">
        <div class="field">
          <label for="fecha">Fecha</label>
          <input type="date" id="fecha" name="fecha" data-desde-hoy required>
        </div>
        <div class="field">
          <label for="cupo_maximo">Cupos</label>
          <input type="number" id="cupo_maximo" name="cupo_maximo" min="1" max="1000" placeholder="Ej: 20" required>
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-large">Crear actividad</button>
    </form>
  </section>

  <section class="bloque" aria-labelledby="tituloActividades">
    <div class="bloque-titulo"><h2 id="tituloActividades">Actividades</h2></div>
    <div class="lista" data-zona="actividades">
      <?php if ($actividades->num_rows === 0): ?>
        <div class="vacio">
          <h3>Todavía no has creado actividades</h3>
          <p>Usa el formulario para crear la primera.</p>
        </div>
      <?php else: ?>
        <?php while ($a = $actividades->fetch_assoc()): ?>
          <?php $lleno = $a['ocupados'] >= $a['cupo_maximo']; $porcentaje = min(100, round($a['ocupados'] * 100 / max(1, $a['cupo_maximo']))); ?>
          <article class="fila <?php echo $a['realizada'] ? 'atenuada' : ''; ?>">
            <span class="icono-fila fecha" aria-hidden="true">
              <b><?php echo date('d', strtotime($a['fecha'])); ?></b>
              <span><?php echo $mesCorto[date('n', strtotime($a['fecha'])) - 1]; ?></span>
            </span>
            <div class="fila-info">
              <h3><?php echo e($a['nombre']); ?></h3>
              <?php if ($a['descripcion']): ?><p><?php echo e($a['descripcion']); ?></p><?php endif; ?>
              <div class="cupos">
                <div class="cupos-barra <?php echo $lleno ? 'lleno' : ''; ?>"><span style="width: <?php echo $porcentaje; ?>%"></span></div>
                <small><?php echo $a['ocupados']; ?> de <?php echo $a['cupo_maximo']; ?> cupos ocupados</small>
              </div>
            </div>
            <?php if ($a['realizada']): ?>
              <span class="sello sello-neutro">Realizada</span>
            <?php elseif ($lleno): ?>
              <span class="sello sello-error">Cupos llenos</span>
            <?php else: ?>
              <span class="sello sello-ok">Con cupos</span>
            <?php endif; ?>
          </article>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>
  </section>
</div>

<?php require '../PHP/Pie.php'; ?>
