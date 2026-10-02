<?php
session_start();
require_once '../PHP/Config.php';

if (!isset($_SESSION['id_vecino'])) {
    header('Location: Acceso.html');
    exit;
}

$id_vecino = $_SESSION['id_vecino'];
$esSocioActivo = $_SESSION['es_socio_activo'] ?? 0;
$idJuntaSesion = $_SESSION['id_junta'];

$stmt = $conexion->prepare("SELECT titulo, contenido, tipo, fecha_publicacion FROM noticias WHERE id_junta = ? AND (tipo = 'general' OR (tipo = 'grupo' AND ? = 1) OR (tipo = 'especifico' AND id_vecino_destino = ?)) ORDER BY fecha_publicacion DESC");
$stmt->bind_param("iii", $idJuntaSesion, $esSocioActivo, $id_vecino);
$stmt->execute();
$noticias = $stmt->get_result();

$etiquetaTipo = [
  'general' => ['General', 'badge-general'],
  'grupo' => ['Solo socios activos', 'badge-group'],
  'especifico' => ['Solo para ti', 'badge-group']
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema Unidad Territorial - Noticias y avisos</title>
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
        <a href="Proyectos.php">Proyectos</a>
        <a href="Noticias.php" class="active">Noticias</a>
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
      <h1>Noticias y avisos</h1>
      <p>Comunicados de tu junta de vecinos. Algunos son generales y otros están dirigidos especialmente a ti o a tu grupo.</p>
    </div>
  </section>

  <section class="simple-list">
    <div class="container" style="display:flex; flex-direction:column; gap:16px;">

      <?php if ($noticias->num_rows === 0): ?>
        <div class="list-item">
          <div class="list-item-main">
            <h3>Todavía no hay noticias publicadas</h3>
            <p>Cuando tu junta de vecinos publique algo, va a aparecer aquí.</p>
          </div>
        </div>
      <?php else: ?>
        <?php while ($n = $noticias->fetch_assoc()): ?>
          <div class="list-item">
            <div class="list-item-main">
              <h3><?php echo htmlspecialchars($n['titulo']); ?></h3>
              <p><?php echo htmlspecialchars($n['contenido']); ?> Publicado el <?php echo date('d/m/Y', strtotime($n['fecha_publicacion'])); ?>.</p>
            </div>
            <span class="badge <?php echo $etiquetaTipo[$n['tipo']][1]; ?>"><?php echo $etiquetaTipo[$n['tipo']][0]; ?></span>
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