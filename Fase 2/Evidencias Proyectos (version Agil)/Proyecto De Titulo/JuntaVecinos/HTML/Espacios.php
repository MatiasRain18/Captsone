<?php
require_once '../PHP/Config.php';
require_once '../PHP/Verificar-sesion.php';

$usuario = verificarSesion($conexion);

$stmt = $conexion->prepare("SELECT espacio, fecha, horario, actividad, estado FROM reservas_espacios WHERE id_vecino = ? ORDER BY fecha DESC");
$stmt->bind_param("i", $usuario['id_vecino']);
$stmt->execute();
$reservas = $stmt->get_result();

// Solo actividades de hoy en adelante (las pasadas ya no se muestran)
$stmt = $conexion->prepare("
    SELECT a.id_actividad, a.nombre, a.descripcion, a.fecha, a.cupo_maximo,
           (SELECT COUNT(*) FROM inscripciones_actividades WHERE id_actividad = a.id_actividad) AS ocupados,
           EXISTS(SELECT 1 FROM inscripciones_actividades WHERE id_actividad = a.id_actividad AND id_vecino = ?) AS ya_inscrito
    FROM actividades a
    WHERE a.id_junta = ? AND a.fecha >= CURDATE()
    ORDER BY a.fecha ASC
");
$stmt->bind_param("ii", $usuario['id_vecino'], $usuario['id_junta']);
$stmt->execute();
$actividades = $stmt->get_result();

$etiquetaEspacio = ['cancha' => 'Cancha multicancha', 'sala' => 'Sala de reuniones', 'plaza' => 'Plaza central'];
$etiquetaEstado = ['pendiente' => ['En revisión', 'sello-espera'], 'aprobado' => ['Aprobada', 'sello-ok'], 'rechazado' => ['Rechazada', 'sello-error']];
$mesCorto = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];

$tituloPagina = 'Espacios y actividades';
$paginaActiva = 'Espacios.php';
require '../PHP/Encabezado.php';
?>

<header class="pagina-cabecera">
  <h1>Espacios y actividades</h1>
  <p>Pide la cancha, la sala o la plaza para tu actividad, e inscríbete en los talleres de tu junta.</p>
</header>

<div class="dos-columnas">
  <section class="tarjeta" aria-labelledby="tituloReservar">
    <h2 id="tituloReservar">Solicitar un espacio</h2>
    <p class="intro">El directorio revisará tu solicitud.</p>

    <form action="../PHP/Procesar-espacio.php" method="POST" novalidate data-ajax data-validar>
      <div class="field">
        <label for="espacio">Espacio</label>
        <select id="espacio" name="espacio" required>
          <option value="">Selecciona una opción</option>
          <option value="cancha">Cancha multicancha</option>
          <option value="sala">Sala de reuniones</option>
          <option value="plaza">Plaza central</option>
        </select>
      </div>

      <div class="field">
        <label for="fecha">Fecha</label>
        <input type="date" id="fecha" name="fecha" data-desde-hoy required>
      </div>

      <div class="field">
        <label for="horario">Horario</label>
        <input type="text" id="horario" name="horario" maxlength="50" placeholder="Ej: 15:00 a 18:00" required>
      </div>

      <div class="field">
        <label for="actividad">Actividad a realizar</label>
        <input type="text" id="actividad" name="actividad" maxlength="200" placeholder="Ej: cumpleaños infantil, taller vecinal" required>
      </div>

      <button type="submit" class="btn btn-primary btn-large">Enviar solicitud</button>
    </form>
  </section>

  <div>
    <section class="bloque" aria-labelledby="tituloReservas">
      <div class="bloque-titulo"><h2 id="tituloReservas">Mis reservas</h2></div>
      <div class="lista" data-zona="mis-reservas">
        <?php if ($reservas->num_rows === 0): ?>
          <div class="vacio">
            <h3>Todavía no has pedido ningún espacio</h3>
            <p>Usa el formulario para hacer tu primera solicitud.</p>
          </div>
        <?php else: ?>
          <?php while ($r = $reservas->fetch_assoc()): ?>
            <article class="fila">
              <span class="icono-fila fecha" aria-hidden="true">
                <b><?php echo date('d', strtotime($r['fecha'])); ?></b>
                <span><?php echo $mesCorto[date('n', strtotime($r['fecha'])) - 1]; ?></span>
              </span>
              <div class="fila-info">
                <h3><?php echo $etiquetaEspacio[$r['espacio']]; ?></h3>
                <p><?php echo fechaLarga($r['fecha']); ?>, <?php echo e($r['horario']); ?></p>
                <p class="meta">Actividad: <?php echo e($r['actividad']); ?></p>
              </div>
              <span class="sello <?php echo $etiquetaEstado[$r['estado']][1]; ?>"><?php echo $etiquetaEstado[$r['estado']][0]; ?></span>
            </article>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>
    </section>

    <section class="bloque" aria-labelledby="tituloActividades">
      <div class="bloque-titulo">
        <h2 id="tituloActividades">Actividades vecinales</h2>
        <p>Inscríbete mientras queden cupos.</p>
      </div>
      <div class="lista" data-zona="actividades">
        <?php if ($actividades->num_rows === 0): ?>
          <div class="vacio">
            <h3>No hay actividades próximas</h3>
            <p>Cuando el directorio cree una, aparecerá aquí.</p>
          </div>
        <?php else: ?>
          <?php while ($a = $actividades->fetch_assoc()): ?>
            <?php $lleno = $a['ocupados'] >= $a['cupo_maximo']; $porcentaje = min(100, round($a['ocupados'] * 100 / max(1, $a['cupo_maximo']))); ?>
            <article class="fila">
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
              <div class="fila-acciones">
                <?php if ($a['ya_inscrito']): ?>
                  <span class="sello sello-ok">Inscrito</span>
                <?php elseif ($lleno): ?>
                  <span class="sello sello-error">Cupos llenos</span>
                <?php else: ?>
                  <form action="../PHP/Procesar-inscribir-actividad.php" method="POST" data-ajax>
                    <input type="hidden" name="id_actividad" value="<?php echo (int) $a['id_actividad']; ?>">
                    <button type="submit" class="btn btn-primary">Inscribirme</button>
                  </form>
                <?php endif; ?>
              </div>
            </article>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>
    </section>
  </div>
</div>

<?php require '../PHP/Pie.php'; ?>
