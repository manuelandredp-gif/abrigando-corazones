<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php
$photo = $point['photo'] ?? null;
$photoAlt = $point['photo_alt'] ?? ('Foto de ' . $point['display_name']);
$hasCoordinates = !empty($point['coordinates_verified']) && $point['latitude'] !== null && $point['longitude'] !== null;
$detailMapPayload = [
    'id' => $point['id'],
    'name' => $point['display_name'],
    'address' => $point['address'],
    'latitude' => $hasCoordinates ? $point['latitude'] : null,
    'longitude' => $hasCoordinates ? $point['longitude'] : null,
    'coordinates_verified' => $hasCoordinates,
    'maps_url' => $point['maps_url'],
];

// El controlador ya publica las fuentes de cada punto y las notas de dato pendiente,
// pero la ficha no las mostraba. Son justamente lo que hace verificable la informacion.
$sourceLabels = array_values(array_filter($point['source_labels'] ?? []));
$dataNotes = array_values(array_filter($point['data_notes'] ?? []));
$schedulePending = in_array('schedule-pending', $point['filter_tags'] ?? [], true);
?>

<?php
/*
 * Botón sticky "Cómo llegar" solo en móvil.
 * Aparece pegado al borde inferior de la pantalla mientras el usuario
 * navega la ficha, y desaparece cuando el mapa ya es visible en pantalla.
 * Se renderiza fuera del <main> para que el CSS sticky funcione
 * correctamente sin conflictos con overflow de secciones internas.
 */
?>
<div class="pickup-sticky-cta" id="pickup-sticky-cta" aria-hidden="true">
    <a href="<?= htmlspecialchars($point['maps_url']) ?>" target="_blank" rel="noopener noreferrer"
       class="btn btn--primary" id="pickup-sticky-cta-link">
        <?= site_icon('pin') ?>
        <span>Cómo llegar · <?= htmlspecialchars($point['short_name']) ?></span>
    </a>
