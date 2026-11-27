<?php
require_once __DIR__ . '/Config.php';
require_once __DIR__ . '/Verificar-sesion.php';
require_once __DIR__ . '/fpdf/fpdf.php';

// Solo vecinos con sesión iniciada y cuenta aprobada
$usuario = verificarSesion($conexion);
$id_vecino = $usuario['id_vecino'];
$id_certificado = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_certificado) {
    header('Location: ../HTML/Certificados.php');
    exit;
}

// 3 condiciones a la vez: el certificado existe, es de este vecino y está aprobado.
// La junta se obtiene desde el id_junta del propio vecino (multi-junta).
$stmt = $conexion->prepare("
    SELECT c.id_certificado, c.tipo, c.motivo, c.codigo_validacion, c.fecha_respuesta, c.fecha_solicitud,
           v.nombre, v.rut, v.dv, v.direccion, v.id_junta,
           j.nombre AS junta, j.comuna, j.direccion_sede
    FROM certificados c
    INNER JOIN vecinos v ON c.id_vecino = v.id_vecino
    INNER JOIN juntas j ON v.id_junta = j.id_junta
    WHERE c.id_certificado = ? AND c.id_vecino = ? AND c.estado = 'aprobado'
");
$stmt->bind_param("ii", $id_certificado, $id_vecino);
$stmt->execute();
$d = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Si no cumple, no se genera nada
if (!$d) {
    header('Location: ../HTML/Certificados.php');
    exit;
}

// Código de validación guardado al aprobar. Si un certificado antiguo no lo tuviera, se crea ahora.
$codigo = $d['codigo_validacion'];
if (!$codigo) {
    $codigo = implode('-', str_split(strtoupper(bin2hex(random_bytes(6))), 4));
    $stmt = $conexion->prepare("UPDATE certificados SET codigo_validacion = ?, fecha_respuesta = COALESCE(fecha_respuesta, NOW()) WHERE id_certificado = ?");
    $stmt->bind_param("si", $codigo, $id_certificado);
    $stmt->execute();
    $stmt->close();
}

// Presidente(a) de la junta, para la firma
$stmt = $conexion->prepare("SELECT nombre FROM vecinos WHERE id_junta = ? AND cargo = 'presidente' AND estado = 'aprobado'");
$stmt->bind_param("i", $d['id_junta']);
$stmt->execute();
$presidente = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Dirección de la página pública de verificación (sirve en localhost y en un servidor real)
$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$carpeta = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
$urlVerificar = $protocolo . '://' . $_SERVER['HTTP_HOST'] . $carpeta . '/HTML/Verificar.php?codigo=' . $codigo;

// Asegura UTF-8 válido (por si un dato llega en otra codificación)
function t_utf8($s) {
    $s = (string) $s;
    return mb_check_encoding($s, 'UTF-8') ? $s : mb_convert_encoding($s, 'UTF-8', 'ISO-8859-1');
}

// FPDF no entiende UTF-8: convertimos para que tildes y ñ se vean bien.
// Si hay caracteres imposibles (ej: emojis), se omiten en vez de romper el PDF.
function t($s) {
    $s = t_utf8($s);
    $r = @iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $s);
    return ($r !== false) ? $r : mb_convert_encoding($s, 'Windows-1252', 'UTF-8');
}

// Fecha de emisión = fecha en que el directorio aprobó el certificado
$fechaEmision = $d['fecha_respuesta'] ?? $d['fecha_solicitud'];
$fechaTexto = fechaLarga($fechaEmision);
$folio = str_pad($d['id_certificado'], 6, '0', STR_PAD_LEFT);

// Textos según el tipo de certificado
if ($d['tipo'] === 'residencia') {
    $titulo = 'CERTIFICADO DE RESIDENCIA';
    $cuerpo = 'tiene su domicilio en la dirección indicada, dentro del territorio correspondiente a esta junta de vecinos, según consta en los registros de la organización.';
} else {
    $titulo = 'CERTIFICADO DE VECINO VIGENTE';
    $cuerpo = 'se encuentra inscrito(a) como vecino vigente y socio activo de esta junta de vecinos, según consta en los registros de la organización a la fecha de emisión del presente documento.';
}

$cierre = $d['motivo']
    ? 'Se extiende el presente certificado a petición del interesado(a), para ser presentado en: ' . $d['motivo'] . '.'
    : 'Se extiende el presente certificado a petición del interesado(a), para los fines que estime conveniente.';

$juntaMayus = mb_strtoupper(t_utf8($d['junta']), 'UTF-8');

// ---------- Armado del PDF ----------
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->SetMargins(25, 25, 25);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();

