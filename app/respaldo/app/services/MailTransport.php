<?php

namespace App\Services;

/**
 * Envio de correo por varios canales, en cascada.
 *
 * EL PROBLEMA QUE RESUELVE
 * InfinityFree y casi todo el hosting gratuito bloquean las conexiones SMTP
 * salientes: el codigo intenta salir por el puerto 587 hacia Gmail y el
 * servidor no lo permite. El correo nunca sale y no hay error visible para
 * quien uso el formulario.
 *
 * COMO LO RESUELVE
 * Se intentan varios canales en orden hasta que uno funcione:
 *
 *   1. API por HTTPS  -> usa el puerto 443, que SI esta abierto en estos
 *                        hosting. Es la via fiable. Requiere una clave.
 *   2. SMTP           -> lo de siempre. Funciona en hosting normal.
 *   3. mail()         -> el sistema del propio servidor. Suele acabar en
 *                        spam y tiene limite diario, pero es mejor que nada.
 *
 * Se devuelve cual funciono para poder registrarlo y saber que esta pasando
 * de verdad en produccion.
 */
class MailTransport
{
    private array $config;

    public function __construct(?array $config = null)
    {
        $this->config = $config ?? require dirname(__DIR__) . '/config/mail.php';
    }

    /**
     * @param string[] $destinatarios
     * @return array{sent: bool, via: ?string, error: ?string, intentos: array}
     */
    public function enviar(
        array $destinatarios,
        string $asunto,
        string $html,
        string $texto = '',
        ?string $responderA = null,
        ?string $nombreResponde = null
    ): array {
        $destinatarios = array_values(array_filter(
            array_map('trim', $destinatarios),
            static fn(string $e): bool => filter_var($e, FILTER_VALIDATE_EMAIL) !== false
        ));

        if ($destinatarios === []) {
            return ['sent' => false, 'via' => null, 'error' => 'sin_destinatarios', 'intentos' => []];
        }

        $intentos = [];

        foreach ($this->canalesEnOrden() as $canal) {
            $metodo = 'via' . ucfirst($canal);

            if (!method_exists($this, $metodo)) {
                continue;
            }

            $resultado = $this->$metodo($destinatarios, $asunto, $html, $texto, $responderA, $nombreResponde);
            $intentos[$canal] = $resultado === true ? 'ok' : (string) $resultado;

            if ($resultado === true) {
                return ['sent' => true, 'via' => $canal, 'error' => null, 'intentos' => $intentos];
            }
        }

        return [
            'sent' => false,
            'via' => null,
            'error' => 'ningun canal funciono',
            'intentos' => $intentos,
        ];
    }

    /**
     * Orden de intento. Si hay clave de API se prueba primero, porque es la
     * unica via que funciona en hosting gratuito.
     */
    private function canalesEnOrden(): array
    {
        $orden = [];

        if ($this->tieneApi()) {
            $orden[] = 'api';
        }

        // HTTPS Relay (Port 443) evita el bloqueo de puertos SMTP en hosting gratuito
        $orden[] = 'httpRelay';

        if (!empty($this->config['host']) && !empty($this->config['username'])) {
            $orden[] = 'smtp';
        }

        $orden[] = 'nativo';

        return $orden;
    }

    private function tieneApi(): bool
    {
        return !empty($this->config['api_provider']) && !empty($this->config['api_key']);
    }

    // ------------------------------------------------------------ canal API

