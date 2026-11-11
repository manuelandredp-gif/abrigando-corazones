<?php

namespace App\Services;

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

class ContactNotificationService
{
    private array $config;
    private string $logFile;

    public function __construct(?array $config = null)
    {
        $this->config = $config ?? require dirname(__DIR__) . '/config/mail.php';
        $this->logFile = dirname(__DIR__, 2) . '/logs/mail.log';
    }

    public function notify(array $contact): array
    {
        if (empty($this->config['enabled'])) {
            $this->log($contact, false, 'mail_disabled');
            return ['sent' => false, 'error' => 'mail_disabled', 'via' => null];
        }

        $route = $this->resolveNotificationRoute($contact);

        /*
         * El envio va por MailTransport, que prueba varios canales en orden:
         * API por HTTPS, SMTP y mail().
         *
         * Antes esto intentaba SMTP y nada mas. En hosting gratuito el puerto
         * 587 esta bloqueado, asi que el correo no salia nunca y no habia
         * forma de enterarse: quien llenaba el formulario veia "gracias" y el
         * equipo no recibia ningun aviso.
         */
        $resultado = (new MailTransport($this->config))->enviar(
            $route['recipients'],
            $route['subject'],
            $this->renderHtml($contact),
            $this->renderText($contact),
            $this->sanitizeEmail((string) ($contact['email'] ?? '')) ?: null,
            $this->sanitizeHeader((string) ($contact['nombre'] ?? ''))
        );

        // Se registra POR QUE canal salio: es lo unico que permite saber que
        // esta pasando de verdad en produccion sin adivinar.
        $this->log($contact, $resultado['sent'], sprintf(
            'route=%s recipient=%s via=%s intentos=%s',
            $route['key'],
            implode(',', $route['recipients']),
            $resultado['via'] ?? 'ninguno',
            json_encode($resultado['intentos'], JSON_UNESCAPED_UNICODE)
        ));

        return [
            'sent' => $resultado['sent'],
            'error' => $resultado['error'],
            'via' => $resultado['via'],
            'route' => $route['key'],
            'recipients' => $route['recipients'],
            'subject' => $route['subject'],
        ];
    }

    public function buildSubject(array $contact): string
    {
        return $this->resolveNotificationRoute($contact)['subject'];
    }

    public function resolveNotificationRoute(array $contact): array
    {
        $interest = mb_strtolower((string) ($contact['como_se_entero'] ?? ''), 'UTF-8');
        $centralEmails = $this->config['notification_emails'] ?? ['somos.fuerzaupt@gmail.com'];

        $specificRecipients = [];
        $key = 'consulta';
        $subject = '💬 Nueva consulta — Abrigando Corazones';

        if ($this->containsAny($interest, ['convocatoria', 'artist', 'artíst', 'talento'])) {
            $key = 'convocatoria_artistica';
            $specificRecipients = ['jc2022075505@virtual.upt.pe'];
            $subject = '🎨 Nueva inscripción / consulta de la convocatoria artística — Abrigando Corazones';
        } elseif ($this->containsAny($interest, ['volunt', 'recepci', 'clasific', 'inventario', 'transporte', 'difusi', 'fotograf', 'dise', 'coordinaci'])) {
            $key = 'voluntariado';
            $specificRecipients = ['cristtelhuayhua7@gmail.com'];
            $subject = '🧡 Nueva solicitud de voluntariado — Abrigando Corazones';
        } elseif ($this->containsAny($interest, ['ropa', 'donar', 'dona'])) {
            $key = 'donar_ropa';
            $specificRecipients = ['md2023076842@upt.pe'];
            $subject = '🧥 Nueva solicitud para donar ropa — Abrigando Corazones';
        } elseif ($this->containsAny($interest, ['punto', 'acopio'])) {
            $key = 'punto_acopio';
            $specificRecipients = ['ao2025000720@virtual.upt.pe'];
            $subject = '📍 Nueva solicitud de punto de acopio — Abrigando Corazones';
        } elseif ($this->containsAny($interest, ['aliado', 'alianza', 'social', 'universitaria', 'empresarial', 'colecta'])) {
            $key = 'alianza';
            $specificRecipients = ['ao2025000720@virtual.upt.pe'];
            $subject = '🤝 Nueva solicitud de alianza — Abrigando Corazones';
        } else {
            $key = 'consulta';
            $specificRecipients = ['jc2022075505@virtual.upt.pe'];
            $subject = '💬 Nueva consulta — Abrigando Corazones';
        }

        // Siempre incluye las cuentas centrales (somos.fuerzaupt@gmail.com) para garantizar recepcion
        $recipients = array_values(array_unique(array_filter(array_merge($specificRecipients, $centralEmails))));

        return [
            'key' => $key,
            'recipients' => $recipients,
            'subject' => $subject,
        ];
    }

