<?php
session_start();
require_once '../PHP/Config.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'directorio') {
    header('Location: Acceso.html');
    exit;
}

$idJuntaSesion = $_SESSION['id_junta'];

$stmt1 = $conexion->prepare("SELECT id_vecino, nombre, rut, correo, fecha_registro FROM vecinos WHERE estado = 'pendiente' AND id_junta = ? ORDER BY fecha_registro ASC");
$stmt1->bind_param("i", $idJuntaSesion);
$stmt1->execute();
$resultado = $stmt1->get_result();

$stmt2 = $conexion->prepare("SELECT id_vecino, nombre, rut, es_socio_activo FROM vecinos WHERE estado = 'aprobado' AND rol = 'vecino' AND id_junta = ? ORDER BY nombre ASC");
$stmt2->bind_param("i", $idJuntaSesion);
$stmt2->execute();
$aprobados = $stmt2->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema Unidad Territorial - Gestión de vecinos</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../CSS/Styles.css">
</head>
<body>

  <header class="site-header">
    <div class="header-inner">
      <a href="PanelDirectorio.html" class="logo">
        <span class="logo-mark">
          <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 10.5 12 3l9 7.5"/>
            <path d="M5 9.5V21h14V9.5"/>
            <path d="M9 21v-6h6v6"/>
          </svg>
        </span>
        Unidad Territorial
      </a>

      <nav class="main-nav" id="mainNav">
        <a href="PanelDirectorio.html">Panel</a>
        <a href="GestionVecinos.php" class="active">Vecinos</a>
        <a href="GestionCertificados.php">Certificados</a>
        <a href="GestionProyectos.php">Proyectos</a>
        <a href="GestionNoticias.php">Noticias</a>
        <a href="GestionEspacios.php">Espacios</a>
        <a href="GestionJuntas.php">Juntas</a>
      </nav>

      <div class="header-actions">
        <a href="../PHP/Logout.php" class="btn btn-outline">Cerrar sesión</a>
        <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú" aria-expanded="false" aria-controls="mainNav">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <line x1="4" y1="7" x2="20" y2="7"/>
            <line x1="4" y1="12" x2="20" y2="12"/>
            <line x1="4" y1="17" x2="20" y2="17"/>
          </svg>
        </button>
      </div>
    </div>
  </header>

  <section class="page-head">
    <div class="container">
      <h1>Vecinos pendientes de aprobación</h1>
      <p>Revisa cada solicitud de inscripción y apruébala o recházala.</p>
    </div>
  </section>

  <section class="simple-list">
    <div class="container" style="display:flex; flex-direction:column; gap:16px;">

      <?php if ($resultado->num_rows === 0): ?>
        <div class="list-item">
          <div class="list-item-main">
            <h3>No hay vecinos pendientes por ahora</h3>
            <p>Todas las inscripciones ya fueron revisadas.</p>
          </div>
        </div>
      <?php else: ?>
        <?php while ($vecino = $resultado->fetch_assoc()): ?>
          <div class="list-item">
            <div class="list-item-main">
              <h3><?php echo htmlspecialchars($vecino['nombre']); ?></h3>
              <p>RUT: <?php echo htmlspecialchars($vecino['rut']); ?> — <?php echo htmlspecialchars($vecino['correo']); ?></p>
            </div>
            <form action="../PHP/procesar-aprobar-vecino.php" method="POST" style="display:flex; gap:10px;">
              <input type="hidden" name="id_vecino" value="<?php echo $vecino['id_vecino']; ?>">
              <button type="submit" name="accion" value="aprobar" class="btn btn-primary">Aprobar</button>
              <button type="submit" name="accion" value="rechazar" class="btn btn-outline" onclick="return confirm('¿Seguro que quieres rechazar esta inscripción?');">Rechazar</button>
            </form>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>

    </div>
  </section>

  <section class="section-block">
    <div class="container">
      <h2>Socios activos</h2>
      <p>Marca o quita la condición de socio activo a los vecinos ya aprobados.</p>
    </div>
  </section>

  <section class="simple-list">
    <div class="container" style="display:flex; flex-direction:column; gap:16px;">

      <?php if ($aprobados->num_rows === 0): ?>
        <div class="list-item">
          <div class="list-item-main">
            <h3>Todavía no hay vecinos aprobados</h3>
            <p>Cuando apruebes a alguien, va a aparecer aquí.</p>
          </div>
        </div>
      <?php else: ?>
        <?php while ($v = $aprobados->fetch_assoc()): ?>
          <div class="list-item">
            <div class="list-item-main">
              <h3><?php echo htmlspecialchars($v['nombre']); ?></h3>
              <p>RUT: <?php echo htmlspecialchars($v['rut']); ?></p>
            </div>
            <form action="../PHP/procesar-socio-activo.php" method="POST">
              <input type="hidden" name="id_vecino" value="<?php echo $v['id_vecino']; ?>">
              <?php if ($v['es_socio_activo'] == 1): ?>
                <span class="badge badge-group" style="margin-right:10px;">Socio activo</span>
                <button type="submit" name="accion" value="quitar" class="btn btn-outline">Quitar</button>
              <?php else: ?>
                <button type="submit" name="accion" value="marcar" class="btn btn-primary">Marcar como socio activo</button>
              <?php endif; ?>
            </form>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>

    </div>
  </section>

  <div class="text-tools">
    <button type="button" data-texto="disminuir" aria-label="Letra normal">A</button>
    <button type="button" data-texto="aumentar" aria-label="Aumentar letra">A+</button>
  </div>

  <script src="../JS/Script.js"></script>
</body>
</html>