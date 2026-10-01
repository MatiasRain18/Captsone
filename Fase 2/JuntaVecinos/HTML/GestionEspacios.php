<?php
session_start();
require_once '../PHP/Config.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'directorio') {
    header('Location: Acceso.html');
    exit;
}

$idJuntaSesion = $_SESSION['id_junta'];

$stmt = $conexion->prepare("SELECT r.id_reserva, r.espacio, r.fecha, r.horario, r.actividad, v.nombre, v.rut FROM reservas_espacios r JOIN vecinos v ON r.id_vecino = v.id_vecino WHERE r.estado = 'pendiente' AND v.id_junta = ? ORDER BY r.fecha ASC");
$stmt->bind_param("i", $idJuntaSesion);
$stmt->execute();
$resultado = $stmt->get_result();

$etiquetaEspacio = ['cancha' => 'Cancha multicancha', 'sala' => 'Sala de reuniones', 'plaza' => 'Plaza central'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema Unidad Territorial - Gestión de espacios</title>
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
        <a href="GestionEspacios.php" class="active">Espacios</a>
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
      <h1>Reservas pendientes</h1>
      <p>Revisa cada solicitud de espacio y apruébala o recházala.</p>
    </div>
  </section>

  <section class="simple-list">
    <div class="container" style="display:flex; flex-direction:column; gap:16px;">

      <?php if ($resultado->num_rows === 0): ?>
        <div class="list-item">
          <div class="list-item-main">
            <h3>No hay reservas pendientes por ahora</h3>
            <p>Todas las solicitudes ya fueron revisadas.</p>
          </div>
        </div>
      <?php else: ?>
        <?php while ($r = $resultado->fetch_assoc()): ?>
          <div class="list-item">
            <div class="list-item-main">
              <h3><?php echo $etiquetaEspacio[$r['espacio']]; ?> — <?php echo date('d/m/Y', strtotime($r['fecha'])); ?>, <?php echo htmlspecialchars($r['horario']); ?></h3>
              <p><?php echo htmlspecialchars($r['nombre']); ?> — RUT <?php echo htmlspecialchars($r['rut']); ?><br>Actividad: <?php echo htmlspecialchars($r['actividad']); ?></p>
            </div>
            <form action="../PHP/procesar-aprobar-espacio.php" method="POST" style="display:flex; gap:10px;">
              <input type="hidden" name="id_reserva" value="<?php echo $r['id_reserva']; ?>">
              <button type="submit" name="accion" value="aprobar" class="btn btn-primary">Aprobar</button>
              <button type="submit" name="accion" value="rechazar" class="btn btn-outline" onclick="return confirm('¿Seguro que quieres rechazar esta reserva?');">Rechazar</button>
            </form>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>

    </div>
  </section>

  <section class="section-block">
    <div class="container">
      <h2>Actividades vecinales con cupo</h2>
      <p>Crea una actividad nueva y define cuántos cupos tiene.</p>
    </div>
  </section>

  <section class="access" style="padding-top: 0;">
    <div class="container">
      <div class="access-card">
        <h1>Nueva actividad</h1>

        <form action="../PHP/procesar-actividad.php" method="POST" novalidate data-validar>
          <div class="field">
            <label for="nombre">Nombre de la actividad</label>
            <input type="text" id="nombre" name="nombre" placeholder="Ej: Taller de manualidades" required>
          </div>

          <div class="field">
            <label for="descripcion">Descripción (opcional)</label>
            <input type="text" id="descripcion" name="descripcion" placeholder="Breve descripción de la actividad">
          </div>

          <div class="field">
            <label for="fecha">Fecha</label>
            <input type="date" id="fecha" name="fecha" required>
          </div>

          <div class="field">
            <label for="cupo_maximo">Cupo máximo</label>
            <input type="number" id="cupo_maximo" name="cupo_maximo" min="1" placeholder="Ej: 20" required>
          </div>

          <button type="submit" class="btn btn-primary btn-large">Crear actividad</button>
        </form>
      </div>
    </div>
  </section>

  <section class="simple-list">
    <div class="container" style="display:flex; flex-direction:column; gap:16px;">

      <?php
      $stmtAct = $conexion->prepare("SELECT a.id_actividad, a.nombre, a.fecha, a.cupo_maximo, (SELECT COUNT(*) FROM inscripciones_actividades WHERE id_actividad = a.id_actividad) AS ocupados FROM actividades a WHERE a.id_junta = ? ORDER BY a.fecha ASC");
      $stmtAct->bind_param("i", $idJuntaSesion);
      $stmtAct->execute();
      $actividades = $stmtAct->get_result();
      ?>

      <?php if ($actividades->num_rows === 0): ?>
        <div class="list-item">
          <div class="list-item-main">
            <h3>Todavía no has creado ninguna actividad</h3>
          </div>
        </div>
      <?php else: ?>
        <?php while ($a = $actividades->fetch_assoc()): ?>
          <div class="list-item">
            <div class="list-item-main">
              <h3><?php echo htmlspecialchars($a['nombre']); ?> — <?php echo date('d/m/Y', strtotime($a['fecha'])); ?></h3>
            </div>
            <span class="badge badge-general"><?php echo $a['ocupados']; ?> / <?php echo $a['cupo_maximo']; ?> cupos</span>
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