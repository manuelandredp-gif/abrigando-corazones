<?php
if (!function_exists('site_icon')) {
    function site_icon(string $name): string
    {
        $icons = [
            'heart' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s-7-4.35-9.2-8.1C.95 9.8 2.2 5.8 5.9 4.45c2.3-.85 4.6.1 6.1 2 1.5-1.9 3.8-2.85 6.1-2 3.7 1.35 4.95 5.35 3.1 8.45C19 16.65 12 21 12 21Z" fill="currentColor"/></svg>',
            'menu' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>',
            'close' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>',
            'pin' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-6.14 7-11a7 7 0 1 0-14 0c0 4.86 7 11 7 11Z"/><circle cx="12" cy="10" r="2.8"/></svg>',
            'users' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9.5" cy="8" r="3"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16.5 4.15a3 3 0 0 1 0 5.7"/></svg>',
            'target' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.7"/></svg>',
            'coat' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 3v4l-4 2 1 12h14l1-12-4-2V3l-4 2-4-2Z"/><path d="M12 5v16"/></svg>',
            'house' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 10.5 12 3l9 7.5V21H3Z"/><path d="M9 21v-6h6v6"/></svg>',
            'people' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="8" cy="8" r="3"/><circle cx="16.5" cy="9" r="2.5"/><path d="M2.5 20a5.5 5.5 0 0 1 11 0"/><path d="M13.5 20a4.5 4.5 0 0 1 8 0"/></svg>',
            'play' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m8 6 10 6-10 6Z" fill="currentColor" stroke="none"/></svg>',
            'phone' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 17.2v2a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3.8 5.18 2 2 0 0 1 5.8 3h2a2 2 0 0 1 2 1.72c.1.75.32 1.48.66 2.16a2 2 0 0 1-.45 2.11l-.84.84a16 16 0 0 0 5.99 5.99l.84-.84a2 2 0 0 1 2.11-.45c.68.34 1.41.56 2.16.66A2 2 0 0 1 22 17.2Z"/></svg>',
            'mail' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18v12H3Z"/><path d="m3 7 9 7 9-7"/></svg>',
            'clock' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
            'check' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>',
            'x' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6 18 18M18 6 6 18"/></svg>',
            'truck' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h11v8H3Z"/><path d="M14 10h3l3 3v2h-6Z"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/></svg>',
            'box' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9Z"/><path d="m12 12 8-4.5M12 12 4 7.5M12 12v9"/></svg>',
            'megaphone' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 11v2l10 4V7L3 11Z"/><path d="M13 8c4 0 6-1 8-3v14c-2-2-4-3-8-3"/><path d="M6 14v5"/></svg>',
            'handshake' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 11 7 8 3 12l4 4 3-3"/><path d="m14 11 3-3 4 4-4 4-3-3"/><path d="m8 14 2 2a2 2 0 0 0 3 0l3-3"/><path d="m9 9 2-2a3 3 0 0 1 4 0l1 1"/></svg>',
            'chart' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V5"/><path d="M4 19h16"/><path d="M8 15V9"/><path d="M12 15V6"/><path d="M16 15v-3"/><path d="m7 8 5-3 4 3"/></svg>',
            'facebook' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8h3V4h-3a5 5 0 0 0-5 5v3H6v4h3v4h4v-4h3l1-4h-4V9a1 1 0 0 1 1-1Z" fill="currentColor" stroke="none"/></svg>',
            'instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg>',
            'whatsapp' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 11.2A8.2 8.2 0 0 1 7.4 18l-4.2 1.2 1.3-4A8.2 8.2 0 1 1 20 11.2Z"/><path d="M9.4 8.5c.2-.5.4-.5.7-.5h.6c.2 0 .5.1.6.4l.9 2.1c.1.2 0 .4-.1.5l-.5.6c-.1.1-.2.3 0 .5.3.6 1 1.6 2.3 2.2.2.1.4 0 .5-.1l.7-.8c.1-.1.3-.2.5-.1l2 .9c.2.1.4.3.4.6v.7c0 .3-.2.6-.5.7-.7.2-1.8.3-3.3-.2-3.3-1.3-5.4-4.7-5.8-5.4-.4-.8-.8-2.1-.2-3.1Z"/></svg>',
            'chevron-down' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>',
            'search' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6"/><path d="m20 20-4-4"/></svg>',
            'filter' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16l-6 7v5l-4 2v-7Z"/></svg>',
            'grid' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>',
            'calendar-days' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 2v4M16 2v4"/><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18"/><path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/></svg>',
            'calendar-range' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 2v4M16 2v4"/><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18"/><path d="M7 15h4M13 15h4"/></svg>',
            'help-circle' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 1 1 4.2 1.8c-.86.75-1.7 1.32-1.7 2.7"/><path d="M12 17h.01"/></svg>',
            'locate-fixed' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/><circle cx="12" cy="12" r="7"/><circle cx="12" cy="12" r="2.5"/></svg>',
            'layers' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 4 8 4-8 4-8-4 8-4Z"/><path d="m4 12 8 4 8-4"/><path d="m4 16 8 4 8-4"/></svg>',
            'arrow-right' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m13 5 7 7-7 7"/></svg>',
            'building' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 21V7l6-4 6 4v14"/><path d="M4 21h16"/><path d="M9 10h.01"/><path d="M15 10h.01"/><path d="M9 14h.01"/><path d="M15 14h.01"/><path d="M11 21v-3h2v3"/></svg>',
            'heart-handshake' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.5 14.5c1.8-1.7 2.8-3.7 2.8-5.9 0-2.5-1.8-4.6-4.2-5.1-1.8-.4-3.7.2-5.1 1.7-1.4-1.5-3.3-2.1-5.1-1.7C4.5 4 2.7 6.1 2.7 8.6c0 2.2 1 4.2 2.8 5.9L12 20l2.2-1.8"/><path d="m9 13 2.1 2.1a2 2 0 0 0 2.8 0l4.1-4.1"/><path d="m13 11 1.4-1.4a2 2 0 0 1 2.8 0L20 12"/></svg>',
            'search-x' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6"/><path d="m20 20-4-4"/><path d="m15.5 6.5-3 3"/><path d="m12.5 6.5 3 3"/></svg>',
            'share' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4"/><path d="m15.4 6.5-6.8 4"/></svg>',
            'link' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 14a5 5 0 0 1 0-7l1.5-1.5a5 5 0 0 1 7 7L17 14"/><path d="M14 10a5 5 0 0 1 0 7l-1.5 1.5a5 5 0 0 1-7-7L7 10"/></svg>',
            // Convocatoria artistica
            'mic' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="9" y="2" width="6" height="11" rx="3"/><path d="M5 11a7 7 0 0 0 14 0"/><path d="M12 18v4"/><path d="M8.5 22h7"/></svg>',
            'dance' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="4" r="1.8"/><path d="M9 6.5 7 11l2.5 2L8 22"/><path d="m9.5 13 3 3 .5 6"/><path d="M7 11 3.5 9"/><path d="m12.5 16 4-2.5"/><circle cx="18" cy="4.5" r="1.6"/><path d="m18 6.5 1.5 4-2 2.5"/></svg>',
            'masks' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5h9v6a4.5 4.5 0 0 1-9 0Z"/><path d="M12 7h9v6a4.5 4.5 0 0 1-9 0"/><path d="M5.5 8.5h.01M9.5 8.5h.01"/><path d="M5.5 12.5c.9.8 2.6.8 3.5 0"/><path d="M14.5 10.5h.01M18.5 10.5h.01"/><path d="M18 14.5c-.9.8-2.6.8-3.5 0"/></svg>',
            'globe' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a15 15 0 0 1 0 18a15 15 0 0 1 0-18Z"/></svg>',
            'star' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 2.6 5.6 6.1.8-4.5 4.2 1.2 6-5.4-3-5.4 3 1.2-6L3.3 9.4l6.1-.8Z"/></svg>',
            'palette' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3a9 9 0 1 0 0 18c1.4 0 2-1 2-2s-.8-1.6-.8-2.4c0-.9.7-1.6 1.6-1.6H17a4 4 0 0 0 4-4c0-4.4-4-8-9-8Z"/><circle cx="7.8" cy="11.5" r="1"/><circle cx="10.5" cy="7.8" r="1"/><circle cx="15" cy="8.5" r="1"/></svg>',
            'sparkles' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 1.6 4.4L18 9l-4.4 1.6L12 15l-1.6-4.4L6 9l4.4-1.6Z"/><path d="M18.5 15.5 19.3 18l2.2.8-2.2.8-.8 2.4-.8-2.4-2.2-.8 2.2-.8Z"/></svg>',
            'file-text' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8Z"/><path d="M14 3v5h5"/><path d="M9 13h6M9 17h4"/></svg>',
        ];

        return $icons[$name] ?? '';
    }
}

