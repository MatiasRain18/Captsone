<?php
session_start();
require_once '../PHP/Config.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'directorio') {
    header('Location: Acceso.html');
    exit;
}

$idJuntaSesion = $_SESSION['id_junta'];

$stmt = $conexion->prepare("SELECT id_noticia, titulo, contenido, tipo, fecha_publicacion FROM noticias WHERE id_junta = ? ORDER BY fecha_publicacion DESC");
$stmt->bind_param("i", $idJuntaSesion);
$stmt->execute();
$resultado = $stmt->get_result();

$etiquetaTipo = [
  'general' => ['General', 'badge-general'],
  'grupo' => ['Socios activos', 'badge-group'],
  'especifico' => ['Vecino específico', 'badge-group']
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema Unidad Territorial - Gestión de noticias</title>
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
        <a href="GestionProyectos.php">Proyectos</a>
        <a href="GestionNoticias.php" class="active">Noticias</a>
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
      <h1>Gestión de noticias</h1>
      <p>Publica un aviso general, para socios activos, o para un vecino específico.</p>
    </div>
  </section>

  <section class="access" style="padding-top: 24px;">
    <div class="container">
      <div class="access-card">
        <h1>Publicar noticia</h1>
        <p>Completa el título, el contenido, y a quién va dirigida.</p>

        <form action="../PHP/procesar-noticia.php" method="POST" novalidate data-validar>
          <div class="field">
            <label for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo" placeholder="Ej: Corte de agua programado" required>
          </div>

          <div class="field">
            <label for="contenido">Contenido</label>
            <input type="text" id="contenido" name="contenido" placeholder="Escribe el mensaje completo" required>
          </div>

          <div class="field">
            <label for="tipo">Destinatarios</label>
            <select id="tipo" name="tipo" required style="width:100%; padding:14px 16px; font-size:1.05rem; border:2px solid var(--border); border-radius:10px; font-family:inherit; background:var(--bg);">
              <option value="">Selecciona una opción</option>
              <option value="general">General (todos los vecinos)</option>
              <option value="grupo">Solo socios activos</option>
              <option value="especifico">Un vecino específico</option>
            </select>
          </div>

          <div class="field">
            <label for="rut_destino">RUT del vecino (solo si elegiste "Un vecino específico")</label>
            <input type="text" id="rut_destino" name="rut_destino" placeholder="Ej: 12.345.678-9">
          </div>

          <button type="submit" class="btn btn-primary btn-large">Publicar noticia</button>
        </form>
      </div>
    </div>
  </section>

  <section class="section-block">
    <div class="container">
      <h2>Noticias publicadas</h2>
      <p>Historial de todo lo que se ha publicado.</p>
    </div>
  </section>

  <section class="simple-list">
    <div class="container" style="display:flex; flex-direction:column; gap:16px;">

      <?php if ($resultado->num_rows === 0): ?>
        <div class="list-item">
          <div class="list-item-main">
            <h3>Todavía no has publicado ninguna noticia</h3>
            <p>Usa el formulario de arriba para publicar la primera.</p>
          </div>
        </div>
      <?php else: ?>
        <?php while ($n = $resultado->fetch_assoc()): ?>
          <div class="list-item">
            <div class="list-item-main">
              <h3><?php echo htmlspecialchars($n['titulo']); ?></h3>
              <p><?php echo htmlspecialchars($n['contenido']); ?></p>
            </div>
            <div style="display:flex; align-items:center; gap:14px;">
              <span class="badge <?php echo $etiquetaTipo[$n['tipo']][1]; ?>"><?php echo $etiquetaTipo[$n['tipo']][0]; ?></span>
              <form action="../PHP/procesar-eliminar-noticia.php" method="POST">
                <input type="hidden" name="id_noticia" value="<?php echo $n['id_noticia']; ?>">
                <button type="submit" class="btn btn-outline" onclick="return confirm('¿Seguro que quieres eliminar esta noticia?');">Eliminar</button>
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