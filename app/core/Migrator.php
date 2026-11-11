<?php

namespace App\Core;

/**
 * Creacion automatica de las tablas de metricas.
 *
 * Existe para que nadie tenga que abrir phpMyAdmin. La primera vez que el sitio
 * intenta registrar una visita y la tabla no existe, se crean aqui y se
 * reintenta. Despues, un archivo marcador evita volver a comprobarlo, asi que
 * el coste en las siguientes peticiones es cero.
 *
 * Todas las sentencias son CREATE TABLE IF NOT EXISTS: ejecutarlas de nuevo no
 * borra ni altera nada.
 */
class Migrator
{
    /** Se sube cuando cambian las tablas, para que el marcador se regenere. */
    private const VERSION = '1';

    private const SENTENCIAS = [
        "CREATE TABLE IF NOT EXISTS metricas_visitas (
            id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
            ruta            VARCHAR(190) NOT NULL,
            titulo          VARCHAR(190) NULL,
            referente       VARCHAR(190) NULL,
            dispositivo     ENUM('movil','tablet','escritorio','desconocido') NOT NULL DEFAULT 'desconocido',
            visitante_hash  CHAR(16) NOT NULL,
            creado_en       DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY idx_visitas_fecha (creado_en),
            KEY idx_visitas_ruta (ruta),
            KEY idx_visitas_visitante (visitante_hash, creado_en)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS metricas_eventos (
            id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
            ruta            VARCHAR(190) NOT NULL,
            tipo            VARCHAR(40) NOT NULL,
            etiqueta        VARCHAR(190) NOT NULL,
            destino         VARCHAR(190) NULL,
            visitante_hash  CHAR(16) NOT NULL,
            creado_en       DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY idx_eventos_fecha (creado_en),
            KEY idx_eventos_tipo (tipo, creado_en),
            KEY idx_eventos_etiqueta (etiqueta)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS metricas_envios (
            id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
            fecha           DATE NOT NULL,
            enviado_en      DATETIME NOT NULL,
            destinatarios   VARCHAR(255) NOT NULL,
            exito           TINYINT(1) NOT NULL DEFAULT 0,
            error           VARCHAR(255) NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_envio_fecha (fecha)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ];

    /**
     * Crea las tablas si faltan. Devuelve true si al terminar existen.
     */
    public static function asegurarTablasDeMetricas(bool $forzar = false): bool
    {
        $marcador = self::rutaMarcador();

        if (!$forzar && is_file($marcador)) {
            return true;
        }

        try {
            $db = Database::getInstance()->getConnection();

            foreach (self::SENTENCIAS as $sql) {
                $db->exec($sql);
            }

            @file_put_contents($marcador, date('c'));

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * ¿El error de PDO se debe a que la tabla no existe?
     * SQLSTATE 42S02 = base table or view not found.
     */
    public static function esTablaInexistente(\Throwable $e): bool
    {
        if ($e instanceof \PDOException && ($e->getCode() === '42S02' || $e->getCode() === 42)) {
            return true;
        }

        return stripos($e->getMessage(), "doesn't exist") !== false
            || stripos($e->getMessage(), 'no existe') !== false;
    }

    private static function rutaMarcador(): string
    {
        return dirname(__DIR__, 2) . '/logs/.migracion-metricas-v' . self::VERSION;
    }
}