/*
 * Politica de contenido con nonce.
 *
 * Se envia desde PHP y no desde el .htaccess porque el nonce cambia en cada
 * peticion y Apache no puede generarlo. Limita de donde puede cargar el
 * navegador, de modo que un script inyectado no pueda traer codigo externo ni
 * filtrar datos a otro dominio.
 *
 * style-src conserva 'unsafe-inline' porque las vistas usan atributos style
 * con valores calculados en PHP (progreso, posiciones del mapa). Los nonce no
 * cubren atributos style, solo etiquetas <style>.
 */
if (!headers_sent()) {
    header(
        "Content-Security-Policy: "
        . "default-src 'self'; "
        . "base-uri 'self'; "
        . "object-src 'none'; "
        . "frame-ancestors 'self'; "
        . "form-action 'self'; "
        // El mapa de Leaflet trae los tiles de CDNs externos y sus iconos de
        // unpkg. Sin estos hosts la CSP los bloquea y el mapa queda en blanco
        // con los pines flotando sobre la nada.
        . "img-src 'self' data: blob: https://*.basemaps.cartocdn.com https://server.arcgisonline.com https://*.tile.openstreetmap.org https://unpkg.com; "
        . "font-src 'self' https://fonts.gstatic.com data:; "
        . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://unpkg.com; "
        . "script-src 'self' 'nonce-" . CSP_NONCE . "' https://unpkg.com; "
        . "connect-src 'self'; "
        . "upgrade-insecure-requests"
    );
}

