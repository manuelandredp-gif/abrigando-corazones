<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        try {
            $this->connection = new PDO(
                'mysql:host=' . DB_HOST
                    . (defined('DB_PORT') && DB_PORT ? ';port=' . DB_PORT : '')
                    . ';dbname=' . DB_NAME
                    . ';charset=' . DB_CHARSET,
                DB_USER,
                DB_PASS,
                [
                    /*
                     * Tope de espera al conectar.
                     *
                     * Sin esto, si el servidor MySQL de InfinityFree deja de
                     * responder -y en hosting gratuito pasa- PHP se queda
                     * esperando el tiempo por defecto del sistema, que puede
                     * ser mas de un minuto, y CADA visita se cuelga con el.
                     *
                     * Con 5 segundos, ante una caida de la base el sitio sigue
                     * mostrandose: el contenido vive en archivos, no en MySQL.
                     * Solo dejan de funcionar el guardado de formularios y las
                     * metricas, que ya estan envueltos en try/catch.
                     */
                    PDO::ATTR_TIMEOUT => 5,
                ]
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            // Con prepares emulados PDO interpola los parametros en PHP y no en
            // el servidor. Con ciertos juegos de caracteres eso reabre la puerta
            // a inyeccion. Se fuerzan prepares reales.
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
