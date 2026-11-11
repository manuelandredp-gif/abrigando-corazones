<?php

namespace App\Services;

use App\Core\Database;

/**
 * Reporte diario de como va la pagina.
 *
 * Arma un correo con las visitas del dia, que paginas se vieron mas, donde
 * hizo clic la gente, con que dispositivo entro y de donde llego, y lo envia
 * por el mismo SMTP que ya usa el formulario de contacto.
 *
 * Se dispara con una peticion a /metricas/reporte?token=... porque InfinityFree
 * no ofrece cron en el plan gratuito. Ver DEPLOY_INFINITYFREE.md para las dos
 * formas de programarlo a las 15:00.
 */
class DailyReportService
{
    private array $mail;
    private array $config;

    public function __construct()
    {
        $this->mail = require dirname(__DIR__) . '/config/mail.php';
        $this->config = require dirname(__DIR__) . '/config/metrics.php';
    }

    /**
     * Genera y envia el reporte del dia indicado (por defecto, hoy).
     */
    public function enviar(?string $fecha = null): array
    {
        $fecha = $fecha ?: date('Y-m-d');

        try {
            $resumen = MetricsService::resumenDelDia($fecha);
        } catch (\Throwable $e) {
            return ['sent' => false, 'error' => 'metricas_no_disponibles: ' . $e->getMessage()];
        }

        $destinatarios = array_values(array_filter(array_map('trim', $this->config['report_recipients'])));

        if ($destinatarios === []) {
            return ['sent' => false, 'error' => 'sin_destinatarios'];
        }

        if (empty($this->mail['enabled'])) {
            return ['sent' => false, 'error' => 'mail_disabled'];
        }

        // Mismo transporte que los avisos del formulario: prueba API por
        // HTTPS, SMTP y mail(). En hosting gratuito el puerto 587 esta
        // cerrado, asi que sin esto el reporte no saldria nunca.
        $asunto = sprintf(
            '\u{1F4CA} Reporte diario \u{B7} %s visitas \u{B7} %s',
            number_format($resumen['visitas'], 0, ',', ' '),
            $this->fechaLegible($fecha)
        );

        $resultado = (new MailTransport($this->mail))->enviar(
            $destinatarios,
            $asunto,
            $this->html($resumen),
            $this->texto($resumen)
        );

        $detalle = $resultado['sent']
            ? 'via=' . $resultado['via']
            : $resultado['error'] . ' intentos=' . json_encode($resultado['intentos'], JSON_UNESCAPED_UNICODE);

        $this->registrarEnvio($fecha, implode(', ', $destinatarios), $resultado['sent'], $detalle);

        return [
            'sent' => $resultado['sent'],
            'error' => $resultado['sent'] ? null : $detalle,
            'via' => $resultado['via'],
            'resumen' => $resumen,
        ];
    }

    // ------------------------------------------------------------ redaccion

