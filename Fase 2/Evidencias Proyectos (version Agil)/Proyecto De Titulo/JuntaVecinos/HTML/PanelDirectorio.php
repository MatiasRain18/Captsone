<?php
require_once '../PHP/Config.php';
require_once '../PHP/Verificar-sesion.php';

$usuario = verificarSesion($conexion, 'directorio');

// Cargos que faltan en la directiva de esta junta
$stmt = $conexion->prepare("SELECT cargo FROM vecinos WHERE id_junta = ? AND cargo IS NOT NULL AND estado = 'aprobado'");
$stmt->bind_param("i", $usuario['id_junta']);
$stmt->execute();
$ocupados = array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'cargo');
$faltan = array_diff(['presidente', 'secretario', 'tesorero'], $ocupados);

$primerNombre = explode(' ', $usuario['nombre'])[0];

$tituloPagina = 'Panel del directorio';
$paginaActiva = 'PanelDirectorio.php';
require '../PHP/Encabezado.php';
?>

<header class="pagina-cabecera">
  <p class="saludo-junta"><?php echo e($usuario['junta']); ?></p>
  <h1>Hola, <?php echo e($primerNombre); ?></h1>
  <p>Eres <?php echo e(mb_strtolower(nombreCargo($usuario['cargo']))); ?> de la junta. Aquí ves lo que está esperando una respuesta.</p>
</header>

<?php if ($faltan): ?>
  <div class="aviso aviso-espera">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    <div>
      <h3>La directiva de tu junta está incompleta</h3>
      <p>Una junta de vecinos debe tener presidente, secretario y tesorero. Falta: <?php echo e(implode(' y ', array_map(fn($c) => mb_strtolower(nombreCargo($c)), $faltan))); ?>.</p>
      <a href="GestionDirectiva.php" class="btn btn-secundario btn-sm">Completar la directiva</a>
    </div>
  </div>
<?php endif; ?>

<section class="bloque">
  <div class="bloque-titulo"><h2>Por revisar</h2></div>
  <div class="accesos">
    <a href="GestionVecinos.php" class="acceso">
      <span class="icono-fila"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
      <h3>Inscripciones de vecinos</h3>
      <p>Aprueba o rechaza a quienes se registraron.</p>
      <span class="dato" data-dato="vecinos" data-alerta data-cero="Nada pendiente" data-uno="1 inscripción pendiente" data-varios="{n} inscripciones pendientes"></span>
    </a>
    <a href="GestionCertificados.php" class="acceso">
      <span class="icono-fila"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
      <h3>Certificados</h3>
      <p>Solicitudes de residencia y vecino vigente.</p>
      <span class="dato" data-dato="certificados" data-alerta data-cero="Nada pendiente" data-uno="1 solicitud pendiente" data-varios="{n} solicitudes pendientes"></span>
    </a>
    <a href="GestionProyectos.php" class="acceso">
      <span class="icono-fila"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></span>
      <h3>Postulaciones a proyectos</h3>
      <p>Socios que postularon a un proyecto.</p>
      <span class="dato" data-dato="postulaciones" data-alerta data-cero="Nada pendiente" data-uno="1 postulación pendiente" data-varios="{n} postulaciones pendientes"></span>
    </a>
    <a href="GestionEspacios.php" class="acceso">
      <span class="icono-fila"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
      <h3>Reservas de espacios</h3>
      <p>Cancha, sala de reuniones y plaza.</p>
      <span class="dato" data-dato="espacios" data-alerta data-cero="Nada pendiente" data-uno="1 reserva pendiente" data-varios="{n} reservas pendientes"></span>
    </a>
  </div>
</section>

<section class="bloque">
  <div class="bloque-titulo"><h2>Tu junta en cifras</h2></div>
  <div class="cifras">
    <div class="cifra"><b data-dato="aprobados" data-cero="0" data-uno="1" data-varios="{n}">–</b><span>vecinos inscritos</span></div>
    <div class="cifra"><b data-dato="socios" data-cero="0" data-uno="1" data-varios="{n}">–</b><span>socios activos</span></div>
    <div class="cifra"><b data-dato="proyectos" data-cero="0" data-uno="1" data-varios="{n}">–</b><span>proyectos abiertos</span></div>
    <div class="cifra"><b data-dato="noticias" data-cero="0" data-uno="1" data-varios="{n}">–</b><span>noticias publicadas</span></div>
  </div>
</section>

<section class="bloque">
  <div class="bloque-titulo"><h2>Publicar y organizar</h2></div>
  <div class="accesos">
    <a href="GestionNoticias.php" class="acceso">
      <h3>Publicar una noticia</h3>
      <p>Para todos, para socios activos o para un vecino.</p>
    </a>
    <a href="GestionProyectos.php" class="acceso">
      <h3>Publicar un proyecto</h3>
      <p>Los socios activos podrán postular.</p>
    </a>
    <a href="GestionEspacios.php#nuevaActividad" class="acceso">
      <h3>Crear una actividad</h3>
      <p>Talleres y eventos con cupos.</p>
    </a>
    <a href="GestionDirectiva.php" class="acceso">
      <h3>Directiva</h3>
      <p>Presidente, secretario y tesorero.</p>
    </a>
  </div>
</section>

<?php require '../PHP/Pie.php'; ?>
