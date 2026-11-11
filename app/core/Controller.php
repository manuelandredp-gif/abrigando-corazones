<?php
namespace App\Core;

class Controller
{
    protected function view(string $view, array $data = [])
    {
        $this->registrarVisita($data);

        extract($data);
        require_once __DIR__ . '/../views/' . $view . '.php';
    }

    /**
     * Registra la visita antes de pintar la vista.
     *
     * Va del lado del servidor y no con un script de terceros: no depende de
     * que el visitante acepte cookies ni de que no tenga bloqueador, y ningun
     * dato sale hacia fuera. Cualquier fallo se traga en silencio: medir jamas
     * puede impedir que la pagina se vea.
     */
    private function registrarVisita(array $data): void
    {
        try {
            $config = require __DIR__ . '/../config/metrics.php';

            if (empty($config['enabled'])) {
                return;
            }

            $ruta = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
            $titulo = $data['page']['title'] ?? ($data['pageKey'] ?? null);

            \App\Services\MetricsService::registrarVisita($ruta, $titulo !== null ? (string) $titulo : null);

            // Reporte diario sin cron: la primera visita pasada la hora lo
            // dispara. Coste normal por peticion: un is_file().
            \App\Services\DailyReportService::dispararSiCorresponde($config);
        } catch (\Throwable $e) {
            // sin efecto sobre la pagina
        }
    }

    protected function model(string $model)
    {
        $className = 'App\\Models\\' . ucfirst($model) . 'Model';
        return new $className();
    }
}
