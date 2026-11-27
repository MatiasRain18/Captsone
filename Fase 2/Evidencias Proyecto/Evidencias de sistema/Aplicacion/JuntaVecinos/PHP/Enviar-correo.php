<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

/**
 * Envía un correo desde la cuenta del sistema.
 * Devuelve true si se envió y false si falló.
 * Nunca detiene el sistema: si falla, solo lo registra y sigue.
 */
function enviarCorreo($para, $nombre, $asunto, $mensajeHtml) {
    $config = __DIR__ . '/Config-correo.php';

    // Sin configuración o sin un correo válido, no se intenta enviar
    if (!file_exists($config) || !filter_var($para, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    require_once $config;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = CORREO_USUARIO;
        $mail->Password   = CORREO_CLAVE;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->Timeout    = 10;

        // Entorno local (XAMPP): algunos antivirus en Windows interceptan las
        // conexiones de correo con su propio certificado y PHP las rechaza.
        // Se omite la verificación del certificado para que el envío funcione
        // en cualquier equipo de desarrollo. En un servidor de producción
        // esta configuración debe eliminarse.
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ];

        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(CORREO_USUARIO, CORREO_NOMBRE);
        $mail->addAddress($para, $nombre);

        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $mensajeHtml;
        $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], "\n", $mensajeHtml));

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Error al enviar correo: ' . $mail->ErrorInfo);
        return false;
    }
}

// Arma el correo con un diseño simple (encabezado verde con el nombre de la junta)
function plantillaCorreo($junta, $contenidoHtml) {
    $junta = htmlspecialchars($junta, ENT_QUOTES, 'UTF-8');
    return '
    <div style="font-family:Arial,sans-serif;background:#EEF2EF;padding:24px;">
      <div style="max-width:560px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #D5DED8;">
        <div style="background:#1F4A41;color:#ffffff;padding:18px 24px;font-size:18px;font-weight:bold;">' . $junta . '</div>
        <div style="padding:24px;color:#1E2B2A;font-size:15px;line-height:1.6;">' . $contenidoHtml . '
          <p style="margin-top:24px;">Saludos,<br>Directorio de la ' . $junta . '</p>
        </div>
        <div style="padding:14px 24px;background:#F6F8F6;color:#5B6866;font-size:12px;">Mensaje automático del Sistema Unidad Territorial. No respondas a este correo.</div>
      </div>
    </div>';
}
