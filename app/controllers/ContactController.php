<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ContactModel;
use App\Services\ContactNotificationService;

class ContactController extends Controller
{
    public function subscribe()
    {
        $fallback = BASE_URL . 'contacto';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $redirect = trim((string)($_POST['redirect'] ?? ''));
            if ($redirect === '' || preg_match('/^https?:\/\//i', $redirect)) {
                $redirect = $fallback;
            } else {
                $redirect = BASE_URL . ltrim($redirect, '/');
            }

            /*
             * Verificacion CSRF. Antes no existia: cualquier pagina de terceros
             * podia hacer POST aqui con el navegador de la victima y crear
             * registros o disparar correos en nombre de la campana. El honeypot
             * y el limite de 5 segundos frenan bots torpes, no un envio forjado.
             */
            $token = isset($_POST[\App\Core\Csrf::FIELD]) ? (string) $_POST[\App\Core\Csrf::FIELD] : null;

            if (!\App\Core\Csrf::isValid($token) || !\App\Core\Csrf::isSameOrigin()) {
                header('Location: ' . $redirect . '?success=error');
                exit;
            }

            $data = $this->sanitizeContactData($_POST);

            if (!$this->passesSpamChecks() || !$this->isValidContactData($data)) {
                header('Location: ' . $redirect . '?success=error');
                exit;
            }

            try {
                $contactModel = new ContactModel();
                $contactId = $contactModel->saveContact($data);
            } catch (\Throwable $exception) {
                header('Location: ' . $redirect . '?success=error');
                exit;
            }

            if ($contactId) {
                $data['id'] = $contactId;
                $data['fecha_registro'] = date('d/m/Y H:i');

                $notification = (new ContactNotificationService())->notify($data);
                $contactModel->updateNotificationStatus(
                    $contactId,
                    (bool) ($notification['sent'] ?? false),
                    $notification['error'] ?? null
                );

                $_SESSION['last_contact_submit_at'] = time();
                header('Location: ' . $redirect . '?success=true');
                exit;
            }

            header('Location: ' . $redirect . '?success=error');
            exit;
        }

        header('Location: ' . $fallback);
        exit;
    }

    private function sanitizeContactData(array $input): array
    {
        return [
            'nombre' => $this->cleanText((string) ($input['nombre'] ?? ''), 100),
            'telefono' => $this->cleanText((string) ($input['telefono'] ?? ''), 20),
            'email' => $this->cleanText((string) ($input['email'] ?? ''), 100),
            'como_se_entero' => $this->cleanText((string) ($input['como_se_entero'] ?? ''), 100),
            'mensaje' => $this->cleanText((string) ($input['mensaje'] ?? ''), 600),
            'website' => $this->cleanText((string) ($input['website'] ?? ''), 120),
        ];
    }

    private function cleanText(string $value, int $maxLength): string
    {
        $value = trim(str_replace(["\r", "\n"], ' ', $value));
        $value = preg_replace('/\s+/', ' ', $value) ?? '';
        return mb_substr($value, 0, $maxLength, 'UTF-8');
    }

    private function isValidContactData(array $data): bool
    {
        $allowedInterests = [
            'Dona ropa',
            'Donar ropa',
            'Voluntariado',
            'Se voluntario',
            'Sé voluntario',
            'SÃ© voluntario',
            'Punto de acopio',
            'Abre un punto de acopio',
            'Aliado',
            'Convocatoria artística',
            'Convocatoria artistica',
            'Consulta',
            'Otro',
            'Colecta',
            'Social',
            'Universitaria',
            'Empresarial',
            'Boletin',
            'Boletín',
            'BoletÃ­n',
            'Recepción',
            'RecepciÃ³n',
            'Clasificación',
            'ClasificaciÃ³n',
            'Inventario',
            'Transporte',
            'Difusión',
            'DifusiÃ³n',
            'Fotografía y video',
            'FotografÃ­a y video',
            'Diseño y redes',
            'DiseÃ±o y redes',
            'Coordinación',
            'CoordinaciÃ³n',
        ];

        $phoneDigits = preg_replace('/\D+/', '', $data['telefono']) ?? '';

        return $data['nombre'] !== ''
            && mb_strlen($data['nombre'], 'UTF-8') >= 3
            && preg_match('/^[\p{L}\p{N}\s.\'-]+$/u', $data['nombre'])
            && strlen($phoneDigits) >= 7
            && strlen($phoneDigits) <= 15
            && filter_var($data['email'], FILTER_VALIDATE_EMAIL)
            && mb_strlen($data['mensaje'] ?? '', 'UTF-8') <= 600
            && in_array($data['como_se_entero'], $allowedInterests, true);
    }

    private function passesSpamChecks(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!empty($_POST['website'])) {
            return false;
        }

        $lastSubmitAt = (int) ($_SESSION['last_contact_submit_at'] ?? 0);
        return $lastSubmitAt === 0 || (time() - $lastSubmitAt) >= 5;
    }
}
