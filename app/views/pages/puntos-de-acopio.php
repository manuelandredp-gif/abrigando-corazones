<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php
$requestScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$requestHost = $_SERVER['HTTP_HOST'] ?? parse_url(BASE_URL, PHP_URL_HOST);
$assetBase = $requestHost ? $requestScheme . '://' . $requestHost . '/' : BASE_URL;

$districts = [];
foreach ($points as $point) {
    $districts[$point['district_slug']] = $point['district'];
}

$mapPointsPayload = array_map(static function (array $point): array {
    return [
        'id' => $point['id'],
        'slug' => $point['slug'],
        'name' => $point['name'],
        'short_name' => $point['short_name'],
        'district' => $point['district'],
        'address' => $point['address'],
        'reference' => $point['reference'],
        'maps_url' => $point['maps_url'],
        'photo' => $point['photo'],
        'photo_alt' => $point['photo_alt'],
        'latitude' => $point['latitude'],
        'longitude' => $point['longitude'],
        'coordinates_verified' => $point['coordinates_verified'],
        'days_label' => $point['reception_days_label'],
        'schedule_label' => $point['reception_schedule_label'],
        'state' => $point['public_status']['state'],
        'status_label' => $point['public_status']['label'],
        'status_detail' => $point['public_status']['detail'],
    ];
}, $points);

$openNowCount = (int) ($pointsSummary['open_now_count'] ?? 0);
$donationSummary = 'Ropa en buen estado, frazadas, ropa de abrigo, calzado y mantas.';
$mapOverlay = [
    'image' => $assetBase . 'images/maps/tacna-overlay.webp',
    'bounds' => [
        [-18.065, -70.285],
        [-17.985, -70.225],
    ],
];

$renderPointFallback = static function (array $point): string {
    ob_start();
    ?>
    <div class="pickup-card__fallback pickup-card__fallback--<?= htmlspecialchars($point['fallback_visual'] ?? 'institutional') ?>" aria-hidden="true">
        <span class="pickup-card__fallback-grid"></span>
        <span class="pickup-card__fallback-route"></span>
        <span class="pickup-card__fallback-pin"><?= site_icon('pin') ?></span>
        <div class="pickup-card__fallback-copy">
            <small><?= htmlspecialchars(($point['fallback_visual'] ?? 'institutional') === 'map' ? 'Referencia de ubicación' : 'Punto de acopio') ?></small>
            <strong><?= htmlspecialchars($point['short_name']) ?></strong>
            <p><?= htmlspecialchars($point['district']) ?></p>
            <span>ABRIGANDO CORAZONES</span>
        </div>
    </div>
    <?php
    return (string) ob_get_clean();
};
?>