    private function html(array $r): string
    {
        $e = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
        $n = static fn($v): string => number_format((int) $v, 0, ',', ' ');

        $filas = static function (array $items, callable $cel) use ($e): string {
            if ($items === []) {
                return '<tr><td colspan="3" style="padding:10px 12px;color:#6C7B91;font-style:italic">Sin datos este día.</td></tr>';
            }
            $out = '';
            foreach ($items as $i => $item) {
                $fondo = $i % 2 === 0 ? '#ffffff' : '#FBF8F4';
                $out .= '<tr style="background:' . $fondo . '">' . $cel($item) . '</tr>';
            }
            return $out;
        };

        $tarjeta = static function (string $valor, string $etiqueta): string {
            return '<td style="padding:14px 10px;text-align:center;border:1px solid #E6E0D8;border-radius:10px">'
                . '<div style="font:800 26px/1 Arial,sans-serif;color:#0F2544">' . $valor . '</div>'
                . '<div style="font:400 11px/1.4 Arial,sans-serif;color:#6C7B91;text-transform:uppercase;letter-spacing:.08em;padding-top:6px">' . $etiqueta . '</div>'
                . '</td>';
        };

        $paginas = $filas($r['paginas'], static fn(array $p): string =>
            '<td style="padding:9px 12px;font:400 13px Arial,sans-serif;color:#0F2544">' . $e($p['ruta']) . '</td>'
            . '<td style="padding:9px 12px;font:700 13px Arial,sans-serif;color:#0F2544;text-align:right">' . $n($p['visitas']) . '</td>'
            . '<td style="padding:9px 12px;font:400 13px Arial,sans-serif;color:#6C7B91;text-align:right">' . $n($p['personas']) . '</td>');

        $clicks = $filas($r['clicks'], static fn(array $c): string =>
            '<td style="padding:9px 12px;font:400 13px Arial,sans-serif;color:#0F2544">' . $e($c['etiqueta']) . '</td>'
            . '<td style="padding:9px 12px;font:400 12px Arial,sans-serif;color:#6C7B91">' . $e($c['ruta']) . '</td>'
            . '<td style="padding:9px 12px;font:700 13px Arial,sans-serif;color:#C2410C;text-align:right">' . $n($c['veces']) . '</td>');

        $dispositivos = $filas($r['dispositivos'], static fn(array $d): string =>
            '<td style="padding:9px 12px;font:400 13px Arial,sans-serif;color:#0F2544" colspan="2">' . $e(ucfirst($d['dispositivo'])) . '</td>'
            . '<td style="padding:9px 12px;font:700 13px Arial,sans-serif;color:#0F2544;text-align:right">' . $n($d['visitas']) . '</td>');

        $referentes = $filas($r['referentes'], static fn(array $f): string =>
            '<td style="padding:9px 12px;font:400 13px Arial,sans-serif;color:#0F2544" colspan="2">' . $e($f['referente']) . '</td>'
            . '<td style="padding:9px 12px;font:700 13px Arial,sans-serif;color:#0F2544;text-align:right">' . $n($f['visitas']) . '</td>');

        $th = 'padding:8px 12px;font:700 10px Arial,sans-serif;color:#6C7B91;text-transform:uppercase;letter-spacing:.1em;text-align:left;border-bottom:2px solid #E6E0D8';
        $h2 = 'font:800 15px Arial,sans-serif;color:#0F2544;margin:26px 0 8px';

        return '<!doctype html><html lang="es"><body style="margin:0;padding:24px;background:#F4EFE8">'
            . '<div style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #E6E0D8;border-radius:14px;overflow:hidden">'

            . '<div style="background:#0F2544;padding:22px 24px">'
            . '<div style="font:700 10px Arial,sans-serif;color:#F4B183;letter-spacing:.16em;text-transform:uppercase">Abrigando Corazones</div>'
            . '<div style="font:800 21px Arial,sans-serif;color:#fff;padding-top:4px">Reporte del ' . $e($this->fechaLegible($r['fecha'])) . '</div>'
            . '</div>'

            . '<div style="padding:22px 24px">'

            . '<table role="presentation" width="100%" cellspacing="8" cellpadding="0"><tr>'
            . $tarjeta($n($r['visitas']), 'Visitas')
            . $tarjeta($n($r['visitantes']), 'Personas')
            . $tarjeta($n($r['solicitudes']), 'Solicitudes')
            . '</tr></table>'

            . '<h2 style="' . $h2 . '">Páginas más vistas</h2>'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0">'
            . '<tr><th style="' . $th . '">Página</th><th style="' . $th . ';text-align:right">Visitas</th><th style="' . $th . ';text-align:right">Personas</th></tr>'
            . $paginas . '</table>'

            . '<h2 style="' . $h2 . '">Dónde hicieron clic</h2>'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0">'
            . '<tr><th style="' . $th . '">Elemento</th><th style="' . $th . '">En la página</th><th style="' . $th . ';text-align:right">Clics</th></tr>'
            . $clicks . '</table>'

            . '<h2 style="' . $h2 . '">Con qué entraron</h2>'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0">' . $dispositivos . '</table>'

            . '<h2 style="' . $h2 . '">De dónde llegaron</h2>'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0">' . $referentes . '</table>'

            . '<p style="font:400 12px/1.6 Arial,sans-serif;color:#6C7B91;margin:26px 0 0;padding-top:16px;border-top:1px solid #E6E0D8">'
            . 'Medición propia del sitio. No se guarda ninguna dirección IP ni datos personales de los visitantes: '
            . 'solo un identificador anónimo que cambia cada día y sirve únicamente para no contar dos veces a la misma persona.'
            . '</p>'

            . '</div></div></body></html>';
    }

    private function texto(array $r): string
    {
        $l = ['REPORTE DEL ' . strtoupper($this->fechaLegible($r['fecha'])), ''];
        $l[] = 'Visitas: ' . $r['visitas'];
        $l[] = 'Personas distintas: ' . $r['visitantes'];
        $l[] = 'Solicitudes recibidas: ' . $r['solicitudes'];
        $l[] = '';
        $l[] = 'PAGINAS MAS VISTAS';
        foreach ($r['paginas'] as $p) {
            $l[] = sprintf('  %-42s %5d visitas (%d personas)', $p['ruta'], $p['visitas'], $p['personas']);
        }
        $l[] = '';
        $l[] = 'DONDE HICIERON CLIC';
        foreach ($r['clicks'] as $c) {
            $l[] = sprintf('  %-42s %5d clics  [%s]', $c['etiqueta'], $c['veces'], $c['ruta']);
        }
        $l[] = '';
        $l[] = 'DISPOSITIVOS';
        foreach ($r['dispositivos'] as $d) {
            $l[] = sprintf('  %-16s %5d', $d['dispositivo'], $d['visitas']);
        }

        if ($r['referentes'] !== []) {
            $l[] = '';
            $l[] = 'DE DONDE LLEGARON';
            foreach ($r['referentes'] as $f) {
                $l[] = sprintf('  %-32s %5d', $f['referente'], $f['visitas']);
            }
        }

        return implode("\n", $l);
    }

