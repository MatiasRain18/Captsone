<?php
/*
 * Pruebas automaticas del Sistema Unidad Territorial
 * Grupo 7 - Matias Rain y Daniel Cruz - Capstone PTY4614
 *
 * Que hace: recorre el sistema como lo haria una persona (inicia sesion, abre paginas,
 * descarga certificados, verifica codigos) y revisa que cada respuesta sea la esperada.
 * Tambien intenta cosas que NO deberian funcionar, como entrar al panel del directorio
 * siendo vecino o bajar el PDF de otra persona, para comprobar que el sistema las bloquea.
 *
 * Las pruebas solo leen datos: no crean, no modifican ni borran nada en la base.
 * Usan las cuentas de prueba que vienen en unidad_territorial.sql (ver README).
 *
 * Como ejecutarlo (con el sistema ya levantado):
 *   XAMPP:  C:\xampp\php\php.exe pruebas_automaticas.php
 *   Docker: C:\xampp\php\php.exe pruebas_automaticas.php http://localhost:8080/JuntaVecinos
 *   (sirve cualquier PHP 8 con la extension curl activada)
 */

$base = rtrim($argv[1] ?? 'http://localhost/JuntaVecinos', '/');

// Cuentas de prueba del archivo unidad_territorial.sql
$cuentas = [
    'benjamin' => ['rut' => '21.691.775-8', 'clave' => 'HolaQueHace19', 'junta' => 'Los Aromos'],      // directorio, junta 1
    'matias'   => ['rut' => '21.691.775-K', 'clave' => 'HolaQueHace19', 'junta' => 'Los Aromos'],      // vecino, junta 1
    'luis'     => ['rut' => '22.222.222-K', 'clave' => '12345678',      'junta' => 'Vista Hermosa'],   // directorio, junta 3
    'hernan'   => ['rut' => '12.121.212-K', 'clave' => 'MatiasRain21',  'junta' => 'Vista Hermosa'],   // vecino, junta 3
];

$aprobadas = 0;
$fallidas = 0;
$numero = 0;
$carpetaCookies = sys_get_temp_dir();

// ---------------------------------------------------------------- utilidades

function pedir($url, $sesion = null, $post = null, $json = false) {
    global $carpetaCookies;
    $c = curl_init($url);
    $cabeceras = $json ? ['Accept: application/json', 'X-Requested-With: XMLHttpRequest'] : [];
    curl_setopt_array($c, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HEADER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_HTTPHEADER => $cabeceras,
    ]);
    if ($sesion) {
        $archivo = $carpetaCookies . DIRECTORY_SEPARATOR . 'ut_prueba_' . $sesion . '.txt';
        curl_setopt($c, CURLOPT_COOKIEJAR, $archivo);
        curl_setopt($c, CURLOPT_COOKIEFILE, $archivo);
    }
    if ($post !== null) {
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    $respuesta = curl_exec($c);
    if ($respuesta === false) {
        $error = curl_error($c);
        curl_close($c);
        return ['codigo' => 0, 'destino' => '', 'cuerpo' => '', 'error' => $error];
    }
    $codigo = curl_getinfo($c, CURLINFO_HTTP_CODE);
    $tamCabecera = curl_getinfo($c, CURLINFO_HEADER_SIZE);
    curl_close($c);
    $cabecera = substr($respuesta, 0, $tamCabecera);
    preg_match('/^Location:\s*(.+)$/mi', $cabecera, $m);
    return ['codigo' => $codigo, 'destino' => trim($m[1] ?? ''), 'cuerpo' => substr($respuesta, $tamCabecera), 'error' => ''];
}

function prueba($descripcion, $resultado, $detalle = '') {
    global $aprobadas, $fallidas, $numero;
    $numero++;
    $n = str_pad($numero, 2, '0', STR_PAD_LEFT);
    if ($resultado) {
        $aprobadas++;
        echo "  [OK]    PA-$n  $descripcion\n";
    } else {
        $fallidas++;
        echo "  [FALLA] PA-$n  $descripcion" . ($detalle ? "  ->  $detalle" : '') . "\n";
    }
}

function json($r) {
    return json_decode($r['cuerpo'], true) ?? [];
}

function iniciarSesion($quien) {
    global $base, $cuentas;
    $archivo = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ut_prueba_' . $quien . '.txt';
    if (file_exists($archivo)) {
        unlink($archivo);
    }
    return json(pedir("$base/PHP/Procesar-login.php", $quien, ['rut' => $cuentas[$quien]['rut'], 'clave' => $cuentas[$quien]['clave']], true));
}

function esPdf($r) {
    return str_starts_with($r['cuerpo'], '%PDF');
}