<main>
    <section class="hero hero--interior hero--pickup-network">
        <img src="<?= htmlspecialchars($page['hero']['image']) ?>" alt="Voluntariado y comunidades organizando abrigo en Tacna" class="hero__bg">
        <div class="hero__shade"></div>
        <div class="site-shell hero__inner hero__inner--interior">
            <div class="hero__copy">
                <span class="section-kicker">Red de solidaridad</span>
                <h1 class="hero__title">
                    <span>ENCUENTRA TU</span>
                    <span>PUNTO DE ACOPIO</span>
                </h1>
                <p class="hero__text">Explora la red activa de puntos de acopio, identifica el local físicamente, revisa horarios de recepción y encuentra la mejor ruta para entregar tu donación.</p>
                <div class="hero__buttons">
                    <a href="#red-puntos" class="btn btn--primary">VER PUNTOS</a>
                    <a href="<?= BASE_URL ?>contacto?motivo=Punto%20de%20acopio" class="btn btn--ghost-light">QUIERO ABRIR UN PUNTO</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section section--tight" id="red-puntos">
        <div class="site-shell" data-collection-points-root>
            <section class="pickup-reference-shell" data-points-directory data-stagger aria-label="Buscador, geolocalización y filtros de puntos de acopio">
                <div class="pickup-toolbar pickup-toolbar--reference" data-reveal="up">
                    <label class="pickup-toolbar__search pickup-toolbar__search--reference" aria-label="Buscar un punto, distrito o dirección">
                        <span class="pickup-toolbar__icon"><?= site_icon('search') ?></span>
                        <input type="search" placeholder="Busca un punto, distrito o dirección..." data-point-search>
                        <span class="pickup-toolbar__search-trailing"><?= site_icon('locate-fixed') ?></span>
                    </label>

                    <div class="pickup-toolbar__counter" aria-live="polite">
                        <strong data-point-count data-point-count-kpi><?= count($points) ?></strong>
                        <span data-point-count-label data-point-count-kpi-label><?= count($points) === 1 ? 'PUNTO VISIBLE' : 'PUNTOS VISIBLES' ?></span>
                    </div>

                    <button type="button" class="pickup-toolbar__clear pickup-toolbar__clear--reference" data-point-clear>
                        <span class="pickup-toolbar__clear-icon"><?= site_icon('arrow-right') ?></span>
                        <span>LIMPIAR</span>
                    </button>
                </div>

                <div class="pickup-geo-banner" data-reveal="up">
                    <div class="pickup-geo-banner__copy">
                        <span class="pickup-geo-banner__icon"><?= site_icon('pin') ?></span>
                        <div>
                            <strong>ENCONTRAR EL PUNTO MÁS CERCANO</strong>
                            <p>Tu ubicación solo se utiliza en tu navegador para mostrarte los puntos más cercanos. No la almacenamos.</p>
                        </div>
                    </div>

                    <button type="button" class="pickup-geo-banner__action" data-point-geolocate>
                        <span><?= site_icon('locate-fixed') ?></span>
                        <span>USAR MI UBICACIÓN</span>
                    </button>
                </div>

                <div class="pickup-toolbar__filters pickup-toolbar__filters--reference" role="group" data-reveal="up" aria-label="Filtros de puntos">
                    <button type="button" class="pickup-filter is-active" data-point-filter="all" aria-pressed="true"><span><?= site_icon('grid') ?></span><span>TODOS</span></button>
                    <button type="button" class="pickup-filter" data-point-filter="open-now" aria-pressed="false"><span><?= site_icon('clock') ?></span><span>ABIERTOS AHORA</span></button>
                    <button type="button" class="pickup-filter" data-point-filter="everyday" aria-pressed="false"><span><?= site_icon('calendar-days') ?></span><span>TODOS LOS DÍAS</span></button>
                    <button type="button" class="pickup-filter" data-point-filter="monday-saturday" aria-pressed="false"><span><?= site_icon('calendar-range') ?></span><span>LUNES A SÁBADO</span></button>
                    <button type="button" class="pickup-filter" data-point-filter="schedule-pending" aria-pressed="false"><span><?= site_icon('help-circle') ?></span><span>HORARIO POR CONFIRMAR</span></button>
                    <button type="button" class="pickup-filter" data-point-filter="near-me" data-point-near-filter aria-pressed="false" disabled><span><?= site_icon('locate-fixed') ?></span><span>CERCA DE MÍ</span></button>
                    <?php foreach ($districts as $districtSlug => $districtLabel): ?>
                        <button type="button" class="pickup-filter" data-point-filter="district-<?= htmlspecialchars($districtSlug) ?>" aria-pressed="false"><span><?= site_icon('pin') ?></span><span><?= htmlspecialchars(mb_strtoupper($districtLabel, 'UTF-8')) ?></span></button>
                    <?php endforeach; ?>
                </div>

                <section class="pickup-map pickup-map--reference" data-reveal="up" aria-label="Mapa interactivo de puntos de acopio">
                    <div class="pickup-map__frame pickup-map__frame--reference pickup-map__frame--live">
                        <div class="pickup-map__surface">
                            <label class="pickup-map__search-zone" aria-label="Buscar en esta zona">
                                <span><?= site_icon('search') ?></span>
                                <input type="search" placeholder="Buscar en esta zona" data-point-search-viewport>
                            </label>

                            <div class="pickup-map__floating-tools">
                                <button type="button" class="pickup-map__tool pickup-map__tool--locate" data-point-geolocate-secondary aria-label="Usar mi ubicación"><?= site_icon('locate-fixed') ?></button>
                                <button type="button" class="pickup-map__tool pickup-map__tool--layers" data-point-fit-visible aria-label="Ver todos los puntos"><?= site_icon('layers') ?></button>
                            </div>

                            <div class="pickup-map__canvas" id="collection-points-map" data-collection-points-map aria-label="Mapa interactivo con puntos de acopio"></div>
                        </div>

                        <div class="pickup-map__legend-inline" aria-label="Leyenda del mapa">
                            <span><i class="pickup-map__legend-dot pickup-map__legend-dot--user"></i>Tu ubicación</span>
                            <span><i class="pickup-map__legend-dot pickup-map__legend-dot--open"></i>Punto abierto</span>
                            <span><i class="pickup-map__legend-dot pickup-map__legend-dot--closed"></i>Punto cerrado</span>
                        </div>

                        <div class="pickup-map__privacy-band">
                            <span><?= site_icon('check') ?></span>
                            <p>Tu ubicación solo se usa en tu navegador para mostrarte los puntos más cercanos. No la almacenamos.</p>
                        </div>
                    </div>

                    <aside class="pickup-map__aside pickup-map__aside--reference">
                        <div class="pickup-sidegrid">
                            <article class="pickup-kpi pickup-kpi--navy">
                                <div class="pickup-kpi__icon"><?= site_icon('pin') ?></div>
                                <div class="pickup-kpi__body">
                                    <?php /* Cifra estatica del servidor: el contador animado no compite con el JS del directorio. */ ?>
                                    <strong data-countup><?= (int) $pointsSummary['active_count'] ?></strong>
                                    <span>PUNTOS VISIBLES</span>
                                    <p>Total de puntos de acopio mostrados en el mapa.</p>
                                </div>
                            </article>

                            <article class="pickup-kpi pickup-kpi--green">
                                <div class="pickup-kpi__icon"><?= site_icon('clock') ?></div>
                                <div class="pickup-kpi__body">
                                    <strong data-point-open-kpi><?= $openNowCount ?></strong>
                                    <span><?= $openNowCount === 1 ? 'ABIERTO AHORA' : 'ABIERTOS AHORA' ?></span>
                                    <p>Puntos disponibles según los horarios de recepción.</p>
                                </div>
                            </article>

                            <article class="pickup-info-card pickup-info-card--warm">
                                <div class="pickup-info-card__icon"><?= site_icon('locate-fixed') ?></div>
                                <div>
                                    <strong>Cerca de ti</strong>
                                    <p data-nearest-distance-label>Explora los puntos más cercanos a tu ubicación.</p>
                                </div>
                            </article>

                            <article class="pickup-info-card pickup-info-card--cool">
                                <div class="pickup-info-card__icon"><?= site_icon('coat') ?></div>
                                <div>
                                    <strong>¿Qué puedes donar?</strong>
                                    <p><?= htmlspecialchars($donationSummary) ?></p>
                                </div>
                            </article>
                        </div>

                        <div class="pickup-nearby-panel">
                            <div class="pickup-nearby-panel__head">
                                <strong>PUNTOS MÁS CERCA DE TI</strong>
                                <button type="button" class="pickup-nearby-panel__all" data-point-show-all>VER TODOS <?= site_icon('arrow-right') ?></button>
                            </div>

                            <div class="pickup-nearby-panel__empty" data-point-nearby-empty>
                                <span class="pickup-nearby-panel__empty-icon"><?= site_icon('locate-fixed') ?></span>
                                <strong>ACTIVA TU UBICACIÓN</strong>
                                <p>Te mostraremos automáticamente los puntos más próximos y la distancia hasta cada uno.</p>
                                <button type="button" class="pickup-nearby-panel__cta" data-point-geolocate-empty>USAR MI UBICACIÓN</button>
                            </div>

                            <div class="pickup-nearby pickup-nearby-panel__results" data-point-nearby hidden>
                                <ul class="pickup-nearby__list" data-point-nearby-list></ul>
                                <button type="button" class="pickup-nearby-panel__maplink" data-point-fit-visible>
                                    <span>Ver todos los puntos en el mapa</span>
                                    <span><?= site_icon('arrow-right') ?></span>
                                </button>
                            </div>
                        </div>
                    </aside>
                </section>

                <div class="pickup-map__footerband" data-reveal="up" data-point-map-help-root>
                    <div class="pickup-map__footeritem">
                        <span><?= site_icon('heart-handshake') ?></span>
                        <p>Abrigando Corazones, transformamos el invierno en esperanza.</p>
                    </div>
                    <div class="pickup-map__footeritem">
                        <span><?= site_icon('check') ?></span>
                        <p>Puntos de acopio activos de la campaña.</p>
                    </div>
                    <div class="pickup-map__footeritem pickup-map__footeritem--action">
                        <button type="button" data-point-map-help-toggle aria-expanded="false">
                            <span>¿Cómo usar el mapa?</span>
                            <span><?= site_icon('chevron-down') ?></span>
                        </button>
                    </div>
                </div>

                <div class="pickup-map__help" data-point-map-help hidden>
                    <ol>
                        <li>Busca un punto por nombre, distrito, dirección o referencia.</li>
                        <li>Activa tu ubicación si deseas calcular distancias automáticas.</li>
                        <li>Selecciona un marcador o una tarjeta para ver más detalles.</li>
                        <li>Consulta horario, dirección y el estado actual de recepción.</li>
                        <li>Abre Google Maps para llegar al punto elegido.</li>
                    </ol>
                </div>
            </section>

            <script type="application/json" data-point-map-payload><?= json_encode($mapPointsPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
            <script type="application/json" data-point-map-overlay><?= json_encode($mapOverlay, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>

            <div class="pickup-grid" data-point-grid data-stagger>
                <?php foreach ($points as $point): ?>
                    <?php
                    $photoAlt = $point['photo_alt'] ?: ('Punto de acopio ' . $point['name']);
                    $scheduleJson = htmlspecialchars(json_encode($point['reception_schedule'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                    $daysCsv = htmlspecialchars(implode(',', $point['reception_days']));
                    $tagsCsv = htmlspecialchars(implode(' ', $point['filter_tags']));
                    $hasPhoto = !empty($point['photo']);
                    $hasCoordinates = !empty($point['coordinates_verified']) && $point['latitude'] !== null && $point['longitude'] !== null;
                    $objectPosition = $point['photo_object_position'] ?? 'center center';
                    // El backend ya distingue el horario confirmado del pendiente con la etiqueta
                    // "schedule-pending". Se reutiliza aqui para que la tarjeta lo marque en vez
                    // de mostrar el dato pendiente con el mismo peso que uno verificado.
                    $schedulePending = in_array('schedule-pending', $point['filter_tags'], true);
                    ?>
                    <article
                        class="pickup-card<?= $hasCoordinates ? '' : ' pickup-card--map-pending' ?>"
                        id="pickup-<?= htmlspecialchars($point['slug']) ?>"
                        data-reveal="up"
                        data-point-card
                        tabindex="0"
                        aria-label="Ver ubicación y detalles de <?= htmlspecialchars($point['name']) ?>"
                        data-point-id="<?= (int) $point['id'] ?>"
                        data-point-slug="<?= htmlspecialchars($point['slug']) ?>"
                        data-point-name="<?= htmlspecialchars($point['name']) ?>"
                        data-point-district="<?= htmlspecialchars($point['district']) ?>"
                        data-point-reference="<?= htmlspecialchars($point['reference']) ?>"
                        data-point-searchable="<?= htmlspecialchars($point['search_blob']) ?>"
                        data-point-filter-tags="<?= $tagsCsv ?>"
                        data-point-days="<?= $daysCsv ?>"
                        data-point-schedule='<?= $scheduleJson ?>'
                        data-point-state="<?= htmlspecialchars($point['public_status']['state']) ?>"
                        data-point-address="<?= htmlspecialchars($point['address']) ?>"
                        data-point-maps="<?= htmlspecialchars($point['maps_url']) ?>"
                        data-point-latitude="<?= $hasCoordinates ? htmlspecialchars((string) $point['latitude']) : '' ?>"
                        data-point-longitude="<?= $hasCoordinates ? htmlspecialchars((string) $point['longitude']) : '' ?>"
                        data-point-coordinates-verified="<?= $hasCoordinates ? 'true' : 'false' ?>"
                        data-point-photo="<?= htmlspecialchars((string) ($point['photo'] ?? '')) ?>"
                        data-point-photo-alt="<?= htmlspecialchars($photoAlt) ?>"
                    >
                        <div class="pickup-card__media">
                            <?php if ($hasPhoto): ?>
                                <img src="<?= htmlspecialchars($point['photo']) ?>" alt="<?= htmlspecialchars($photoAlt) ?>" loading="lazy" decoding="async" style="object-position: <?= htmlspecialchars($objectPosition) ?>;">
                            <?php else: ?>
                                <?= $renderPointFallback($point) ?>
                            <?php endif; ?>
                            <div class="pickup-card__badges">
                                <span class="pickup-badge pickup-badge--<?= htmlspecialchars($point['public_status']['state']) ?>" data-point-status-badge><?= htmlspecialchars($point['public_status']['label']) ?></span>
                                <span class="pickup-badge pickup-badge--closest" data-point-nearest-badge hidden>MÁS CERCA DE TI</span>
                            </div>
                        </div>

                        <div class="pickup-card__body">
                            <div class="pickup-card__head">
                                <span class="pickup-card__district"><?= htmlspecialchars($point['district']) ?></span>
                                <h2><?= htmlspecialchars($point['name']) ?></h2>
                                <p class="pickup-card__status-detail" data-point-status-detail><?= htmlspecialchars($point['public_status']['detail']) ?></p>
                                <p class="pickup-card__distance" data-point-distance hidden></p>
                            </div>

                            <ul class="pickup-card__meta">
                                <li><span><?= site_icon('pin') ?></span><span><?= htmlspecialchars($point['address']) ?></span></li>
                                <li><span><?= site_icon('link') ?></span><span><?= htmlspecialchars($point['reference']) ?></span></li>
                                <?php if (!empty($point['location_hint'])): ?>
                                    <li class="pickup-card__meta-note"><span><?= site_icon('pin') ?></span><span><?= htmlspecialchars($point['location_hint']) ?></span></li>
                                <?php endif; ?>
                                <li><span><?= site_icon('calendar-days') ?></span><span><?= htmlspecialchars($point['reception_days_label']) ?></span></li>
                                <li<?= $schedulePending ? ' class="pickup-card__meta-note"' : '' ?>><span><?= site_icon($schedulePending ? 'help-circle' : 'clock') ?></span><span><?= htmlspecialchars($point['reception_schedule_label']) ?></span></li>
                            </ul>

                            <div class="pickup-card__actions">
                                <a href="<?= htmlspecialchars($point['maps_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn--navy pickup-card__action">VER EN GOOGLE MAPS</a>
                                <a href="<?= BASE_URL ?>puntos-de-acopio/<?= htmlspecialchars($point['slug']) ?>" class="btn btn--outline pickup-card__action">VER DETALLE</a>
                            </div>

                            <div class="pickup-card__microactions">
                                <button type="button" class="pickup-card__microbutton" data-point-copy>Copiar dirección</button>
                                <button type="button" class="pickup-card__microbutton" data-point-share>Compartir</button>
                            </div>

                            <div class="pickup-card__details" id="<?= htmlspecialchars($point['detail_id']) ?>" hidden>
                                <div class="pickup-card__details-grid">
                                    <div>
                                        <h3>Dirección y referencia</h3>
                                        <p><?= htmlspecialchars($point['address']) ?></p>
                                        <p><?= htmlspecialchars($point['reference']) ?></p>
                                    </div>
                                    <div>
                                        <h3>Recepción de campaña</h3>
                                        <p><?= htmlspecialchars($point['reception_days_label']) ?></p>
                                        <p><?= htmlspecialchars($point['reception_schedule_label']) ?></p>
                                    </div>
                                </div>

                                <?php if (!empty($point['collection_start_label'])): ?>
                                    <div class="pickup-card__note">
                                        <strong>Punto habilitado desde</strong>
                                        <span><?= htmlspecialchars($point['collection_start_label']) ?></span>
                                    </div>
                                <?php endif; ?>

                                <div class="pickup-card__note">
                                    <strong>Qué donar</strong>
                                    <span><?= htmlspecialchars($point['accepted_items_label']) ?></span>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="allies-empty pickup-empty" data-point-empty hidden>
                <div class="allies-empty__icon"><?= site_icon('search-x') ?></div>
                <h3>NO ENCONTRAMOS PUNTOS<br>CON ESA BÚSQUEDA</h3>
                <p>Prueba con otro nombre, distrito o referencia para encontrar el punto que mejor se adapte a tu ruta.</p>
            </div>
        </div>
    </section>

    <section class="section section--soft">
        <div class="site-shell">
            <div class="pickup-donation-guide" data-reveal="up">
                <div>
                    <span class="section-kicker">Qué donar</span>
                    <h2 class="section-title section-title--left">PREPARA TU DONACIÓN</h2>
                    <p>Todos los puntos reciben prendas de abrigo para la campaña. Prioriza ropa limpia, seca y en buen estado.</p>
                </div>
                <div class="pickup-donation-guide__grid" data-stagger>
                    <article data-reveal="up"><strong>CASACAS Y CHOMPAS</strong><span>Abrigo para niños, jóvenes y adultos.</span></article>
                    <article data-reveal="up"><strong>PANTALONES Y BUZOS</strong><span>Prendas cómodas y listas para usar.</span></article>
                    <article data-reveal="up"><strong>MANTAS Y FRAZADAS</strong><span>Especialmente útiles para noches de frío intenso.</span></article>
                    <article data-reveal="up"><strong>ZAPATOS Y BOTAS</strong><span>En buen estado y de preferencia por pares completos.</span></article>
                    <article data-reveal="up"><strong>GORROS Y BUFANDAS</strong><span>Accesorios térmicos para el invierno altoandino.</span></article>
                </div>
                <div class="pickup-donation-guide__note">
                    <p>Las prendas deben estar limpias, secas y en buen estado.</p>
                    <a href="<?= BASE_URL ?>como-ayudar#donar-ropa" class="btn btn--primary">VER CÓMO PREPARAR TU DONACIÓN</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="site-shell">
            <div class="business-cta business-cta--editorial" data-stagger>
                <div class="business-cta__photo" data-reveal="left">
                    <img src="<?= htmlspecialchars($assetBase) ?>images/puntos-de-acopio/obra-51-cafe.webp" alt="Punto de acopio Obra 51 Café en Tacna" loading="lazy" decoding="async">
                </div>
                <div class="business-cta__content" data-reveal="up">
                    <span class="section-kicker">Nueva sede de apoyo</span>
                    <h2>QUIERO ABRIR UN PUNTO DE ACOPIO</h2>
                    <p>Si tu negocio, institución o comunidad quiere convertirse en parte de la red, podemos ayudarte a coordinar recepción, difusión y recolección sin crear expectativas no confirmadas.</p>
                    <div class="business-cta__benefits">
                        <article><span><?= site_icon('heart') ?></span><p>Tu espacio puede acercar abrigo a más familias.</p></article>
                        <article><span><?= site_icon('megaphone') ?></span><p>La campaña puede difundir tu punto activo.</p></article>
                        <article><span><?= site_icon('users') ?></span><p>Te integras a una red organizada y actualizable.</p></article>
                        <article><span><?= site_icon('box') ?></span><p>Coordinamos la recepción según capacidad real.</p></article>
                    </div>
                </div>
                <div class="business-cta__action" data-reveal="right">
                    <a href="<?= BASE_URL ?>contacto?motivo=Punto%20de%20acopio" class="btn btn--primary">QUIERO HABILITAR UN PUNTO</a>
                    <a href="<?= BASE_URL ?>como-ayudar#punto-de-acopio" class="btn btn--outline">VER CÓMO FUNCIONA</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section section--tight">
        <div class="site-shell">
            <h2 class="section-title">PREGUNTAS FRECUENTES</h2>
            <div class="faq-stack" data-stagger>
                <?php foreach ($faqPoints as $item): ?>
                    <details class="faq-item faq-item--wide" data-reveal="up">
                        <summary><span><?= htmlspecialchars($item['question']) ?></span><span class="faq-item__chevron"><?= site_icon('chevron-down') ?></span></summary>
                        <p><?= htmlspecialchars($item['answer']) ?></p>
                    </details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