// Encabezado con la junta real
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 8, t($juntaMayus), 0, 1, 'C');
$pdf->SetFont('Arial', '', 11);
$sub = 'Comuna de ' . $d['comuna'];
if (!empty($d['direccion_sede'])) {
    $sub .= ' - Sede: ' . $d['direccion_sede'];
}
$pdf->Cell(0, 6, t($sub), 0, 1, 'C');
$pdf->Ln(3);
$pdf->SetLineWidth(0.6);
$pdf->Line(25, $pdf->GetY(), 185, $pdf->GetY());
$pdf->Ln(14);

// Título y folio
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 10, t($titulo), 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, t('Folio N° ' . $folio), 0, 1, 'C');
$pdf->Ln(12);

// Introducción
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 7, t('El Directorio de la ' . $d['junta'] . ', comuna de ' . $d['comuna'] . ', certifica que la persona individualizada a continuación:'), 0, 'J');
$pdf->Ln(5);

// Datos del vecino
$datos = ['Nombre' => $d['nombre'], 'RUT' => formatearRut($d['rut'], $d['dv']), 'Domicilio' => $d['direccion']];
foreach ($datos as $etiqueta => $valor) {
    $pdf->SetX(35);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(35, 8, t($etiqueta . ':'), 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, t($valor), 0, 1);
}
$pdf->Ln(5);

// Cuerpo según tipo y cierre
$pdf->MultiCell(0, 7, t($cuerpo), 0, 'J');
$pdf->Ln(4);
$pdf->MultiCell(0, 7, t($cierre), 0, 'J');
$pdf->Ln(10);

// Lugar y fecha
$pdf->Cell(0, 7, t($d['comuna'] . ', ' . $fechaTexto . '.'), 0, 1, 'R');

// ---------- Firma (izquierda) ----------
$pdf->SetLineWidth(0.3);
$pdf->SetDrawColor(0, 0, 0);
$pdf->Line(30, 222, 100, 222);
$pdf->SetXY(30, 224);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(70, 6, t($presidente ? $presidente['nombre'] : 'Directorio'), 0, 2, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 5, t($presidente ? 'Presidente(a)' : ''), 0, 2, 'C');
$pdf->MultiCell(70, 5, t($d['junta']), 0, 'C');

// ---------- Sello digital (derecha) ----------
$verde = [44, 95, 86];
$pdf->SetDrawColor($verde[0], $verde[1], $verde[2]);
$pdf->SetTextColor($verde[0], $verde[1], $verde[2]);
$pdf->SetLineWidth(0.9);
$pdf->Rect(116, 196, 66, 38);
$pdf->SetLineWidth(0.3);
$pdf->Rect(118, 198, 62, 34);

$pdf->SetXY(118, 200);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(62, 5, 'SELLO DIGITAL', 0, 2, 'C');
$pdf->SetFont('Arial', 'B', 8);
$pdf->MultiCell(62, 4, t($juntaMayus), 0, 'C');
$pdf->SetX(118);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(62, 4, 'DIRECTORIO', 0, 2, 'C');
$pdf->Cell(62, 4, t('Folio N° ' . $folio), 0, 2, 'C');
$pdf->Cell(62, 4, t('Emitido: ' . date('d/m/Y', strtotime($fechaEmision))), 0, 2, 'C');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(62, 4, $codigo, 0, 1, 'C');

// Volver a negro
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetTextColor(0, 0, 0);

// ---------- Verificación en línea ----------
$pdf->SetDrawColor(210, 220, 215);
$pdf->SetFillColor(244, 248, 246);
$pdf->SetLineWidth(0.3);
$pdf->Rect(25, 244, 160, 22, 'DF');
$pdf->SetXY(29, 246.5);
$pdf->SetFont('Arial', 'B', 9.5);
$pdf->SetTextColor($verde[0], $verde[1], $verde[2]);
$pdf->Cell(152, 5, t('Verifica la autenticidad de este certificado de la ' . $d['junta']), 0, 2, 'L');
$pdf->SetFont('Arial', '', 8.5);
$pdf->SetTextColor(60, 60, 60);
$pdf->Cell(152, 4.5, t('Ingresa al siguiente enlace o escribe el código ' . $codigo . ' en la página "Verificar certificado":'), 0, 2, 'L');
$pdf->SetFont('Arial', 'U', 8.5);
$pdf->SetTextColor(17, 80, 160);
$pdf->Cell(152, 5, t($urlVerificar), 0, 2, 'L', false, $urlVerificar);

// Pie de página
$pdf->SetY(-26);
$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(110, 110, 110);
$pdf->Cell(0, 5, t('Documento emitido por el Sistema Unidad Territorial - Folio N° ' . $folio . ' - Código de validación ' . $codigo . ' - Emitido el ' . date('d/m/Y', strtotime($fechaEmision))), 0, 1, 'C');

$pdf->Output('D', 'Certificado_' . $d['tipo'] . '_' . $folio . '.pdf');