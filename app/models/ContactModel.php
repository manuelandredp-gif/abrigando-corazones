<?php
namespace App\Models;

use App\Core\Model;

class ContactModel extends Model
{
    public function saveContact(array $data): ?int
    {
        try {
            $sql = 'INSERT INTO registros_contacto (nombre, telefono, email, como_se_entero, mensaje) VALUES (?, ?, ?, ?, ?)';
            $stmt = $this->db->prepare($sql);
            $saved = $stmt->execute([
                $data['nombre'],
                $data['telefono'],
                $data['email'],
                $data['como_se_entero'],
                $data['mensaje'] ?? '',
            ]);
        } catch (\Throwable $exception) {
            $sql = 'INSERT INTO registros_contacto (nombre, telefono, email, como_se_entero) VALUES (?, ?, ?, ?)';
            $stmt = $this->db->prepare($sql);
            $saved = $stmt->execute([
                $data['nombre'],
                $data['telefono'],
                $data['email'],
                $data['como_se_entero']
            ]);
        }

        return $saved ? (int) $this->db->lastInsertId() : null;
    }

    public function updateNotificationStatus(int $id, bool $sent, ?string $error = null): void
    {
        try {
            $sql = 'UPDATE registros_contacto
                    SET notification_email_sent = ?,
                        notification_email_sent_at = ?,
                        notification_email_error = ?
                    WHERE id = ?';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $sent ? 1 : 0,
                $sent ? date('Y-m-d H:i:s') : null,
                $error ? substr($error, 0, 250) : null,
                $id,
            ]);
        } catch (\Throwable $exception) {
            // Older databases may not have the optional notification columns yet.
        }
    }
}
