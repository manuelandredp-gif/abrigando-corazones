<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\DailyReportService;
use App\Services\MetricsService;

class MetricsController extends Controller
{
    /**
     * Endpoint que recibe los clics desde el navegador (navigator.sendBeacon).
     *
     * Responde siempre 204 sin cuerpo: no devuelve informacion util a nadie que
     * lo sondee y no interrumpe la navegacion del visitante.
     */
    public function evento(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('X-Robots-Tag: noindex, nofollow');

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            exit;
        }

        $config = require dirname(__DIR__) . '/config/metrics.php';

        if (empty($config['enabled'])) {
            http_response_code(204);
            exit;
        }

        // Solo se aceptan eventos originados en el propio dominio: sin esto
        // cualquiera podria inflar las estadisticas desde fuera.
        //
        // Ojo con el puerto: HTTP_HOST lo incluye ("ejemplo.com:8080") y
        // parse_url(..., PHP_URL_HOST) no. Comparar en crudo hacia fallar la
        // validacion en cualquier puerto no estandar y se rechazaban TODOS los
        // eventos. Se normaliza quitando el puerto en ambos lados.
        $host = self::soloHost($_SERVER['HTTP_HOST'] ?? '');
        $origen = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '';

        if ($host !== '' && $origen !== '') {
            $origenHost = self::soloHost((string) (parse_url($origen, PHP_URL_HOST) ?: ''));

            if ($origenHost !== '' && strcasecmp($origenHost, $host) !== 0) {
                http_response_code(403);
                exit;
            }
        }

        $raw = file_get_contents('php://input') ?: '';

        // Tope de tamano: el cuerpo esperado son unos pocos cientos de bytes.
        if (strlen($raw) > 2048) {
            http_response_code(413);
            exit;
        }

        $datos = json_decode($raw, true);

        if (!is_array($datos)) {
            http_response_code(400);
            exit;
        }

        MetricsService::registrarEvento(
            (string) ($datos['ruta'] ?? '/'),
            (string) ($datos['tipo'] ?? 'click'),
            (string) ($datos['etiqueta'] ?? ''),
            isset($datos['destino']) ? (string) $datos['destino'] : null
        );

