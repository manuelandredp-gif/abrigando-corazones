<?php
/**
 * Front Controller - punto de entrada de la aplicacion.
 *
 * Soporta dos estructuras:
 * - Local: proyecto/public/index.php y app/vendor un nivel arriba.
 * - InfinityFree: htdocs/index.php con app/vendor dentro de htdocs.
 */

$projectRoot = is_dir(__DIR__ . '/../app') ? dirname(__DIR__) : __DIR__;

require_once $projectRoot . '/app/config/config.php';
require_once $projectRoot . '/app/config/database.php';

date_default_timezone_set('America/Lima');

/*
 * Sesion.
 *
 * Antes solo se configuraban los parametros de la cookie y NUNCA se llamaba a
 * session_start(): cada modulo la abria por su cuenta, ya con el HTML enviado.
 * A esas alturas las cabeceras estan cerradas, asi que la cookie de sesion no
 * llegaba a emitirse y el token CSRF no podia coincidir entre la pagina y el
 * envio del formulario.
 *
 * Se abre aqui, antes de cualquier salida.
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

spl_autoload_register(function ($class) use ($projectRoot) {
    if (strpos($class, 'App\\') === 0) {
        $parts = explode('\\', substr($class, 4));
        $file  = $projectRoot . '/app/';
        foreach ($parts as $i => $part) {
            $last = ($i === count($parts) - 1);
            $file .= $last ? $part . '.php' : strtolower($part) . '/';
        }
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

$vendorAutoload = $projectRoot . '/vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}

if (APP_ENV === 'development' && APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

use App\Core\Router;

$router = new Router();
$router->dispatch();
