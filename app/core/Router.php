<?php

namespace App\Core;

class Router
{
    public function dispatch()
    {
        $rawUrl = isset($_GET['url']) ? trim($_GET['url'], '/') : '';

        if ($rawUrl === '') {
            $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
            $scriptDir = trim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/.');

            if ($scriptDir !== '' && strpos(trim($path, '/'), $scriptDir) === 0) {
                $path = substr($path, strlen('/' . $scriptDir));
            }

            $rawUrl = trim($path, '/');
        }

        $routeMap = [
            '' => ['HomeController', 'index'],
            'home/index' => ['HomeController', 'index'],
            'como-ayudar' => ['HomeController', 'comoAyudar'],
            'voluntariado' => ['HomeController', 'voluntariado'],
            'puntos-de-acopio' => ['HomeController', 'puntosDeAcopio'],
            'aliados' => ['HomeController', 'aliados'],
            'impacto' => ['HomeController', 'impacto'],
            'noticias' => ['HomeController', 'noticias'],
            'contacto' => ['HomeController', 'contacto'],
            'convocatoria-artistica' => ['HomeController', 'convocatoriaArtistica'],
            'metricas/evento' => ['MetricsController', 'evento'],
            'metricas/reporte' => ['MetricsController', 'reporte'],
            'metricas/diagnostico-correo' => ['MetricsController', 'diagnosticoCorreo'],
            'metricas/probar-correo' => ['MetricsController', 'probarCorreo'],
            'solicitudes' => ['MetricsController', 'solicitudes'],
            'quienes-somos' => ['HomeController', 'quienesSomos'],
            'donar' => ['HomeController', 'donar'],
        ];

        if (isset($routeMap[$rawUrl])) {
            [$controllerName, $methodName] = $routeMap[$rawUrl];
            $controllerPath = __DIR__ . '/../controllers/' . $controllerName . '.php';

            if (file_exists($controllerPath)) {
                require_once $controllerPath;
                $fullClass = 'App\\Controllers\\' . $controllerName;

                if (class_exists($fullClass)) {
                    $controller = new $fullClass();
                    call_user_func([$controller, $methodName]);
                    return;
                }
            }
        }

        if (str_starts_with($rawUrl, 'noticias/')) {
            $slug = trim(substr($rawUrl, strlen('noticias/')), '/');

            if ($slug !== '') {
                require_once __DIR__ . '/../controllers/HomeController.php';
                $controller = new \App\Controllers\HomeController();
                $controller->noticiaDetalle($slug);
                return;
            }
        }

        if (str_starts_with($rawUrl, 'puntos-de-acopio/')) {
            $slug = trim(substr($rawUrl, strlen('puntos-de-acopio/')), '/');

            if ($slug !== '') {
                require_once __DIR__ . '/../controllers/HomeController.php';
                $controller = new \App\Controllers\HomeController();
                $controller->puntoAcopioDetalle($slug);
                return;
            }
        }

        $url = explode('/', $rawUrl);

        $controllerName = ucfirst(!empty($url[0]) ? $url[0] : DEFAULT_CONTROLLER) . 'Controller';
        $methodName = isset($url[1]) && $url[1] !== '' ? $url[1] : DEFAULT_METHOD;
        $controllerPath = __DIR__ . '/../controllers/' . $controllerName . '.php';

        if (file_exists($controllerPath)) {
            require_once $controllerPath;

            $fullClass = 'App\\Controllers\\' . $controllerName;

            if (class_exists($fullClass)) {
                $controller = new $fullClass();

                if (method_exists($controller, $methodName)) {
                    call_user_func_array([$controller, $methodName], array_slice($url, 2));
                    return;
                }
            }
        }

        require_once __DIR__ . '/../controllers/HomeController.php';
        $fallback = new \App\Controllers\HomeController();
        $fallback->notFound();
    }
}
