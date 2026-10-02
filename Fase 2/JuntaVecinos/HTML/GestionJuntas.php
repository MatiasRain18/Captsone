<?php
session_start();
require_once '../PHP/Config.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'directorio') {
    header('Location: Acceso.html');
    exit;
}

$juntas = $conexion->query("SELECT id_junta, nombre, comuna, direccion_sede, correo_contacto FROM juntas ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema Unidad Territorial - Juntas de vecinos</title>
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
        <a href="GestionNoticias.php">Noticias</a>
        <a href="GestionEspacios.php">Espacios</a>
        <a href="GestionJuntas.php" class="active">Juntas</a>
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
      <h1>Juntas de vecinos</h1>
      <p>Da de alta una junta nueva en el sistema junto con su primer director.</p>
    </div>
  </section>

  <section class="access" style="padding-top: 24px;">
    <div class="container">
      <div class="access-card">
        <h1>Crear junta nueva</h1>
        <p>Completa los datos de la junta y de la persona que quedará como su primer directorio.</p>

        <form action="../PHP/procesar-junta.php" method="POST" novalidate data-validar>
          <div class="field">
            <label for="nombre_junta">Nombre de la junta</label>
            <input type="text" id="nombre_junta" name="nombre_junta" placeholder="Ej: Junta de Vecinos Vista Hermosa" required>
          </div>

          <div class="field">
            <label for="comuna">Comuna</label>
            <input type="text" id="comuna" name="comuna" placeholder="Ej: Santiago" required>
          </div>

          <div class="field">
            <label for="direccion_sede">Dirección de la sede</label>
            <input type="text" id="direccion_sede" name="direccion_sede" placeholder="Calle y número de la sede vecinal" required>
          </div>

          <div class="field">
            <label for="correo_junta">Correo de contacto de la junta</label>
            <input type="email" id="correo_junta" name="correo_junta" placeholder="contacto@ejemplo.cl" required>
          </div>

          <div class="field" style="border-top: 2px solid var(--border); padding-top: 20px; margin-top: 24px;">
            <label for="nombre_director">Nombre del primer director</label>
            <input type="text" id="nombre_director" name="nombre_director" placeholder="Nombre completo" required>
          </div>

          <div class="field">
            <label for="rut_director">RUT del director</label>
            <input type="text" id="rut_director" name="rut_director" placeholder="Ej: 12.345.678-9" required>
          </div>

          <div class="field">
            <label for="correo_director">Correo del director</label>
            <input type="email" id="correo_director" name="correo_director" placeholder="tucorreo@ejemplo.cl" required>
          </div>

          <div class="field">
            <label for="clave_director">Clave del director</label>
            <div class="campo-clave">
              <input type="password" id="clave_director" name="clave_director" placeholder="Mínimo 8 caracteres" required>
              <button type="button" class="btn-ver-clave" data-target="clave_director" aria-label="Mostrar clave">👁</button>
            </div>
          </div>

          <button type="submit" class="btn btn-primary btn-large">Crear junta y director</button>
        </form>
      </div>
    </div>
  </section>

  <section class="section-block">
    <div class="container">
      <h2>Juntas registradas</h2>
      <p>Todas las juntas que existen en el sistema.</p>
    </div>
  </section>

  <section class="simple-list">
    <div class="container" style="display:flex; flex-direction:column; gap:16px;">

      <?php if ($juntas->num_rows === 0): ?>
        <div class="list-item">
          <div class="list-item-main">
            <h3>Todavía no hay juntas registradas</h3>
          </div>
        </div>
      <?php else: ?>
        <?php while ($j = $juntas->fetch_assoc()): ?>
          <div class="list-item">
            <div class="list-item-main">
              <h3><?php echo htmlspecialchars($j['nombre']); ?></h3>
              <p><?php echo htmlspecialchars($j['comuna']); ?> — <?php echo htmlspecialchars($j['direccion_sede']); ?></p>
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