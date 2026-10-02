<?php
session_start();
require_once '../PHP/Config.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'directorio') {
    header('Location: Acceso.html');
    exit;
}

$idJuntaSesion = $_SESSION['id_junta'];

$stmt = $conexion->prepare("SELECT c.id_certificado, c.tipo, c.motivo, c.fecha_solicitud, v.nombre, v.rut FROM certificados c JOIN vecinos v ON c.id_vecino = v.id_vecino WHERE c.estado = 'pendiente' AND v.id_junta = ? ORDER BY c.fecha_solicitud ASC");
$stmt->bind_param("i", $idJuntaSesion);
$stmt->execute();
$resultado = $stmt->get_result();

$etiquetaTipo = ['residencia' => 'Certificado de residencia', 'socio_activo' => 'Certificado de vecino vigente'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema Unidad Territorial - Gestión de certificados</title>
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
        <a href="GestionVecinos.php">Vecinos</a>
        <a href="GestionCertificados.php" class="active">Certificados</a>
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
      <h1>Certificados pendientes</h1>
      <p>Revisa cada solicitud y apruébala o recházala.</p>
    </div>
  </section>

  <section class="simple-list">
    <div class="container" style="display:flex; flex-direction:column; gap:16px;">

      <?php if ($resultado->num_rows === 0): ?>
        <div class="list-item">
          <div class="list-item-main">
            <h3>No hay certificados pendientes por ahora</h3>
            <p>Todas las solicitudes ya fueron revisadas.</p>
          </div>
        </div>
      <?php else: ?>
        <?php while ($c = $resultado->fetch_assoc()): ?>
          <div class="list-item">
            <div class="list-item-main">
              <h3><?php echo $etiquetaTipo[$c['tipo']]; ?></h3>
              <p><?php echo htmlspecialchars($c['nombre']); ?> — RUT <?php echo htmlspecialchars($c['rut']); ?><?php echo $c['motivo'] ? '<br>Motivo: ' . htmlspecialchars($c['motivo']) : ''; ?></p>
            </div>
            <form action="../PHP/procesar-aprobar-certificado.php" method="POST" style="display:flex; gap:10px;">
              <input type="hidden" name="id_certificado" value="<?php echo $c['id_certificado']; ?>">
              <button type="submit" name="accion" value="aprobar" class="btn btn-primary">Aprobar</button>
              <button type="submit" name="accion" value="rechazar" class="btn btn-outline" onclick="return confirm('¿Seguro que quieres rechazar esta solicitud?');">Rechazar</button>
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