$pageTitle = $page['title'] ?? APP_NAME;
$pageDescriptionMap = [
    'inicio' => 'Abrigando Corazones es una campaña solidaria de Tacna que reúne abrigo, esperanza y acción concreta para familias de zonas altoandinas.',
    'quienes-somos' => 'Conoce la historia, el propósito y los valores que sostienen a Abrigando Corazones en Tacna.',
    'como-ayudar' => 'Conoce cómo donar ropa, participar como voluntario o convertir tu espacio en un punto de acopio de Abrigando Corazones.',
    'puntos-de-acopio' => 'Encuentra puntos de acopio de Abrigando Corazones en Tacna y conoce horarios, ubicación y formas de entrega.',
    'aliados' => 'Conoce a las organizaciones aliadas que hacen posible la campaña Abrigando Corazones junto a voluntarios y comunidades de Tacna.',
    'impacto' => 'Revisa el impacto, la transparencia y los principales hitos de Abrigando Corazones en favor de familias altoandinas de Tacna.',
    'noticias' => 'Entérate de realidades, prevención y acciones solidarias vinculadas a las bajas temperaturas en las zonas altoandinas.',
    'convocatoria-artistica' => 'Convocatoria artística de Abrigando Corazones: cantantes, danza, teatro y talentos que quieran sumarse a la jornada solidaria por Candarave, Tacna.',
    'contacto' => 'Comunícate con Abrigando Corazones para donar, sumarte como voluntario, abrir puntos de acopio o construir alianzas.',
    'donar' => 'Conoce cómo donar ropa, organizar una colecta o sumarte como voluntario en Abrigando Corazones.',
    '404' => 'No encontramos la página que buscabas dentro de Abrigando Corazones.',
];
$pageDescription = $page['description'] ?? ($pageDescriptionMap[$pageKey] ?? 'Abrigando Corazones: campaña solidaria de Tacna que reúne abrigo, esperanza y solidaridad para las familias de las zonas altoandinas.');
$canonicalPath = $page['canonical_path'] ?? match ($pageKey ?? '') {
    'inicio' => '',
    'quienes-somos' => 'quienes-somos',
    'como-ayudar' => 'como-ayudar',
    'puntos-de-acopio' => 'puntos-de-acopio',
    'aliados' => 'aliados',
    'impacto' => 'impacto',
    'noticias' => 'noticias',
    'contacto' => 'contacto',
    'convocatoria-artistica' => 'convocatoria-artistica',
    'donar' => 'donar',
    default => '',
};
$canonicalUrl = rtrim(BASE_URL, '/') . ($canonicalPath !== '' ? '/' . $canonicalPath : '/');
$ogImage = $page['og_image'] ?? (BASE_URL . 'images/hero_bg.webp');
$robots = $page['robots'] ?? (($pageKey ?? '') === '404' ? 'noindex,follow' : 'index,follow');
$articleMeta = $page['article'] ?? null;
/*
 * Cache-busting real.
 *
 * El proyecto se despliega en dos estructuras distintas:
 *   - local:        <raiz>/public/css/style.css   (app/ y vendor/ un nivel arriba)
 *   - InfinityFree: htdocs/css/style.css          (app/ y vendor/ dentro de htdocs)
 *
 * Antes solo se miraba la ruta "public/". En produccion ese filemtime fallaba
 * siempre y caia en time(), generando una URL nueva en CADA request: el CSS y
 * el JS se volvian a descargar completos en cada visita y el cache del
 * navegador nunca servia para nada. Ahora se resuelve la ruta que exista.
 */