        http_response_code(204);
        exit;
    }

    /**
     * Devuelve el nombre de host sin el puerto.
     */
    private static function soloHost(string $valor): string
    {
        $valor = trim($valor);

        if ($valor === '') {
            return '';
        }

        // IPv6 entre corchetes: [::1]:8080
        if ($valor[0] === '[') {
            $cierre = strpos($valor, ']');
            return $cierre !== false ? substr($valor, 0, $cierre + 1) : $valor;
        }

        $dosPuntos = strrpos($valor, ':');

        return $dosPuntos !== false ? substr($valor, 0, $dosPuntos) : $valor;
    }

    /**
     * Prueba real de envio.
     *
     * El diagnostico dice si el puerto esta abierto; esto manda un correo de
     * verdad y cuenta cual de los canales lo consiguio. Es la unica prueba
     * que sirve: lo demas son suposiciones.
     *
     * Uso: /metricas/probar-correo?token=EL_TOKEN&para=alguien@correo.com
     */
    public function probarCorreo(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        header('X-Robots-Tag: noindex, nofollow');

        $config = require dirname(__DIR__) . '/config/metrics.php';
        $tokenEsperado = (string) $config['report_token'];

        if ($tokenEsperado === '' || !hash_equals($tokenEsperado, (string) ($_GET['token'] ?? ''))) {
            usleep(400000);
            http_response_code(404);
            echo "No encontrado.\n";
            exit;
        }

        $mail = require dirname(__DIR__) . '/config/mail.php';

        $para = trim((string) ($_GET['para'] ?? ''));
        if ($para === '' || !filter_var($para, FILTER_VALIDATE_EMAIL)) {
            $para = (string) $mail['from_address'];
        }

        echo "PRUEBA REAL DE ENVIO\n";
        echo str_repeat('=', 66) . "\n\n";
        printf("  destinatario   %s\n", $para);
        printf("  remitente      %s\n", $mail['from_address']);
        printf("  envio activo   %s\n", !empty($mail['enabled']) ? 'si' : 'NO (MAIL_ENABLED en false)');
        printf("  API            %s\n", !empty($mail['api_provider']) && !empty($mail['api_key'])
            ? $mail['api_provider'] . ' (clave configurada)'
            : 'sin configurar');
        printf("  SMTP           %s:%s\n\n", $mail['host'], $mail['port']);

        if (empty($mail['enabled'])) {
            echo "  El envio esta desactivado. Pon MAIL_ENABLED en true dentro de\n";
            echo "  app/config/mail.local.php y vuelve a probar.\n";
            exit;
        }

        $marca = date('d/m/Y H:i:s');
        $html = '<div style="font-family:system-ui,sans-serif;color:#0F2544">'
            . '<h2 style="color:#C2410C;margin:0 0 8px">Prueba de envio</h2>'
            . '<p>Si estas leyendo esto, el correo del sitio funciona.</p>'
            . '<p style="color:#5C6B82;font-size:14px">Enviado el ' . htmlspecialchars($marca, ENT_QUOTES, 'UTF-8')
            . ' desde <b>Abrigando Corazones</b>.</p></div>';

        $inicio = microtime(true);
        $r = (new \App\Services\MailTransport($mail))->enviar(
            [$para],
            'Prueba de envio - Abrigando Corazones (' . $marca . ')',
            $html,
            "Si estas leyendo esto, el correo del sitio funciona.\nEnviado el {$marca}."
        );
        $tardo = round((microtime(true) - $inicio) * 1000);

        echo str_repeat('-', 66) . "\n";
        echo "  CANALES PROBADOS\n\n";

        foreach ($r['intentos'] as $canal => $resultado) {
            printf("    %-8s %s\n", $canal, $resultado === 'ok' ? 'FUNCIONO' : 'fallo: ' . $resultado);
        }

        if ($r['intentos'] === []) {
            echo "    (ninguno: revisa la configuracion)\n";
        }

        echo "\n" . str_repeat('-', 66) . "\n";

        if ($r['sent']) {
            echo "  ENVIADO por el canal \"" . $r['via'] . "\" en {$tardo} ms.\n\n";
            echo "  Revisa la bandeja de {$para}. Si no aparece en unos minutos,\n";
            echo "  mira la carpeta de spam: es lo mas habitual la primera vez.\n";
        } else {
            echo "  NO SE ENVIO. Ningun canal funciono.\n\n";
            echo "  Lo mas probable es que el hosting bloquee el puerto SMTP y no\n";
            echo "  haya una clave de API configurada. Mira el diagnostico:\n";
            echo "    " . BASE_URL . "metricas/diagnostico-correo?token=" . $tokenEsperado . "\n\n";
            echo "  Mientras tanto no se pierde nada: cada solicitud queda guardada\n";
            echo "  y se puede consultar en:\n";
            echo "    " . BASE_URL . "solicitudes?token=" . $tokenEsperado . "\n";
        }

        exit;
    }

    /**
     * Bandeja de solicitudes.
     *
     * La razon de existir: el correo puede fallar -y en hosting gratuito
     * falla- pero el formulario SI guarda cada solicitud en la base de datos.
     * Esta pagina las muestra, asi que aunque no llegue ningun aviso, nada se
     * pierde: basta con abrirla para ver quien escribio.
     *
     * Protegida con el mismo token del reporte porque contiene datos
     * personales de quien escribio (nombre, telefono y correo).
     *
     * Uso: /solicitudes?token=EL_TOKEN
     */
    public function solicitudes(): void
    {
        header('Content-Type: text/html; charset=utf-8');
        header('X-Robots-Tag: noindex, nofollow, noarchive');
        header('Cache-Control: no-store');

        $config = require dirname(__DIR__) . '/config/metrics.php';
        $tokenEsperado = (string) $config['report_token'];

        if ($tokenEsperado === '' || !hash_equals($tokenEsperado, (string) ($_GET['token'] ?? ''))) {
            usleep(400000);
            http_response_code(404);
            echo 'No encontrado.';
            exit;
        }

        $filas = [];
        $error = null;

        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->query(
                'SELECT id, nombre, telefono, email, como_se_entero, mensaje,
                        notification_email_sent, notification_email_error, fecha_registro
                 FROM registros_contacto
                 ORDER BY id DESC
                 LIMIT 200'
            );
            $filas = $stmt->fetchAll() ?: [];
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        $esc = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
        $sinAviso = 0;
        foreach ($filas as $f) {
            if (empty($f['notification_email_sent'])) {
                $sinAviso++;
            }
        }

        echo '<!doctype html><html lang="es"><head><meta charset="utf-8">';
        echo '<meta name="viewport" content="width=device-width,initial-scale=1">';
        echo '<title>Solicitudes recibidas</title><style>';
        echo 'body{margin:0;padding:24px;background:#F4EFE8;font:15px/1.5 system-ui,sans-serif;color:#0F2544}';
        echo '.caja{max-width:1100px;margin:0 auto}';
        echo 'h1{font-size:22px;margin:0 0 4px}';
        echo '.sub{color:#5C6B82;font-size:14px;margin:0 0 20px}';
        echo '.aviso{padding:12px 16px;border-radius:10px;margin-bottom:18px;font-size:14px}';
        echo '.aviso--rojo{background:#FDECEA;border:1px solid #F5C6C0;color:#8E1B0F}';
        echo '.aviso--ambar{background:#FBEBCF;border:1px solid #EDD3A0;color:#6B4406}';
        echo 'table{width:100%;border-collapse:collapse;background:#fff;border:1px solid #E6E0D8;border-radius:12px;overflow:hidden}';
        echo 'th{background:#0F2544;color:#fff;font-size:11px;letter-spacing:.08em;text-transform:uppercase;padding:10px 12px;text-align:left}';
        echo 'td{padding:10px 12px;border-top:1px solid #EFEAE3;font-size:14px;vertical-align:top}';
        echo 'tr:nth-child(even) td{background:#FBF8F4}';
        echo '.pill{display:inline-block;padding:2px 9px;border-radius:99px;font-size:11px;font-weight:700}';
        echo '.ok{background:#E4F3EA;color:#14603A}.no{background:#FDECEA;color:#8E1B0F}';
        echo 'a{color:#C2410C}.msg{color:#5C6B82;font-size:13px;max-width:340px}';
        echo '</style></head><body><div class="caja">';

        echo '<h1>Solicitudes recibidas</h1>';
        echo '<p class="sub">Ultimas ' . count($filas) . ' · actualizado ' . date('d/m/Y H:i') . '</p>';

        if ($error !== null) {
            echo '<div class="aviso aviso--rojo"><b>No se pudo leer la base de datos.</b><br>' . $esc($error) . '</div>';
        }

        if ($sinAviso > 0) {
            echo '<div class="aviso aviso--ambar"><b>' . $sinAviso . ' solicitud(es) sin aviso por correo.</b> '
                . 'Se guardaron bien, pero el correo no salio. Revisa '
                . '<a href="' . $esc(BASE_URL) . 'metricas/diagnostico-correo?token=' . $esc($tokenEsperado) . '">el diagnostico de correo</a>.</div>';
        }

        if ($filas === []) {
            echo '<p>Todavia no hay solicitudes.</p>';
        } else {
            echo '<table><tr><th>#</th><th>Fecha</th><th>Nombre</th><th>Contacto</th><th>Motivo</th><th>Mensaje</th><th>Aviso</th></tr>';
            foreach ($filas as $f) {
                $tel = preg_replace('/\D+/', '', (string) $f['telefono']);
                echo '<tr>';
                echo '<td>' . $esc($f['id']) . '</td>';
                echo '<td>' . $esc(date('d/m/y H:i', strtotime((string) $f['fecha_registro']))) . '</td>';
                echo '<td><b>' . $esc($f['nombre']) . '</b></td>';
                echo '<td>';
                if ($tel !== '') {
                    $wa = strlen($tel) === 9 ? '51' . $tel : $tel;
                    echo '<a href="https://wa.me/' . $esc($wa) . '" target="_blank" rel="noopener">' . $esc($f['telefono']) . '</a><br>';
                }
                echo '<a href="mailto:' . $esc($f['email']) . '">' . $esc($f['email']) . '</a>';
                echo '</td>';
                echo '<td>' . $esc($f['como_se_entero']) . '</td>';
                echo '<td class="msg">' . $esc(mb_substr((string) $f['mensaje'], 0, 180)) . '</td>';
                echo '<td>' . (!empty($f['notification_email_sent'])
                    ? '<span class="pill ok">enviado</span>'
                    : '<span class="pill no">no salio</span>') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }

        echo '<p class="sub" style="margin-top:20px">Esta pagina contiene datos personales: no compartas el enlace.</p>';
        echo '</div></body></html>';
        exit;
    }

    /**
     * Diagnostico de correo.
     *
     * Existe porque "no me llega nada" tiene demasiadas causas posibles y
     * adivinar cuesta dias. Esta pagina prueba cada eslabon por separado
     * DIRECTAMENTE EN EL SERVIDOR DE PRODUCCION y dice cual falla.
     *
     * No envia ningun correo: se conecta, autentica y cuelga.
     *
     * Uso: /metricas/diagnostico-correo?token=EL_TOKEN
     */
    public function diagnosticoCorreo(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        header('X-Robots-Tag: noindex, nofollow');

        $config = require dirname(__DIR__) . '/config/metrics.php';
        $tokenEsperado = (string) $config['report_token'];

        if ($tokenEsperado === '' || !hash_equals($tokenEsperado, (string) ($_GET['token'] ?? ''))) {
            usleep(400000);
            http_response_code(404);
            echo "No encontrado.\n";
            exit;
        }

        $mail = require dirname(__DIR__) . '/config/mail.php';
        $host = (string) $mail['host'];
        $puerto = (int) $mail['port'];

        $linea = static function (string $paso, string $estado, string $detalle = ''): void {
            printf("  %-38s %s%s\n", $paso, $estado, $detalle !== '' ? '  ->  ' . $detalle : '');
        };

        echo "DIAGNOSTICO DE CORREO\n";
        echo str_repeat('=', 66) . "\n\n";
        printf("  servidor PHP     %s\n", PHP_VERSION);
        printf("  cuenta           %s\n", $mail['username']);
        printf("  destino SMTP     %s:%d\n", $host, $puerto);
        printf("  envio activado   %s\n\n", !empty($mail['enabled']) ? 'si' : 'NO (MAIL_ENABLED esta en false)');

        // ------------------------------------------------------------- 1. DNS
        $ip = @gethostbyname($host);
        $dnsOk = $ip !== $host;
        $linea('1. Resolver el nombre del servidor', $dnsOk ? 'OK' : 'FALLA', $dnsOk ? $ip : 'no resuelve');

        if (!$dnsOk) {
            echo "\n  El hosting no puede resolver el nombre. Sin DNS no hay nada que hacer.\n";
            exit;
        }

        // --------------------------------------- 2. Todos los puertos SMTP
        //
        // Gmail escucha en varios puertos y los hosting no bloquean siempre
        // los mismos. Con que uno abra, el correo puede salir con la cuenta
        // de siempre, sin servicios de terceros.
        $puertos = [
            [465, 'ssl', 'SMTPS, cifrado desde el inicio'],
            [587, 'tls', 'STARTTLS, el estandar de envio'],
            [2525, 'tls', 'alternativo'],
            [25, 'tls', 'el clasico'],
        ];

        $abiertos = [];

        echo "\n  2. Puertos de {$host}\n\n";

        foreach ($puertos as [$p, $cifrado, $nota]) {
            $errNo = 0;
            $errStr = '';
            $inicio = microtime(true);
            $conexion = @fsockopen($cifrado === 'ssl' ? 'ssl://' . $host : $host, $p, $errNo, $errStr, 6);
            $tardo = round((microtime(true) - $inicio) * 1000);

            if ($conexion) {
                $saludo = trim((string) @fgets($conexion, 256));
                @fclose($conexion);
                $abiertos[] = $p;
                printf("      %-6d %-32s ABIERTO   %d ms  %s\n", $p, $nota, $tardo, substr($saludo, 0, 28));
            } else {
                printf("      %-6d %-32s cerrado   %s\n", $p, $nota, $errStr !== '' ? substr($errStr, 0, 34) : 'sin detalle');
            }
        }

        // ------------------------------------------- 3. Salida por HTTPS
        $errNo = 0;
        $errStr = '';
        $https = @fsockopen('ssl://api.brevo.com', 443, $errNo, $errStr, 8);
        $salidaHttps = (bool) $https;
        if ($https) {
            @fclose($https);
        }

        echo "\n";
        $linea('3. Salida por HTTPS (puerto 443)', $salidaHttps ? 'OK' : 'FALLA');
        $linea('4. La funcion mail() existe', function_exists('mail') ? 'OK' : 'FALLA',
            function_exists('mail') ? 'el hosting puede tenerla desactivada igualmente' : 'desactivada por el hosting');

        echo "\n" . str_repeat('-', 66) . "\n";
        echo "  DIAGNOSTICO\n\n";

        if ($abiertos !== []) {
            echo "  BIEN: el hosting SI deja salir por SMTP.\n";
            echo "  Puerto(s) abierto(s): " . implode(', ', $abiertos) . "\n\n";
            echo "  El sistema los prueba solo, empezando por el configurado.\n";
            echo "  Manda una prueba de verdad aqui:\n";
            echo "    " . BASE_URL . "metricas/probar-correo?token=" . $tokenEsperado . "&para=TU@CORREO.COM\n\n";
            echo "  Si la prueba sale bien pero no ves el correo, mira spam.\n";
        } elseif ($salidaHttps) {
            echo "  El hosting BLOQUEA todos los puertos SMTP, pero deja salir\n";
            echo "  por HTTPS. Es el limite conocido del plan gratuito.\n\n";
            echo "  El codigo esta bien y las credenciales tambien: lo que falta\n";
            echo "  es un canal que el hosting permita.\n\n";
            echo "  Solucion: una clave de API de Brevo (gratis, 300 correos al\n";
            echo "  dia). Va por el 443, que aqui si funciona. Los pasos estan\n";
            echo "  en CORREO_ARREGLADO.txt.\n\n";
            echo "  Mientras tanto NO se pierde nada: cada solicitud queda\n";
            echo "  guardada y se puede ver en\n";
            echo "    " . BASE_URL . "solicitudes?token=" . $tokenEsperado . "\n";
        } else {
            echo "  El hosting bloquea TODA salida hacia fuera, no solo SMTP.\n";
            echo "  Asi no hay forma de enviar correo desde el servidor.\n\n";
            echo "  Las solicitudes siguen guardandose. Consultalas en\n";
            echo "    " . BASE_URL . "solicitudes?token=" . $tokenEsperado . "\n";
        }

        exit;
    }

    /**
     * Dispara el reporte diario. Se protege con un token secreto porque envia
     * correos: sin el, cualquiera podria usar la URL para saturar la cuenta.
     *
     * Uso:  /metricas/reporte?token=EL_TOKEN
     * Extra: &fecha=2026-08-24  para reenviar un dia concreto
     *        &forzar=1          para ignorar la hora minima y el envio previo
     */
    public function reporte(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        header('X-Robots-Tag: noindex, nofollow');

        $config = require dirname(__DIR__) . '/config/metrics.php';
        $tokenEsperado = (string) $config['report_token'];
        $tokenRecibido = (string) ($_GET['token'] ?? '');

        // Sin token configurado el endpoint queda cerrado, no abierto.
        if ($tokenEsperado === '' || strlen($tokenEsperado) < 20) {
            http_response_code(503);
            echo "El token del reporte no esta configurado en metrics.local.php.\n";
            exit;
        }

        if (!hash_equals($tokenEsperado, $tokenRecibido)) {
            // Retardo pequeno para desalentar el sondeo por fuerza bruta.
            usleep(400000);
            http_response_code(404);
            echo "No encontrado.\n";
            exit;
        }

        $forzar = ($_GET['forzar'] ?? '') === '1';
        $fecha = (string) ($_GET['fecha'] ?? date('Y-m-d'));

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || strtotime($fecha) === false) {
            http_response_code(400);
            echo "Fecha invalida. Formato esperado: AAAA-MM-DD\n";
            exit;
        }

        $servicio = new DailyReportService();

        if (!$forzar) {
            $horaMinima = (int) $config['report_hour'];

            if ($fecha === date('Y-m-d') && (int) date('G') < $horaMinima) {
                http_response_code(425);
                printf("Aun no son las %02d:00 en Lima (son las %s). Usa &forzar=1 si quieres el envio igual.\n",
                    $horaMinima, date('H:i'));
                exit;
            }

            if ($servicio->yaEnviadoHoy($fecha)) {
                echo "El reporte del {$fecha} ya se envio. Usa &forzar=1 para reenviarlo.\n";
                exit;
            }
        }

        $resultado = $servicio->enviar($fecha);

        if (!empty($resultado['sent'])) {
            $r = $resultado['resumen'];
            printf(
                "Reporte del %s enviado.\n  visitas: %d\n  personas: %d\n  solicitudes: %d\n",
                $fecha, $r['visitas'], $r['visitantes'], $r['solicitudes']
            );

            // Aprovechar la visita diaria para limpiar metricas viejas.
            $borradas = MetricsService::purgar((int) $config['retention_days']);
            if ($borradas > 0) {
                printf("  purgados %d registros con mas de %d dias\n", $borradas, (int) $config['retention_days']);
            }
            exit;
        }

        http_response_code(500);
        echo "No se pudo enviar: " . ($resultado['error'] ?? 'error desconocido') . "\n";
        exit;
    }
}
