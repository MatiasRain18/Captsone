<?php
// Parte superior de todas las páginas con sesión iniciada (vecino y directorio).
// Antes de incluirlo, cada página define:
//   $usuario        -> lo que devuelve verificarSesion()
//   $tituloPagina   -> texto de la pestaña del navegador
//   $paginaActiva   -> nombre del archivo actual, para marcarlo en el menú

$iconos = [
    'inicio'       => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M9 21v-6h6v6"/>',
    'certificados' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="13" y2="17"/>',
    'proyectos'    => '<path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
    'noticias'     => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
    'espacios'     => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
    'vecinos'      => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
    'directiva'    => '<circle cx="12" cy="8" r="4"/><path d="M6 21v-1a6 6 0 0 1 12 0v1"/><path d="m15.5 3.5 1.5-1.5"/>',
    'juntas'       => '<path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/>',
];

if ($usuario['rol'] === 'directorio') {
    $menu = [
        ['PanelDirectorio.php', 'Inicio', 'inicio', null],
        ['GestionVecinos.php', 'Vecinos', 'vecinos', 'vecinos'],
        ['GestionCertificados.php', 'Certificados', 'certificados', 'certificados'],
        ['GestionProyectos.php', 'Proyectos', 'proyectos', 'postulaciones'],
        ['GestionNoticias.php', 'Noticias', 'noticias', null],
        ['GestionEspacios.php', 'Espacios y actividades', 'espacios', 'espacios'],
        ['GestionDirectiva.php', 'Directiva', 'directiva', 'directiva'],
        ['GestionJuntas.php', 'Juntas de vecinos', 'juntas', null],
    ];
    $descripcionUsuario = nombreCargo($usuario['cargo']);
} else {
    $menu = [
        ['Panel.php', 'Inicio', 'inicio', null],
        ['Certificados.php', 'Certificados', 'certificados', null],
        ['Proyectos.php', 'Proyectos', 'proyectos', null],
        ['Noticias.php', 'Noticias', 'noticias', null],
        ['Espacios.php', 'Espacios y actividades', 'espacios', null],
    ];
    $descripcionUsuario = ((int) $usuario['es_socio_activo'] === 1) ? 'Vecino socio activo' : 'Vecino';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo e($tituloPagina); ?> - Unidad Territorial</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400&family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../CSS/Styles.css">
</head>
<body class="app">

<div class="app-shell">
  <aside class="barra-lateral" id="barraLateral">
    <div class="barra-cabecera">
      <a href="<?php echo e($menu[0][0]); ?>" class="logo logo-claro">
        <span class="logo-mark" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?php echo $iconos['inicio']; ?></svg>
        </span>
        Unidad Territorial
      </a>
      <button class="menu-toggle menu-toggle-claro" id="menuToggle" aria-label="Abrir menú" aria-expanded="false" aria-controls="menuApp">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/></svg>
      </button>
    </div>

    <div class="barra-contenido" id="menuApp">
      <p class="barra-junta">
        <span>Tu junta</span>
        <strong><?php echo e($usuario['junta']); ?></strong>
      </p>

      <nav class="menu-app" aria-label="Menú principal">
        <?php foreach ($menu as [$archivo, $texto, $icono, $contador]): ?>
          <a href="<?php echo e($archivo); ?>" class="<?php echo ($archivo === $paginaActiva) ? 'activo' : ''; ?>" <?php echo ($archivo === $paginaActiva) ? 'aria-current="page"' : ''; ?>>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $iconos[$icono]; ?></svg>
            <span><?php echo e($texto); ?></span>
            <?php if ($contador): ?>
              <span class="contador" data-contador="<?php echo e($contador); ?>" hidden></span>
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
      </nav>

      <div class="barra-usuario">
        <span class="avatar avatar-claro" aria-hidden="true"><?php echo e(iniciales($usuario['nombre'])); ?></span>
        <div>
          <strong><?php echo e($usuario['nombre']); ?></strong>
          <span><?php echo e($descripcionUsuario); ?></span>
        </div>
      </div>

      <div class="barra-acciones">
        <div class="text-tools text-tools-barra" role="group" aria-label="Tamaño de letra">
          <button type="button" data-texto="disminuir" aria-label="Letra normal">A</button>
          <button type="button" data-texto="aumentar" aria-label="Aumentar letra">A+</button>
        </div>
        <a href="../PHP/Logout.php" class="btn btn-salir">Cerrar sesión</a>
      </div>
    </div>
  </aside>

  <main class="app-main" id="principal">
