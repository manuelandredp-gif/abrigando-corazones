<?php

namespace App\Core;

/**
 * Proteccion CSRF para los formularios del sitio.
 *
 * Antes no existia: cualquier pagina de terceros podia hacer POST a
 * /contacto/subscribe con el navegador de la victima y crear registros o
 * disparar correos en nombre de la campana. El honeypot y el limite de 5
 * segundos frenan bots torpes, pero no un envio forjado desde otro sitio.
 *
 * Se usa un token por sesion (no por formulario) para que abrir varias
 * pestanas no invalide las anteriores, comparado con hash_equals para evitar
 * fugas por tiempo de comparacion.
 */
class Csrf
{
    private const SESSION_KEY = '_csrf_token';
    public const FIELD = '_token';

    /**
     * Devuelve el token de la sesion, creandolo la primera vez.
     */
    public static function token(): string
    {
        self::ensureSession();

        if (empty($_SESSION[self::SESSION_KEY]) || !is_string($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Campo oculto listo para pegar dentro de un <form>.
     */
    public static function field(): string
    {
        return '<input type="hidden" name="' . self::FIELD . '" value="'
            . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Valida el token recibido en un POST.
     */
    public static function isValid(?string $candidate): bool
    {
        self::ensureSession();

        $stored = $_SESSION[self::SESSION_KEY] ?? '';

        if (!is_string($stored) || $stored === '' || !is_string($candidate) || $candidate === '') {
            return false;
        }

        return hash_equals($stored, $candidate);
    }

    /**
     * Comprobacion adicional de origen. No sustituye al token: es una segunda
     * barrera para peticiones que traen cabecera Origin o Referer.
     */
    public static function isSameOrigin(): bool
    {
        // HTTP_HOST trae el puerto y parse_url(PHP_URL_HOST) no. Comparar en
        // crudo hace fallar la validacion en cualquier puerto no estandar.
        $host = self::hostSinPuerto($_SERVER['HTTP_HOST'] ?? '');

        if ($host === '') {
            return true; // sin host no se puede comparar; decide el token
        }

        $source = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '';

        if ($source === '') {
            return true; // muchos navegadores omiten Referer con politicas estrictas
        }

        $sourceHost = self::hostSinPuerto((string) (parse_url($source, PHP_URL_HOST) ?: ''));

        return $sourceHost === '' || strcasecmp($sourceHost, $host) === 0;
    }

    private static function hostSinPuerto(string $valor): string
    {
        $valor = trim($valor);

        if ($valor === '') {
            return '';
        }

        if ($valor[0] === '[') {
            $cierre = strpos($valor, ']');
            return $cierre !== false ? substr($valor, 0, $cierre + 1) : $valor;
        }

        $dosPuntos = strrpos($valor, ':');

        return $dosPuntos !== false ? substr($valor, 0, $dosPuntos) : $valor;
    }

    private static function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}
