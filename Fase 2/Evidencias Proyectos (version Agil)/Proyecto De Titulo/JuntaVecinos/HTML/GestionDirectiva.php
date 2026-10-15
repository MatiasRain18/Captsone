<?php
require_once '../PHP/Config.php';
require_once '../PHP/Verificar-sesion.php';

$usuario = verificarSesion($conexion, 'directorio');
$idJunta = $usuario['id_junta'];

// Quién ocupa cada cargo
$stmt = $conexion->prepare("SELECT id_vecino, nombre, rut, dv, correo, cargo FROM vecinos WHERE id_junta = ? AND cargo IS NOT NULL AND estado = 'aprobado'");
$stmt->bind_param("i", $idJunta);
$stmt->execute();
$directiva = [];
foreach ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $p) {
    $directiva[$p['cargo']] = $p;
}

// Vecinos aprobados que pueden asumir un cargo
$stmt = $conexion->prepare("SELECT id_vecino, nombre, rut, dv FROM vecinos WHERE id_junta = ? AND rol = 'vecino' AND estado = 'aprobado' ORDER BY nombre ASC");
$stmt->bind_param("i", $idJunta);
$stmt->execute();
$candidatos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$funciones = [
    'presidente' => 'Representa a la junta y firma los certificados.',
    'secretario' => 'Lleva las actas, los registros de socios y la correspondencia.',
    'tesorero'   => 'Administra las cuotas, los fondos y rinde cuentas a la asamblea.',
];

$tituloPagina = 'Directiva';
$paginaActiva = 'GestionDirectiva.php';
require '../PHP/Encabezado.php';
?>

<header class="pagina-cabecera">
  <h1>Directiva de la junta</h1>
  <p>Toda junta de vecinos debe tener presidente, secretario y tesorero. Las tres personas entran al panel del directorio.</p>
</header>

<section class="directiva" data-zona="directiva" aria-label="Cargos de la directiva">
  <?php foreach (['presidente', 'secretario', 'tesorero'] as $cargo): ?>
    <?php $persona = $directiva[$cargo] ?? null; ?>
    <article class="cargo <?php echo $persona ? '' : 'vacante'; ?>">
      <h3><?php echo nombreCargo($cargo); ?></h3>
      <p class="field-hint" style="margin:0;"><?php echo $funciones[$cargo]; ?></p>

      <?php if ($persona): ?>
        <div class="persona">
          <span class="avatar" aria-hidden="true"><?php echo e(iniciales($persona['nombre'])); ?></span>
          <div>
            <strong><?php echo e($persona['nombre']); ?><?php echo ((int) $persona['id_vecino'] === (int) $usuario['id_vecino']) ? ' (tú)' : ''; ?></strong>
            <span>RUT <?php echo formatearRut($persona['rut'], $persona['dv']); ?></span>
          </div>
        </div>
        <?php if ($cargo !== 'presidente' && (int) $persona['id_vecino'] !== (int) $usuario['id_vecino']): ?>
          <form action="../PHP/Procesar-directiva.php" method="POST" data-ajax>
            <input type="hidden" name="accion" value="quitar">
            <input type="hidden" name="cargo" value="<?php echo $cargo; ?>">
            <button type="submit" class="btn btn-peligro btn-sm" data-confirmar="¿Quitar a <?php echo e($persona['nombre']); ?> del cargo de <?php echo mb_strtolower(nombreCargo($cargo)); ?>? Seguirá como vecino de la junta." data-peligro="Quitar del cargo">Quitar del cargo</button>
          </form>
        <?php endif; ?>
      <?php elseif ($cargo === 'presidente'): ?>
        <p>Cargo vacante. Contacta al administrador del sistema.</p>
      <?php else: ?>
        <p>Cargo vacante</p>
        <?php if ($candidatos): ?>
          <form action="../PHP/Procesar-directiva.php" method="POST" data-ajax data-validar novalidate>
            <input type="hidden" name="accion" value="asignar">
            <input type="hidden" name="cargo" value="<?php echo $cargo; ?>">
            <div class="field">
              <label for="vecino_<?php echo $cargo; ?>">Elige a un vecino de la junta</label>
              <select id="vecino_<?php echo $cargo; ?>" name="id_vecino" required>
                <option value="">Selecciona un vecino</option>
                <?php foreach ($candidatos as $c): ?>
                  <option value="<?php echo (int) $c['id_vecino']; ?>"><?php echo e($c['nombre']); ?> (<?php echo formatearRut($c['rut'], $c['dv']); ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Asignar cargo</button>
          </form>
        <?php else: ?>
          <p class="field-hint">Primero aprueba vecinos en la sección Vecinos para poder asignarles el cargo.</p>
        <?php endif; ?>
      <?php endif; ?>
    </article>
  <?php endforeach; ?>
</section>

<?php require '../PHP/Pie.php'; ?>
