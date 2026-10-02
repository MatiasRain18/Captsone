<?php
session_start();
require_once '../PHP/Config.php';

if (!isset($_SESSION['id_vecino'])) {
    header('Location: Acceso.html');
    exit;
}

if (($_SESSION['es_socio_activo'] ?? 0) != 1) {
    header('Location: Proyectos.php');
    exit;
}

$id_proyecto = intval($_GET['id_proyecto'] ?? 0);
$idJuntaSesion = $_SESSION['id_junta'];

$stmt = $conexion->prepare("SELECT nombre, descripcion, fecha_limite FROM proyectos WHERE id_proyecto = ? AND id_junta = ? AND estado = 'abierto'");
$stmt->bind_param("ii", $id_proyecto, $idJuntaSesion);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header('Location: Proyectos.php');
    exit;
}

$proyecto = $resultado->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema Unidad Territorial - Postulación</title>
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
        <a href="Proyectos.php" class="active">Proyectos</a>
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

  <section class="access">
    <div class="container">
      <div class="access-card">
        <h1>Postular a proyecto</h1>
        <p><?php echo htmlspecialchars($proyecto['nombre']); ?> — postulaciones hasta el <?php echo date('d/m/Y', strtotime($proyecto['fecha_limite'])); ?></p>

        <form action="../PHP/procesar-postulacion.php" method="POST" novalidate data-validar>
          <input type="hidden" name="id_proyecto" value="<?php echo $id_proyecto; ?>">

          <div class="field">
            <label for="motivacion">¿Por qué quieres participar? (opcional)</label>
            <input type="text" id="motivacion" name="motivacion" placeholder="Cuéntanos brevemente tu motivación">
          </div>

          <button type="submit" class="btn btn-primary btn-large">Enviar postulación</button>
        </form>

        <p class="access-foot">
          <a href="Proyectos.php">Volver a Proyectos</a>
        </p>
      </div>
    </div>
  </section>

  <div class="text-tools">
    <button type="button" data-texto="disminuir" aria-label="Letra normal">A</button>
    <button type="button" data-texto="aumentar" aria-label="Aumentar letra">A+</button>
  </div>

  <script src="../JS/Script.js"></script>
</body>
</html>