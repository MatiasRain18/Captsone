<?php
require_once '../PHP/Config.php';
$juntas = $conexion->query("SELECT id_junta, nombre FROM juntas ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema Unidad Territorial - Registro de vecino</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../CSS/Styles.css">
</head>
<body>

  <header class="site-header">
    <div class="header-inner">
      <a href="Index.html" class="logo">
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
        <a href="Index.html">Inicio</a>
        <a href="Servicios.html">Servicios</a>
        <a href="ComoFunciona.html">Cómo funciona</a>
        <a href="Acceso.html">Contacto</a>
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

  <section class="access">
    <div class="container">
      <div class="access-card">
        <h1>Registra tu cuenta de vecino</h1>
        <p>Completa tus datos. Tu directiva vecinal revisará y aprobará tu inscripción antes de darte acceso completo.</p>

        <form action="../PHP/procesar-registro.php" method="POST" novalidate data-validar>
          <div class="field">
            <label for="nombre">Nombre completo</label>
            <input type="text" id="nombre" name="nombre" placeholder="Ej: María González Pérez" autocomplete="name" required>
          </div>

          <div class="field">
            <label for="rut">RUT</label>
            <input type="text" id="rut" name="rut" placeholder="Ej: 12.345.678-9" autocomplete="off" required>
          </div>

          <div class="field">
            <label for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion" placeholder="Calle, número, comuna" autocomplete="street-address" required>
          </div>

          <div class="field">
            <label for="id_junta">Junta de vecinos</label>
            <select id="id_junta" name="id_junta" required style="width:100%; padding:14px 16px; font-size:1.05rem; border:2px solid var(--border); border-radius:10px; font-family:inherit; background:var(--bg);">
              <option value="">Selecciona tu junta</option>
              <?php while ($j = $juntas->fetch_assoc()): ?>
                <option value="<?php echo $j['id_junta']; ?>"><?php echo htmlspecialchars($j['nombre']); ?></option>
              <?php endwhile; ?>
            </select>
            <p class="field-hint">Si tu junta de vecinos no aparece en la lista, contacta a tu directiva.</p>
          </div>

          <div class="field">
            <label for="correo">Correo electrónico</label>
            <input type="email" id="correo" name="correo" placeholder="tucorreo@ejemplo.cl" autocomplete="email" required>
          </div>

          <div class="field">
            <label for="clave">Crea una clave</label>
            <div class="campo-clave">
              <input type="password" id="clave" name="clave" placeholder="Mínimo 8 caracteres" autocomplete="new-password" required>
              <button type="button" class="btn-ver-clave" data-target="clave" aria-label="Mostrar clave">👁</button>
            </div>
          </div>

          <div class="field">
            <label for="claveConfirmar">Confirma tu clave</label>
            <div class="campo-clave">
              <input type="password" id="claveConfirmar" name="claveConfirmar" placeholder="Escribe la clave nuevamente" autocomplete="new-password" required>
              <button type="button" class="btn-ver-clave" data-target="claveConfirmar" aria-label="Mostrar clave">👁</button>
            </div>
          </div>

          <button type="submit" class="btn btn-primary btn-large">Enviar solicitud de registro</button>
        </form>

        <p class="access-foot">
          ¿Ya tienes cuenta? <a href="Acceso.html">Inicia sesión aquí</a>
        </p>
      </div>
    </div>
  </section>

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

  <div class="text-tools">
    <button type="button" data-texto="disminuir" aria-label="Letra normal">A</button>
    <button type="button" data-texto="aumentar" aria-label="Aumentar letra">A+</button>
  </div>

  <script src="../JS/Script.js"></script>
</body>
</html>