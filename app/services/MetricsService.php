<?php

namespace App\Services;

use App\Core\Database;
use PDO;

/**
 * Metricas propias del sitio.
 *
 * Se registran del lado del servidor, no con un script de terceros. Ventajas
 * para este caso concreto:
 *   - No depende de que el visitante acepte cookies ni de que no tenga un
 *     bloqueador, que es lo habitual en publico movil.
 *   - No se envia ni un dato a un tercero.
 *   - Funciona en InfinityFree sin instalar nada.
 *
 * Privacidad: no se almacena la IP ni el user agent. De la IP se deriva un
 * hash truncado con sal que CAMBIA CADA DIA, suficiente para contar visitantes
 * distintos dentro de la jornada e inutil para seguir a alguien entre dias o
 * para reconstruir la direccion original.
 *
 * Regla de oro: ninguna falla de metricas puede tumbar una pagina. Todo va
 * dentro de try/catch y, ante la duda, se descarta el registro.
 */
class MetricsService
{
    /** Rutas que no interesa medir. */
    private const IGNORAR = ['metricas/evento', 'metricas/reporte'];

    public static function registrarVisita(string $ruta, ?string $titulo = null): void
    {
        try {
            if (self::esBot() || self::rutaIgnorada($ruta)) {
                return;
            }

            $valores = [
                self::limitar($ruta === '' ? '/' : $ruta, 190),
                $titulo !== null ? self::limitar($titulo, 190) : null,
                self::referenteExterno(),
                self::dispositivo(),
                self::visitanteHash(),
                date('Y-m-d H:i:s'),
            ];

            $sql = 'INSERT INTO metricas_visitas (ruta, titulo, referente, dispositivo, visitante_hash, creado_en)
                    VALUES (?, ?, ?, ?, ?, ?)';

            $db = Database::getInstance()->getConnection();

            try {
                $db->prepare($sql)->execute($valores);
            } catch (\Throwable $fallo) {
                /*
                 * Instalacion sin intervencion manual: si las tablas de
                 * metricas aun no existen, se crean aqui y se reintenta una
                 * sola vez. Asi no hace falta entrar a phpMyAdmin a importar
                 * nada; basta con subir los archivos.
                 */
                if (!\App\Core\Migrator::esTablaInexistente($fallo)) {
                    throw $fallo;
                }

                if (!\App\Core\Migrator::asegurarTablasDeMetricas(true)) {
                    return;
                }

                $db->prepare($sql)->execute($valores);
            }
        } catch (\Throwable $e) {
            // Silencio deliberado: medir nunca debe romper la pagina.
        }
    }

