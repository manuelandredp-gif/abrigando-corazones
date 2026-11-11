<?php

$localMailConfig = __DIR__ . '/mail.local.php';
$mailLocal = is_file($localMailConfig) ? require $localMailConfig : [];

$mailValue = static function (string $key, $default = null) use ($mailLocal) {
    $value = getenv($key);

    if ($value !== false && $value !== '') {
        return $value;
    }

    return $mailLocal[$key] ?? $default;
};

return [
    'enabled' => filter_var($mailValue('MAIL_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
    'host' => (string) $mailValue('MAIL_HOST', 'smtp.gmail.com'),
    'port' => (int) $mailValue('MAIL_PORT', 587),
    'encryption' => (string) $mailValue('MAIL_ENCRYPTION', 'tls'),
    'username' => (string) $mailValue('MAIL_USERNAME', ''),
    'password' => (string) $mailValue('MAIL_PASSWORD', ''),
    'from_address' => (string) $mailValue('MAIL_FROM_ADDRESS', ''),
    'from_name' => (string) $mailValue('MAIL_FROM_NAME', 'Abrigando Corazones'),
    'notification_emails' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) $mailValue('CONTACT_NOTIFICATION_EMAILS', $mailValue('CONTACT_NOTIFICATION_EMAIL', 'abrigandocorazones.tacna@gmail.com')))
    ))),
    /*
     * Envio por HTTPS.
     *
     * El hosting gratuito bloquea el puerto 587, asi que el SMTP nunca sale.
     * El puerto 443 si esta abierto -es el que sirve las paginas- y por ahi
     * funcionan las APIs de correo. Con estos dos valores puestos, el sistema
     * usa la API primero y deja el SMTP como respaldo.
     *
     * Proveedores admitidos: brevo, resend, mailersend.
     */
    'api_provider' => (string) $mailValue('MAIL_API_PROVIDER', ''),
    'api_key' => (string) $mailValue('MAIL_API_KEY', ''),

    'timeout' => (int) $mailValue('MAIL_TIMEOUT', 8),
    'debug' => filter_var($mailValue('MAIL_DEBUG', false), FILTER_VALIDATE_BOOLEAN),
];
