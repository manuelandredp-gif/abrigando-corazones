<?php
$escape = static fn($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$field = static function (string $label, ?string $value) use ($escape): string {
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }

    return '<tr><td style="padding:13px 0;border-bottom:1px solid #E8EDF3;">'
        . '<div style="font-family:Arial,sans-serif;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#F97316;font-weight:800;">' . $escape($label) . '</div>'
        . '<div style="font-family:Arial,sans-serif;font-size:16px;line-height:1.5;color:#071F3D;font-weight:700;margin-top:5px;">' . nl2br($escape($value)) . '</div>'
        . '</td></tr>';
};

$recordId = isset($contact['id']) ? '#' . $contact['id'] : 'Por confirmar';
$dateLabel = $contact['fecha_registro'] ?? date('d/m/Y H:i');
$requestType = trim((string) ($contact['como_se_entero'] ?? 'Consulta'));
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Nueva solicitud - Abrigando Corazones</title>
</head>
<body style="margin:0;padding:0;background:#FFF8F0;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#FFF8F0;margin:0;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;background:#FFFFFF;border-radius:22px;overflow:hidden;border:1px solid #F3D7BE;box-shadow:0 16px 40px rgba(7,31,61,.12);">
                    <tr>
                        <td style="background:#071F3D;padding:30px;">
                            <div style="font-family:Arial,sans-serif;font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:#FDBA74;font-weight:800;">Abrigando Corazones</div>
                            <h1 style="font-family:Arial,sans-serif;margin:10px 0 0;color:#FFFFFF;font-size:28px;line-height:1.12;">Nueva solicitud recibida</h1>
                            <p style="font-family:Arial,sans-serif;margin:12px 0 0;color:#DCE8F6;font-size:15px;line-height:1.55;">Una persona completó el formulario web. El registro ya fue guardado en MySQL antes de enviar este aviso.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:22px 30px 0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#FFF4EA;border:1px solid #FED7AA;border-radius:16px;">
                                <tr>
                                    <td style="padding:16px 18px;">
                                        <div style="font-family:Arial,sans-serif;font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:#EA580C;font-weight:800;">Tipo de solicitud</div>
                                        <div style="font-family:Arial,sans-serif;font-size:22px;line-height:1.25;color:#071F3D;font-weight:900;margin-top:4px;"><?= $escape($requestType) ?></div>
                                    </td>
                                    <td align="right" style="padding:16px 18px;width:120px;">
                                        <div style="display:inline-block;background:#071F3D;color:#FFFFFF;font-family:Arial,sans-serif;font-size:14px;font-weight:800;padding:9px 12px;border-radius:999px;"><?= $escape($recordId) ?></div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 30px 8px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <?= $field('Nombre', $contact['nombre'] ?? '') ?>
                                <?= $field('Teléfono / WhatsApp', $contact['telefono'] ?? '') ?>
                                <?= $field('Correo', $contact['email'] ?? '') ?>
                                <?= $field('Mensaje', $contact['mensaje'] ?? '') ?>
                                <?= $field('Fecha de registro', $dateLabel) ?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 30px 30px;">
                            <?php if (!empty($whatsappUrl)): ?>
                                <a href="<?= $escape($whatsappUrl) ?>" style="display:block;background:#F97316;color:#FFFFFF;text-decoration:none;font-family:Arial,sans-serif;font-size:14px;font-weight:900;letter-spacing:.04em;text-align:center;padding:15px 18px;border-radius:13px;margin-bottom:12px;">CONTACTAR POR WHATSAPP</a>
                            <?php endif; ?>

                            <?php if (!empty($replyEmail)): ?>
                                <a href="mailto:<?= $escape($replyEmail) ?>" style="display:block;background:#071F3D;color:#FFFFFF;text-decoration:none;font-family:Arial,sans-serif;font-size:14px;font-weight:900;letter-spacing:.04em;text-align:center;padding:15px 18px;border-radius:13px;">RESPONDER POR CORREO</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#F5F6F8;padding:16px 30px;">
                            <p style="font-family:Arial,sans-serif;margin:0;color:#4B5563;font-size:12px;line-height:1.5;">Aviso automático del sistema web. No compartas credenciales ni datos sensibles por canales no verificados.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
