<?php
require_once '../PHP/Config.php';
$juntas = $conexion->query("SELECT id_junta, nombre, comuna FROM juntas ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Crear cuenta - Unidad Territorial</title>
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
        <a href="Verificar.php">Verificar certificado</a>
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
          <h1>Crea tu cuenta de vecino</h1>
          <p>Completa tus datos. El directorio de tu junta revisará tu inscripción antes de darte acceso.</p>

          <form action="../PHP/Procesar-registro.php" method="POST" novalidate data-validar data-ajax data-al-terminar="reemplazar" data-exito="registroListo">
            <div class="aviso" data-mensaje-form hidden role="alert"><p></p></div>

            <div class="field">
              <label for="nombre">Nombre completo</label>
              <input type="text" id="nombre" name="nombre" placeholder="Ej: María González Pérez" autocomplete="name" maxlength="150" required>
            </div>

            <div class="field">
              <label for="rut">RUT</label>
              <input type="text" id="rut" name="rut" placeholder="Ej: 12.345.678-5" autocomplete="off" data-rut data-revisar-rut="ayudaRut" required>
              <p class="ayuda-vivo" id="ayudaRut" aria-live="polite"></p>
            </div>

            <div class="field">
              <label for="direccion">Dirección</label>
              <input type="text" id="direccion" name="direccion" placeholder="Calle, número, comuna" autocomplete="street-address" maxlength="200" required>
            </div>

            <div class="field">
              <label for="id_junta">Junta de vecinos</label>
              <select id="id_junta" name="id_junta" required>
                <option value="">Selecciona tu junta</option>
                <?php while ($j = $juntas->fetch_assoc()): ?>
                  <option value="<?php echo $j['id_junta']; ?>"><?php echo e($j['nombre'] . ' (' . $j['comuna'] . ')'); ?></option>
                <?php endwhile; ?>
              </select>
              <p class="field-hint">Si tu junta de vecinos no aparece en la lista, contacta a tu directiva.</p>
            </div>

            <div class="field">
              <label for="correo">Correo electrónico</label>
              <input type="email" id="correo" name="correo" placeholder="tucorreo@ejemplo.cl" autocomplete="email" required>
              <p class="field-hint">Aquí te avisaremos cuando aprueben tu cuenta y tus certificados.</p>
            </div>

          <div class="field">
            <label for="clave">Crea una clave</label>
            <div class="campo-clave">
              <input type="password" id="clave" name="clave" placeholder="Mínimo 8 caracteres" autocomplete="new-password" required minlength="8">
              <button type="button" class="btn-ver-clave" data-target="clave" aria-label="Mostrar clave" aria-pressed="false">
                <svg class="icono-ver" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="icono-ocultar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.9 4.2A10.4 10.4 0 0 1 12 4c6.5 0 10 8 10 8a17.6 17.6 0 0 1-2.2 3.2"/><path d="M6.6 6.6A17.4 17.4 0 0 0 2 12s3.5 8 10 8a10 10 0 0 0 5.4-1.6"/><path d="M14.1 14.1a3 3 0 1 1-4.2-4.2"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
              </button>
            </div>
          </div>

          <div class="field">
            <label for="claveConfirmar">Confirma tu clave</label>
            <div class="campo-clave">
              <input type="password" id="claveConfirmar" name="claveConfirmar" placeholder="Escribe la clave nuevamente" autocomplete="new-password" required minlength="8" data-igual-a="clave">
              <button type="button" class="btn-ver-clave" data-target="claveConfirmar" aria-label="Mostrar clave" aria-pressed="false">
                <svg class="icono-ver" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="icono-ocultar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.9 4.2A10.4 10.4 0 0 1 12 4c6.5 0 10 8 10 8a17.6 17.6 0 0 1-2.2 3.2"/><path d="M6.6 6.6A17.4 17.4 0 0 0 2 12s3.5 8 10 8a10 10 0 0 0 5.4-1.6"/><path d="M14.1 14.1a3 3 0 1 1-4.2-4.2"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
              </button>
            </div>
          </div>

            <div class="field">
              <label class="casilla" for="mayor_14">
                <input type="checkbox" id="mayor_14" name="mayor_14" value="1" required data-mensaje="Para inscribirte debes tener 14 años o más.">
                <span>Declaro tener 14 años o más (edad mínima para inscribirse en una junta de vecinos).</span>
              </label>
            </div>

            <button type="submit" class="btn btn-primary btn-large">Enviar solicitud de registro</button>
          </form>

          <div class="estado-envio" id="registroListo" hidden>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="8 12.5 11 15.5 16 9.5"/></svg>
            <h2>¡Solicitud enviada!</h2>
            <p data-texto-exito></p>
            <a href="Acceso.html" class="btn btn-primary btn-large">Ir a iniciar sesión</a>
          </div>

          <p class="access-foot">
            ¿Ya tienes cuenta? <a href="Acceso.html">Inicia sesión aquí</a>
          </p>
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
