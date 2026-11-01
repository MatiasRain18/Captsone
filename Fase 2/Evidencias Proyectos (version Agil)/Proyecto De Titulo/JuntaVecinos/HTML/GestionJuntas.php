<?php
require_once '../PHP/Config.php';
require_once '../PHP/Verificar-sesion.php';

$usuario = verificarSesion($conexion, 'directorio');

// Juntas registradas, con cuántos cargos de la directiva tienen ocupados
$juntas = $conexion->query("
    SELECT j.id_junta, j.nombre, j.comuna, j.direccion_sede,
           (SELECT COUNT(*) FROM vecinos WHERE id_junta = j.id_junta AND rol = 'vecino' AND estado = 'aprobado') AS vecinos,
           (SELECT GROUP_CONCAT(cargo) FROM vecinos WHERE id_junta = j.id_junta AND cargo IS NOT NULL AND estado = 'aprobado') AS cargos
    FROM juntas j
    ORDER BY j.nombre ASC
");

$tituloPagina = 'Juntas de vecinos';
$paginaActiva = 'GestionJuntas.php';
require '../PHP/Encabezado.php';
?>

<header class="pagina-cabecera">
  <h1>Juntas de vecinos</h1>
  <p>Da de alta una junta nueva en el sistema junto con su directiva completa: presidente, secretario y tesorero.</p>
</header>

<div class="dos-columnas" style="grid-template-columns: minmax(320px, 520px) 1fr;">
  <section class="tarjeta" aria-labelledby="tituloCrear">
    <h2 id="tituloCrear">Crear junta nueva</h2>
    <p class="intro">Cada integrante de la directiva entrará al sistema con su RUT y la clave que escribas aquí.</p>

    <form action="../PHP/Procesar-junta.php" method="POST" novalidate data-ajax data-validar>
      <fieldset class="grupo-campos">
        <legend>Datos de la junta</legend>
        <div class="field">
          <label for="nombre_junta">Nombre de la junta</label>
          <input type="text" id="nombre_junta" name="nombre_junta" maxlength="150" placeholder="Ej: Junta de Vecinos Vista Hermosa" required>
        </div>
        <div class="campos-2">
          <div class="field">
            <label for="comuna">Comuna</label>
            <input type="text" id="comuna" name="comuna" maxlength="100" placeholder="Ej: Santiago" required>
          </div>
          <div class="field">
            <label for="correo_junta">Correo de contacto</label>
            <input type="email" id="correo_junta" name="correo_junta" placeholder="contacto@ejemplo.cl" required>
          </div>
        </div>
        <div class="field">
          <label for="direccion_sede">Dirección de la sede</label>
          <input type="text" id="direccion_sede" name="direccion_sede" maxlength="200" placeholder="Calle y número de la sede vecinal" required>
        </div>
      </fieldset>

      <fieldset class="grupo-campos">
        <legend>Presidente(a)</legend>
        <p class="field-hint">Representa a la junta y firma los certificados.</p>
        <div class="field">
          <label for="nombre_presidente">Nombre completo</label>
          <input type="text" id="nombre_presidente" name="nombre_presidente" maxlength="150" placeholder="Nombre y apellidos" required>
        </div>
        <div class="campos-2">
          <div class="field">
            <label for="rut_presidente">RUT</label>
            <input type="text" id="rut_presidente" name="rut_presidente" placeholder="Ej: 12.345.678-5" autocomplete="off" data-rut required>
          </div>
          <div class="field">
            <label for="correo_presidente">Correo</label>
            <input type="email" id="correo_presidente" name="correo_presidente" placeholder="correo@ejemplo.cl" required>
          </div>
        </div>
        <div class="field">
          <label for="clave_presidente">Clave inicial</label>
          <div class="campo-clave">
            <input type="password" id="clave_presidente" name="clave_presidente" placeholder="Mínimo 8 caracteres" autocomplete="new-password" minlength="8" required>
            <button type="button" class="btn-ver-clave" data-target="clave_presidente" aria-label="Mostrar clave" aria-pressed="false">
                <svg class="icono-ver" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="icono-ocultar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.9 4.2A10.4 10.4 0 0 1 12 4c6.5 0 10 8 10 8a17.6 17.6 0 0 1-2.2 3.2"/><path d="M6.6 6.6A17.4 17.4 0 0 0 2 12s3.5 8 10 8a10 10 0 0 0 5.4-1.6"/><path d="M14.1 14.1a3 3 0 1 1-4.2-4.2"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
            </button>
          </div>
        </div>
      </fieldset>
      <fieldset class="grupo-campos">
        <legend>Secretario(a)</legend>
        <p class="field-hint">Lleva las actas y el registro de socios.</p>
        <div class="field">
          <label for="nombre_secretario">Nombre completo</label>
          <input type="text" id="nombre_secretario" name="nombre_secretario" maxlength="150" placeholder="Nombre y apellidos" required>
        </div>
        <div class="campos-2">
          <div class="field">
            <label for="rut_secretario">RUT</label>
            <input type="text" id="rut_secretario" name="rut_secretario" placeholder="Ej: 12.345.678-5" autocomplete="off" data-rut required>
          </div>
          <div class="field">
            <label for="correo_secretario">Correo</label>
            <input type="email" id="correo_secretario" name="correo_secretario" placeholder="correo@ejemplo.cl" required>
          </div>
        </div>
        <div class="field">
          <label for="clave_secretario">Clave inicial</label>
          <div class="campo-clave">
            <input type="password" id="clave_secretario" name="clave_secretario" placeholder="Mínimo 8 caracteres" autocomplete="new-password" minlength="8" required>
            <button type="button" class="btn-ver-clave" data-target="clave_secretario" aria-label="Mostrar clave" aria-pressed="false">
                <svg class="icono-ver" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="icono-ocultar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.9 4.2A10.4 10.4 0 0 1 12 4c6.5 0 10 8 10 8a17.6 17.6 0 0 1-2.2 3.2"/><path d="M6.6 6.6A17.4 17.4 0 0 0 2 12s3.5 8 10 8a10 10 0 0 0 5.4-1.6"/><path d="M14.1 14.1a3 3 0 1 1-4.2-4.2"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
            </button>
          </div>
        </div>
      </fieldset>
      <fieldset class="grupo-campos">
        <legend>Tesorero(a)</legend>
        <p class="field-hint">Administra las cuotas y los fondos.</p>
        <div class="field">
          <label for="nombre_tesorero">Nombre completo</label>
          <input type="text" id="nombre_tesorero" name="nombre_tesorero" maxlength="150" placeholder="Nombre y apellidos" required>
        </div>
        <div class="campos-2">
          <div class="field">
            <label for="rut_tesorero">RUT</label>
            <input type="text" id="rut_tesorero" name="rut_tesorero" placeholder="Ej: 12.345.678-5" autocomplete="off" data-rut required>
          </div>
          <div class="field">
            <label for="correo_tesorero">Correo</label>
            <input type="email" id="correo_tesorero" name="correo_tesorero" placeholder="correo@ejemplo.cl" required>
          </div>
        </div>
        <div class="field">
          <label for="clave_tesorero">Clave inicial</label>
          <div class="campo-clave">
            <input type="password" id="clave_tesorero" name="clave_tesorero" placeholder="Mínimo 8 caracteres" autocomplete="new-password" minlength="8" required>
            <button type="button" class="btn-ver-clave" data-target="clave_tesorero" aria-label="Mostrar clave" aria-pressed="false">
                <svg class="icono-ver" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="icono-ocultar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.9 4.2A10.4 10.4 0 0 1 12 4c6.5 0 10 8 10 8a17.6 17.6 0 0 1-2.2 3.2"/><path d="M6.6 6.6A17.4 17.4 0 0 0 2 12s3.5 8 10 8a10 10 0 0 0 5.4-1.6"/><path d="M14.1 14.1a3 3 0 1 1-4.2-4.2"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
            </button>
          </div>
        </div>
      </fieldset>

      <button type="submit" class="btn btn-primary btn-large">Crear junta y directiva</button>
    </form>
  </section>

  <section class="bloque" aria-labelledby="tituloRegistradas">
    <div class="bloque-titulo"><h2 id="tituloRegistradas">Juntas registradas</h2></div>
    <div class="lista" data-zona="juntas">
      <?php while ($j = $juntas->fetch_assoc()): ?>
        <?php
          $tiene = $j['cargos'] ? explode(',', $j['cargos']) : [];
          $faltan = array_diff(['presidente', 'secretario', 'tesorero'], $tiene);
        ?>
        <article class="fila">
          <span class="icono-fila" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg></span>
          <div class="fila-info">
            <h3><?php echo e($j['nombre']); ?><?php echo ((int) $j['id_junta'] === (int) $usuario['id_junta']) ? ' (tu junta)' : ''; ?></h3>
            <p><?php echo e($j['comuna']); ?>, <?php echo e($j['direccion_sede']); ?></p>
            <p class="meta"><?php echo (int) $j['vecinos']; ?> vecino<?php echo ((int) $j['vecinos'] === 1) ? '' : 's'; ?> inscrito<?php echo ((int) $j['vecinos'] === 1) ? '' : 's'; ?></p>
          </div>
          <?php if (!$faltan): ?>
            <span class="sello sello-ok">Directiva completa</span>
          <?php else: ?>
            <span class="sello sello-espera">Falta <?php echo e(implode(' y ', array_map(fn($c) => mb_strtolower(nombreCargo($c)), $faltan))); ?></span>
          <?php endif; ?>
        </article>
      <?php endwhile; ?>
    </div>
  </section>
</div>

<?php require '../PHP/Pie.php'; ?>