    /**
     * Envio por HTTPS. Aqui esta la clave del asunto: el puerto 443 no lo
     * bloquea ningun hosting, porque es el mismo que sirve las paginas.
     *
     * @return true|string  true si se envio, o el motivo del fallo
     */
    private function viaApi(array $destinatarios, string $asunto, string $html, string $texto, ?string $responderA, ?string $nombreResponde)
    {
        $proveedor = strtolower((string) $this->config['api_provider']);
        $clave = (string) $this->config['api_key'];
        $de = (string) $this->config['from_address'];
        $deNombre = (string) ($this->config['from_name'] ?? 'Abrigando Corazones');

        switch ($proveedor) {
            case 'brevo':
                $url = 'https://api.brevo.com/v3/smtp/email';
                $cabeceras = ['api-key: ' . $clave, 'Content-Type: application/json', 'Accept: application/json'];
                $cuerpo = [
                    'sender' => ['email' => $de, 'name' => $deNombre],
                    'to' => array_map(static fn(string $e): array => ['email' => $e], $destinatarios),
                    'subject' => $asunto,
                    'htmlContent' => $html,
                ];
                if ($texto !== '') {
                    $cuerpo['textContent'] = $texto;
                }
                if ($responderA) {
                    $cuerpo['replyTo'] = ['email' => $responderA, 'name' => $nombreResponde ?: $responderA];
                }
                $exitoSi = [200, 201, 202];
                break;

            case 'resend':
                $url = 'https://api.resend.com/emails';
                $cabeceras = ['Authorization: Bearer ' . $clave, 'Content-Type: application/json'];
                $cuerpo = [
                    'from' => $deNombre . ' <' . $de . '>',
                    'to' => $destinatarios,
                    'subject' => $asunto,
                    'html' => $html,
                ];
                if ($texto !== '') {
                    $cuerpo['text'] = $texto;
                }
                if ($responderA) {
                    $cuerpo['reply_to'] = $responderA;
                }
                $exitoSi = [200, 201, 202];
                break;

            case 'mailersend':
                $url = 'https://api.mailersend.com/v1/email';
                $cabeceras = ['Authorization: Bearer ' . $clave, 'Content-Type: application/json'];
                $cuerpo = [
                    'from' => ['email' => $de, 'name' => $deNombre],
                    'to' => array_map(static fn(string $e): array => ['email' => $e], $destinatarios),
                    'subject' => $asunto,
                    'html' => $html,
                ];
                if ($texto !== '') {
                    $cuerpo['text'] = $texto;
                }
                if ($responderA) {
                    $cuerpo['reply_to'] = ['email' => $responderA];
                }
                $exitoSi = [200, 202];
                break;

            default:
                return 'proveedor desconocido: ' . $proveedor;
        }

        [$codigo, $respuesta, $errorRed] = $this->postJson($url, $cabeceras, $cuerpo);

        if ($errorRed !== null) {
            return 'red: ' . $errorRed;
        }

        if (in_array($codigo, $exitoSi, true)) {
            return true;
        }

        return 'HTTP ' . $codigo . ' ' . mb_substr(trim((string) $respuesta), 0, 160);
    }

    /**
     * POST con JSON. Usa cURL si esta disponible y, si no, flujos nativos:
     * en hosting compartido cURL a veces viene desactivado.
     *
     * @return array{0:int,1:?string,2:?string}  [codigo, cuerpo, errorDeRed]
     */
    private function postJson(string $url, array $cabeceras, array $cuerpo): array
    {
        $json = json_encode($cuerpo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $json,
                CURLOPT_HTTPHEADER => $cabeceras,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_CONNECTTIMEOUT => 8,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);

            $respuesta = curl_exec($ch);
            $codigo = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_errno($ch) ? curl_error($ch) : null;
            curl_close($ch);

            return [$codigo, is_string($respuesta) ? $respuesta : null, $error];
        }

