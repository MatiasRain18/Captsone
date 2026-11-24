<?php
require_once '../PHP/Config.php';
require_once '../PHP/Verificar-sesion.php';

$usuario = verificarSesion($conexion, 'directorio');
$idJunta = $usuario['id_junta'];

$stmt = $conexion->prepare("SELECT id_vecino, nombre, rut, dv, correo, direccion, fecha_registro FROM vecinos WHERE estado = 'pendiente' AND id_junta = ? ORDER BY fecha_registro ASC");
$stmt->bind_param("i", $idJunta);
$stmt->execute();
$pendientes = $stmt->get_result();

$stmt = $conexion->prepare("SELECT id_vecino, nombre, rut, dv, direccion, es_socio_activo FROM vecinos WHERE estado = 'aprobado' AND rol = 'vecino' AND id_junta = ? ORDER BY nombre ASC");
$stmt->bind_param("i", $idJunta);
$stmt->execute();
$aprobados = $stmt->get_result();

$stmt = $conexion->prepare("SELECT id_vecino, nombre, rut, dv FROM vecinos WHERE estado = 'baja' AND rol = 'vecino' AND id_junta = ? ORDER BY nombre ASC");
$stmt->bind_param("i", $idJunta);
$stmt->execute();
$deBaja = $stmt->get_result();

$tituloPagina = 'Vecinos';
$paginaActiva = 'GestionVecinos.php';
require '../PHP/Encabezado.php';
?>

<header class="pagina-cabecera">
  <h1>Vecinos de la junta</h1>
  <p>Aprueba las inscripciones nuevas, marca a los socios activos y da de baja a quienes ya no pertenecen a la junta.</p>
</header>

<section class="bloque" aria-labelledby="tituloPendientes">
  <div class="bloque-titulo">
    <h2 id="tituloPendientes">Inscripciones por revisar</h2>
  </div>
  <div class="lista" data-zona="pendientes">
    <?php if ($pendientes->num_rows === 0): ?>
      <div class="vacio">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="8 12.5 11 15.5 16 9.5"/></svg>
        <h3>No hay inscripciones pendientes</h3>
        <p>Cuando alguien se registre en tu junta, aparecerá aquí.</p>
      </div>
    <?php else: ?>
      <?php while ($v = $pendientes->fetch_assoc()): ?>
        <article class="fila">
          <span class="avatar" aria-hidden="true"><?php echo e(iniciales($v['nombre'])); ?></span>
          <div class="fila-info">
            <h3><?php echo e($v['nombre']); ?></h3>
            <p>RUT <?php echo formatearRut($v['rut'], $v['dv']); ?> · <?php echo e($v['correo']); ?></p>
            <p class="meta"><?php echo e($v['direccion']); ?> — se registró el <?php echo fechaLarga($v['fecha_registro']); ?></p>
          </div>
          <form class="fila-acciones" action="../PHP/Procesar-aprobar-vecino.php" method="POST" data-ajax>
            <input type="hidden" name="id_vecino" value="<?php echo (int) $v['id_vecino']; ?>">
            <button type="submit" name="accion" value="aprobar" class="btn btn-primary">Aprobar</button>
            <button type="submit" name="accion" value="rechazar" class="btn btn-peligro" data-confirmar="¿Rechazar la inscripción de <?php echo e($v['nombre']); ?>? No podrá ingresar al sistema." data-peligro="Rechazar">Rechazar</button>
          </form>
        </article>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</section>

<section class="bloque" aria-labelledby="tituloAprobados">
  <div class="bloque-titulo">
    <h2 id="tituloAprobados">Vecinos inscritos</h2>
    <p>Los socios activos pueden postular a proyectos, pedir el certificado de vecino vigente y leer los avisos para socios.</p>
  </div>
  <div class="lista" data-zona="aprobados">
    <?php if ($aprobados->num_rows === 0): ?>
      <div class="vacio">
        <h3>Todavía no hay vecinos inscritos</h3>
        <p>Cuando apruebes a alguien, aparecerá aquí.</p>
      </div>
    <?php else: ?>
      <?php while ($v = $aprobados->fetch_assoc()): ?>
        <article class="fila">
          <span class="avatar" aria-hidden="true"><?php echo e(iniciales($v['nombre'])); ?></span>
          <div class="fila-info">
            <h3><?php echo e($v['nombre']); ?></h3>
            <p>RUT <?php echo formatearRut($v['rut'], $v['dv']); ?></p>
          </div>
          <div class="fila-acciones">
            <form action="../PHP/Procesar-socio-activo.php" method="POST" data-ajax>
              <input type="hidden" name="id_vecino" value="<?php echo (int) $v['id_vecino']; ?>">
              <?php if ((int) $v['es_socio_activo'] === 1): ?>
                <span class="sello sello-grupo">Socio activo</span>
                <button type="submit" name="accion" value="quitar" class="btn btn-secundario btn-sm" data-confirmar="¿Quitarle a <?php echo e($v['nombre']); ?> la condición de socio activo?">Quitar</button>
              <?php else: ?>
                <button type="submit" name="accion" value="marcar" class="btn btn-secundario btn-sm">Marcar como socio activo</button>
              <?php endif; ?>
            </form>
            <form action="../PHP/Procesar-baja-vecino.php" method="POST" data-ajax>
              <input type="hidden" name="id_vecino" value="<?php echo (int) $v['id_vecino']; ?>">
              <button type="submit" name="accion" value="baja" class="btn btn-peligro btn-sm" data-confirmar="¿Dar de baja a <?php echo e($v['nombre']); ?>? Ya no podrá ingresar al sistema, pero su historial se conservará." data-peligro="Dar de baja">Dar de baja</button>
            </form>
          </div>
        </article>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</section>

<section class="bloque" aria-labelledby="tituloBaja">
  <div class="bloque-titulo">
    <h2 id="tituloBaja">Dados de baja</h2>
    <p>No pueden ingresar al sistema. Si fue un error, puedes reactivarlos.</p>
  </div>
  <div class="lista" data-zona="baja">
    <?php if ($deBaja->num_rows === 0): ?>
      <div class="vacio">
        <h3>No hay vecinos dados de baja</h3>
      </div>
    <?php else: ?>
      <?php while ($v = $deBaja->fetch_assoc()): ?>
        <article class="fila atenuada">
          <span class="avatar" aria-hidden="true"><?php echo e(iniciales($v['nombre'])); ?></span>
          <div class="fila-info">
            <h3><?php echo e($v['nombre']); ?></h3>
            <p>RUT <?php echo formatearRut($v['rut'], $v['dv']); ?></p>
          </div>
          <form class="fila-acciones" action="../PHP/Procesar-baja-vecino.php" method="POST" data-ajax>
            <input type="hidden" name="id_vecino" value="<?php echo (int) $v['id_vecino']; ?>">
            <span class="sello sello-error">Dado de baja</span>
            <button type="submit" name="accion" value="reactivar" class="btn btn-primary btn-sm">Reactivar</button>
          </form>
        </article>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</section>

<?php require '../PHP/Pie.php'; ?>
