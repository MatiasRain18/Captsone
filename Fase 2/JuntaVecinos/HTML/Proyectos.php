<?php
session_start();
require_once '../PHP/Config.php';

if (!isset($_SESSION['id_vecino'])) {
    header('Location: Acceso.html');
    exit;
}

$id_vecino = $_SESSION['id_vecino'];
$idJuntaSesion = $_SESSION['id_junta'];
$esSocioActivo = $_SESSION['es_socio_activo'] ?? 0;

$stmt = $conexion->prepare("SELECT p.id_proyecto, p.nombre, p.descripcion, p.fecha_limite, p.estado, EXISTS(SELECT 1 FROM postulaciones WHERE id_proyecto = p.id_proyecto AND id_vecino = ?) AS ya_postulo FROM proyectos p WHERE p.id_junta = ? ORDER BY p.fecha_limite ASC");
$stmt->bind_param("ii", $id_vecino, $idJuntaSesion);
$stmt->execute();
$proyectos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema Unidad Territorial - Proyectos vecinales</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../CSS/Styles.css">
</head>
<body>

  <header class="site-header">
    <div class="header-inner">
      <a href="Panel.html" class="logo">
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
        <a href="Panel.html">Mi panel</a>
        <a href="Certificados.php">Certificados</a>
        <a href="Proyectos.php" class="active">Proyectos</a>
        <a href="Noticias.php">Noticias</a>
        <a href="Espacios.php">Espacios</a>
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
      <h1>Proyectos vecinales</h1>
      <p>Revisa los proyectos abiertos de tu junta de vecinos y postula si ya estás inscrito como socio activo.</p>
    </div>
  </section>

  <section class="simple-list">
    <div class="container" style="display:flex; flex-direction:column; gap:16px;">

      <?php if ($proyectos->num_rows === 0): ?>
        <div class="list-item">
          <div class="list-item-main">
            <h3>Todavía no hay proyectos publicados</h3>
          </div>
        </div>
      <?php else: ?>
        <?php while ($p = $proyectos->fetch_assoc()): ?>
          <div class="list-item">
            <div class="list-item-main">
              <h3><?php echo htmlspecialchars($p['nombre']); ?></h3>
              <p><?php echo htmlspecialchars($p['descripcion']); ?><br>Postulaciones hasta el <?php echo date('d/m/Y', strtotime($p['fecha_limite'])); ?></p>
            </div>
            <?php if ($p['ya_postulo']): ?>
              <span class="badge badge-approved">Ya postulaste</span>
            <?php elseif ($p['estado'] !== 'abierto'): ?>
              <span class="badge badge-rejected">Cerrado</span>
            <?php elseif ($esSocioActivo == 1): ?>
              <a href="Postulacion.php?id_proyecto=<?php echo $p['id_proyecto']; ?>" class="btn btn-primary">Postular</a>
            <?php else: ?>
              <span class="badge badge-pending">Solo socios activos</span>
            <?php endif; ?>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>

    </div>
  </section>

  <?php if ($esSocioActivo != 1): ?>
  <section class="section-block" style="padding-bottom: 60px;">
    <div class="container">
      <div class="list-item" style="background: var(--accent-tint); border-color: var(--accent);">
        <div class="list-item-main">
          <h3>¿No puedes postular?</h3>
          <p>Solo los vecinos marcados como socios activos por su directiva pueden postular a proyectos. Si crees que deberías serlo, contacta a tu junta de vecinos.</p>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <div class="text-tools">
    <button type="button" data-texto="disminuir" aria-label="Letra normal">A</button>
    <button type="button" data-texto="aumentar" aria-label="Aumentar letra">A+</button>
  </div>

  <script src="../JS/Script.js"></script>
</body>
</html>