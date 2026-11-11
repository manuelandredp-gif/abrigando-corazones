<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        $data = $this->baseData();
        $this->renderPage('home/index', 'inicio', $data);
    }

    public function comoAyudar(): void
    {
        $data = $this->baseData();
        $this->renderPage('pages/como-ayudar', 'como-ayudar', $data);
    }

    public function puntosDeAcopio(): void
    {
        $data = $this->baseData();
        $this->renderPage('pages/puntos-de-acopio', 'puntos-de-acopio', $data);
    }

    public function puntoAcopioDetalle(string $slug): void
    {
        $data = $this->baseData();
        $point = null;

        foreach ($data['points'] as $candidate) {
            if (($candidate['slug'] ?? '') === $slug) {
                $point = $candidate;
                break;
            }
        }

        if ($point === null) {
            $this->notFound();
            return;
        }

        $related = array_values(array_filter(
            $data['points'],
            static fn(array $candidate): bool => ($candidate['slug'] ?? '') !== $slug
        ));
        usort($related, static function (array $a, array $b) use ($point): int {
            $sameDistrictA = ($a['district'] ?? '') === ($point['district'] ?? '') ? 0 : 1;
            $sameDistrictB = ($b['district'] ?? '') === ($point['district'] ?? '') ? 0 : 1;

            return $sameDistrictA <=> $sameDistrictB ?: (($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));
        });

        $this->view('pages/punto-acopio-detalle', array_merge($data, [
            'pageKey' => 'puntos-de-acopio',
            'point' => $point,
            'relatedPoints' => array_slice($related, 0, 4),
            'page' => [
                'title' => $point['name'] . ' - Punto de acopio',
                'description' => $point['seo_description'] ?? ($point['name'] . ' recibe donaciones de Abrigando Corazones en ' . $point['district'] . '. Revisa dirección, horario y detalles antes de acercarte.'),
                'canonical_path' => 'puntos-de-acopio/' . $point['slug'],
                'og_image' => $point['photo'] ?? (BASE_URL . 'images/hero_bg.webp'),
            ],
        ]));
    }

    public function voluntariado(): void
    {
        $data = $this->baseData();
        $this->view('pages/voluntariado', array_merge($data, [
            'pageKey' => 'voluntariado',
            'page' => [
                'title' => 'Voluntariado',
                'description' => 'Conoce las áreas de voluntariado de Abrigando Corazones y encuentra la forma de aportar tu tiempo a la campaña.',
                'hero' => [
                    'eyebrow' => 'Participación activa',
                    'title' => ['TU TIEMPO', 'TAMBIÉN ABRIGA'],
                    'text' => 'Cada jornada necesita manos, organización y compromiso. Encuentra la forma de sumarte según tu tiempo, habilidades y disponibilidad.',
                    'image' => BASE_URL . 'images/hero_bg.webp',
                    'variant' => 'interior',
                ],
            ],
        ]));
    }

    public function aliados(): void
    {
        $data = $this->baseData();
        $this->renderPage('pages/aliados', 'aliados', $data);
    }

    public function impacto(): void
    {
        $data = $this->baseData();
        $this->renderPage('pages/impacto', 'impacto', $data);
    }

    public function noticias(): void
    {
        $data = $this->baseData();
        $this->view('pages/noticias', array_merge($data, [
            'pageKey' => 'noticias',
            'page' => [
                'title' => 'Noticias',
                'description' => 'Historias y realidades verificadas sobre heladas, salud, prevención y solidaridad en las zonas altoandinas de Tacna.',
                'canonical_path' => 'noticias',
                'og_image' => $data['newsFeatured']['image'] ?? (BASE_URL . 'images/hero_bg.webp'),
                'hero' => [
                    'eyebrow' => 'Actualidad',
                    'title' => ['HISTORIAS Y REALIDADES', 'QUE NOS MUEVEN.'],
                    'text' => 'Conoce lo que ocurre en las zonas altoandinas, las acciones de solidaridad que están en marcha y por qué cada aporte puede marcar una diferencia.',
                    'image' => $data['newsHeroImage'] ?? (BASE_URL . 'images/hero_bg.webp'),
                    'variant' => 'interior',
                ],
            ],
        ]));
    }

    public function noticiaDetalle(string $slug): void
    {
        $data = $this->baseData();
        $article = $data['newsBySlug'][$slug] ?? null;

        if ($article === null) {
            $this->notFound();
            return;
        }

        $this->view('pages/noticia-detalle', array_merge($data, [
            'pageKey' => 'noticias',
            'article' => $article,
            'page' => [
                'title' => $article['title'],
                'description' => $article['summary'],
                'canonical_path' => 'noticias/' . $article['slug'],
                'og_image' => $article['image'],
                'article' => [
                    'published_time' => $article['published_time'],
                    'modified_time' => $article['published_time'],
                    'author' => 'Abrigando Corazones',
                    'section' => $article['primary_category_label'],
                ],
            ],
        ]));
    }

    public function contacto(): void
    {
        $data = $this->baseData();
        $this->renderPage('pages/contacto', 'contacto', $data);
    }

    public function convocatoriaArtistica(): void
    {
        $data = $this->baseData();
        $artCall = $this->getArtCall();

        $this->view('pages/convocatoria-artistica', array_merge($data, [
            'pageKey' => 'convocatoria-artistica',
            'artCall' => $artCall,
            'page' => [
                'title' => 'Convocatoria artística',
                'description' => 'Cantantes, grupos de danza, teatro y talentos artísticos: súmate al gran cierre de campaña de Abrigando Corazones por las comunidades altoandinas de Candarave, Tacna.',
                'canonical_path' => 'convocatoria-artistica',
                'og_image' => $artCall['poster_url'] ?? (BASE_URL . 'images/hero_bg.webp'),
                'hero' => [
                    'eyebrow' => 'Convocatoria abierta',
                    'title' => ['TU ARTE DEJA HUELLA,', 'TU CORAZÓN ABRIGA'],
                    'text' => 'Súmate al gran cierre de campaña y sé parte de una jornada solidaria que llevará abrigo y esperanza a las comunidades altoandinas de Candarave, Tacna.',
                    'image' => BASE_URL . 'images/hero_bg.webp',
                    'variant' => 'interior',
                ],
            ],
        ]));
    }

    public function quienesSomos(): void
    {
        $data = $this->baseData();
        $this->view('pages/quienes-somos', array_merge($data, [
            'pageKey' => 'quienes-somos',
            'page' => [
                'title' => 'Quiénes somos',
                'description' => 'Conoce la historia, el propósito y los valores que sostienen a Abrigando Corazones en Tacna.',
                'hero' => [
                    'eyebrow' => 'Nuestra historia',
                    'title' => ['SOMOS UNA RED', 'QUE ABRIGA CORAZONES'],
                    'text' => 'Abrigando Corazones articula ciudadanos, voluntarios y organizaciones comprometidas con llevar abrigo, esperanza y acción concreta a las familias altoandinas de Tacna.',
                    'image' => BASE_URL . 'images/hero_bg.webp',
                    'variant' => 'interior',
                ],
            ],
        ]));
    }

    public function donar(): void
    {
        $data = $this->baseData();
        $this->view('pages/donar', array_merge($data, [
            'pageKey' => 'donar',
            'page' => [
                'title' => 'Donar',
                'description' => 'Descubre cómo donar ropa, organizar una colecta o sumarte como voluntario en Abrigando Corazones.',
                'hero' => [
                    'eyebrow' => 'Aporte solidario',
                    'title' => ['CADA APORTE', 'ABRIGA VIDAS'],
                    'text' => 'Ya sea con prendas, colectas, voluntariado o coordinación logística, tu ayuda se convierte en abrigo real para familias altoandinas de Tacna.',
                    'image' => BASE_URL . 'images/hero_bg.webp',
                    'variant' => 'interior',
                ],
            ],
        ]));
    }

    public function notFound(): void
    {
        http_response_code(404);
        $data = $this->baseData();
        $this->view('pages/404', array_merge($data, [
            'pageKey' => '404',
            'page' => [
                'title' => 'Página no encontrada',
                'description' => 'No encontramos la página que buscabas dentro de Abrigando Corazones.',
                'hero' => [
                    'eyebrow' => 'Navegación',
                    'title' => ['PÁGINA NO', 'ENCONTRADA'],
                    'text' => 'Parece que este camino no lleva a un punto de acopio. Te ayudamos a retomar la ruta.',
                    'image' => BASE_URL . 'images/hero_bg.webp',
                    'variant' => 'interior',
                ],
            ],
        ]));
    }

    private function renderPage(string $view, string $pageKey, array $data): void
    {
        $pages = [
            'inicio' => [
                'title' => 'Inicio',
                'hero' => [
                    'eyebrow' => null,
                    'title' => ['ABRIGANDO', 'CORAZONES'],
                    'text' => 'Llevamos abrigo, esperanza y solidaridad a las familias de las zonas altoandinas de Tacna.',
                    'image' => BASE_URL . 'images/hero_bg.webp',
                    'variant' => 'home',
                ],
            ],
            'como-ayudar' => [
                'title' => '¿Cómo ayudar?',
                'description' => 'Conoce cómo donar ropa, participar como voluntario o convertir tu espacio en un punto de acopio de Abrigando Corazones.',
                'hero' => [
                    'eyebrow' => null,
                    'title' => ['¿CÓMO PUEDES', 'AYUDAR?'],
                    'text' => 'Cada acción cuenta para llevar abrigo, esperanza y solidaridad a las familias de las zonas altoandinas de Tacna. Elige la forma en que quieres transformar el invierno.',
                    'image' => BASE_URL . 'images/hero_bg.webp',
                    'variant' => 'interior',
                ],
            ],
            'puntos-de-acopio' => [
                'title' => 'Puntos de acopio',
                'hero' => [
                    'eyebrow' => null,
                    'title' => ['PUNTOS DE ACOPIO', 'EN TACNA'],
                    'text' => 'Encuentra el punto de acopio más cercano y súmate a llevar abrigo, esperanza y solidaridad a las familias altoandinas.',
                    'image' => BASE_URL . 'images/hero_bg.webp',
                    'variant' => 'interior',
                ],
            ],
            'aliados' => [
                'title' => 'Aliados',
                'hero' => [
                    'eyebrow' => null,
                    'title' => ['ORGANIZACIONES', 'ALIADAS'],
                    'text' => 'Voluntarios de diferentes organizaciones trabajando juntos por las familias altoandinas de Tacna.',
                    'image' => BASE_URL . 'images/hero_bg.webp',
                    'variant' => 'interior allies',
                ],
            ],
            'impacto' => [
                'title' => 'Impacto',
                'hero' => [
                    'eyebrow' => null,
                    'title' => ['NUESTRO', 'IMPACTO'],
                    'text' => 'Cada prenda entregada es más que abrigo: es esperanza, dignidad y un futuro mejor para las familias altoandinas de Tacna.',
                    'image' => BASE_URL . 'images/hero_bg.webp',
                    'variant' => 'interior',
                ],
            ],
            'contacto' => [
                'title' => 'Contacto',
                'hero' => [
                    'eyebrow' => null,
                    'title' => ['SÚMATE Y', 'PARTICIPA'],
                    'text' => 'Conversemos sobre voluntariado, alianzas, donaciones y nuevos puntos de acopio para seguir abrigando corazones en Tacna.',
                    'image' => BASE_URL . 'images/hero_bg.webp',
                    'variant' => 'interior',
                ],
            ],
        ];

        $this->view($view, array_merge($data, [
            'pageKey' => $pageKey,
            'page' => $pages[$pageKey],
        ]));
    }

    private function getNewsroom(): array
    {
        $config = require dirname(__DIR__) . '/config/newsroom.php';
        $stories = [];

        foreach ($config['stories'] as $story) {
            $story['url'] = BASE_URL . 'noticias/' . $story['slug'];
            $story['image'] = BASE_URL . ltrim($story['image'], '/');
            $story['primary_category_label'] = $config['categories'][$story['primary_category']] ?? $story['primary_category'];
            $story['category_labels'] = array_map(
                static fn(string $key): string => $config['categories'][$key] ?? $key,
                $story['categories']
            );
            $story['reading_minutes'] = $this->estimateReadingMinutes($story);
            $story['reading_label'] = $story['reading_minutes'] . ' min de lectura';
            $story['search_blob'] = $this->normalizeSearchText(implode(' ', array_merge(
                [$story['title'], $story['summary'], $story['excerpt'], $story['source_name']],
                $story['category_labels'],
                $story['search_terms']
            )));
            $stories[] = $story;
        }

        usort($stories, static function (array $a, array $b): int {
            return strcmp($b['published_time'], $a['published_time']);
        });

        $featured = null;
        foreach ($stories as $story) {
            if (!empty($story['featured'])) {
                $featured = $story;
                break;
            }
        }
        $featured ??= $stories[0] ?? null;

        $filters = [];
        foreach ($config['categories'] as $key => $label) {
            $count = 0;
            foreach ($stories as $story) {
                if (in_array($key, $story['categories'], true)) {
                    $count++;
                }
            }

            if ($count > 0) {
                $filters[] = [
                    'key' => $key,
                    'label' => $label,
                    'count' => $count,
                ];
            }
        }

        $bySlug = [];
        foreach ($stories as $story) {
            $story['related'] = $this->buildRelatedStories($story, $stories);
            $bySlug[$story['slug']] = $story;
        }

        return [
            'newsStories' => $stories,
            'newsFeatured' => $featured,
            'newsFilters' => $filters,
            'newsBySlug' => $bySlug,
            'newsHeroImage' => $featured['image'] ?? null,
        ];
    }

    private function buildRelatedStories(array $current, array $stories): array
    {
        $related = [];

        foreach ($stories as $story) {
            if ($story['slug'] === $current['slug']) {
                continue;
            }

            $sharedCategories = array_intersect($current['categories'], $story['categories']);
            if ($sharedCategories === []) {
                continue;
            }

            $story['shared_category_count'] = count($sharedCategories);
            $related[] = $story;
        }

        usort($related, static function (array $a, array $b): int {
            if ($a['shared_category_count'] !== $b['shared_category_count']) {
                return $b['shared_category_count'] <=> $a['shared_category_count'];
            }

            return strcmp($b['published_time'], $a['published_time']);
        });

        return array_slice($related, 0, 3);
    }

    private function estimateReadingMinutes(array $story): int
    {
        $words = str_word_count(implode(' ', array_merge(
            [$story['summary'], $story['excerpt']],
            array_map(
                static fn(array $section): string => $section['title'] . ' ' . implode(' ', $section['body']),
                $story['sections']
            ),
            [$story['callout'] ?? '']
        )));

        return max(1, (int) ceil($words / 215));
    }

    private function normalizeSearchText(string $value): string
    {
        $value = mb_strtolower($value, 'UTF-8');
        $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        return $normalized !== false ? $normalized : $value;
    }

    /**
     * Datos de la convocatoria artística.
     *
     * Resuelve el afiche oficial (si el archivo fue subido a images/convocatoria/),
     * calcula el estado de la convocatoria y los días restantes, de modo que la
     * vista no tenga que hacer lógica de fechas ni de archivos.
     */
    private function getArtCall(): array
    {
        $config = require dirname(__DIR__) . '/config/art_call.php';

        $projectRoot = is_dir(dirname(__DIR__, 2) . '/css')
            ? dirname(__DIR__, 2)               // htdocs/ (produccion)
            : dirname(__DIR__, 2) . '/public';  // <raiz>/public/ (local)

        $config['poster_url'] = null;
        foreach ($config['poster_candidates'] as $candidate) {
            if (is_file($projectRoot . '/' . $candidate)) {
                $config['poster_url'] = BASE_URL . $candidate;
                break;
            }
        }

        $now = new \DateTimeImmutable('now');
        $deadline = new \DateTimeImmutable($config['deadline']);

        $config['deadline_iso'] = $deadline->format(DATE_ATOM);
        $config['is_closed'] = $now > $deadline;
        $config['is_open'] = ($config['is_open'] ?? true) && !$config['is_closed'];
        $config['days_left'] = $config['is_closed'] ? 0 : (int) $now->diff($deadline)->days;

        return $config;
    }

    private function buildCollectionPoints(): array
    {
        $projectRoot = dirname(__DIR__, 2);
        $assetBase = BASE_URL . 'images/puntos-de-acopio/';
        $assetRoots = [
            $projectRoot . '/public/images/puntos-de-acopio/',
            $projectRoot . '/images/puntos-de-acopio/',
        ];
        $now = new \DateTimeImmutable('now', new \DateTimeZone('America/Lima'));

        $imageExists = static function (string $fileName) use ($assetRoots): bool {
            foreach ($assetRoots as $assetRoot) {
                if (is_file($assetRoot . $fileName)) {
                    return true;
                }
            }

            return false;
        };

        $imageUrl = static function (string $fileName) use ($assetBase, $imageExists): ?string {
            return $imageExists($fileName) ? $assetBase . $fileName : null;
        };

        $points = [
            [
                'id' => 1,
                'slug' => 'obra-51-cafe',
                'name' => 'OBRA 51 CAFÉ',
                'short_name' => 'OBRA 51',
                'address' => 'Av. Jorge Basadre con Av. Humboldt, Tacna.',
                'reference' => 'Cerca de la UNJBG.',
                'district' => 'Tacna',
                'google_maps_url' => null,
                'map_query' => 'Obra 51 Café Av Jorge Basadre Humboldt Tacna',
                'latitude' => null,
                'longitude' => null,
                'coordinates_verified' => false,
                'photo' => $imageUrl('obra-51-cafe.webp'),
                'photo_path' => $imageExists('obra-51-cafe.webp') ? 'images/puntos-de-acopio/obra-51-cafe.webp' : null,
                'photo_source' => $imageExists('obra-51-cafe.webp') ? 'user-provided' : null,
                'photo_credit' => null,
                'photo_verified' => $imageExists('obra-51-cafe.webp'),
                'photo_object_position' => 'center center',
                'photo_alt' => 'Fachada de Obra 51 Café, punto de acopio de Abrigando Corazones cerca de la UNJBG en Tacna.',
                'reception_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'reception_days_label' => 'Lunes a sábado',
                'reception_schedule' => ['default' => [['start' => '16:00', 'end' => '22:00']]],
                'reception_schedule_label' => '4:00 p. m. – 10:00 p. m.',
                'collection_start_label' => 'Caja de recolección habilitada desde el viernes a las 7:00 p. m.',
                'status' => 'ACTIVE',
                'is_featured' => true,
                'accepted_items' => [],
                'verification_status' => 'campaign_confirmed',
                'sort_order' => 10,
            ],
            [
                'id' => 2,
                'slug' => 'fresh-light',
                'name' => 'FRESH LIGHT',
                'short_name' => 'FRESH LIGHT',
                'address' => 'Frente a la Municipalidad Distrital de Alto de la Alianza, Tacna.',
                'reference' => 'Frente a la Municipalidad Distrital de Alto de la Alianza.',
                'district' => 'Alto de la Alianza',
                'google_maps_url' => null,
                'map_query' => 'Fresh Light frente Municipalidad Alto de la Alianza Tacna',
                'latitude' => -17.9934539,
                'longitude' => -70.2443887,
                'coordinates_verified' => true,
                'photo' => $imageUrl('fresh-light.webp'),
                'photo_path' => $imageExists('fresh-light.webp') ? 'images/puntos-de-acopio/fresh-light.webp' : null,
                'photo_source' => $imageExists('fresh-light.webp') ? 'user-provided' : null,
                'photo_credit' => null,
                'photo_verified' => $imageExists('fresh-light.webp'),
                'photo_object_position' => 'center center',
                'photo_alt' => 'Fachada de Fresh Light, punto de acopio de Abrigando Corazones frente a la Municipalidad Distrital de Alto de la Alianza en Tacna.',
                'reception_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'reception_days_label' => 'Lunes a sábado',
                'reception_schedule' => ['default' => [['start' => '08:30', 'end' => '17:00']]],
                'reception_schedule_label' => '8:30 a. m. – 5:00 p. m.',
                'collection_start_label' => 'Caja de recolección habilitada desde el 30 de julio.',
                'status' => 'ACTIVE',
                'is_featured' => true,
                'accepted_items' => [],
                'verification_status' => 'campaign_confirmed',
                'sort_order' => 20,
            ],
            [
                'id' => 3,
                'slug' => 'la-paleteria',
                'name' => 'LA PALETERÍA',
                'short_name' => 'LA PALETERÍA',
                'address' => 'Calle Ayacucho N.º 94, Tacna.',
                'reference' => 'Entre la heladería Piamonte y el Hostal Virrey.',
                'district' => 'Tacna',
                'google_maps_url' => 'https://maps.app.goo.gl/EhPp4whcXh6sbUWz7',
                'map_query' => 'La Paletería de Alma Calle Ayacucho 94 Tacna',
                'latitude' => -18.0136904,
                'longitude' => -70.2496936,
                'coordinates_verified' => true,
                'photo' => $imageUrl('la-paleteria.webp'),
                'photo_path' => $imageExists('la-paleteria.webp') ? 'images/puntos-de-acopio/la-paleteria.webp' : null,
                'photo_source' => $imageExists('la-paleteria.webp') ? 'user-provided' : null,
                'photo_credit' => null,
                'photo_verified' => $imageExists('la-paleteria.webp'),
                'photo_object_position' => 'center center',
                'photo_alt' => 'Fachada de La Paletería de Alma, punto de acopio de Abrigando Corazones en la calle Ayacucho de Tacna.',
                'reception_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
                'reception_days_label' => 'Todos los días',
                'reception_schedule' => ['default' => [['start' => '13:00', 'end' => '18:30']]],
                'reception_schedule_label' => '1:00 p. m. – 6:30 p. m.',
                'collection_start_label' => 'Caja de recolección habilitada desde el sábado 8 de agosto, 1:00 p. m.',
                'status' => 'ACTIVE',
                'is_featured' => true,
                'accepted_items' => [],
                'verification_status' => 'publicly_verified',
                'sort_order' => 30,
            ],
            [
                'id' => 4,
                'slug' => 'max-cafe',
                'name' => 'MAX CAFÉ',
                'short_name' => 'MAX CAFÉ',
                'address' => 'Av. Bolognesi 392, Tacna.',
                'reference' => 'Al lado de la pollería Pollo Pechugón y frente a Correo.',
                'district' => 'Tacna',
                'google_maps_url' => 'https://maps.app.goo.gl/8cxoDwsE3BSy5TdY8',
                'map_query' => 'Max Café Av Bolognesi 392 Tacna',
                'latitude' => -18.0154816,
                'longitude' => -70.2507244,
                'coordinates_verified' => true,
                'photo' => $imageUrl('max-cafe.webp'),
                'photo_path' => $imageExists('max-cafe.webp') ? 'images/puntos-de-acopio/max-cafe.webp' : null,
                'photo_source' => $imageExists('max-cafe.webp') ? 'user-provided' : null,
                'photo_credit' => null,
                'photo_verified' => $imageExists('max-cafe.webp'),
                'photo_object_position' => 'center center',
                'photo_alt' => 'Fachada de Max Café en la avenida Bolognesi, punto de acopio de Abrigando Corazones en Tacna.',
                'reception_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
                'reception_days_label' => 'Todos los días',
                'reception_schedule' => ['default' => [['start' => '09:00', 'end' => '21:30']]],
                'reception_schedule_label' => '9:00 a. m. – 9:30 p. m.',
                'collection_start_label' => 'Caja habilitada desde el viernes 7 de agosto, 6:30 p. m.',
                'status' => 'ACTIVE',
                'is_featured' => true,
                'accepted_items' => [],
                'verification_status' => 'publicly_verified',
                'sort_order' => 40,
            ],
            [
                'id' => 5,
                'slug' => 'trascender',
                'name' => 'CENTRO PSICOLÓGICO TRASCENDER',
                'short_name' => 'TRASCENDER',
                'address' => 'Av. Manuel A. Odría 904, Tacna.',
                'reference' => 'Al frente del GORE.',
                'district' => 'Tacna',
                'google_maps_url' => 'https://maps.app.goo.gl/FYnP7MWEkyQykFmeA',
                'map_query' => 'Centro Psicológico Trascender Av Manuel A Odría 904 Tacna',
                'latitude' => -18.0274550,
                'longitude' => -70.2629202,
                'coordinates_verified' => true,
                'photo' => $imageUrl('centro-psicologico-trascender.webp'),
                'photo_path' => $imageExists('centro-psicologico-trascender.webp') ? 'images/puntos-de-acopio/centro-psicologico-trascender.webp' : null,
                'photo_source' => $imageExists('centro-psicologico-trascender.webp') ? 'user-provided' : null,
                'photo_credit' => null,
                'photo_verified' => $imageExists('centro-psicologico-trascender.webp'),
                'photo_object_position' => 'center center',
                'photo_alt' => 'Fachada del Centro Psicológico Trascender, punto de acopio de Abrigando Corazones en Tacna.',
                'reception_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'reception_days_label' => 'Lunes a sábado',
                'reception_schedule' => [
                    'monday' => [['start' => '09:00', 'end' => '13:00'], ['start' => '14:00', 'end' => '20:00']],
                    'tuesday' => [['start' => '09:00', 'end' => '13:00'], ['start' => '14:00', 'end' => '20:00']],
                    'wednesday' => [['start' => '09:00', 'end' => '13:00'], ['start' => '14:00', 'end' => '20:00']],
                    'thursday' => [['start' => '09:00', 'end' => '13:00'], ['start' => '14:00', 'end' => '20:00']],
                    'friday' => [['start' => '09:00', 'end' => '13:00'], ['start' => '14:00', 'end' => '20:00']],
                    'saturday' => [['start' => '09:00', 'end' => '13:00']],
                ],
                'reception_schedule_label' => 'Lunes a viernes: 9:00 a. m. – 1:00 p. m. / 2:00 p. m. – 8:00 p. m. – Sábado: 9:00 a. m. – 1:00 p. m.',
                'collection_start_label' => 'Caja habilitada desde el viernes 7 de agosto, 11:00 a. m.',
                'status' => 'ACTIVE',
                'is_featured' => false,
                'accepted_items' => [],
                'verification_status' => 'publicly_verified',
                'sort_order' => 50,
            ],
            [
                'id' => 6,
                'slug' => 'academia-izy',
                'name' => 'ACADEMIA IZY',
                'short_name' => 'ACADEMIA IZY',
                'address' => 'Calle Bolívar N.º 168, 2.º y 3.er piso, Tacna.',
                'reference' => 'Al costado de Hora de Makis y Autoplan.',
                'district' => 'Tacna',
                'google_maps_url' => null,
                'map_query' => 'Academia Izy Bolívar 168 Tacna',
                'latitude' => -18.0133240,
                'longitude' => -70.2489864,
                'coordinates_verified' => true,
                'photo' => BASE_URL . 'images/puntos-de-acopio/academia-izy.webp',
                'photo_path' => 'images/puntos-de-acopio/academia-izy.webp',
                'photo_source' => 'Imagen proporcionada por el usuario',
                'photo_credit' => 'Abrigando Corazones',
                'photo_verified' => true,
                'photo_object_position' => 'center center',
                'photo_alt' => 'Fachada de Academia Izy, punto de acopio de Abrigando Corazones en Tacna.',
                'reception_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'reception_days_label' => 'Lunes a sábado',
                'reception_schedule' => [
                    'monday' => [['start' => '09:00', 'end' => '11:00'], ['start' => '14:30', 'end' => '16:10']],
                    'tuesday' => [['start' => '09:00', 'end' => '11:00'], ['start' => '14:30', 'end' => '16:10']],
                    'wednesday' => [['start' => '09:00', 'end' => '11:00'], ['start' => '14:30', 'end' => '16:10']],
                    'thursday' => [['start' => '09:00', 'end' => '11:00'], ['start' => '14:30', 'end' => '16:10']],
                    'friday' => [['start' => '09:00', 'end' => '11:00'], ['start' => '14:30', 'end' => '16:10']],
                    'saturday' => [['start' => '09:00', 'end' => '11:00']],
                ],
                'reception_schedule_label' => 'Lunes a viernes: 9:00 a. m. – 11:00 a. m. / 2:30 p. m. – 4:10 p. m. – Sábado: 9:00 a. m. – 11:00 a. m.',
                'collection_start_label' => 'Ya habilitada desde el sábado 8 de agosto.',
                'status' => 'ACTIVE',
                'is_featured' => false,
                'accepted_items' => [],
                'verification_status' => 'campaign_confirmed',
                'sort_order' => 60,
            ],
            [
                'id' => 7,
                'slug' => 'bodylife-gimnasio',
                'name' => 'BODYLIFE GIMNASIO TACNA',
                'short_name' => 'BODYLIFE',
                'address' => 'Gral. Deustua 312, Tacna 23001.',
                'reference' => 'Punto incorporado a la red de campaña.',
                'district' => 'Tacna',
                'google_maps_url' => null,
                'map_query' => 'BODYLIFE GIMNASIO TACNA Gral Deustua 312 Tacna',
                'latitude' => -18.0114822,
                'longitude' => -70.2506098,
                'coordinates_verified' => true,
                'photo' => $imageUrl('body-life-gimnasio.webp'),
                'photo_path' => $imageExists('body-life-gimnasio.webp') ? 'images/puntos-de-acopio/body-life-gimnasio.webp' : null,
                'photo_source' => $imageExists('body-life-gimnasio.webp') ? 'user-provided' : null,
                'photo_credit' => null,
                'photo_verified' => $imageExists('body-life-gimnasio.webp'),
                'photo_object_position' => 'center center',
                'photo_alt' => 'Fachada de Bodylife Gimnasio Tacna, punto de acopio de Abrigando Corazones en Tacna.',
                'reception_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
                'reception_days_label' => 'Lunes a domingo',
                'reception_schedule' => [
                    'monday' => [['start' => '05:00', 'end' => '22:00']],
                    'tuesday' => [['start' => '05:00', 'end' => '22:00']],
                    'wednesday' => [['start' => '05:00', 'end' => '22:00']],
                    'thursday' => [['start' => '05:00', 'end' => '22:00']],
                    'friday' => [['start' => '05:00', 'end' => '22:00']],
                    'saturday' => [['start' => '05:00', 'end' => '22:00']],
                    'sunday' => [['start' => '08:00', 'end' => '16:00']],
                ],
                'reception_schedule_label' => 'Lunes a sábado: 5:00 a. m. – 10:00 p. m. / Domingos: 8:00 a. m. – 4:00 p. m.',
                'collection_start_label' => null,
                'status' => 'ACTIVE',
                'is_featured' => false,
                'accepted_items' => [],
                'verification_status' => 'publicly_verified',
                'sort_order' => 70,
            ],
            [
                'id' => 8,
                'slug' => 'guadalupe-sur-textil',
                'name' => 'GUADALUPE SUR TEXTIL',
                'short_name' => 'GUADALUPE SUR',
                'address' => 'Asoc. Vista Alegre, C. Marcelino Varela, Mz. 30 Lt. 11, Gregorio Albarracín Lanchipa, Tacna.',
                'reference' => 'Distrito Gregorio Albarracín Lanchipa.',
                'district' => 'Gregorio Albarracín',
                'google_maps_url' => 'https://share.google/EpCZg6EKTbeUFkldu',
                'map_query' => 'Guadalupe Sur Textil Marcelino Varela Gregorio Albarracín Tacna',
                'latitude' => -18.0555729,
                'longitude' => -70.2450815,
                'coordinates_verified' => true,
                'photo' => $imageUrl('guadalupe-sur-textil.webp'),
                'photo_path' => $imageExists('guadalupe-sur-textil.webp') ? 'images/puntos-de-acopio/guadalupe-sur-textil.webp' : null,
                'photo_source' => $imageExists('guadalupe-sur-textil.webp') ? 'user-provided' : null,
                'photo_credit' => null,
                'photo_verified' => $imageExists('guadalupe-sur-textil.webp'),
                'photo_object_position' => 'center center',
                'photo_alt' => 'Fachada de Guadalupe Sur Textil, punto de acopio de Abrigando Corazones en Gregorio Albarracín, Tacna.',
                'reception_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'reception_days_label' => 'Lunes a sábado',
                'reception_schedule' => ['default' => [['start' => '09:00', 'end' => '18:00']]],
                'reception_schedule_label' => '9:00 a. m. – 6:00 p. m.',
                'collection_start_label' => 'Caja habilitada desde el martes 11 de agosto, 4:00 p. m.',
                'status' => 'ACTIVE',
                'is_featured' => true,
                'accepted_items' => [],
                'verification_status' => 'campaign_confirmed',
                'sort_order' => 80,
            ],
            [
                'id' => 9,
                'slug' => 'semillero-de-campeones',
                'name' => 'FUNDACIÓN SEMILLERO DE CAMPEONES',
                'short_name' => 'SEMILLERO DE CAMPEONES',
                'address' => 'Campo deportivo Los Próceres, Tacna.',
                'reference' => 'Campo deportivo Los Próceres.',
                'district' => 'Tacna',
                'google_maps_url' => null,
                'map_query' => 'Campo deportivo Los Próceres Tacna',
                'latitude' => null,
                'longitude' => null,
                'coordinates_verified' => false,
                'photo' => $imageUrl('semillero-de-campeones.jpg'),
                'photo_path' => $imageExists('semillero-de-campeones.jpg') ? 'images/puntos-de-acopio/semillero-de-campeones.jpg' : null,
                'photo_source' => $imageExists('semillero-de-campeones.jpg') ? 'user-provided' : null,
                'photo_credit' => null,
                'photo_verified' => $imageExists('semillero-de-campeones.jpg'),
                'photo_object_position' => 'center center',
                'photo_alt' => 'Fundación Semillero de Campeones, punto de acopio de Abrigando Corazones en el campo deportivo Los Próceres, Tacna.',
                // La inscripción no incluyó horarios: se dejan vacíos a propósito
                // para que la ficha muestre 'HORARIO DE ACOPIO POR CONFIRMAR' en
                // lugar de un horario inventado.
                'reception_days' => [],
                'reception_days_label' => 'Días por confirmar',
                'reception_schedule' => [],
                'reception_schedule_label' => 'Horario por confirmar',
                'collection_start_label' => 'Coordina la entrega antes de acercarte.',
                'status' => 'ACTIVE',
                'is_featured' => false,
                'accepted_items' => [],
                'verification_status' => 'campaign_reported',
                'sort_order' => 90,
            ],
        ];

        usort($points, static fn(array $a, array $b): int => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));
        $pointProfiles = $this->getCollectionPointProfiles();

        return array_map(function (array $point) use ($now, $pointProfiles): array {
            $profile = $pointProfiles[$point['slug']] ?? [];
            $point['detail_id'] = 'point-details-' . $point['slug'];
            $point['district_slug'] = $this->normalizeSlug($point['district']);
            $point['maps_url'] = $point['google_maps_url'] ?: $this->buildGoogleMapsSearchUrl($point['map_query'] ?: trim($point['name'] . ' ' . $point['address']));
            $point['public_status'] = $this->computeCollectionStatus($point, $now);
            $point['display_name'] = $profile['display_name'] ?? $point['name'];
            $point['place_type'] = $profile['place_type'] ?? 'Punto de acopio';
            $point['detail_intro'] = $profile['detail_intro'] ?? 'Punto de recepción integrado a la red de Abrigando Corazones en Tacna.';
            $point['about'] = $profile['about'] ?? 'Este punto permite acercar donaciones de abrigo a la red de recepción de Abrigando Corazones.';
            $point['more_info'] = $profile['more_info'] ?? [];
            $point['data_notes'] = $profile['data_notes'] ?? [];
            $point['source_labels'] = $profile['source_labels'] ?? ['Datos de campaña'];
            $point['social'] = $profile['social'] ?? [];
            $point['verification_label'] = $profile['verification_label'] ?? 'Información de campaña en revisión';
            $point['campaign_relation_label'] = $profile['campaign_relation_label'] ?? 'Relación con campaña en validación';
            $point['accepted_items'] = $profile['accepted_items'] ?? [
                'Ropa de abrigo limpia y seca',
                'Frazadas, mantas y accesorios de invierno',
                'Calzado en buen estado',
            ];
            $point['accepted_items_label'] = implode(', ', $point['accepted_items']) . '.';
            $point['seo_description'] = $profile['seo_description'] ?? null;
            $point['gallery'] = array_values(array_filter([
                $point['photo'] ? ['src' => $point['photo'], 'alt' => $point['photo_alt'], 'caption' => 'Foto principal'] : null,
                ['src' => BASE_URL . 'images/logo_heart.webp', 'alt' => 'Abrigando Corazones', 'caption' => 'Campana'],
                ['src' => BASE_URL . 'images/ayuda-ropa.svg', 'alt' => 'Donacion de ropa de abrigo', 'caption' => 'Que donar'],
            ]));
            $point['search_blob'] = $this->normalizeSearchText(implode(' ', array_filter([
                $point['name'],
                $point['short_name'],
                $point['display_name'],
                $point['place_type'],
                $point['address'],
                $point['reference'],
                $point['district'],
                $point['reception_days_label'],
                $point['reception_schedule_label'],
            ])));
            $point['filter_tags'] = $this->buildPointFilterTags($point);
            $point['location_hint'] = $point['coordinates_verified'] ? null : 'Ubicación del mapa por confirmar';
            $point['fallback_visual'] = !$point['photo'] && ($point['maps_url'] || $point['map_query']) ? 'map' : (!$point['photo'] ? 'institutional' : null);

            return $point;
        }, $points);
    }

    private function buildGoogleMapsSearchUrl(string $query): string
    {
        return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($query);
    }

    private function getCollectionPointProfiles(): array
    {
        return [
            'obra-51-cafe' => [
                'social' => [
                    ['label' => 'Instagram', 'href' => 'https://www.instagram.com/obra51_coffee', 'handle' => '@obra51_coffee', 'icon' => 'instagram'],
                    ['label' => 'Facebook', 'href' => 'https://www.facebook.com/search/top?q=Obra%2051', 'handle' => 'Obra 51', 'icon' => 'facebook'],
                ],
                'display_name' => 'Obra 51 Café',
                'place_type' => 'Caféteria / espacio comunitario',
                'verification_label' => 'Confirmado por publicaciones de campaña',
                'campaign_relation_label' => 'Primer punto difundido públicamente por CPJ Tacna',
                'detail_intro' => 'Café local y espacio comunitario que se sumó a Abrigando Corazones como punto de recepción cerca de la UNJBG.',
                'about' => 'Obra 51 Café es un establecimiento tacneño ubicado cerca de la UNJBG que se sumó públicamente como uno de los primeros puntos de acopio de Abrigando Corazones. Su ubicación en el eje Jorge Basadre-Humboldt lo convierte en una alternativa práctica para estudiantes, vecinos y personas que transitan por la zona.',
                'more_info' => [
                    'Funciona como punto de recepción para donantes que se mueven por el entorno universitario.',
                    'El horario de recepción públicado para campaña es de lunes a sábado, de 4:00 p. m. a 10:00 p. m.',
                    'No se publica propietario o fundador porque no existe una fuente pública suficientemente sólida.',
                ],
                'data_notes' => ['Coordenada exacta pendiente de confirmación; se muestra ruta por búsqueda de Google Maps.'],
                'source_labels' => ['CPJ Tacna', 'Instagram/Facebook del local'],
            ],
            'fresh-light' => [
                'display_name' => 'Fresh Light',
                'place_type' => 'Establecimiento comercial',
                'verification_label' => 'Confirmado por publicaciones de campaña',
                'campaign_relation_label' => 'Incluido en la red pública de puntos difundida por CPJ',
                'detail_intro' => 'Punto de recepción ubicado frente a la Municipalidad Distrital de Alto de la Alianza.',
                'about' => 'Fresh Light funciona como punto de recepción en Alto de la Alianza, frente a la municipalidad distrital. Su ubicación permite acercar la campaña a vecinos del sector y facilita la entrega de prendas durante el horario definido para Abrigando Corazones.',
                'more_info' => [
                    'Es útil para donantes del distrito Alto de la Alianza y zonas cercanas.',
                    'El horario de recepción de campaña es de lunes a sábado, de 8:30 a. m. a 5:00 p. m.',
                    'No se publica historia empresarial ni propietario porque no hay fuente pública inequévoca.',
                ],
                'data_notes' => ['Coordenada del pin en validación editorial.'],
                'source_labels' => ['CPJ Tacna', 'Material de campaña'],
            ],
            'la-paleteria' => [
                'display_name' => 'La Paleteria Tacna',
                'place_type' => 'Heladería / paletería',
                'verification_label' => 'Dirección y presencia pública verificables',
                'campaign_relation_label' => 'Confirmado por publicaciones de campaña',
                'detail_intro' => 'Heladería céntrica ubicada en Calle Ayacucho 94 que recibe donaciones todos los días.',
                'about' => 'La Paleteria Tacna es una heladeria ubicada en Calle Ayacucho 94, en una zona céntrica de la ciudad. Además de recibir donaciones de Abrigando Corazones, el establecimiento ha aparecido públicamente en otras acciones sólidarias locales, reforzando su perfil como espacio abierto a iniciativas comunitarias.',
                'more_info' => [
                    'Su ubicación céntrica facilita entregas rápidas para personas que transitan por el centro de Tacna.',
                    'Recibe donaciones todos los días de 1:00 p. m. a 6:30 p. m.',
                    'El dato de fundador o propietario queda fuera hasta tener una fuente oficial confiable.',
                ],
                'source_labels' => ['Facebook oficial', 'CPJ Tacna', 'Directorios públicos'],
            ],
            'max-cafe' => [
                'display_name' => 'Max Café',
                'place_type' => 'Caféteria / restaurante-cafe',
                'verification_label' => 'Dirección de campaña con observación',
                'campaign_relation_label' => 'Confirmado por publicaciones de campaña',
                'detail_intro' => 'Marca de cafetería difundida como punto de acopio en Av. Bolognesi, con dirección a reconfirmar.',
                'about' => 'Max Café es una marca de cafetería con presencia pública en Tacna y canales digitales propios. Para Abrigando Corazones fue difundido como punto de acopio en la avenida Bolognesi; como existe una diferencia entre la dirección de campaña y una dirección más reciente publicada por la marca, conviene confirmar el local exacto antes de fijar el pin como definitivo.',
                'more_info' => [
                    'La campaña difundió Av. Bolognesi 392 como referencia del punto.',
                    'Publicaciones recientes de la marca mencionan Av. Bolognesi 78; por eso la dirección requiere reconfirmación.',
                    'El horario de recepción de campaña no debe confundirse con el horario comercial del negocio.',
                ],
                'data_notes' => ['Discrepancia editorial: campaña 392 vs. publicaciones de marca 78.'],
                'source_labels' => ['Sitio oficial Max Café', 'Redes oficiales', 'CPJ Tacna'],
            ],
            'trascender' => [
                'social' => [
                    ['label' => 'Instagram', 'href' => 'https://www.instagram.com/trascendercentropicologico', 'handle' => '@trascendercentropicologico', 'icon' => 'instagram'],
                    ['label' => 'Facebook', 'href' => 'https://www.facebook.com/search/top?q=Centro%20Psicol%C3%B3gico%20Trascender', 'handle' => 'Centro Psicologico Trascender', 'icon' => 'facebook'],
                ],
                'display_name' => 'Centro Psicológico Trascender',
                'place_type' => 'Centro de atención psicológica',
                'verification_label' => 'Dirección pública localizada',
                'campaign_relation_label' => 'Confirmado por publicaciones de campaña',
                'detail_intro' => 'Centro psicológico ubicado en Av. Manuel A. Odria 904 que habilita recepción en horario partido.',
                'about' => 'Trascender es un centro psicológico de Tacna orientado al bienestar emocional, atención psicológica y actividades formativas. Su sede de Av. Manuel A. Odría 904 participa como punto de recepción de Abrigando Corazones, permitiendo que pacientes, familias y vecinos del sector puedan acercar sus donaciones.',
                'more_info' => [
                    'El horario de recepción se organiza de lunes a viernes en mañana y tarde, y sábado por la mañana.',
                    'La información pública vincula profesionales al centro, pero no se afirma propiedad legal sin fuente registral.',
                    'El punto es útil para donantes que se mueven por el eje institucional de Av. Manuel A. Odria.',
                ],
                'source_labels' => ['Instagram/Facebook del centro', 'Directorios públicos', 'CPJ Tacna'],
            ],
            'academia-izy' => [
                'display_name' => 'IZI Academy',
                'place_type' => 'Academia preuniversitaria',
                'verification_label' => 'Dirección pública encontrada; relación de campaña interna',
                'campaign_relation_label' => 'Incorporación reportada por información de campaña',
                'detail_intro' => 'Academia preuniversitaria ubicada en Calle Bolívar 168, integrada como punto adicional de acopio.',
                'about' => 'IZI Academy es una academia preuniversitaria ubicada en Calle Bolívar 168, en el centro de Tacna. Su actividad se enfoca en preparación académica y acompañamiento a postulantes; dentro de la red informada por la campaña, el local funciona como un punto adicional para acercar donaciones.',
                'more_info' => [
                    'Es una alternativa práctica para estudiantes, familias y personas que transitan por el centro.',
                    'La relación con Abrigando Corazones proviene de información interna de campaña aportada por el usuario.',
                    'No se publica fundador ni propietario por falta de verificación pública suficiente.',
                ],
                'data_notes' => ['Imagen real pendiente; se usa fallback institucional hasta contar con foto verificable.'],
                'source_labels' => ['Facebook/Instagram del local', 'Datos internos de campaña'],
            ],
            'bodylife-gimnasio' => [
                'display_name' => 'Body Life Gimnasio & Fitness Club Tacna',
                'place_type' => 'Gimnasio / fitness club',
                'verification_label' => 'Dirección pública encontrada; horario de acopio pendiente',
                'campaign_relation_label' => 'Incorporación reportada por información de campaña',
                'detail_intro' => 'Gimnasio céntrico de Tacna integrado como espacio adicional para recepción de donaciones de lunes a domingo.',
                'about' => 'Body Life es un gimnasio y fitness club ubicado en Calle Deustua 312, en una zona céntrica de Tacna. Cuenta con presencia digital activa y una oferta de entrenamiento amplia; dentro de la red comunicada por la campaña, funciona como un espacio adicional para facilitar el acopio, sujeto al horario específico de recepción que debe confirmarse por separado.',
                'more_info' => [
                    'No se usa el horario comercial del gimnasio como horario de recepción de donaciones.',
                    'El punto es útil para personas que circulan por el centro y usuarios del gimnasio.',
                    'No se afirma propiedad legal; menciones públicas a administradores no equivalen a titularidad.',
                ],
                'data_notes' => ['Horario de recepción actualizado: lunes a sábado de 5:00 a. m. a 10:00 p. m. y domingos de 8:00 a. m. a 4:00 p. m.'],
                'source_labels' => ['Sitio oficial', 'Redes oficiales', 'Datos internos de campaña'],
            ],
            'guadalupe-sur-textil' => [
                'display_name' => 'Confecciones Guadalupe Sur Textil',
                'place_type' => 'Emprendimiento textil',
                'verification_label' => 'Dirección pública y rubro local identificados',
                'campaign_relation_label' => 'Incorporación reportada por información de campaña',
                'detail_intro' => 'Emprendimiento textil de Gregorio Albarracín que se integra como punto barrial de acopio.',
                'about' => 'Guadalupe Sur Textil es un emprendimiento tacneño dedicado a la confección de buzos, uniformes escolares y prendas personalizadas, con más de 16 años de experiencia comunicada públicamente. Carmen Rosa Chipana aparece como representante del negocio en espacios de emprendimiento local; el establecimiento se integra a la red informada por Abrigando Corazones en Gregorio Albarracín.',
                'more_info' => [
                    'Aporta cobertura territorial en Gregorio Albarracín, fuera del centro de Tacna.',
                    'Su rubro textil conecta naturalmente con una campaña de abrigo y prendas de invierno.',
                    'Se evita afirmar propietaria legal sin fuente registral u oficial.',
                ],
                'source_labels' => ['Instagram/Facebook del negocio', 'Publicaciones de emprendimiento local', 'Datos internos de campaña'],
            ],
            'semillero-de-campeones' => [
                'social' => [
                    ['label' => 'Instagram', 'href' => 'https://www.instagram.com/fundacionsemillerodecampeones', 'handle' => '@fundacionsemillerodecampeones', 'icon' => 'instagram'],
                ],
                'display_name' => 'Fundación Semillero de Campeones',
                'place_type' => 'Fundación deportiva / espacio comunitario',
                'verification_label' => 'Inscrito por la propia organización',
                'campaign_relation_label' => 'Se inscribió como punto de acopio de la campaña',
                'detail_intro' => 'Fundación deportiva que habilitó el campo deportivo Los Próceres como punto de recepción de donaciones.',
                'about' => 'La Fundación Semillero de Campeones se sumó a Abrigando Corazones inscribiéndose como punto de acopio a través del formulario oficial de la campaña. Su sede es el campo deportivo Los Próceres, en Tacna.',
                'more_info' => [
                    'El horario de recepción aún no fue informado: conviene coordinar antes de acercarse.',
                    'La ubicación indicada por la organización es el campo deportivo Los Próceres.',
                ],
                'data_notes' => ['Dias y horario pendientes de confirmación por la organización.'],
                'source_labels' => ['Formulario de apoyo de la campaña', 'Instagram de la organización'],
            ],
        ];
    }

    private function normalizeSlug(string $value): string
    {
        $value = mb_strtolower($value, 'UTF-8');
        $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $normalized = $normalized !== false ? $normalized : $value;
        $normalized = preg_replace('/[^a-z0-9]+/', '-', $normalized) ?? '';

        return trim($normalized, '-');
    }

    private function buildPointFilterTags(array $point): array
    {
        $tags = ['all', 'status-' . strtolower($point['status'])];

        if (($point['reception_days_label'] ?? '') === 'Lunes a sábado') {
            $tags[] = 'monday-saturday';
        }

        if (($point['reception_days_label'] ?? '') === 'Todos los días') {
            $tags[] = 'everyday';
        }

        if (empty($point['reception_schedule']) || ($point['reception_schedule_label'] ?? '') === 'Horario de acopio por confirmar') {
            $tags[] = 'schedule-pending';
        }

        if (!empty($point['district_slug'])) {
            $tags[] = 'district-' . $point['district_slug'];
        }

        return array_values(array_unique($tags));
    }

    private function computeCollectionStatus(array $point, \DateTimeImmutable $now): array
    {
        if (empty($point['reception_schedule']) || empty($point['reception_days'])) {
            return [
                'state' => 'pending',
                'label' => 'HORARIO DE ACOPIO POR CONFIRMAR',
                'detail' => 'Consulta antes de acercarte.',
            ];
        }

        $dayKey = strtolower($now->format('l'));
        $todayWindows = $this->getPointScheduleForDay($point, $dayKey);
        $currentTime = $now->format('H:i');

        foreach ($todayWindows as $window) {
            if ($currentTime >= $window['start'] && $currentTime < $window['end']) {
                return [
                    'state' => 'open',
                    'label' => 'ABIERTO PARA RECIBIR DONACIONES',
                    'detail' => 'Cierra a las ' . $this->formatTimeLabel($window['end']) . '.',
                ];
            }
        }

        foreach ($todayWindows as $window) {
            if ($currentTime < $window['start']) {
                return [
                    'state' => 'closed',
                    'label' => 'CERRADO POR AHORA',
                    'detail' => 'Abre hoy a las ' . $this->formatTimeLabel($window['start']) . '.',
                ];
            }
        }

        for ($offset = 1; $offset <= 7; $offset++) {
            $candidate = $now->modify('+' . $offset . ' day');
            $candidateDay = strtolower($candidate->format('l'));
            $windows = $this->getPointScheduleForDay($point, $candidateDay);
            if ($windows === []) {
                continue;
            }

            return [
                'state' => 'closed',
                'label' => 'CERRADO POR AHORA',
                'detail' => ($offset === 1 ? 'Abre mañana a las ' : 'Vuelve el ' . $this->getSpanishDayName($candidateDay) . ' a las ') . $this->formatTimeLabel($windows[0]['start']) . '.',
            ];
        }

        return [
            'state' => 'closed',
            'label' => 'CERRADO POR AHORA',
            'detail' => 'Revisa los días de recepción antes de acercarte.',
        ];
    }

    private function getPointScheduleForDay(array $point, string $dayKey): array
    {
        if (!in_array($dayKey, $point['reception_days'] ?? [], true)) {
            return [];
        }

        $schedule = $point['reception_schedule'][$dayKey] ?? $point['reception_schedule']['default'] ?? [];

        return is_array($schedule) ? $schedule : [];
    }

    private function formatTimeLabel(string $time): string
    {
        [$hour, $minute] = array_pad(explode(':', $time), 2, '00');
        $hourInt = (int) $hour;
        $minuteInt = (int) $minute;
        $period = $hourInt >= 12 ? 'p. m.' : 'a. m.';
        $displayHour = $hourInt % 12;
        if ($displayHour === 0) {
            $displayHour = 12;
        }

        return sprintf('%d:%02d %s', $displayHour, $minuteInt, $period);
    }

    private function getSpanishDayName(string $dayKey): string
    {
        return match ($dayKey) {
            'monday' => 'lunes',
            'tuesday' => 'martes',
            'wednesday' => 'miércoles',
            'thursday' => 'jueves',
            'friday' => 'viernes',
            'saturday' => 'sábado',
            'sunday' => 'domingo',
            default => 'próximo día disponible',
        };
    }

    private function baseData(): array
    {
        $profiles = require dirname(__DIR__) . '/config/organization_profiles.php';
        $logoBase = BASE_URL . 'images/abrigando/organizations/logos_abrigando_corazones/public/images/abrigando/organizations/';
        $newsroom = $this->getNewsroom();

        $navigation = [
            ['key' => 'inicio', 'label' => 'Inicio', 'href' => BASE_URL],
            ['key' => 'como-ayudar', 'label' => '¿Cómo ayudar?', 'href' => BASE_URL . 'como-ayudar'],
            ['key' => 'puntos-de-acopio', 'label' => 'Puntos de acopio', 'href' => BASE_URL . 'puntos-de-acopio'],
            ['key' => 'aliados', 'label' => 'Aliados', 'href' => BASE_URL . 'aliados'],
            ['key' => 'impacto', 'label' => 'Impacto', 'href' => BASE_URL . 'impacto'],
            ['key' => 'convocatoria-artistica', 'label' => 'Convocatoria', 'href' => BASE_URL . 'convocatoria-artistica'],
            ['key' => 'noticias', 'label' => 'Noticias', 'href' => BASE_URL . 'noticias'],
            ['key' => 'contacto', 'label' => 'Contacto', 'href' => BASE_URL . 'contacto'],
        ];

        $heroMetrics = [
            ['icon' => 'users', 'label' => '+20 ORGANIZACIONES UNIDAS'],
            ['icon' => 'target', 'label' => 'CAMPAÑA REGIONAL'],
            ['icon' => 'pin', 'label' => 'TACNA'],
        ];

        $stats = [
            ['icon' => 'coat', 'value' => '6,350+', 'label' => 'prendas recolectadas'],
            ['icon' => 'house', 'value' => '1,250+', 'label' => 'familias beneficiadas'],
            ['icon' => 'people', 'value' => '160+', 'label' => 'voluntarios activos'],
            ['icon' => 'pin', 'value' => '12', 'label' => 'puntos de acopio'],
            ['icon' => 'heart', 'value' => '20+', 'label' => 'organizaciones aliadas'],
        ];

        $homeHelp = [
            [
                'icon' => BASE_URL . 'images/ayuda-ropa.svg',
                'title' => 'DONA ROPA',
                'text' => 'Casacas, frazadas, mantas, chompas, pantalones, zapatillas y más en buen estado.',
                'button' => 'VER QUÉ DONAR',
                'href' => BASE_URL . 'como-ayudar#donar-ropa',
                'tone' => 'peach',
            ],
            [
                'icon' => BASE_URL . 'images/ayuda-voluntario.svg',
                'title' => 'SÉ VOLUNTARIO',
                'text' => 'Apoya en la clasificación, campañas, difusión y entrega de donaciones.',
                'button' => 'QUIERO SUMARME',
                'href' => BASE_URL . 'como-ayudar#voluntariado',
                'tone' => 'mint',
            ],
            [
                'icon' => BASE_URL . 'images/ayuda-acopio.svg',
                'title' => 'ABRE UN PUNTO DE ACOPIO',
                'text' => 'Tu negocio puede convertirse en un centro de recepción.',
                'button' => 'MÁS INFORMACIÓN',
                'href' => BASE_URL . 'como-ayudar#punto-de-acopio',
                'tone' => 'ice',
            ],
        ];

        $points = $this->buildCollectionPoints();
        $activePoints = array_values(array_filter($points, static fn(array $point): bool => ($point['status'] ?? '') === 'ACTIVE'));
        $featuredPoints = array_values(array_filter($activePoints, static fn(array $point): bool => !empty($point['is_featured'])));
        $districtCount = count(array_unique(array_map(static fn(array $point): string => $point['district'], $activePoints)));
        $stats[3]['value'] = (string) count($activePoints);

        $organizationRows = [
            ['slug' => 'cruz-roja-peruana-tacna', 'name' => 'Cruz Roja Peruana', 'subtitle' => 'Filial Tacna', 'category' => 'sociales', 'logo_file' => 'cruz-roja-peruana-tacna.webp', 'logo_alt' => 'Logo de Cruz Roja Peruana', 'logo_variant' => 'portrait', 'placeholder_icon' => 'heart-handshake', 'home_featured' => false, 'filter_tags' => ['sociales', 'institucionales']],
            ['slug' => 'cpj-tacna', 'name' => 'Consejo Provincial de la Juventud de Tacna', 'subtitle' => 'CPJ Tacna', 'category' => 'sociales', 'logo_file' => 'cpj-tacna.webp', 'logo_alt' => 'Logo del Consejo Provincial de la Juventud de Tacna', 'logo_variant' => 'portrait', 'placeholder_icon' => 'users', 'home_featured' => false, 'filter_tags' => ['sociales', 'institucionales', 'juveniles']],
            ['slug' => 'rua-unjbg', 'name' => 'RUA Perú UNJBG', 'subtitle' => 'Tacna', 'category' => 'universitarias', 'logo_file' => null, 'logo_alt' => 'Logo de RUA Perú UNJBG', 'logo_variant' => 'standard', 'placeholder_icon' => 'building', 'home_featured' => false, 'filter_tags' => ['universitarias', 'ambientales', 'juveniles']],
            ['slug' => 'rua-upt', 'name' => 'RUA Perú', 'subtitle' => 'Nodo UPT Tacna', 'category' => 'universitarias', 'logo_file' => 'rua-upt.webp', 'logo_alt' => 'Logo de RUA Perú Nodo UPT Tacna', 'logo_variant' => 'wide', 'placeholder_icon' => 'building', 'home_featured' => true, 'filter_tags' => ['universitarias', 'ambientales', 'juveniles'], 'support_type' => 'Difusión y comunicación'],
            ['slug' => 'funt-unjbg', 'name' => 'FUNT', 'subtitle' => 'Federación Universitaria de Tacna / UNJBG', 'category' => 'universitarias', 'logo_file' => 'funt-unjbg.webp', 'logo_alt' => 'Logo de FUNT', 'logo_variant' => 'dark wide', 'placeholder_icon' => 'building', 'home_featured' => true, 'filter_tags' => ['universitarias', 'juveniles']],
            ['slug' => 'manos-en-accion-tacna', 'name' => 'Manos en Acción Tacna', 'subtitle' => '', 'category' => 'voluntariado', 'logo_file' => 'manos-en-accion-tacna.webp', 'logo_alt' => 'Logo de Manos en Acción Tacna', 'logo_variant' => 'standard', 'placeholder_icon' => 'users', 'home_featured' => true, 'filter_tags' => ['voluntariado', 'sociales']],
            ['slug' => 'fuerza-upt', 'name' => 'Fuerza UPT', 'subtitle' => '', 'category' => 'universitarias', 'logo_file' => 'fuerza-upt.webp', 'logo_alt' => 'Logo de Fuerza UPT', 'logo_variant' => 'portrait', 'placeholder_icon' => 'building', 'home_featured' => true, 'filter_tags' => ['universitarias', 'juveniles']],
            ['slug' => 'redmun-tacna', 'name' => 'Red Mundial de Jóvenes Políticos', 'subtitle' => 'REDMUN Tacna', 'category' => 'sociales', 'logo_file' => 'red-mun-tacna.webp', 'logo_alt' => 'Logo de REDMUN Tacna', 'logo_variant' => 'portrait', 'placeholder_icon' => 'users', 'home_featured' => true, 'filter_tags' => ['sociales', 'juveniles']],
            ['slug' => 'recuperando-valores-tacna', 'name' => 'Recuperando Valores Tacna', 'subtitle' => '', 'category' => 'voluntariado', 'logo_file' => 'recuperando-valores-tacna.webp', 'logo_alt' => 'Logo de Recuperando Valores Tacna', 'logo_variant' => 'wide', 'placeholder_icon' => 'heart-handshake', 'home_featured' => false, 'filter_tags' => ['voluntariado', 'sociales']],
            ['slug' => 'hukap', 'name' => 'Comunidad Universitaria HUKAP', 'subtitle' => '', 'category' => 'voluntariado', 'logo_file' => 'hukap.webp', 'logo_alt' => 'Logo de HUKAP', 'logo_variant' => 'dark', 'placeholder_icon' => 'building', 'home_featured' => false, 'filter_tags' => ['voluntariado', 'universitarias', 'juveniles']],
            ['slug' => 'make-sense', 'name' => 'makesense', 'subtitle' => '', 'category' => 'empresariales', 'logo_file' => 'make-sense.webp', 'logo_alt' => 'Logo de makesense', 'logo_variant' => 'ultra-wide', 'placeholder_icon' => 'heart-handshake', 'home_featured' => false, 'filter_tags' => ['empresariales', 'sociales']],
            ['slug' => 'genera-verde-huayna', 'name' => 'Genera Verde Huayna', 'subtitle' => 'Tacna, Perú', 'category' => 'sociales', 'logo_file' => 'genera-verde-huayna.webp', 'logo_alt' => 'Logo de Genera Verde Huayna', 'logo_variant' => 'portrait', 'placeholder_icon' => 'users', 'home_featured' => true, 'filter_tags' => ['sociales', 'ambientales', 'voluntariado']],
            ['slug' => 'tacclaun-voluntariado', 'name' => 'TACCLAUN Voluntariado', 'subtitle' => '', 'category' => 'voluntariado', 'logo_file' => 'tacclaun-voluntariado.webp', 'logo_alt' => 'Logo de TACCLAUN Voluntariado', 'logo_variant' => 'portrait', 'placeholder_icon' => 'users', 'home_featured' => false, 'filter_tags' => ['voluntariado', 'sociales']],
            ['slug' => 'reu-upt', 'name' => 'REU UPT', 'subtitle' => '', 'category' => 'universitarias', 'logo_file' => 'reu-upt.webp', 'logo_alt' => 'Logo de REU UPT', 'logo_variant' => 'portrait', 'placeholder_icon' => 'building', 'home_featured' => true, 'filter_tags' => ['universitarias']],
            ['slug' => 'donalegria', 'name' => 'DonAlegría', 'subtitle' => 'Fundación', 'category' => 'sociales', 'logo_file' => 'donalegria.webp', 'logo_alt' => 'Logo de DonAlegría', 'logo_variant' => 'wide', 'placeholder_icon' => 'heart-handshake', 'home_featured' => true, 'filter_tags' => ['sociales']],
            ['slug' => 'bodylife-gimnasio-tacna', 'name' => 'BODYLIFE Gimnasio Tacna', 'subtitle' => 'Gimnasio & Fitness Club', 'category' => 'empresariales', 'logo_file' => 'bodylife-gimnasio-tacna.webp', 'logo_alt' => 'Logo de BODYLIFE Gimnasio Tacna', 'logo_variant' => 'dark ultra-wide', 'placeholder_icon' => 'building', 'home_featured' => true, 'filter_tags' => ['empresariales'], 'support_type' => 'Punto de acopio', 'support_note' => 'Recibe donaciones en su sede de Calle Deusto 312.'],
            ['slug' => 'fogos', 'name' => 'FOGO\'S', 'subtitle' => 'Empresa / Negocio aliado', 'category' => 'empresariales', 'logo_file' => 'fogos.webp', 'logo_alt' => 'Logo de FOGO\'S', 'logo_variant' => 'wide', 'placeholder_icon' => 'handshake', 'home_featured' => true, 'filter_tags' => ['empresariales'], 'support_type' => 'Apoyo logístico', 'support_note' => 'Brindó el espacio para el armado de las cajas de donación.'],
            ['slug' => 'grupo-capsu', 'name' => 'Grupo CAPSU', 'subtitle' => '', 'category' => 'universitarias', 'logo_file' => 'grupo-capsu.webp', 'logo_alt' => 'Logo de Grupo CAPSU', 'logo_variant' => 'dark ultra-wide', 'placeholder_icon' => 'building', 'home_featured' => false, 'filter_tags' => ['universitarias']],
            ['slug' => 'cabcor', 'name' => 'CABCOR', 'subtitle' => '', 'category' => 'universitarias', 'logo_file' => 'cabcor.webp', 'logo_alt' => 'Logo de CABCOR', 'logo_variant' => 'portrait', 'placeholder_icon' => 'building', 'home_featured' => false, 'filter_tags' => ['universitarias']],
            // Organizaciones sumadas desde el formulario de apoyo de la campaña.
            // Aún sin logo propio: la ficha usa el icono de respaldo y el perfil
            // queda marcado como pendiente de verificación.
            ['slug' => 'mirror-films', 'name' => 'Mirror Films', 'subtitle' => 'Producción audiovisual', 'category' => 'empresariales', 'logo_file' => null, 'logo_alt' => 'Logo de Mirror Films', 'logo_variant' => 'standard', 'placeholder_icon' => 'megaphone', 'home_featured' => false, 'filter_tags' => ['empresariales'], 'support_type' => 'Difusión y comunicación'],
            ['slug' => 'ma-kinetic', 'name' => 'MA Kinetic', 'subtitle' => 'Tacna', 'category' => 'empresariales', 'logo_file' => null, 'logo_alt' => 'Logo de MA Kinetic', 'logo_variant' => 'standard', 'placeholder_icon' => 'megaphone', 'home_featured' => false, 'filter_tags' => ['empresariales'], 'support_type' => 'Difusión y comunicación'],
            ['slug' => 'pocatino', 'name' => 'Pocatino', 'subtitle' => 'Negocio aliado', 'category' => 'empresariales', 'logo_file' => null, 'logo_alt' => 'Logo de Pocatino', 'logo_variant' => 'standard', 'placeholder_icon' => 'handshake', 'home_featured' => false, 'filter_tags' => ['empresariales'], 'support_type' => 'Difusión y comunicación'],
            ['slug' => 'presto', 'name' => 'Presto', 'subtitle' => 'Negocio aliado', 'category' => 'empresariales', 'logo_file' => null, 'logo_alt' => 'Logo de Presto', 'logo_variant' => 'standard', 'placeholder_icon' => 'handshake', 'home_featured' => false, 'filter_tags' => ['empresariales'], 'support_type' => 'Difusión y comunicación'],
            ['slug' => 'pollo-pechugon', 'name' => 'Pollo Pechugón', 'subtitle' => 'Negocio aliado', 'category' => 'empresariales', 'logo_file' => null, 'logo_alt' => 'Logo de Pollo Pechugón', 'logo_variant' => 'standard', 'placeholder_icon' => 'handshake', 'home_featured' => false, 'filter_tags' => ['empresariales'], 'support_type' => 'Difusión y comunicación'],
            ['slug' => 'food-and-fruits', 'name' => 'Food & Fruits', 'subtitle' => 'Negocio aliado', 'category' => 'empresariales', 'logo_file' => null, 'logo_alt' => 'Logo de Food & Fruits', 'logo_variant' => 'standard', 'placeholder_icon' => 'handshake', 'home_featured' => false, 'filter_tags' => ['empresariales'], 'support_type' => 'Difusión y comunicación'],
            ['slug' => 'club-leo-san-francisco', 'name' => 'Club Leo San Francisco', 'subtitle' => 'Voluntariado juvenil', 'category' => 'voluntariado', 'logo_file' => null, 'logo_alt' => 'Logo del Club Leo San Francisco', 'logo_variant' => 'standard', 'placeholder_icon' => 'users', 'home_featured' => false, 'filter_tags' => ['voluntariado', 'sociales', 'juveniles'], 'support_type' => 'Apoyo logístico', 'support_note' => 'Aportó material para el embalaje de las donaciones.'],
            ['slug' => 'semillero-de-campeones', 'name' => 'Fundación Semillero de Campeones', 'subtitle' => 'Tacna', 'category' => 'sociales', 'logo_file' => null, 'logo_alt' => 'Logo de la Fundación Semillero de Campeones', 'logo_variant' => 'standard', 'placeholder_icon' => 'heart-handshake', 'home_featured' => false, 'filter_tags' => ['sociales', 'voluntariado'], 'support_type' => 'Punto de acopio', 'support_note' => 'Habilitó el campo deportivo Los Próceres como punto de recepción.'],
        ];

        $defaultProfile = [
            'short_name' => 'Organización aliada',
            'category_display' => 'Organización aliada',
            'description' => 'Organización aliada de Abrigando Corazones. Perfil institucional detallado pendiente de verificación.',
            'summary_short' => 'Organización aliada de Abrigando Corazones.',
            'focus' => [],
            'verification' => 'pending',
            'verification_label' => 'Perfil institucional pendiente de verificación',
            'source_label' => null,
            'status_badge' => 'PERFIL EN ACTUALIZACIÓN',
        ];

        $logoOverrides = [
            'rua-unjbg' => [
                'logo_file' => 'rua-unjbg.webp',
                'logo_alt' => 'Logo de RUA Perú Nodo UNJBG Tacna',
                'logo_variant' => 'wide',
            ],
        ];

        $hiddenOrganizationSlugs = ['rua-upt'];
        $organizationRows = array_values(array_filter(
            $organizationRows,
            static fn(array $organization): bool => !in_array($organization['slug'] ?? '', $hiddenOrganizationSlugs, true)
        ));

        $organizations = array_map(static function (array $organization) use ($profiles, $logoBase, $defaultProfile, $logoOverrides): array {
            if (isset($logoOverrides[$organization['slug']])) {
                $organization = array_merge($organization, $logoOverrides[$organization['slug']]);
            }

            if (($organization['slug'] ?? '') === 'rua-unjbg') {
                $organization['name'] = 'RUA Perú';
                $organization['subtitle'] = 'Nodo UNJBG Tacna';
                $organization['home_featured'] = true;
            }

            $profile = $profiles[$organization['slug']] ?? $defaultProfile;

            $organization['logo'] = $organization['logo_file'] ? $logoBase . $organization['logo_file'] : null;
            $organization['short_name'] = $profile['short_name'];
            $organization['category_display'] = $profile['category_display'];
            $organization['description'] = $profile['description'];
            $organization['overlay_description'] = $profile['overlay_description'] ?? $profile['description'];
            $organization['summary_short'] = $profile['summary_short'];
            $organization['focus'] = $profile['focus'];
            $organization['verification'] = $profile['verification'];
            $organization['verification_label'] = $profile['verification_label'];
            $organization['source_label'] = $profile['source_label'];
            $organization['status_badge'] = $profile['status_badge'];
            $organization['support_type'] = $organization['support_type'] ?? null;
            $organization['support_note'] = $organization['support_note'] ?? null;
            $organization['anchor'] = 'aliado-' . $organization['slug'];
            $organization['focus_slugs'] = array_map(
                static fn(string $focus): string => strtolower(str_replace([' ', '?', '?', '?', '?', '?', '?'], ['-', 'a', 'e', 'i', 'o', 'u', 'n'], $focus)),
                $organization['focus']
            );
            $organization['verification_rank'] = match ($organization['verification']) {
                'verified' => 0,
                'partial', 'verified_limited' => 1,
                default => 2,
            };

            return $organization;
        }, $organizationRows);

        // Igual que los puntos de acopio: la cifra de aliados se deriva de la
        // lista real para que no quede desfasada al sumar organizaciones.
        $stats[4]['value'] = count($organizations) . '+';

        $allyFilterLabels = [
            'all' => 'Todas',
            'voluntariado' => 'Voluntariado',
            'universitarias' => 'Universitarias',
            'sociales' => 'Sociales',
            'empresariales' => 'Empresariales',
            'ambientales' => 'Ambientales',
            'institucionales' => 'Institucionales',
            'juveniles' => 'Juveniles',
        ];

        $availableFilterCounts = ['all' => count($organizations)];
        foreach ($organizations as $organization) {
            foreach ($organization['filter_tags'] as $tag) {
                $availableFilterCounts[$tag] = ($availableFilterCounts[$tag] ?? 0) + 1;
            }
        }

        $allyFilters = [];
        foreach ($allyFilterLabels as $key => $label) {
            if (($availableFilterCounts[$key] ?? 0) > 0) {
                $allyFilters[] = ['key' => $key, 'label' => $label];
            }
        }

        $allyAreaCounts = [];
        $allyAreaLabels = [];
        foreach ($organizations as $organization) {
            foreach ($organization['focus'] as $index => $focus) {
                $slug = $organization['focus_slugs'][$index] ?? null;
                if ($slug === null) {
                    continue;
                }

                $allyAreaCounts[$slug] = ($allyAreaCounts[$slug] ?? 0) + 1;
                $allyAreaLabels[$slug] = $focus;
            }
        }

        ksort($allyAreaLabels);
        $allyAreaFilters = [];
        foreach ($allyAreaLabels as $slug => $label) {
            if (($allyAreaCounts[$slug] ?? 0) > 0) {
                $allyAreaFilters[] = [
                    'key' => $slug,
                    'label' => $label,
                    'count' => $allyAreaCounts[$slug],
                ];
            }
        }

        $homeOrganizations = array_values(array_filter(
            $organizations,
            static fn(array $organization): bool => !empty($organization['home_featured'])
        ));
        $homePriority = ['bodylife-gimnasio-tacna' => 0, 'fogos' => 1];
        usort($homeOrganizations, static function (array $left, array $right) use ($homePriority): int {
            return ($homePriority[$left['slug']] ?? 10) <=> ($homePriority[$right['slug']] ?? 10);
        });

        $testimonials = [
            ['quote' => 'Ser voluntaria me cambió la vida. Ver la alegría en los ojos de los niños cuando reciben abrigo no tiene precio.', 'name' => 'María Fernanda', 'role' => 'Voluntaria'],
            ['quote' => 'Gracias a Abrigando Corazones, mis hijos ya no pasan frío en las noches. Dios bendiga a todos los que hacen posible esta ayuda.', 'name' => 'Rosa Mamani', 'role' => 'Beneficiaria - Comunidad de Palca'],
            ['quote' => 'Como organización aliada, valoramos la transparencia y el compromiso del equipo. Juntos llegamos más lejos.', 'name' => 'Carlos Quispe', 'role' => 'Representante - CPJ Tacna'],
        ];

        return array_merge([
            'navigation' => $navigation,
            'artCall' => $this->getArtCall(),
            'heroMetrics' => $heroMetrics,
            'stats' => $stats,
            'homeHelp' => $homeHelp,
            'points' => $points,
            'homePoints' => array_slice($featuredPoints !== [] ? $featuredPoints : $activePoints, 0, 3),
            'pointsSummary' => [
                'active_count' => count($activePoints),
                'district_count' => $districtCount,
                'map_verified_count' => count(array_filter($activePoints, static fn(array $point): bool => !empty($point['coordinates_verified']))),
                'open_now_count' => count(array_filter($activePoints, static fn(array $point): bool => ($point['public_status']['state'] ?? null) === 'open')),
            ],
            'organizations' => $organizations,
            'homeOrganizations' => $homeOrganizations,
            'allyFilters' => $allyFilters,
            'allyAreaFilters' => $allyAreaFilters,
            'news' => $newsroom['newsStories'],
            'testimonials' => $testimonials,
            'contact' => [
                'phone' => '+51 921 193 346',
                'phone_link' => 'https://wa.me/51921193346',
                'email' => 'abrigandocorazones.tacna@gmail.com',
                'location' => 'Tacna, Perú',
                'socials' => [
                    ['label' => 'WhatsApp', 'href' => 'https://wa.me/51921193346', 'icon' => 'whatsapp'],
                ],
            ],
            'faqHome' => [
                ['question' => '¿Qué tipo de ropa puedo donar?', 'answer' => 'Casacas, chompas, mantas, frazadas, pantalones y zapatos de invierno en buen estado.'],
                ['question' => '¿En qué condiciones debe estar la ropa?', 'answer' => 'Las prendas deben estar limpias, secas y listas para ser usadas por otra familia.'],
                ['question' => '¿Puedo donar desde otra ciudad?', 'answer' => 'Sí. Coordinamos puntos de recepción y entregas especiales para donaciones fuera de Tacna.'],
                ['question' => '¿Cómo sé que mi donación llega a las familias?', 'answer' => 'Compartimos avances, hitos de campaña y reportes visuales de cada entrega organizada.'],
                ['question' => '¿Puedo donar en nombre de mi empresa o colegio?', 'answer' => 'Sí. También podemos ayudarte a organizar una colecta institucional.'],
                ['question' => '¿Puedo ser voluntario si no tengo experiencia?', 'answer' => 'Sí. Te guiaremos en clasificación, recepción, difusión o jornadas de campo según tu disponibilidad.'],
            ],
            'faqPoints' => [
                ['question' => '¿Cuáles son los horarios de atención?', 'answer' => 'Cada punto de acopio tiene su propio horario, visible en cada tarjeta y en Google Maps.'],
                ['question' => '¿Qué tipo de donaciones se reciben?', 'answer' => 'Recibimos ropa de abrigo en buen estado, mantas, frazadas, chompas, guantes, gorros y bufandas.'],
                ['question' => '¿Cómo se coordinan las entregas?', 'answer' => 'Nuestro equipo organiza recolecciones periódicas y coordina los traslados con aliados logísticos.'],
            ],
        ], $newsroom);
    }
}