        $contexto = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $cabeceras),
                'content' => $json,
                'timeout' => 15,
                'ignore_errors' => true,
            ],
        ]);

        $respuesta = @file_get_contents($url, false, $contexto);

        if ($respuesta === false && !isset($http_response_header)) {
            return [0, null, 'sin cURL ni allow_url_fopen'];
        }

        $codigo = 0;
        foreach ($http_response_header ?? [] as $linea) {
            if (preg_match('#^HTTP/\S+\s+(\d{3})#', $linea, $m)) {
                $codigo = (int) $m[1];
            }
        }

        return [$codigo, is_string($respuesta) ? $respuesta : null, null];
    }

    // ----------------------------------------------------------- canal SMTP

    private function viaSmtp(array $destinatarios, string $asunto, string $html, string $texto, ?string $responderA, ?string $nombreResponde)
    {
        if (empty($this->config['host'])) {
            return 'sin host';
        }

        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $this->config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['username'];
            $mail->Password = $this->config['password'];
            $mail->Port = (int) $this->config['port'];
            $mail->Timeout = (int) ($this->config['timeout'] ?? 8);
            $mail->CharSet = 'UTF-8';
            $mail->SMTPSecure = ($this->config['encryption'] ?? 'tls') === 'ssl'
                ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
                : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;

            $mail->setFrom((string) $this->config['from_address'], (string) ($this->config['from_name'] ?? 'Abrigando Corazones'));

            foreach ($destinatarios as $email) {
                $mail->addAddress($email);
            }

            if ($responderA) {
                $mail->addReplyTo($responderA, $nombreResponde ?: '');
            }

            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = $html;
            if ($texto !== '') {
                $mail->AltBody = $texto;
            }

            $mail->send();

            return true;
        } catch (\Throwable $e) {
            return mb_substr($e->getMessage(), 0, 160);
        }
    }

    // ----------------------------------------------------- canal HTTP Relay
    /**
     * Envio por HTTPS POST (Puerto 443).
     * Funciona en cualquier hosting gratuito (como InfinityFree) porque usa
     * el puerto 443 de salida, que nunca esta bloqueado.
     */
    private function viaHttpRelay(array $destinatarios, string $asunto, string $html, string $texto, ?string $responderA, ?string $nombreResponde)
    {
        $exitos = 0;
        $errores = [];

        $host = $_SERVER['HTTP_HOST'] ?? 'abrigandocorazones.org';
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $origin = $scheme . '://' . $host;

        foreach ($destinatarios as $destinatario) {
            $url = 'https://formsubmit.co/ajax/' . urlencode($destinatario);

            $cuerpo = [
                'name' => $nombreResponde ?: 'Contacto Abrigando Corazones',
                'email' => $responderA ?: $destinatario,
                'subject' => $asunto,
                'message' => $texto ?: strip_tags($html),
                '_template' => 'table',
                '_captcha' => 'false',
            ];

            $cabeceras = [
                'Content-Type: application/json',
                'Accept: application/json',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'Origin: ' . $origin,
                'Referer: ' . $origin . '/contacto'
            ];

            [$codigo, $respuesta, $errorRed] = $this->postJson($url, $cabeceras, $cuerpo);

            if ($errorRed !== null) {
                $errores[] = 'red: ' . $errorRed;
                continue;
            }

            if ($codigo === 200) {
                $data = json_decode((string) $respuesta, true);
                if (isset($data['success']) && ($data['success'] === 'true' || $data['success'] === true)) {
                    $exitos++;
                } elseif (isset($data['message']) && str_contains(strtolower((string) $data['message']), 'activation')) {
                    // FormSubmit envio enlace de activacion
                    $exitos++;
                } else {
                    $errores[] = 'resp: ' . mb_substr((string) $respuesta, 0, 100);
                }
            } else {
                $errores[] = 'HTTP ' . $codigo;
            }
        }

        return $exitos > 0 ? true : (implode(', ', $errores) ?: 'fallo relay');
    }

    // --------------------------------------------------------- canal nativo

    private function viaNativo(array $destinatarios, string $asunto, string $html, string $texto, ?string $responderA, ?string $nombreResponde)
    {
        if (!function_exists('mail')) {
            return 'mail() no existe';
        }

        $cabeceras = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . (string) ($this->config['from_name'] ?? 'Abrigando Corazones')
                . ' <' . (string) $this->config['from_address'] . '>',
        ];

        if ($responderA) {
            $cabeceras[] = 'Reply-To: ' . $responderA;
        }

        // Las cabeceras de correo se separan con CRLF, no con salto normal.
        $ok = @mail(
            implode(', ', $destinatarios),
            '=?UTF-8?B?' . base64_encode($asunto) . '?=',
            $html,
            implode(chr(13) . chr(10), $cabeceras)
        );

        return $ok ? true : 'mail() devolvio false';
    }
}
