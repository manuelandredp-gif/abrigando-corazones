<?php
/**
 * Configuración global de la aplicación
 */

if (!defined('BASE_URL')) {
    $configuredBaseUrl = getenv('APP_BASE_URL') ?: '';

    if ($configuredBaseUrl !== '') {
        define('BASE_URL', rtrim($configuredBaseUrl, '/') . '/');
    } else {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'dominio-pendiente.invalid';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        $basePath = trim(str_replace('\\', '/', dirname($scriptName)), '/.');
        define('BASE_URL', $scheme . '://' . $host . ($basePath !== '' ? '/' . $basePath . '/' : '/'));
    }
}
define('APP_NAME', 'Abrigando Corazones');
define('APP_VERSION', '0.6.1');

/*
 * Nonce de la politica de contenido.
 *
 * Se genera uno por peticion. Permite que la CSP autorice EXACTAMENTE los
 * scripts en linea del sitio (hoy solo uno) en lugar de abrir la mano con
 * 'unsafe-inline', que en la practica anula la proteccion contra XSS: con
 * 'unsafe-inline' cualquier script inyectado en el HTML se ejecuta igual.
 */
if (!defined('CSP_NONCE')) {
    define('CSP_NONCE', base64_encode(random_bytes(16)));
}

define('DEFAULT_CONTROLLER', 'Home');
define('DEFAULT_METHOD', 'index');

// Ajustar segun el entorno sin hardcodear configuracion de produccion.
//
// El valor por defecto es 'production' a proposito: en InfinityFree no hay
// variables de entorno configuradas, asi que el sitio publicado caia siempre en
// 'development'. Hoy no expone errores porque APP_DEBUG tambien esta en false,
// pero bastaba activar la depuracion para filtrar rutas y credenciales. Es mas
// seguro que el descuido sea del lado restrictivo.
define('APP_ENV', getenv('APP_ENV') ?: 'production'); // development | production
define('APP_DEBUG', filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN));