    private function fechaLegible(string $fecha): string
    {
        $ts = strtotime($fecha);
        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $meses = [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
            'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

        return $dias[(int) date('w', $ts)] . ' ' . date('j', $ts) . ' de ' . $meses[(int) date('n', $ts)];
    }

    private function registrarEnvio(string $fecha, string $destinatarios, bool $exito, ?string $error): void
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare(
                'INSERT INTO metricas_envios (fecha, enviado_en, destinatarios, exito, error)
                 VALUES (?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE enviado_en = VALUES(enviado_en), exito = VALUES(exito), error = VALUES(error)'
            );
            $stmt->execute([
                $fecha,
                date('Y-m-d H:i:s'),
                mb_substr($destinatarios, 0, 255),
                $exito ? 1 : 0,
                $error !== null ? mb_substr($error, 0, 255) : null,
            ]);
        } catch (\Throwable $e) {
            // El registro del envio es informativo: si falla, no importa.
        }
    }

    /**
     * Disparo automatico sin cron.
     *
     * InfinityFree no ofrece cron en el plan gratuito. En lugar de depender de
     * un servicio externo, el propio sitio comprueba en cada visita si ya paso
     * la hora del reporte y todavia no se envio el de hoy. La primera visita
     * despues de las 15:00 lo dispara.
     *
     * Precauciones para que esto no cueste nada ni moleste a nadie:
     *   - Un archivo marcador por dia evita cualquier consulta a la base en el
     *     resto de visitas. El caso normal es un is_file() y nada mas.
     *   - El marcador se crea con fopen(..., 'x'), que es atomico: si dos
     *     visitas coinciden en el mismo segundo, solo una gana.
     *   - El envio ocurre despues de responder al visitante, para que nadie
     *     espere por el SMTP.
     *   - La clave unica de metricas_envios es la ultima barrera contra
     *     duplicados.
     *
     * Si se configura un cron de verdad, basta con poner report_auto en false.
     */
    public static function dispararSiCorresponde(array $config): void
    {
        try {
            if (empty($config['report_auto'])) {
                return;
            }

            if ((int) date('G') < (int) $config['report_hour']) {
                return;
            }

            $hoy = date('Y-m-d');
            $marcador = dirname(__DIR__, 2) . '/logs/.reporte-' . $hoy;

            // Camino rapido: ya se intento hoy, no se toca la base.
            if (is_file($marcador)) {
                return;
            }

            /*
             * Reintentos acotados.
             *
             * El marcador guarda cuantas veces se intento. Si el SMTP falla por
             * un corte pasajero, el marcador se borra y una visita posterior lo
             * vuelve a intentar; sin esto el reporte del dia se perdia por un
             * fallo de red de un segundo.
             *
             * Se corta a los 3 intentos para que una configuracion de correo
             * rota no haga que cada visita abra una conexion SMTP.
             */
            $intentos = 0;
            $parcial = $marcador . '.intentos';

            if (is_file($parcial)) {
                $intentos = (int) @file_get_contents($parcial);

                if ($intentos >= 3) {
                    @file_put_contents($marcador, 'agotados los reintentos ' . date('c'));
                    return;
                }
            }

            // Creacion atomica: si otra peticion llego primero, aqui falla.
            $handle = @fopen($marcador, 'x');
            if ($handle === false) {
                return;
            }
            fwrite($handle, date('c'));
            fclose($handle);

            @file_put_contents($parcial, (string) ($intentos + 1));

            self::limpiarMarcadoresViejos();

            // Enviar cuando el visitante ya tenga su pagina.
            register_shutdown_function(static function () use ($hoy): void {
                try {
                    if (function_exists('fastcgi_finish_request')) {
                        fastcgi_finish_request();
                    }

                    ignore_user_abort(true);

                    $servicio = new self();

                    if ($servicio->yaEnviadoHoy($hoy)) {
                        return;
                    }

                    $resultado = $servicio->enviar($hoy);

                    if (empty($resultado['sent'])) {
                        // Falló: se libera el marcador para que otra visita lo
                        // reintente. El contador de intentos se conserva.
                        @unlink(dirname(__DIR__, 2) . '/logs/.reporte-' . $hoy);
                    }
                } catch (\Throwable $e) {
                    // El reporte es informativo: nunca puede afectar al sitio.
                }
            });
        } catch (\Throwable $e) {
            // idem
        }
    }

    /**
     * Los marcadores diarios son archivos vacios; se borran los de mas de una
     * semana para no dejar basura en logs/.
     */
    private static function limpiarMarcadoresViejos(): void
    {
        try {
            $dir = dirname(__DIR__, 2) . '/logs';
            $limite = time() - (7 * 86400);

            foreach (glob($dir . '/.reporte-*') ?: [] as $archivo) {
                if (@filemtime($archivo) < $limite) {
                    @unlink($archivo);
                }
            }
        } catch (\Throwable $e) {
            // sin consecuencias
        }
    }

    /**
     * Evita que un cron mal configurado mande el mismo reporte diez veces.
     */
    public function yaEnviadoHoy(string $fecha): bool
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare('SELECT exito FROM metricas_envios WHERE fecha = ? LIMIT 1');
            $stmt->execute([$fecha]);
            $fila = $stmt->fetch();

            return $fila !== false && (int) $fila['exito'] === 1;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