$assetVersion = static function (string $relativePath): string {
    $roots = [
        dirname(__DIR__, 3),               // htdocs/  (produccion)
        dirname(__DIR__, 3) . '/public',   // <raiz>/public/  (local)
    ];

    foreach ($roots as $root) {
        $timestamp = @filemtime($root . '/' . ltrim($relativePath, '/'));
        if ($timestamp !== false) {
            return (string) $timestamp;
        }
    }

    // Ultimo recurso: version de la app, estable entre requests.
    return APP_VERSION;
};

$styleVersion = $assetVersion('css/style.css');
$scriptVersion = $assetVersion('js/main.js');
$headerCtaMap = [
    'inicio' => ['label' => 'DONA ROPA', 'href' => BASE_URL . 'como-ayudar#donar-ropa'],
    'quienes-somos' => ['label' => 'QUIERO AYUDAR', 'href' => BASE_URL . 'como-ayudar'],
    'como-ayudar' => ['label' => 'DONA ROPA', 'href' => BASE_URL . 'como-ayudar#donar-ropa'],
    'voluntariado' => ['label' => 'QUIERO SUMARME', 'href' => BASE_URL . 'voluntariado#formulario-voluntariado'],
    'puntos-de-acopio' => ['label' => 'ENCONTRAR UN PUNTO', 'href' => BASE_URL . 'puntos-de-acopio#red-puntos'],
    'aliados' => ['label' => 'QUIERO SER ALIADO', 'href' => BASE_URL . 'aliados#formulario-aliados'],
    'impacto' => ['label' => 'QUIERO AYUDAR', 'href' => BASE_URL . 'como-ayudar'],
    'noticias' => ['label' => 'VER CÓMO AYUDAR', 'href' => BASE_URL . 'como-ayudar'],
    'contacto' => ['label' => 'ESCRIBIR AHORA', 'href' => $contact['phone_link'] ?? (BASE_URL . 'contacto')],
    'convocatoria-artistica' => ['label' => 'INSCRIBIR MI TALENTO', 'href' => BASE_URL . 'convocatoria-artistica#inscripcion'],
    'donar' => ['label' => 'DONA ROPA', 'href' => BASE_URL . 'como-ayudar#donar-ropa'],
    '404' => ['label' => 'VOLVER AL INICIO', 'href' => BASE_URL],
];
$headerCta = $headerCtaMap[$pageKey ?? ''] ?? ['label' => 'DONA ROPA', 'href' => BASE_URL . 'como-ayudar#donar-ropa'];
$pagePulseMap = [
    'inicio' => [
        'label' => 'Campaña activa',
        'text' => 'Cada prenda suma abrigo real para familias altoandinas de Tacna.',
        'items' => ['Dona ropa en buen estado', 'Encuentra un punto cercano', 'Súmate como voluntario'],
        'href' => BASE_URL . 'como-ayudar',
        'cta' => 'Ver formas de ayudar',
    ],
    'quienes-somos' => [
        'label' => 'Propósito compartido',
        'text' => 'Trabajamos con una red humana, transparente y cercana.',
        'items' => ['Misión clara', 'Acción comunitaria', 'Cuidado en cada entrega'],
        'href' => BASE_URL . 'impacto',
        'cta' => 'Ver impacto',
    ],
    'como-ayudar' => [
        'label' => 'Elige tu camino',
        'text' => 'Puedes donar ropa, ofrecer tu tiempo o abrir un punto de acopio.',
        'items' => ['Dona ropa', 'Sé voluntario', 'Abre un punto'],
        'href' => BASE_URL . 'contacto',
        'cta' => 'Coordinar ayuda',
    ],
    'voluntariado' => [
        'label' => 'Tu tiempo también abriga',
        'text' => 'Ayúdanos a clasificar, coordinar, difundir y acompañar la campaña.',
        'items' => ['Clasificación', 'Logística', 'Difusión'],
        'href' => BASE_URL . 'voluntariado#formulario-voluntariado',
        'cta' => 'Inscribirme',
    ],
    'puntos-de-acopio' => [
        'label' => 'Red de recepción',
        'text' => 'Revisa horarios, ubicación y ruta antes de acercarte.',
        'items' => ['Mapa real', 'Horarios visibles', 'Ruta en Google Maps'],
        'href' => BASE_URL . 'puntos-de-acopio#red-puntos',
        'cta' => 'Buscar punto',
    ],
    'aliados' => [
        'label' => 'Red aliada',
        'text' => 'Organizaciones, negocios y voluntarios unidos por una campaña común.',
        'items' => ['Logos reales', 'Perfiles claros', 'Sin datos inventados'],
        'href' => BASE_URL . 'aliados#formulario-aliados',
        'cta' => 'Ser aliado',
    ],
    'impacto' => [
        'label' => 'Transparencia',
        'text' => 'Mostramos avances, señales de alcance y cómo se organiza la ayuda.',
        'items' => ['Meta visible', 'Proceso claro', 'Historias reales'],
        'href' => BASE_URL . 'como-ayudar',
        'cta' => 'Ayudar ahora',
    ],
    'noticias' => [
        'label' => 'Contexto y campaña',
        'text' => 'Lee avances, realidades y acciones solidarias vinculadas al friaje.',
        'items' => ['Fotos reales', 'Lectura rápida', 'Noticias relacionadas'],
        'href' => BASE_URL . 'como-ayudar',
        'cta' => 'Quiero ayudar',
    ],
    'contacto' => [
        'label' => 'Canal directo',
        'text' => 'Cuéntanos qué necesitas y enviaremos tu solicitud al equipo correcto.',
        'items' => ['Respuesta clara', 'WhatsApp disponible', 'Correo profesional'],
        'href' => $contact['phone_link'] ?? (BASE_URL . 'contacto'),
        'cta' => 'Escribir por WhatsApp',
    ],
    'donar' => [
        'label' => 'Tu prenda puede abrigar',
        'text' => 'Prepara ropa limpia, útil y en buen estado para que llegue mejor.',
        'items' => ['Revisa qué donar', 'Coordina entrega', 'Comparte campaña'],
        'href' => BASE_URL . 'puntos-de-acopio',
        'cta' => 'Ver puntos',
    ],
    'convocatoria-artistica' => [
        'label' => 'Convocatoria abierta',
        'text' => 'Tu talento puede llevar alegría y esperanza al cierre de campaña.',
        'items' => ['Inscripción gratuita', 'Certificado de participación', 'Cierre: 07 de septiembre'],
        'href' => BASE_URL . 'convocatoria-artistica#inscripcion',
        'cta' => 'Inscribir mi talento',
    ],
    '404' => [
        'label' => 'Ruta no encontrada',
        'text' => 'No pasa nada, volvamos a una acción útil dentro de la campaña.',
        'items' => ['Inicio', 'Cómo ayudar', 'Contacto'],
        'href' => BASE_URL,
        'cta' => 'Volver al inicio',
    ],
];
$pagePulse = $pagePulseMap[$pageKey ?? ''] ?? null;
$navActiveKey = $pageKey ?? '';
if (in_array($navActiveKey, ['voluntariado', 'donar'], true)) {
    $navActiveKey = 'como-ayudar';
}
if (str_starts_with($navActiveKey, 'noticias')) {
    $navActiveKey = 'noticias';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="theme-color" content="#071F3D">
    <meta name="robots" content="<?= htmlspecialchars($robots) ?>">
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:type" content="<?= $articleMeta ? 'article' : 'website' ?>">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle . ' | ' . APP_NAME) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle . ' | ' . APP_NAME) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($ogImage) ?>">
    <?php if ($articleMeta): ?>
        <meta property="article:published_time" content="<?= htmlspecialchars($articleMeta['published_time']) ?>">
        <meta property="article:modified_time" content="<?= htmlspecialchars($articleMeta['modified_time']) ?>">
        <meta property="article:section" content="<?= htmlspecialchars($articleMeta['section']) ?>">
        <script type="application/ld+json">
            <?= json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => $pageTitle,
                'description' => $pageDescription,
                'image' => [$ogImage],
                'datePublished' => $articleMeta['published_time'],
                'dateModified' => $articleMeta['modified_time'],
                'author' => [
                    '@type' => 'Organization',
                    'name' => $articleMeta['author'],
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => APP_NAME,
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => BASE_URL . 'images/logo_heart.webp',
                    ],
                ],
                'mainEntityOfPage' => $canonicalUrl,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
        </script>
    <?php endif; ?>
    <title><?= htmlspecialchars($pageTitle) ?> | <?= APP_NAME ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>images/favicon.svg">
    <?php
    /*
     * 49 - La foto del hero es el elemento mas grande de la portada y competia
     * con el CSS por ancho de banda. Se precarga solo donde existe.
     */
    if (($pageKey ?? '') === 'inicio' && !empty($page['hero']['image'])): ?>
        <link rel="preload" as="image" href="<?= htmlspecialchars($page['hero']['image']) ?>" fetchpriority="high">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php /* Montserrat en cursiva real: el afiche usa italic a gran tamano y la
             cursiva sintetica del navegador se deforma en titulares grandes. */ ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:ital,wght@0,600;0,700;0,800;0,900;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <?php if (($pageKey ?? '') === 'puntos-de-acopio'): ?>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css?v=<?= urlencode((string) $styleVersion) ?>">