    public function normalizePeruWhatsapp(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (strlen($digits) === 9) {
            return '51' . $digits;
        }

        if (strlen($digits) === 11 && strpos($digits, '51') === 0) {
            return $digits;
        }

        if (strlen($digits) === 12 && strpos($digits, '051') === 0) {
            return substr($digits, 1);
        }

        return $digits;
    }

    public function buildWhatsappUrl(array $contact): ?string
    {
        $number = $this->normalizePeruWhatsapp((string) ($contact['telefono'] ?? ''));

        if (!preg_match('/^51\d{9}$/', $number)) {
            return null;
        }

        $name = trim((string) ($contact['nombre'] ?? ''));
        $interest = trim((string) ($contact['como_se_entero'] ?? ''));
        $message = 'Hola' . ($name !== '' ? ' ' . $name : '') . ', somos del equipo de Abrigando Corazones. Recibimos tu registro en nuestra pagina web';

        if ($interest !== '') {
            $message .= ' sobre tu interes en ' . $interest;
        }

        $message .= '. Nos comunicamos contigo para coordinar el siguiente paso.';

        return 'https://wa.me/' . $number . '?text=' . rawurlencode($message);
    }

    private function hasValidSmtpConfig(): bool
    {
        return $this->sanitizeEmail((string) ($this->config['from_address'] ?? '')) !== ''
            && trim((string) ($this->config['host'] ?? '')) !== ''
            && trim((string) ($this->config['username'] ?? '')) !== ''
            && trim((string) ($this->config['password'] ?? '')) !== ''
            && !empty($this->config['notification_emails']);
    }

    private function renderHtml(array $contact): string
    {
        $whatsappUrl = $this->buildWhatsappUrl($contact);
        $replyEmail = $this->sanitizeEmail((string) ($contact['email'] ?? ''));
        $template = dirname(__DIR__) . '/views/emails/contact-notification.php';

        ob_start();
        require $template;
        return (string) ob_get_clean();
    }

    private function renderText(array $contact): string
    {
        $lines = [
            'ABRIGANDO CORAZONES',
            'Nuevo registro desde la pagina web',
            '',
            'Registro: #' . ($contact['id'] ?? 'Por confirmar'),
            'Nombre: ' . ($contact['nombre'] ?? ''),
            'Telefono / WhatsApp: ' . ($contact['telefono'] ?? ''),
            'Correo: ' . ($contact['email'] ?? ''),
            'Como desea ayudar: ' . ($contact['como_se_entero'] ?? ''),
            'Mensaje: ' . ($contact['mensaje'] ?? ''),
            'Fecha: ' . ($contact['fecha_registro'] ?? date('d/m/Y H:i')),
        ];

        $whatsappUrl = $this->buildWhatsappUrl($contact);
        if ($whatsappUrl) {
            $lines[] = 'WhatsApp: ' . $whatsappUrl;
        }

        return implode("\n", array_filter($lines, static fn(string $line): bool => $line !== ''));
    }

    private function sanitizeEmail(string $email): string
    {
        $email = trim(str_replace(["\r", "\n"], '', $email));
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
    }

    private function sanitizeHeader(string $value): string
    {
        return trim(str_replace(["\r", "\n"], ' ', $value));
    }

    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    private function log(array $contact, bool $sent, ?string $error): void
    {
        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $safeError = $error ? substr(preg_replace('/\s+/', ' ', $error) ?? 'mail_error', 0, 220) : '';
        $line = sprintf(
            "[%s] [MAIL] registro=%s type=%s sent=%s error=%s\n",
            date('Y-m-d H:i:s'),
            (string) ($contact['id'] ?? 'unknown'),
            substr((string) ($contact['como_se_entero'] ?? ''), 0, 80),
            $sent ? 'true' : 'false',
            $safeError
        );

        file_put_contents($this->logFile, $line, FILE_APPEND | LOCK_EX);
    }
}
