<?php
session_start();
require_once '../PHP/Config.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'directorio') {
    header('Location: Acceso.html');
    exit;
}

$idJuntaSesion = $_SESSION['id_junta'];

$stmt = $conexion->prepare("SELECT p.id_proyecto, p.nombre, p.descripcion, p.fecha_limite, p.estado, (SELECT COUNT(*) FROM postulaciones WHERE id_proyecto = p.id_proyecto) AS postulantes FROM proyectos p WHERE p.id_junta = ? ORDER BY p.fecha_limite ASC");
$stmt->bind_param("i", $idJuntaSesion);
$stmt->execute();
$proyectos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema Unidad Territorial - Gestión de proyectos</title>
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
        <a href="GestionCertificados.php">Certificados</a>
        <a href="GestionProyectos.php" class="active">Proyectos</a>
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
      <h1>Gestión de proyectos vecinales</h1>
      <p>Publica un proyecto nuevo. Solo los socios activos podrán postular.</p>
    </div>
  </section>

  <section class="access" style="padding-top: 24px;">
    <div class="container">
      <div class="access-card">
        <h1>Publicar proyecto</h1>

        <form action="../PHP/procesar-proyecto.php" method="POST" novalidate data-validar>
          <div class="field">
            <label for="nombre">Nombre del proyecto</label>
            <input type="text" id="nombre" name="nombre" placeholder="Ej: Mejoramiento de la plaza central" required>
          </div>

          <div class="field">
            <label for="descripcion">Descripción</label>
            <input type="text" id="descripcion" name="descripcion" placeholder="Breve descripción del proyecto" required>
          </div>

          <div class="field">
            <label for="fecha_limite">Fecha límite de postulación</label>
            <input type="date" id="fecha_limite" name="fecha_limite" required>
          </div>

          <button type="submit" class="btn btn-primary btn-large">Publicar proyecto</button>
        </form>
      </div>
    </div>
  </section>

  <section class="section-block">
    <div class="container">
      <h2>Proyectos publicados</h2>
      <p>Cantidad de socios activos que han postulado a cada uno.</p>
    </div>
  </section>

  <section class="simple-list">
    <div class="container" style="display:flex; flex-direction:column; gap:16px;">

      <?php if ($proyectos->num_rows === 0): ?>
        <div class="list-item">
          <div class="list-item-main">
            <h3>Todavía no has publicado ningún proyecto</h3>
          </div>
        </div>
      <?php else: ?>
        <?php while ($p = $proyectos->fetch_assoc()): ?>
          <div class="list-item">
            <div class="list-item-main">
              <h3><?php echo htmlspecialchars($p['nombre']); ?></h3>
              <p><?php echo htmlspecialchars($p['descripcion']); ?><br>Cierra: <?php echo date('d/m/Y', strtotime($p['fecha_limite'])); ?> — <?php echo $p['postulantes']; ?> postulante(s)</p>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
              <span class="badge <?php echo $p['estado'] === 'abierto' ? 'badge-approved' : 'badge-rejected'; ?>"><?php echo $p['estado'] === 'abierto' ? 'Abierto' : 'Cerrado'; ?></span>
              <form action="../PHP/procesar-cerrar-proyecto.php" method="POST">
                <input type="hidden" name="id_proyecto" value="<?php echo $p['id_proyecto']; ?>">
                <input type="hidden" name="accion" value="<?php echo $p['estado'] === 'abierto' ? 'cerrar' : 'abrir'; ?>">
                <button type="submit" class="btn btn-outline"><?php echo $p['estado'] === 'abierto' ? 'Cerrar' : 'Reabrir'; ?></button>
              </form>
            </div>
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