</head>
<body class="site page-<?= htmlspecialchars($pageKey) ?>">
<div class="scroll-progress" data-scroll-progress aria-hidden="true"></div>

<?php /*
   Filtros SVG de la identidad grafica: acuarela, brocha y grano de papel.
   Viven en el layout para que cualquier vista pueda usarlos desde CSS con
   filter: url(#...) sin subir una sola imagen.
*/ ?>
<svg class="site-defs" aria-hidden="true" focusable="false">
    <defs>
        <filter id="artcall-watercolor">
            <feTurbulence type="fractalNoise" baseFrequency="0.012 0.018" numOctaves="4" seed="7" result="noise"/>
            <feDisplacementMap in="SourceGraphic" in2="noise" scale="52" xChannelSelector="R" yChannelSelector="G"/>
            <feGaussianBlur stdDeviation="6"/>
        </filter>
        <filter id="artcall-brush">
            <feTurbulence type="fractalNoise" baseFrequency="0.02 0.06" numOctaves="3" seed="3" result="n"/>
            <feDisplacementMap in="SourceGraphic" in2="n" scale="14" xChannelSelector="R" yChannelSelector="G"/>
        </filter>
        <filter id="artcall-grain">
            <feTurbulence type="fractalNoise" baseFrequency="0.85" numOctaves="3" stitchTiles="stitch"/>
            <feColorMatrix type="saturate" values="0"/>
        </filter>
    </defs>