</div>
<style>
.pickup-sticky-cta {
    display: none;
    position: fixed;
    bottom: 0; left: 0; right: 0;
    z-index: 120;
    padding: 0.75rem 1rem calc(0.75rem + env(safe-area-inset-bottom));
    background: linear-gradient(to top, var(--bg, #fff) 60%, transparent);
    pointer-events: none;
}
.pickup-sticky-cta.is-visible {
    pointer-events: auto;
}
.pickup-sticky-cta .btn {
    width: 100%;
    justify-content: center;
    gap: 0.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.18);
}
@media (max-width: 767px) {
    .pickup-sticky-cta { display: flex; }
}
</style>
<script>
(function () {
    var btn = document.getElementById('pickup-sticky-cta');
    if (!btn) return;
    var map = document.querySelector('[data-point-detail-map]');
    if (!map) { btn.classList.add('is-visible'); return; }
    var obs = new IntersectionObserver(function (entries) {
        btn.classList.toggle('is-visible', !entries[0].isIntersecting);
    }, { threshold: 0.3 });
    obs.observe(map);
})();
</script>

<main class="pickup-detail-page">
    <section class="section section--tight pickup-detail-hero">
        <div class="site-shell">
            <nav class="breadcrumb pickup-detail-breadcrumb" aria-label="Breadcrumb">
                <a href="<?= BASE_URL ?>">Inicio</a>
                <span><?= site_icon('arrow-right') ?></span>
                <a href="<?= BASE_URL ?>puntos-de-acopio">Puntos de acopio</a>
                <span><?= site_icon('arrow-right') ?></span>
                <span><?= htmlspecialchars($point['display_name']) ?></span>
            </nav>

            <div class="pickup-detail-title" data-reveal="up">
                <span class="section-kicker"><?= htmlspecialchars($point['campaign_relation_label']) ?></span>
                <h1><?= htmlspecialchars($point['display_name']) ?></h1>
                <p><?= htmlspecialchars($point['detail_intro']) ?></p>
                <div class="pickup-detail-badges" aria-label="Resumen del punto">
                    <span><?= site_icon('pin') ?> <?= htmlspecialchars($point['district']) ?></span>
                    <span><?= site_icon('building') ?> <?= htmlspecialchars($point['place_type']) ?></span>
                    <span><?= site_icon('check') ?> <?= htmlspecialchars($point['verification_label']) ?></span>
                </div>

                <?php /* Redes que el propio local declaro al inscribirse en la campana. */ ?>
                <?php if (!empty($point['social'])): ?>
                    <ul class="pickup-social" aria-label="Redes sociales del punto">
                        <?php foreach ($point['social'] as $red): ?>
                            <li>
                                <a href="<?= htmlspecialchars($red['href']) ?>" target="_blank" rel="noopener">
                                    <?= site_icon($red['icon']) ?>
                                    <span><?= htmlspecialchars($red['handle']) ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="section section--tight">
        <div class="site-shell pickup-detail-layout" data-stagger>
            <div class="pickup-detail-media" data-reveal="left">
                <?php if ($photo): ?>
                    <img src="<?= htmlspecialchars($photo) ?>" alt="<?= htmlspecialchars($photoAlt) ?>" decoding="async">
                <?php else: ?>
                    <div class="pickup-detail-fallback">
                        <span><?= site_icon('pin') ?></span>
                        <strong><?= htmlspecialchars($point['short_name']) ?></strong>
                        <p>Imagen real pendiente de verificación.</p>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="pickup-detail-panel" data-reveal="right" aria-label="Datos principales del punto">
                <div class="pickup-detail-panel__head">
                    <span class="pickup-badge pickup-badge--<?= htmlspecialchars($point['public_status']['state']) ?>"><?= htmlspecialchars($point['public_status']['label']) ?></span>
                    <strong><?= htmlspecialchars($point['public_status']['detail']) ?></strong>
                </div>

                <dl class="pickup-detail-list">
                    <div><dt>Dirección</dt><dd><?= htmlspecialchars($point['address']) ?></dd></div>
                    <div><dt>Referencia</dt><dd><?= htmlspecialchars($point['reference']) ?></dd></div>
                    <div><dt>Días</dt><dd><?= htmlspecialchars($point['reception_days_label']) ?></dd></div>
                    <div<?= $schedulePending ? ' class="pickup-detail-list__row--pending"' : '' ?>><dt>Horario</dt><dd><?= htmlspecialchars($point['reception_schedule_label']) ?></dd></div>
                    <?php if (!empty($point['collection_start_label'])): ?>
                        <div><dt>Inicio</dt><dd><?= htmlspecialchars($point['collection_start_label']) ?></dd></div>
                    <?php endif; ?>
                </dl>

                <?php /* Trazabilidad: de donde sale lo que se publica de este punto. */ ?>
                <?php if ($sourceLabels): ?>
                    <div class="pickup-detail-sources">
                        <strong>Fuentes de esta información</strong>
                        <ul>
                            <?php foreach ($sourceLabels as $sourceLabel): ?>
                                <li><?= site_icon('check') ?><span><?= htmlspecialchars($sourceLabel) ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="pickup-detail-actions">
                    <a class="btn btn--navy" href="<?= htmlspecialchars($point['maps_url']) ?>" target="_blank" rel="noopener noreferrer"><?= site_icon('pin') ?> Ver en Google Maps</a>
                    <button class="btn btn--outline" type="button" data-detail-copy-address data-address="<?= htmlspecialchars($point['address']) ?>"><?= site_icon('link') ?> Copiar dirección</button>
                    <button class="btn btn--outline" type="button" data-detail-share data-share-title="<?= htmlspecialchars($point['display_name']) ?>" data-share-text="<?= htmlspecialchars($point['display_name'] . ' - ' . $point['address']) ?>"><?= site_icon('share') ?> Compartir</button>
                    <a class="btn btn--outline" href="<?= BASE_URL ?>puntos-de-acopio">Volver a puntos</a>
                </div>
            </aside>
        </div>
    </section>

    <section class="section section--tight">
        <div class="site-shell pickup-detail-info" data-stagger>
            <article class="pickup-detail-card pickup-detail-card--story" data-reveal="up">
                <span class="section-kicker">Sobre este punto</span>
                <h2>Por qué este lugar ayuda</h2>
                <p><?= htmlspecialchars($point['about']) ?></p>
            </article>

            <article class="pickup-detail-card" data-reveal="up">
                <h2>Más información</h2>
                <ul class="pickup-detail-checklist">
                    <?php foreach ($point['more_info'] as $item): ?>
                        <li><?= site_icon('check') ?><span><?= htmlspecialchars($item) ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </article>

            <?php /*
                Antes lo aceptado (dato confirmado) y las notas de dato pendiente compartian
                una sola lista y se leian con el mismo peso. Se separan: lo confirmado arriba,
                lo pendiente en su propio bloque rotulado.
            */ ?>
            <article class="pickup-detail-card" data-reveal="up">
                <h2>Qué se recibe aquí</h2>
                <ul class="pickup-detail-checklist">
                    <?php foreach ($point['accepted_items'] as $item): ?>
                        <li><?= site_icon('coat') ?><span><?= htmlspecialchars($item) ?></span></li>
                    <?php endforeach; ?>
                </ul>

                <?php if ($dataNotes): ?>
                    <div class="pickup-detail-pending">
                        <strong><?= site_icon('help-circle') ?><span>Datos aún por confirmar</span></strong>
                        <ul>
                            <?php foreach ($dataNotes as $note): ?>
                                <li><?= htmlspecialchars($note) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </article>
        </div>
    </section>

    <section class="section section--tight">
        <div class="site-shell pickup-detail-map-grid" data-stagger>
            <?php /*
                Sin coordenada verificada el JS marca el contenedor con
                `is-map-pending` y no monta Leaflet: quedaba un rectangulo gris
                vacio de 320px sin explicacion. Se conserva el enganche del mapa
                y se anade, al lado, un marcador honesto de que el pin esta en
                validacion y de que la ruta se abre por direccion.
            */ ?>
            <div class="pickup-detail-map-card<?= $hasCoordinates ? '' : ' pickup-detail-map-card--pending' ?>" data-reveal="left">
                <div class="pickup-detail-map" data-point-detail-map aria-label="Mapa del punto de acopio"></div>
                <script type="application/json" data-point-detail-map-payload><?= json_encode($detailMapPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
                <?php if (!$hasCoordinates): ?>
                    <div class="pickup-detail-map-placeholder">
                        <span class="pickup-detail-map-placeholder__icon"><?= site_icon('pin') ?></span>
                        <strong>Pin exacto en validación</strong>
                        <p><?= htmlspecialchars($point['address']) ?></p>
                        <a class="btn btn--outline" href="<?= htmlspecialchars($point['maps_url']) ?>" target="_blank" rel="noopener noreferrer">Buscar la dirección en Google Maps</a>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="pickup-detail-route" data-reveal="right">
                <h2>Cómo llegar</h2>
                <p>Revisa la dirección, la referencia urbana y confirma el horario de recepción antes de acercarte.</p>
                <a class="btn btn--primary" href="<?= htmlspecialchars($point['maps_url']) ?>" target="_blank" rel="noopener noreferrer">Abrir ruta</a>
            </aside>
        </div>
    </section>

    <section class="section section--tight">
        <div class="site-shell">
            <h2 class="section-title section-title--left">Conoce más de este punto</h2>
            <div class="pickup-detail-gallery" data-stagger>
                <?php foreach ($point['gallery'] as $item): ?>
                    <figure data-reveal="up">
                        <img src="<?= htmlspecialchars($item['src']) ?>" alt="<?= htmlspecialchars($item['alt']) ?>" loading="lazy" decoding="async">
                        <figcaption><?= htmlspecialchars($item['caption']) ?></figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section section--soft">
        <div class="site-shell">
            <h2 class="section-title">Otros puntos de acopio</h2>
            <div class="pickup-related-grid" data-stagger>
                <?php foreach ($relatedPoints as $related): ?>
                    <article class="pickup-related-card" data-reveal="up">
                        <?php if (!empty($related['photo'])): ?>
                            <img src="<?= htmlspecialchars($related['photo']) ?>" alt="<?= htmlspecialchars($related['photo_alt']) ?>" loading="lazy" decoding="async">
                        <?php endif; ?>
                        <div>
                            <span><?= htmlspecialchars($related['district']) ?></span>
                            <h3><?= htmlspecialchars($related['display_name']) ?></h3>
                            <p><?= htmlspecialchars($related['reception_days_label']) ?> · <?= htmlspecialchars($related['reception_schedule_label']) ?></p>
                            <a href="<?= BASE_URL ?>puntos-de-acopio/<?= htmlspecialchars($related['slug']) ?>">Ver detalle <?= site_icon('arrow-right') ?></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section section--tight">
        <div class="site-shell pickup-detail-cta" data-reveal="up">
            <div>
                <span class="section-kicker">Tu donación importa</span>
                <h2>Tu abrigo puede cambiar el invierno de alguien</h2>
                <p>Prepara prendas limpias, secas y en buen estado. Cada punto activo acerca la campaña a más familias.</p>
            </div>
            <div class="pickup-detail-cta__actions">
                <a class="btn btn--primary" href="<?= BASE_URL ?>como-ayudar#donar-ropa">Quiero donar ahora</a>
                <a class="btn btn--outline" href="<?= BASE_URL ?>puntos-de-acopio">Ver más puntos</a>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
