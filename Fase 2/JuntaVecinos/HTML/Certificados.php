<?php
session_start();
require_once '../PHP/Config.php';

if (!isset($_SESSION['id_vecino'])) {
    header('Location: Acceso.html');
    exit;
}

$id_vecino = $_SESSION['id_vecino'];
$resultado = $conexion->prepare("SELECT id_certificado, tipo, motivo, estado, fecha_solicitud FROM certificados WHERE id_vecino = ? ORDER BY fecha_solicitud DESC");
$resultado->bind_param("i", $id_vecino);
$resultado->execute();
$solicitudes = $resultado->get_result();

$etiquetaTipo = ['residencia' => 'Certificado de residencia', 'socio_activo' => 'Certificado de vecino vigente'];
$etiquetaEstado = ['pendiente' => ['Pendiente', 'badge-pending'], 'aprobado' => ['Aprobado', 'badge-approved'], 'rechazado' => ['Rechazado', 'badge-rejected']];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema Unidad Territorial - Certificados</title>
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
        <a href="Certificados.php" class="active">Certificados</a>
        <a href="Proyectos.php">Proyectos</a>
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
      <h1>Certificados</h1>
      <p>Solicita un certificado nuevo o revisa el estado de los que ya pediste.</p>
    </div>
  </section>

  <section class="access" style="padding-top: 24px;">
    <div class="container">
      <div class="access-card">
        <h1>Solicitar certificado</h1>
        <p>Elige el tipo de certificado que necesitas.</p>

        <form action="../PHP/Procesar-certificado.php" method="POST" novalidate data-validar>
          <div class="field">
            <label for="tipo">Tipo de certificado</label>
            <select id="tipo" name="tipo" required style="width:100%; padding:14px 16px; font-size:1.05rem; border:2px solid var(--border); border-radius:10px; font-family:inherit; background:var(--bg);">
              <option value="">Selecciona una opción</option>
              <option value="residencia">Certificado de residencia</option>
              <option value="socio_activo">Certificado de vecino vigente / socio activo</option>
            </select>
          </div>

          <div class="field">
            <label for="motivo">Motivo de la solicitud (opcional)</label>
            <input type="text" id="motivo" name="motivo" placeholder="Ej: trámite municipal, postulación a subsidio, etc.">
          </div>

          <button type="submit" class="btn btn-primary btn-large">Enviar solicitud</button>
        </form>
      </div>
    </div>
  </section>

  <section class="section-block">
    <div class="container">
      <h2>Mis solicitudes</h2>
      <p>Aquí verás el estado de cada certificado que has pedido. Cuando esté aprobado, podrás descargarlo en PDF.</p>
    </div>
  </section>

  <section class="simple-list">
    <div class="container" style="display:flex; flex-direction:column; gap:16px;">

      <?php if ($solicitudes->num_rows === 0): ?>
        <div class="list-item">
          <div class="list-item-main">
            <h3>Todavía no has pedido ningún certificado</h3>
            <p>Usa el formulario de arriba para hacer tu primera solicitud.</p>
          </div>
        </div>
      <?php else: ?>
        <?php while ($s = $solicitudes->fetch_assoc()): ?>
          <div class="list-item">
            <div class="list-item-main">
              <h3><?php echo $etiquetaTipo[$s['tipo']]; ?></h3>
              <p>Solicitado el <?php echo date('d/m/Y', strtotime($s['fecha_solicitud'])); ?><?php echo $s['motivo'] ? ' — ' . htmlspecialchars($s['motivo']) : ''; ?></p>
            </div>
            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
              <span class="badge <?php echo $etiquetaEstado[$s['estado']][1]; ?>"><?php echo $etiquetaEstado[$s['estado']][0]; ?></span>
              <?php if ($s['estado'] === 'aprobado'): ?>
                <a href="../PHP/Generar-certificado.php?id=<?php echo (int)$s['id_certificado']; ?>" class="btn btn-outline">Descargar PDF</a>
              <?php endif; ?>
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