</svg>
<?php if (isset($_GET['success']) && $_GET['success'] === 'true'): ?>
    <div class="toast toast--success" role="status" aria-live="polite" data-toast>
        <span class="toast__icon"><?= site_icon('check') ?></span>
        <span>Gracias por sumarte. Te contactaremos muy pronto.</span>
    </div>
<?php elseif (isset($_GET['success']) && $_GET['success'] === 'error'): ?>
    <div class="toast toast--error" role="alert" aria-live="assertive" data-toast>
        <span class="toast__icon"><?= site_icon('x') ?></span>
        <span>Revisa tus datos e inténtalo nuevamente.</span>
    </div>
<?php endif; ?>

<header class="site-header" id="site-header">
    <div class="site-shell site-header__inner">
        <a class="brand" href="<?= BASE_URL ?>" aria-label="Abrigando Corazones inicio">
            <img src="<?= BASE_URL ?>images/logo_heart.webp" alt="Logotipo Abrigando Corazones" class="brand__mark">
        </a>

        <nav class="main-nav" aria-label="Navegación principal">
            <?php foreach ($navigation as $item): ?>
                <a href="<?= htmlspecialchars($item['href']) ?>" class="<?= $item['key'] === $navActiveKey ? 'is-active' : '' ?>" data-prefetch-link>
                    <span><?= htmlspecialchars($item['label']) ?></span>
                    <i aria-hidden="true"></i>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="site-header__actions">
            <a href="<?= htmlspecialchars($headerCta['href']) ?>" class="btn btn--primary btn--header" data-prefetch-link>
                <span><?= htmlspecialchars($headerCta['label']) ?></span>
                <span class="btn__icon btn__icon--fill"><?= site_icon('heart') ?></span>
            </a>
            <button type="button" class="menu-toggle" data-menu-toggle aria-expanded="false" aria-controls="mobile-menu" aria-label="Abrir menú">
                <span class="menu-toggle__open"><?= site_icon('menu') ?></span>
                <span class="menu-toggle__close"><?= site_icon('close') ?></span>
            </button>
        </div>
    </div>

    <nav class="mobile-nav" id="mobile-menu" aria-label="Menú móvil">
        <?php foreach ($navigation as $item): ?>
            <a href="<?= htmlspecialchars($item['href']) ?>" class="<?= $item['key'] === $navActiveKey ? 'is-active' : '' ?>" data-prefetch-link>
                <?= htmlspecialchars($item['label']) ?>
            </a>
        <?php endforeach; ?>
        <a href="<?= htmlspecialchars($headerCta['href']) ?>" class="btn btn--primary btn--block" data-prefetch-link>
            <span><?= htmlspecialchars($headerCta['label']) ?></span>
            <span class="btn__icon btn__icon--fill"><?= site_icon('heart') ?></span>
        </a>
    </nav>
</header>

<?php if ($pagePulse): ?>
    <aside class="page-pulse" aria-label="Resumen de esta sección">
        <div class="site-shell page-pulse__inner">
            <div class="page-pulse__copy">
                <span><?= htmlspecialchars($pagePulse['label']) ?></span>
                <p><?= htmlspecialchars($pagePulse['text']) ?></p>
            </div>
            <ul class="page-pulse__items">
                <?php foreach ($pagePulse['items'] as $item): ?>
                    <li><?= site_icon('check') ?><span><?= htmlspecialchars($item) ?></span></li>
                <?php endforeach; ?>
            </ul>
            <a href="<?= htmlspecialchars($pagePulse['href']) ?>" class="page-pulse__cta" data-prefetch-link>
                <?= htmlspecialchars($pagePulse['cta']) ?> <?= site_icon('arrow-right') ?>
            </a>
        </div>
    </aside>
<?php endif; ?>
