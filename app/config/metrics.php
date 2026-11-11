<?php
/**
 * Configuracion de las metricas y del reporte diario.
 *
 * Prioridad: variables de entorno > metrics.local.php > valores por defecto.
 * El token real NO vive en este archivo: vive en metrics.local.php, que no se
 * versiona y que el .htaccess bloquea por el patron *.local.php.
 */

$localMetricsConfig = __DIR__ . '/metrics.local.php';
$metricsLocal = is_file($localMetricsConfig) ? require $localMetricsConfig : [];

$metricsValue = static function (string $key, $default = null) use ($metricsLocal) {
    $value = getenv($key);

    if ($value !== false && $value !== '') {
        return $value;
    }

    return $metricsLocal[$key] ?? $default;
};

return [
    // Registrar visitas del lado del servidor.
    'enabled' => filter_var($metricsValue('METRICS_ENABLED', true), FILTER_VALIDATE_BOOLEAN),

    // Secreto que protege /metricas/reporte. Sin el, cualquiera podria
    // disparar correos desde el sitio.
    'report_token' => (string) $metricsValue('METRICS_REPORT_TOKEN', ''),

    // A quien llega el reporte diario.
    'report_recipients' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) $metricsValue('METRICS_REPORT_RECIPIENTS', 'somos.fuerzaupt@gmail.com'))
    ))),

    // Hora local (America/Lima) a partir de la cual se acepta el disparo del
    // reporte. Evita que un cron adelantado mande el resumen a media manana.
    'report_hour' => (int) $metricsValue('METRICS_REPORT_HOUR', 15),

    /*
     * Disparo automatico del reporte, sin depender de un cron externo.
     *
     * Con esto activado, la primera visita que reciba el sitio despues de la
     * hora indicada envia el reporte del dia. Es la opcion practica en
     * InfinityFree gratuito, que no ofrece cron.
     *
     * Poner en false si se configura un cron de verdad.
     */
    'report_auto' => filter_var($metricsValue('METRICS_REPORT_AUTO', true), FILTER_VALIDATE_BOOLEAN),

    // Dias de detalle que se conservan antes de purgar.
    'retention_days' => (int) $metricsValue('METRICS_RETENTION_DAYS', 120),
];
