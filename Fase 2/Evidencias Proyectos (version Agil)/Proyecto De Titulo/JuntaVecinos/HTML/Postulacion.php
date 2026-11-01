<?php
require_once '../PHP/Config.php';
require_once '../PHP/Verificar-sesion.php';

$usuario = verificarSesion($conexion);

if ((int) $usuario['es_socio_activo'] !== 1) {
    header('Location: Proyectos.php');
    exit;
}

$idProyecto = intval($_GET['id_proyecto'] ?? 0);

// Solo proyectos abiertos, con plazo vigente y de la junta del vecino
$stmt = $conexion->prepare("SELECT nombre, descripcion, fecha_limite FROM proyectos WHERE id_proyecto = ? AND id_junta = ? AND estado = 'abierto' AND fecha_limite >= CURDATE()");
$stmt->bind_param("ii", $idProyecto, $usuario['id_junta']);
$stmt->execute();
$proyecto = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$proyecto) {
    header('Location: Proyectos.php');
    exit;
}

$tituloPagina = 'Postular a proyecto';
$paginaActiva = 'Proyectos.php';
require '../PHP/Encabezado.php';
?>

<header class="pagina-cabecera">
  <p class="saludo-junta"><a href="Proyectos.php">Proyectos vecinales</a></p>
  <h1>Postular a "<?php echo e($proyecto['nombre']); ?>"</h1>
  <p><?php echo e($proyecto['descripcion']); ?></p>
</header>

<section class="tarjeta" style="max-width: 640px;">
  <h2>Tu postulación</h2>
  <p class="intro">Puedes postular hasta el <?php echo fechaLarga($proyecto['fecha_limite']); ?>.</p>

  <form action="../PHP/Procesar-postulacion.php" method="POST" novalidate data-ajax data-validar>
    <input type="hidden" name="id_proyecto" value="<?php echo $idProyecto; ?>">

    <div class="field">
      <label for="motivacion">¿Por qué quieres participar? <span class="opcional">(opcional)</span></label>
      <textarea id="motivacion" name="motivacion" maxlength="400" placeholder="Cuéntale brevemente al directorio tu motivación"></textarea>
    </div>

    <button type="submit" class="btn btn-primary btn-large">Enviar postulación</button>
  </form>

  <p class="access-foot"><a href="Proyectos.php">Volver a Proyectos</a></p>
</section>

<?php require '../PHP/Pie.php'; ?>
