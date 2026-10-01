<?php
session_start();
require_once '../PHP/Config.php';

if (!isset($_SESSION['id_vecino'])) {
    header('Location: Acceso.html');
    exit;
}

$id_vecino = $_SESSION['id_vecino'];
$idJuntaSesion = $_SESSION['id_junta'];

$stmt = $conexion->prepare("SELECT espacio, fecha, horario, actividad, estado, fecha_solicitud FROM reservas_espacios WHERE id_vecino = ? ORDER BY fecha_solicitud DESC");
$stmt->bind_param("i", $id_vecino);
$stmt->execute();
$reservas = $stmt->get_result();

$etiquetaEspacio = ['cancha' => 'Cancha multicancha', 'sala' => 'Sala de reuniones', 'plaza' => 'Plaza central'];
$etiquetaEstado = ['pendiente' => ['Pendiente', 'badge-pending'], 'aprobado' => ['Aprobada', 'badge-approved'], 'rechazado' => ['Rechazada', 'badge-rejected']];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema Unidad Territorial - Espacios comunitarios</title>
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
        <a href="Noticias.php">Noticias</a>
        <a href="Espacios.php" class="active">Espacios</a>
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
      <h1>Espacios comunitarios</h1>
      <p>Solicita el uso de canchas, salas o plazas de tu junta de vecinos para tu actividad.</p>
    </div>
  </section>

  <section class="access" style="padding-top: 24px;">
    <div class="container">
      <div class="access-card">
        <h1>Solicitar un espacio</h1>
        <p>Completa los datos de tu actividad.</p>

        <form action="../PHP/procesar-espacio.php" method="POST" novalidate data-validar>
          <div class="field">
            <label for="espacio">Espacio</label>
            <select id="espacio" name="espacio" required style="width:100%; padding:14px 16px; font-size:1.05rem; border:2px solid var(--border); border-radius:10px; font-family:inherit; background:var(--bg);">
              <option value="">Selecciona una opción</option>
              <option value="cancha">Cancha multicancha</option>
              <option value="sala">Sala de reuniones</option>
              <option value="plaza">Plaza central</option>
            </select>
          </div>

          <div class="field">
            <label for="fecha">Fecha solicitada</label>
            <input type="date" id="fecha" name="fecha" required>
          </div>

          <div class="field">
            <label for="horario">Horario</label>
            <input type="text" id="horario" name="horario" placeholder="Ej: 15:00 a 18:00" required>
          </div>

          <div class="field">
            <label for="actividad">Actividad a realizar</label>
            <input type="text" id="actividad" name="actividad" placeholder="Ej: cumpleaños infantil, taller vecinal, etc." required>
          </div>

          <button type="submit" class="btn btn-primary btn-large">Enviar solicitud</button>
        </form>
      </div>
    </div>
  </section>

  <section class="section-block">
    <div class="container">
      <h2>Mis reservas</h2>
      <p>Estado de los espacios que has solicitado.</p>
    </div>
  </section>

  <section class="simple-list">
    <div class="container" style="display:flex; flex-direction:column; gap:16px;">

      <?php if ($reservas->num_rows === 0): ?>
        <div class="list-item">
          <div class="list-item-main">
            <h3>Todavía no has pedido ningún espacio</h3>
            <p>Usa el formulario de arriba para hacer tu primera solicitud.</p>
          </div>
        </div>
      <?php else: ?>
        <?php while ($r = $reservas->fetch_assoc()): ?>
          <div class="list-item">
            <div class="list-item-main">
              <h3><?php echo $etiquetaEspacio[$r['espacio']]; ?> — <?php echo date('d/m/Y', strtotime($r['fecha'])); ?>, <?php echo htmlspecialchars($r['horario']); ?></h3>
              <p>Actividad: <?php echo htmlspecialchars($r['actividad']); ?></p>
            </div>
            <span class="badge <?php echo $etiquetaEstado[$r['estado']][1]; ?>"><?php echo $etiquetaEstado[$r['estado']][0]; ?></span>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>

    </div>
  </section>

  <section class="section-block">
    <div class="container">
      <h2>Actividades vecinales</h2>
      <p>Inscríbete mientras queden cupos disponibles.</p>
    </div>
  </section>

  <section class="simple-list">
    <div class="container" style="display:flex; flex-direction:column; gap:16px;">

      <?php
      $stmtAct = $conexion->prepare("SELECT a.id_actividad, a.nombre, a.descripcion, a.fecha, a.cupo_maximo, (SELECT COUNT(*) FROM inscripciones_actividades WHERE id_actividad = a.id_actividad) AS ocupados, EXISTS(SELECT 1 FROM inscripciones_actividades WHERE id_actividad = a.id_actividad AND id_vecino = ?) AS ya_inscrito FROM actividades a WHERE a.id_junta = ? ORDER BY a.fecha ASC");
      $stmtAct->bind_param("ii", $id_vecino, $idJuntaSesion);
      $stmtAct->execute();
      $actividades = $stmtAct->get_result();
      ?>

      <?php if ($actividades->num_rows === 0): ?>
        <div class="list-item">
          <div class="list-item-main">
            <h3>Todavía no hay actividades publicadas</h3>
          </div>
        </div>
      <?php else: ?>
        <?php while ($a = $actividades->fetch_assoc()): ?>
          <div class="list-item">
            <div class="list-item-main">
              <h3><?php echo htmlspecialchars($a['nombre']); ?> — <?php echo date('d/m/Y', strtotime($a['fecha'])); ?></h3>
              <p><?php echo htmlspecialchars($a['descripcion']); ?> (<?php echo $a['ocupados']; ?> / <?php echo $a['cupo_maximo']; ?> cupos)</p>
            </div>
            <?php if ($a['ya_inscrito']): ?>
              <span class="badge badge-approved">Ya estás inscrito</span>
            <?php elseif ($a['ocupados'] >= $a['cupo_maximo']): ?>
              <span class="badge badge-rejected">Cupos llenos</span>
            <?php else: ?>
              <form action="../PHP/procesar-inscribir-actividad.php" method="POST">
                <input type="hidden" name="id_actividad" value="<?php echo $a['id_actividad']; ?>">
                <button type="submit" class="btn btn-primary">Inscribirme</button>
              </form>
            <?php endif; ?>
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