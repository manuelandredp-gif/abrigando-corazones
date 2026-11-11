<?php

return [
    'MAIL_ENABLED' => true,
    'MAIL_HOST' => 'smtp.gmail.com',
    'MAIL_PORT' => 587,
    'MAIL_ENCRYPTION' => 'tls',
    'MAIL_USERNAME' => 'tu-correo@gmail.com',
    'MAIL_PASSWORD' => 'tu-contrasena-de-aplicacion-google',
    'MAIL_FROM_ADDRESS' => 'tu-correo@gmail.com',
    'MAIL_FROM_NAME' => 'Abrigando Corazones',
    'CONTACT_NOTIFICATION_EMAILS' => 'abrigandocorazones.tacna@gmail.com',
    // Envio por HTTPS: necesario en hosting que bloquea el puerto 587.
    // Proveedores: brevo, resend, mailersend. Vacios = solo SMTP.
    'MAIL_API_PROVIDER' => '',
    'MAIL_API_KEY' => '',

    // Reenvio por formsubmit.co (servicio ajeno). Dejalo en false
    // salvo que el equipo decida lo contrario: manda los datos de
    // quien escribe a un tercero y exige activar cada destinatario.
    'MAIL_RELAY_ENABLED' => false,
    'MAIL_TIMEOUT' => 8,
    'MAIL_DEBUG' => false,
];
