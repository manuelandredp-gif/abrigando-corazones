<?php
/**
 * Secretos de metricas. NO se versiona y el .htaccess bloquea *.local.php.
 *
 * METRICS_REPORT_TOKEN protege la URL que dispara el correo diario:
 *   https://TU-DOMINIO/metricas/reporte?token=EL_TOKEN
 *
 * Si el token se filtra, basta con cambiarlo aqui y en el cron.
 */

return [
    'METRICS_ENABLED' => true,
    'METRICS_REPORT_TOKEN' => 'REEMPLAZAR_POR_UN_TOKEN_LARGO_Y_ALEATORIO',
    'METRICS_REPORT_RECIPIENTS' => 'somos.fuerzaupt@gmail.com',
    'METRICS_REPORT_HOUR' => 15,
    // true = el propio sitio lo envia tras la primera visita pasada esa hora.
    // false = solo se envia si un cron llama a /metricas/reporte?token=...
    'METRICS_REPORT_AUTO' => true,
    'METRICS_RETENTION_DAYS' => 120,
];