// ---------------------------------------------------------------- inicio

echo "\nPRUEBAS AUTOMATICAS - SISTEMA UNIDAD TERRITORIAL\n";
echo "Servidor: $base\n";
echo "Fecha: " . date('d-m-Y H:i') . "\n\n";

$inicio = pedir("$base/HTML/Index.html");
if ($inicio['codigo'] === 0) {
    echo "No se pudo conectar con $base\n";
    echo "Revisa que Apache y MySQL (XAMPP) o los contenedores (Docker) esten encendidos.\n";
    exit(1);
}

echo "1. Paginas publicas\n";
prueba('La pagina de inicio carga', $inicio['codigo'] === 200);
prueba('La pantalla de acceso carga', pedir("$base/HTML/Acceso.html")['codigo'] === 200);
prueba('El formulario de registro carga', pedir("$base/HTML/Registro.php")['codigo'] === 200);
prueba('La verificacion publica de certificados carga', pedir("$base/HTML/Verificar.php")['codigo'] === 200);

echo "\n2. Inicio de sesion\n";
$r = json(pedir("$base/PHP/Procesar-login.php", 'intruso', ['rut' => '21.691.775-8', 'clave' => 'ClaveIncorrecta'], true));
$mensajeClaveMala = $r['mensaje'] ?? '';
prueba('Con la clave incorrecta no deja entrar', ($r['ok'] ?? true) === false);
$r = json(pedir("$base/PHP/Procesar-login.php", 'intruso', ['rut' => '11.111.111-K', 'clave' => 'Cualquiera1'], true));
prueba('Con un RUT inexistente no deja entrar', ($r['ok'] ?? true) === false);
prueba('Muestra el mismo mensaje en ambos casos (no da pistas)', $mensajeClaveMala !== '' && $mensajeClaveMala === ($r['mensaje'] ?? ''));
$r = iniciarSesion('benjamin');
prueba('El presidente de Los Aromos entra a su panel del directorio', ($r['ok'] ?? false) && ($r['redirigir'] ?? '') === 'PanelDirectorio.php');
$r = iniciarSesion('matias');
prueba('El vecino de Los Aromos entra a su panel de vecino', ($r['ok'] ?? false) && ($r['redirigir'] ?? '') === 'Panel.php');
$r = iniciarSesion('luis');
prueba('El presidente de Vista Hermosa entra a su panel del directorio', ($r['ok'] ?? false) && ($r['redirigir'] ?? '') === 'PanelDirectorio.php');
$r = iniciarSesion('hernan');
prueba('El vecino de Vista Hermosa entra a su panel de vecino', ($r['ok'] ?? false) && ($r['redirigir'] ?? '') === 'Panel.php');

echo "\n3. Permisos y sesiones\n";
$r = pedir("$base/HTML/Panel.php", 'anonimo');
prueba('Sin sesion, el panel del vecino manda al login', $r['codigo'] === 302 && str_contains($r['destino'], 'Acceso.html'));
$r = pedir("$base/HTML/PanelDirectorio.php", 'anonimo');
prueba('Sin sesion, el panel del directorio manda al login', $r['codigo'] === 302 && str_contains($r['destino'], 'Acceso.html'));
$r = pedir("$base/HTML/PanelDirectorio.php", 'matias');
prueba('Un vecino que intenta abrir el panel del directorio es devuelto a su panel', $r['codigo'] === 302 && str_contains($r['destino'], 'Panel.php'));
$r = pedir("$base/HTML/GestionVecinos.php", 'hernan');
prueba('Un vecino no puede abrir la gestion de vecinos', $r['codigo'] === 302);
$r = json(pedir("$base/PHP/Api-buscar-vecino.php?rut=" . urlencode('12.121.212-K'), 'matias', null, true));
prueba('Un vecino no puede usar la busqueda de vecinos del directorio', ($r['ok'] ?? true) === false);

echo "\n4. Paginas con sesion iniciada\n";
foreach (['Panel.php', 'Certificados.php', 'Proyectos.php', 'Noticias.php', 'Espacios.php'] as $pagina) {
    $r = pedir("$base/HTML/$pagina", 'matias');
    prueba("El vecino abre $pagina y ve su junta", $r['codigo'] === 200 && str_contains($r['cuerpo'], 'Los Aromos'));
}
foreach (['PanelDirectorio.php', 'GestionVecinos.php', 'GestionCertificados.php', 'GestionProyectos.php', 'GestionNoticias.php', 'GestionEspacios.php', 'GestionDirectiva.php'] as $pagina) {
    $r = pedir("$base/HTML/$pagina", 'benjamin');
    prueba("El directorio abre $pagina", $r['codigo'] === 200 && str_contains($r['cuerpo'], 'Los Aromos'));
}
$r = json(pedir("$base/PHP/Api-resumen.php", 'benjamin', null, true));
prueba('El resumen de pendientes del panel responde en JSON', is_array($r) && count($r) > 0);