    public static function registrarEvento(string $ruta, string $tipo, string $etiqueta, ?string $destino = null): bool
    {
        try {
            if (self::esBot()) {
                return false;
            }

            $tiposValidos = ['click', 'envio_formulario', 'salida'];
            if (!in_array($tipo, $tiposValidos, true)) {
                return false;
            }

            $etiqueta = trim($etiqueta);
            if ($etiqueta === '') {
                return false;
            }

            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare(
                'INSERT INTO metricas_eventos (ruta, tipo, etiqueta, destino, visitante_hash, creado_en)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );

            return $stmt->execute([
                self::limitar($ruta === '' ? '/' : $ruta, 190),
                $tipo,
                self::limitar($etiqueta, 190),
                $destino !== null && $destino !== '' ? self::limitar($destino, 190) : null,
                self::visitanteHash(),
                date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Resumen de un dia concreto, listo para armar el correo.
     */
    public static function resumenDelDia(string $fecha): array
    {
        $db = Database::getInstance()->getConnection();

        $desde = $fecha . ' 00:00:00';
        $hasta = $fecha . ' 23:59:59';

        $uno = static function (PDO $db, string $sql, array $args) {
            $stmt = $db->prepare($sql);
            $stmt->execute($args);
            $fila = $stmt->fetch();
            return $fila ? array_values($fila)[0] : 0;
        };

        $lista = static function (PDO $db, string $sql, array $args): array {
            $stmt = $db->prepare($sql);
            $stmt->execute($args);
            return $stmt->fetchAll() ?: [];
        };

        return [
            'fecha' => $fecha,
            'visitas' => (int) $uno($db, 'SELECT COUNT(*) FROM metricas_visitas WHERE creado_en BETWEEN ? AND ?', [$desde, $hasta]),
            'visitantes' => (int) $uno($db, 'SELECT COUNT(DISTINCT visitante_hash) FROM metricas_visitas WHERE creado_en BETWEEN ? AND ?', [$desde, $hasta]),

            'paginas' => $lista($db,
                'SELECT ruta, COUNT(*) AS visitas, COUNT(DISTINCT visitante_hash) AS personas
                 FROM metricas_visitas WHERE creado_en BETWEEN ? AND ?
                 GROUP BY ruta ORDER BY visitas DESC LIMIT 12', [$desde, $hasta]),

            'clicks' => $lista($db,
                'SELECT etiqueta, ruta, COUNT(*) AS veces
                 FROM metricas_eventos WHERE tipo = ? AND creado_en BETWEEN ? AND ?
                 GROUP BY etiqueta, ruta ORDER BY veces DESC LIMIT 12', ['click', $desde, $hasta]),

            'dispositivos' => $lista($db,
                'SELECT dispositivo, COUNT(*) AS visitas
                 FROM metricas_visitas WHERE creado_en BETWEEN ? AND ?
                 GROUP BY dispositivo ORDER BY visitas DESC', [$desde, $hasta]),

            'referentes' => $lista($db,
                'SELECT referente, COUNT(*) AS visitas
                 FROM metricas_visitas WHERE referente IS NOT NULL AND creado_en BETWEEN ? AND ?
                 GROUP BY referente ORDER BY visitas DESC LIMIT 8', [$desde, $hasta]),

            'formularios' => (int) $uno($db,
                'SELECT COUNT(*) FROM metricas_eventos WHERE tipo = ? AND creado_en BETWEEN ? AND ?',
                ['envio_formulario', $desde, $hasta]),

            'solicitudes' => (int) $uno($db,
                'SELECT COUNT(*) FROM registros_contacto WHERE fecha_registro BETWEEN ? AND ?',
                [$desde, $hasta]),
        ];
    }

    /**
     * Borra metricas antiguas. En hosting gratuito el espacio es limitado y
     * nadie necesita el detalle de hace medio ano.
     */
    public static function purgar(int $diasAConservar = 120): int
    {
        try {
            $limite = date('Y-m-d H:i:s', strtotime('-' . $diasAConservar . ' days'));
            $db = Database::getInstance()->getConnection();

            $total = 0;
            foreach (['metricas_visitas', 'metricas_eventos'] as $tabla) {
                $stmt = $db->prepare('DELETE FROM ' . $tabla . ' WHERE creado_en < ?');
                $stmt->execute([$limite]);
                $total += $stmt->rowCount();
            }

            return $total;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    // ------------------------------------------------------------------ util

    private static function rutaIgnorada(string $ruta): bool
    {
        $ruta = trim($ruta, '/');

        foreach (self::IGNORAR as $ignorada) {
            if ($ruta === $ignorada) {
                return true;
            }
        }

        // Peticiones de archivos, no de paginas. El navegador pide favicon.ico,
        // robots.txt y similares por su cuenta; contarlas como visitas ensucia
        // el reporte (en la primera prueba, favicon.ico era la ruta mas vista).
        if (preg_match('/\.(ico|png|jpe?g|webp|svg|gif|css|js|map|txt|xml|woff2?|ttf|pdf)$/i', $ruta)) {
            return true;
        }

        return false;
    }

    /**
     * Hash del visitante: IP + user agent + sal del dia. Truncado a 16 hex.
     * Al cambiar la sal cada dia, el mismo visitante produce un hash distinto
     * manana: sirve para contar, no para perseguir.
     */
    private static function visitanteHash(): string
    {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP']
            ?? $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR']
            ?? 'sin-ip';

        if (is_string($ip) && str_contains($ip, ',')) {
            $ip = trim(explode(',', $ip)[0]);
        }

        $material = $ip . '|' . ($_SERVER['HTTP_USER_AGENT'] ?? '') . '|' . date('Y-m-d') . '|abrigando';

        return substr(hash('sha256', $material), 0, 16);
    }

    private static function dispositivo(): string
    {
        $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');

        if ($ua === '') {
            return 'desconocido';
        }

        if (preg_match('/ipad|tablet|playbook|silk/i', $ua)) {
            return 'tablet';
        }

        if (preg_match('/mobile|iphone|ipod|android.*mobile|windows phone/i', $ua)) {
            return 'movil';
        }

        return 'escritorio';
    }

    /**
     * Solo se guarda el dominio del referente y solo si es externo: de donde
     * llega la gente es util; por que pagina interna paso ya lo dicen las
     * visitas.
     */
    private static function referenteExterno(): ?string
    {
        $ref = $_SERVER['HTTP_REFERER'] ?? '';

        if ($ref === '') {
            return null;
        }

        $host = parse_url($ref, PHP_URL_HOST);

        if (!$host) {
            return null;
        }

        $propio = $_SERVER['HTTP_HOST'] ?? '';
        if ($propio !== '' && strcasecmp($host, $propio) === 0) {
            return null;
        }

        return self::limitar($host, 190);
    }

    private static function esBot(): bool
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

        if ($ua === '') {
            return true;
        }

        return (bool) preg_match(
            '/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|whatsapp|telegram|preview|monitor|uptime|curl|wget|python-requests|headless|lighthouse|pingdom|semrush|ahrefs/i',
            $ua
        );
    }

    private static function limitar(string $valor, int $max): string
    {
        return mb_substr($valor, 0, $max, 'UTF-8');
    }
}
