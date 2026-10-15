<?php
// Página pública: cualquier persona o institución puede comprobar un certificado
// con el código impreso en el PDF. La consulta se hace por AJAX a Api-verificar-certificado.php
require_once '../PHP/Funciones.php';
$codigoInicial = strtoupper(preg_replace('/[^0-9A-Za-z-]/', '', $_GET['codigo'] ?? ''));
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verificar certificado - Unidad Territorial</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400&family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../CSS/Styles.css">
</head>
<body>

  <header class="site-header">
    <div class="header-inner">
      <a href="Index.html" class="logo">
        <span class="logo-mark" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 10.5 12 3l9 7.5"/>
            <path d="M5 9.5V21h14V9.5"/>
            <path d="M9 21v-6h6v6"/>
          </svg>
        </span>
        Unidad Territorial
      </a>

      <nav class="main-nav" id="mainNav" aria-label="Menú principal">
        <a href="Index.html">Inicio</a>
        <a href="Servicios.html">Servicios</a>
        <a href="ComoFunciona.html">Cómo funciona</a>
        <a href="Verificar.php" class="active" aria-current="page">Verificar certificado</a>
      </nav>

      <div class="header-actions">
        <a href="Acceso.html" class="btn btn-outline">Iniciar sesión</a>
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

  <main id="principal">

    <section class="access">
      <div class="container">
        <div class="access-card">
          <h1>Verificar un certificado</h1>
          <p>Escribe el código de validación que aparece en el certificado PDF, junto al sello digital.</p>

          <form id="formVerificar" novalidate>
            <div class="field">
              <label for="codigo">Código de validación</label>
              <input type="text" id="codigo" name="codigo" placeholder="Ej: 8E2C-62AE-411C" autocomplete="off" maxlength="14" value="<?php echo e($codigoInicial); ?>" required style="font-size:1.25rem; letter-spacing:.06em;">
            </div>
            <button type="submit" class="btn btn-primary btn-large">Verificar</button>
          </form>

          <div class="resultado-verificacion" id="resultadoVerificacion" hidden aria-live="polite"></div>

          <p class="access-foot">Los certificados los emite el directorio de cada junta de vecinos a través del Sistema Unidad Territorial.</p>
        </div>
      </div>
    </section>
  </main>

  <footer class="site-footer">
    <div class="container">
      <div class="footer-inner">
        <div class="footer-col">
          <h4>Sistema Unidad Territorial</h4>
          <p>Plataforma para que las juntas de vecinos gestionen avisos, certificados y trámites en un solo lugar.</p>
        </div>
        <div class="footer-col">
          <h4>Enlaces</h4>
          <a href="Index.html">Inicio</a>
          <a href="Servicios.html">Servicios</a>
          <a href="ComoFunciona.html">Cómo funciona</a>
          <a href="Verificar.php">Verificar un certificado</a>
        </div>
        <div class="footer-col">
          <h4>Contacto</h4>
          <a href="mailto:contacto@unidadterritorial.cl">contacto@unidadterritorial.cl</a>
          <a href="tel:+56220000000">+56 2 2000 0000</a>
        </div>
      </div>
      <div class="footer-bottom">
        <span>© 2026 Sistema Unidad Territorial. Todos los derechos reservados.</span>
        <span>Proyecto académico DUOC UC</span>
      </div>
    </div>
  </footer>

  <div class="text-tools" role="group" aria-label="Tamaño de letra">
    <button type="button" data-texto="disminuir" aria-label="Letra normal">A</button>
    <button type="button" data-texto="aumentar" aria-label="Aumentar letra">A+</button>
  </div>

  <script src="../JS/Script.js"></script>
</body>
</html>
