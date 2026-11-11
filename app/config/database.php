<?php
/**
 * Configuracion de conexion a MySQL.
 *
 * Prioridad:
 * 1. Variables de entorno del hosting.
 * 2. app/config/database.local.php (no versionado).
 * 3. Valores locales de desarrollo.
 */

$localDatabaseConfig = __DIR__ . '/database.local.php';
$databaseLocal = is_file($localDatabaseConfig) ? require $localDatabaseConfig : [];

$databaseValue = static function (string $key, $default = null) use ($databaseLocal) {
    $value = getenv($key);

    if ($value !== false && $value !== '') {
        return $value;
    }

    return $databaseLocal[$key] ?? $default;
};

define('DB_HOST', (string) $databaseValue('DB_HOST', 'sqlXXX.infinityfree.com'));
define('DB_USER', (string) $databaseValue('DB_USER', 'if0_XXXXXXXX'));
define('DB_PASS', (string) $databaseValue('DB_PASS', 'REEMPLAZAR_PASSWORD_MYSQL_INFINITYFREE'));
define('DB_NAME', (string) $databaseValue('DB_NAME', 'if0_XXXXXXXX_abrigando_corazones'));
// InfinityFree publica el puerto junto al host; se respeta por si algun dia
// el proveedor lo cambia. 3306 es el valor por defecto de MySQL.
define('DB_PORT', (int) $databaseValue('DB_PORT', 3306));
define('DB_CHARSET', (string) $databaseValue('DB_CHARSET', 'utf8mb4'));
