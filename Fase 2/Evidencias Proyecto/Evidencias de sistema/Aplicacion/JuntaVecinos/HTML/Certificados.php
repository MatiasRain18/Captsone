<?php
require_once '../PHP/Config.php';
require_once '../PHP/Verificar-sesion.php';

$usuario = verificarSesion($conexion);
$esSocio = (int) $usuario['es_socio_activo'];

$stmt = $conexion->prepare("SELECT id_certificado, tipo, motivo, estado, codigo_validacion, fecha_solicitud, fecha_respuesta FROM certificados WHERE id_vecino = ? ORDER BY fecha_solicitud DESC");
$stmt->bind_param("i", $usuario['id_vecino']);
$stmt->execute();
$solicitudes = $stmt->get_result();

$etiquetaTipo = ['residencia' => 'Certificado de residencia', 'socio_activo' => 'Certificado de vecino vigente'];
$etiquetaEstado = ['pendiente' => ['En revisión', 'sello-espera'], 'aprobado' => ['Aprobado', 'sello-ok'], 'rechazado' => ['Rechazado', 'sello-error']];

$tituloPagina = 'Certificados';
$paginaActiva = 'Certificados.php';
require '../PHP/Encabezado.php';
?>

<header class="pagina-cabecera">
  <h1>Certificados</h1>
  <p>Pide un certificado a tu junta y descárgalo en PDF cuando el directorio lo apruebe. Cada certificado trae un código para verificarlo en línea.</p>
</header>

<div class="dos-columnas">
  <section class="tarjeta" aria-labelledby="tituloSolicitar">
    <h2 id="tituloSolicitar">Solicitar certificado</h2>
    <p class="intro">Elige el tipo de certificado que necesitas.</p>

    <form action="../PHP/Procesar-certificado.php" method="POST" novalidate data-ajax data-validar>
      <div class="field">
        <label for="tipo">Tipo de certificado</label>
        <select id="tipo" name="tipo" required>
          <option value="">Selecciona una opción</option>
          <option value="residencia">Certificado de residencia</option>
          <option value="socio_activo" <?php echo $esSocio ? '' : 'disabled'; ?>>Certificado de vecino vigente (socio activo)<?php echo $esSocio ? '' : ' - solo socios activos'; ?></option>
        </select>
      </div>

      <div class="field">
        <label for="motivo">Motivo de la solicitud <span class="opcional">(opcional)</span></label>
        <input type="text" id="motivo" name="motivo" maxlength="250" placeholder="Ej: trámite municipal, postulación a subsidio">
        <p class="field-hint">Aparecerá en el certificado: "para ser presentado en…".</p>
      </div>

      <button type="submit" class="btn btn-primary btn-large">Enviar solicitud</button>
    </form>
  </section>

  <section class="bloque" aria-labelledby="tituloMis">
    <div class="bloque-titulo">
      <h2 id="tituloMis">Mis solicitudes</h2>
    </div>

    <div class="lista" data-zona="mis-certificados">
      <?php if ($solicitudes->num_rows === 0): ?>
        <div class="vacio">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          <h3>Todavía no has pedido ningún certificado</h3>
          <p>Usa el formulario para hacer tu primera solicitud.</p>
        </div>
      <?php else: ?>
        <?php while ($s = $solicitudes->fetch_assoc()): ?>
          <article class="fila">
            <span class="icono-fila"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
            <div class="fila-info">
              <h3><?php echo $etiquetaTipo[$s['tipo']]; ?></h3>
              <p>Pedido el <?php echo fechaLarga($s['fecha_solicitud']); ?><?php echo $s['motivo'] ? ' para: ' . e($s['motivo']) : ''; ?></p>
              <?php if ($s['estado'] === 'aprobado' && $s['codigo_validacion']): ?>
                <p class="meta">Código de validación: <strong><?php echo e($s['codigo_validacion']); ?></strong></p>
              <?php endif; ?>
            </div>
            <div class="fila-acciones">
              <span class="sello <?php echo $etiquetaEstado[$s['estado']][1]; ?>"><?php echo $etiquetaEstado[$s['estado']][0]; ?></span>
              <?php if ($s['estado'] === 'aprobado'): ?>
                <a href="../PHP/Generar-certificado.php?id=<?php echo (int) $s['id_certificado']; ?>" class="btn btn-primary btn-sm">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                  Descargar PDF
                </a>
              <?php endif; ?>
            </div>
          </article>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>
  </section>
</div>

<?php require '../PHP/Pie.php'; ?>