echo "\n5. Separacion entre juntas (multi-junta)\n";
$r = pedir("$base/HTML/GestionVecinos.php", 'benjamin');
prueba('Los Aromos no ve a los vecinos de Vista Hermosa', $r['codigo'] === 200 && !str_contains($r['cuerpo'], 'Hernan Crespo'));
$r = pedir("$base/HTML/GestionVecinos.php", 'luis');
prueba('Vista Hermosa no ve a los vecinos de Los Aromos', $r['codigo'] === 200 && !str_contains($r['cuerpo'], 'Geraldine Parra'));
$r = json(pedir("$base/PHP/Api-buscar-vecino.php?rut=" . urlencode('12.121.212-K'), 'luis', null, true));
prueba('El directorio encuentra a un vecino de su propia junta', ($r['encontrado'] ?? false) === true);
$r = json(pedir("$base/PHP/Api-buscar-vecino.php?rut=" . urlencode('12.121.212-K'), 'benjamin', null, true));
prueba('El directorio NO encuentra a un vecino de otra junta (no puede enviarle avisos)', ($r['encontrado'] ?? true) === false);

echo "\n6. Certificados en PDF\n";
prueba('El vecino descarga su propio certificado aprobado en PDF', esPdf(pedir("$base/PHP/Generar-certificado.php?id=2", 'matias')));
prueba('El vecino NO puede bajar el certificado de otro vecino cambiando el numero', !esPdf(pedir("$base/PHP/Generar-certificado.php?id=1", 'matias')));
prueba('Un vecino de otra junta NO puede bajar ese certificado', !esPdf(pedir("$base/PHP/Generar-certificado.php?id=2", 'hernan')));
prueba('Sin sesion no se puede bajar ningun certificado', !esPdf(pedir("$base/PHP/Generar-certificado.php?id=2", 'anonimo')));

echo "\n7. Verificacion publica de certificados\n";
$r = json(pedir("$base/PHP/Api-verificar-certificado.php?codigo=6DF2-30BA-A24C"));
prueba('Un codigo real aparece como certificado autentico', ($r['valido'] ?? false) === true);
prueba('El RUT se muestra parcialmente oculto por privacidad', str_contains($r['certificado']['rut'] ?? '', '***'));
$r = json(pedir("$base/PHP/Api-verificar-certificado.php?codigo=AAAA-BBBB-CCCC"));
prueba('Un codigo inventado aparece como no autentico', ($r['valido'] ?? true) === false);
$r = json(pedir("$base/PHP/Api-verificar-certificado.php?codigo=" . urlencode("' OR '1'='1")));
prueba('Un intento de inyeccion SQL en el codigo es rechazado', ($r['valido'] ?? true) === false);

echo "\n8. Validacion del RUT en el registro\n";
$r = json(pedir("$base/PHP/Api-rut-disponible.php?rut=" . urlencode('21.691.775-0')));
prueba('Un RUT con digito verificador incorrecto es rechazado', ($r['valido'] ?? true) === false);
$r = json(pedir("$base/PHP/Api-rut-disponible.php?rut=" . urlencode('21.691.775-8')));
prueba('Un RUT que ya tiene cuenta aparece como no disponible', ($r['valido'] ?? false) === true && ($r['disponible'] ?? true) === false);
$r = json(pedir("$base/PHP/Api-rut-disponible.php?rut=" . urlencode('10.000.000-8')));
prueba('Un RUT valido y nuevo aparece como disponible', ($r['valido'] ?? false) === true && ($r['disponible'] ?? false) === true);

echo "\n9. Cierre de sesion\n";
pedir("$base/PHP/Logout.php", 'matias');
$r = pedir("$base/HTML/Panel.php", 'matias');
prueba('Despues de cerrar sesion el panel vuelve a pedir el login', $r['codigo'] === 302 && str_contains($r['destino'], 'Acceso.html'));

// Se borran las cookies temporales de las pruebas
foreach (glob(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ut_prueba_*.txt') as $archivo) {
    @unlink($archivo);
}

$total = $aprobadas + $fallidas;
echo "\n----------------------------------------------------------\n";
echo "RESULTADO: $aprobadas de $total pruebas aprobadas";
echo $fallidas ? ", $fallidas con falla.\n" : ". Todo funciona como se espera.\n";
echo "----------------------------------------------------------\n\n";
exit($fallidas ? 1 : 0);
