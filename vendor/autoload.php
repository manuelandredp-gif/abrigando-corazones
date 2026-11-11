<?php

spl_autoload_register(static function (string $class): void {
    $prefix = 'PHPMailer\\PHPMailer\\';

    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = __DIR__ . '/phpmailer/phpmailer/src/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});
