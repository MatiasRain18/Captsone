<?php
// Funciones que se usan en todo el sistema: textos seguros, RUT y respuestas AJAX.

// Escapa un texto antes de mostrarlo en la página (evita inyección de HTML)
function e($texto) {
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

// ---------------------------------------------------------------------
// AJAX
// ---------------------------------------------------------------------

// true cuando la petición viene desde JavaScript (fetch) y espera JSON
function esAjax() {
    $conXhr = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    $pideJson = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    return $conXhr || $pideJson;
}

/**
 * Responde una acción del sistema.
 * - Si la petición es AJAX, devuelve JSON: { ok, mensaje, ...extra }
 *   extra puede traer 'redirigir' (ir a otra página) o 'recargar' (actualizar la lista).
 * - Si el navegador no tiene JavaScript, muestra una página con el diseño del sitio
 *   (ya no aparecen las páginas en blanco con texto arriba a la izquierda).
 */
function responder($ok, $mensaje, $extra = []) {
    if (esAjax()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => $ok, 'mensaje' => $mensaje] + $extra, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Sin JavaScript: si salió bien, se vuelve a la página correspondiente
    if ($ok && !empty($extra['redirigir'])) {
        header('Location: ' . rutaDesdePhp($extra['redirigir']));
        exit;
    }
    if ($ok && !empty($extra['recargar']) && !empty($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $volver = $extra['volver'] ?? ($extra['redirigir'] ?? ($_SERVER['HTTP_REFERER'] ?? 'Index.html'));
    mostrarPaginaMensaje($ok, $mensaje, rutaDesdePhp($volver));
}

// Las páginas están en /HTML y los procesos en /PHP: ajusta la ruta de destino
function rutaDesdePhp($ruta) {
    if (preg_match('#^(https?:)?//|^\.\./|^/#', $ruta)) {
        return $ruta;
    }
    return '../HTML/' . $ruta;
}

// Página de mensaje con el diseño del sitio (respaldo cuando no hay JavaScript)
function mostrarPaginaMensaje($ok, $mensaje, $volver) {
    http_response_code($ok ? 200 : 400);
    $titulo = $ok ? 'Listo' : 'No se pudo completar';
    $clase = $ok ? 'aviso-ok' : 'aviso-error';
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo e($titulo); ?> - Unidad Territorial</title>
<link rel="stylesheet" href="../CSS/Styles.css">
</head>
<body class="pagina-mensaje">
  <main class="mensaje-caja <?php echo $clase; ?>">
    <span class="logo-mark logo-mark-grande" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M9 21v-6h6v6"/></svg>
    </span>
    <h1><?php echo e($titulo); ?></h1>
    <p><?php echo e($mensaje); ?></p>
    <a class="btn btn-primary" href="<?php echo e($volver); ?>">Volver</a>
  </main>
</body>
</html>
    <?php
    exit;
}

// ---------------------------------------------------------------------
// RUT: se guarda sin puntos (número) y el dígito verificador aparte
// ---------------------------------------------------------------------

// Calcula el dígito verificador con el método módulo 11
function calcularDv($cuerpo) {
    $suma = 0;
    $multiplicador = 2;
    $digitos = strrev((string) $cuerpo);
    for ($i = 0; $i < strlen($digitos); $i++) {
        $suma += intval($digitos[$i]) * $multiplicador;
        $multiplicador = ($multiplicador === 7) ? 2 : $multiplicador + 1;
    }
    $resto = 11 - ($suma % 11);
    if ($resto === 11) return '0';
    if ($resto === 10) return 'K';
    return (string) $resto;
}

/**
 * Recibe el RUT como lo escribió la persona (con o sin puntos y guión)
 * y lo separa en ['cuerpo' => 12345678, 'dv' => 'K'].
 * Devuelve null si no tiene forma de RUT.
 */
function separarRut($texto) {
    $limpio = strtoupper(preg_replace('/[^0-9kK]/', '', (string) $texto));
    if (strlen($limpio) < 2) {
        return null;
    }
    $dv = substr($limpio, -1);
    $cuerpo = substr($limpio, 0, -1);
    if (!ctype_digit($cuerpo) || strlen($cuerpo) > 8 || intval($cuerpo) < 100000) {
        return null;
    }
    return ['cuerpo' => intval($cuerpo), 'dv' => $dv];
}

// true si el dígito verificador corresponde al número
function rutValido($rut) {
    return $rut !== null && calcularDv($rut['cuerpo']) === $rut['dv'];
}

// 12345678 + 'K'  ->  '12.345.678-K'  (solo para mostrar en pantalla)
function formatearRut($cuerpo, $dv) {
    return number_format((int) $cuerpo, 0, ',', '.') . '-' . $dv;
}

// ---------------------------------------------------------------------
// Otros
// ---------------------------------------------------------------------

// Nombre del cargo para mostrar en pantalla
function nombreCargo($cargo) {
    $nombres = [
        'presidente' => 'Presidente(a)',
        'secretario' => 'Secretario(a)',
        'tesorero'   => 'Tesorero(a)',
    ];
    return $nombres[$cargo] ?? 'Directorio';
}

// Iniciales para el círculo de cada persona en las listas
function iniciales($nombre) {
    $partes = preg_split('/\s+/', trim((string) $nombre));
    $letras = mb_substr($partes[0] ?? '', 0, 1);
    if (count($partes) > 1) {
        $letras .= mb_substr(end($partes), 0, 1);
    }
    return mb_strtoupper($letras);
}

// Fecha en español: 8 de octubre de 2026
function fechaLarga($fecha) {
    $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    $t = strtotime($fecha);
    return date('j', $t) . ' de ' . $meses[date('n', $t) - 1] . ' de ' . date('Y', $t);
}

// Lee un campo de texto del formulario sin espacios sobrantes
function campo($nombre) {
    return trim((string) ($_POST[$nombre] ?? ''));
}

// true si la fecha viene en formato AAAA-MM-DD y existe
function fechaValida($fecha) {
    $d = DateTime::createFromFormat('Y-m-d', $fecha);
    return $d && $d->format('Y-m-d') === $fecha;
}
