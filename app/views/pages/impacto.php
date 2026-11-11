<?php
require __DIR__ . '/../layouts/header.php';

$requestScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$requestHost = $_SERVER['HTTP_HOST'] ?? parse_url(BASE_URL, PHP_URL_HOST);
$assetBase = $requestHost ? $requestScheme . '://' . $requestHost . '/' : BASE_URL;

$garmentsStat = $stats[0] ?? ['value' => 'En actualización', 'label' => 'prendas registradas'];
$familiesStat = $stats[1] ?? ['value' => 'En actualización', 'label' => 'familias acompañadas'];
$volunteersStat = $stats[2] ?? ['value' => 'En actualización', 'label' => 'voluntariado activo'];
$pointsStat = $stats[3] ?? ['value' => 'En actualización', 'label' => 'puntos de acopio'];
$alliesStat = $stats[4] ?? ['value' => 'En actualización', 'label' => 'organizaciones aliadas'];

$impactStories = [
    [
        'image' => $assetBase . 'images/news/mujeres-mantas-comunidad.webp',
        'alt' => 'Mujer y niña de una comunidad altoandina abrigadas frente al paisaje rural.',
        'title' => 'Familias de comunidades altoandinas',
        'text' => 'Cada jornada recuerda por qué la campaña necesita continuidad, escucha territorial y abrigo útil.',
    ],
    [
        'image' => $assetBase . 'images/news/entrega-frazadas-camion.webp',
        'alt' => 'Jornada de entrega y organización de ayuda para comunidades altoandinas.',
        'title' => 'Jornadas de clasificación y entrega',
        'text' => 'La coordinación hace que cada prenda llegue con mayor orden, trazabilidad y cercanía.',
    ],
    [
        'image' => $assetBase . 'images/news/familia-vivienda-nevada-paisaje.webp',
        'alt' => 'Familia junto a su vivienda en un paisaje andino cubierto por nieve.',
        'title' => 'Niñez y abrigo en altura',
        'text' => 'El invierno en la sierra sur exige respuestas constantes para proteger salud, descanso y dignidad.',
    ],
    [
        'image' => $assetBase . 'images/news/familia-caminando-nieve.webp',
        'alt' => 'Familia caminando por una zona altoandina durante la temporada de bajas temperaturas.',
        'title' => 'Abrigo que acompaña',
        'text' => 'No solo se entregan prendas: también se acompaña la ruta de comunidades que viven el frío con mayor intensidad.',
    ],
];

$impactMilestones = [
    [
        'number' => '01',
        'icon' => 'megaphone',
        'title' => 'Campaña activa',
        'text' => 'La convocatoria pública sigue enfocada en ropa, abrigo, voluntariado y puntos de acopio.',
    ],
    [
        'number' => '02',
        'icon' => 'pin',
        'title' => 'Red de acopio habilitada',
        'text' => $pointsStat['value'] . ' ' . $pointsStat['label'] . ' sostienen la recepción organizada de donaciones.',
    ],
    [
        'number' => '03',
        'icon' => 'users',
        'title' => 'Voluntariado articulado',
        'text' => $volunteersStat['value'] . ' ' . $volunteersStat['label'] . ' fortalecen clasificación, campañas y apoyo logístico.',
    ],
    [
        'number' => '04',
        'icon' => 'heart-handshake',
        'title' => 'Aliados en coordinación',
        'text' => $alliesStat['value'] . ' ' . $alliesStat['label'] . ' amplían capacidad de llegada y coordinación regional.',
    ],
];

$processSteps = [
    ['number' => '1', 'icon' => 'box', 'title' => 'Recojo', 'text' => 'Recibimos donaciones en puntos de acopio y jornadas coordinadas.'],
    ['number' => '2', 'icon' => 'coat', 'title' => 'Clasificación', 'text' => 'Ordenamos por tipo, estado y necesidad para asegurar utilidad real.'],
    ['number' => '3', 'icon' => 'target', 'title' => 'Priorización', 'text' => 'Identificamos destinos según vulnerabilidad, clima y contexto comunitario.'],
    ['number' => '4', 'icon' => 'truck', 'title' => 'Entrega', 'text' => 'La ayuda se canaliza con apoyo voluntario y aliados en territorio.'],
];
?>

<main>
    <section class="impact-hero">
        <img src="<?= htmlspecialchars($page['hero']['image']) ?>" alt="Voluntario abrigando a una niña en una comunidad altoandina durante la temporada de frío." class="impact-hero__image" fetchpriority="high">
        <div class="impact-hero__shade"></div>
        <div class="site-shell impact-hero__inner">
            <div class="impact-hero__copy" data-reveal="left">
                <span class="section-kicker">Transparencia en acción</span>
                <h1 class="impact-hero__title">
                    <span>CADA APORTE</span>
                    <span>DEJA UNA</span>
                    <span>HUELLA</span>
                </h1>
                <p>Aquí reunimos resultados, hitos y señales de avance para que puedas entender cómo se organiza y se extiende el impacto de la campaña.</p>
            </div>
        </div>
    </section>

    <section class="impact-stats" data-reveal="up">
        <div class="site-shell">
            <div class="impact-stats__bar" data-stagger>
                <?php foreach ($stats as $item): ?>
                    <article class="impact-stat" data-reveal="up">
                        <span class="impact-stat__icon impact-stat__icon--<?= htmlspecialchars($item['icon']) ?>"><?= site_icon($item['icon']) ?></span>
                        <div>
                            <?php /* data-countup solo anima el valor que ya viene del servidor; si el
                                     dato dice "En actualizacion" el contador se salta la cifra. */ ?>
                            <strong data-countup><?= htmlspecialchars($item['value']) ?></strong>
                            <span><?= htmlspecialchars($item['label']) ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section impact-overview">
        <div class="site-shell">
            <div class="impact-overview__grid" data-stagger>
                <article class="impact-card impact-card--goal" data-reveal="left">
                    <span class="section-kicker">Meta de campaña</span>
                    <h2>NUESTRA META ACTUAL</h2>
                    <p class="impact-card__intro">La campaña mantiene como referencia pública el registro actual de abrigo recolectado. La meta general sigue en actualización hasta contar con validación operativa completa.</p>
                    <div class="impact-card__headline" data-countup><?= htmlspecialchars($garmentsStat['value']) ?></div>
                    <div class="impact-card__metric-label"><?= htmlspecialchars($garmentsStat['label']) ?></div>
                    <?php /*
                        La barra venia con un relleno del 63% sobre una meta declarada "en
                        actualizacion": daba a entender un avance que nadie ha validado. Se deja
                        el mismo elemento y el mismo enganche de JS, pero al 100% y con el
                        tramado de "sin meta definida" que aplica el CSS, para no cifrar un
                        porcentaje que no existe.
                    */ ?>
                    <div class="impact-card__progress">
                        <div class="impact-card__progress-track">
                            <span class="impact-card__progress-fill impact-card__progress-fill--pending" data-progress-fill style="--progress-target:100%;"></span>
                        </div>
                        <div class="impact-card__progress-scale">
                            <span>Registro actual</span>
                            <span>Meta en actualización</span>
                        </div>
                        <p class="impact-card__progress-note">Sin meta numérica publicada todavía: la barra no representa un porcentaje de avance.</p>
                    </div>
                    <a href="<?= BASE_URL ?>como-ayudar" class="impact-card__button">VER CÓMO AYUDAR</a>
                </article>

                <article class="impact-card impact-card--operations" data-reveal="up">
                    <span class="section-kicker">Cómo se organiza la ayuda</span>
                    <h2>TRANSPARENCIA OPERATIVA</h2>
                    <div class="impact-donut" aria-hidden="true">
                        <div class="impact-donut__chart"></div>
                        <div class="impact-donut__center">
                            <strong>Proceso</strong>
                            <span>4 etapas</span>
                        </div>
                    </div>
                    <ul class="impact-donut__legend" aria-label="Resumen textual del proceso operativo">
                        <li><span class="impact-donut__swatch impact-donut__swatch--navy"></span><strong>Recepción</strong><span>Ingreso de donaciones en puntos y jornadas.</span></li>
                        <li><span class="impact-donut__swatch impact-donut__swatch--orange"></span><strong>Clasificación</strong><span>Revisión por tipo, estado y utilidad.</span></li>
                        <li><span class="impact-donut__swatch impact-donut__swatch--sky"></span><strong>Priorización</strong><span>Orden según necesidad y contexto.</span></li>
                        <li><span class="impact-donut__swatch impact-donut__swatch--gold"></span><strong>Entrega</strong><span>Coordinación con voluntariado y aliados.</span></li>
                    </ul>
                    <p class="impact-card__note">Esquema operativo referencial basado en el proceso público de la campaña. No representa porcentajes auditados.</p>
                </article>

                <article class="impact-card impact-card--reach" data-reveal="right">
                    <span class="section-kicker">Señales de alcance</span>
                    <h2>LO QUE ESTA RED YA HA MOVIDO</h2>
                    <ul class="impact-reach-list">
                        <li><strong data-countup><?= htmlspecialchars($garmentsStat['value']) ?></strong><span><?= htmlspecialchars($garmentsStat['label']) ?></span></li>
                        <li><strong data-countup><?= htmlspecialchars($familiesStat['value']) ?></strong><span><?= htmlspecialchars($familiesStat['label']) ?></span></li>
                        <li><strong data-countup><?= htmlspecialchars($volunteersStat['value']) ?></strong><span><?= htmlspecialchars($volunteersStat['label']) ?></span></li>
                        <li><strong data-countup><?= htmlspecialchars($pointsStat['value']) ?></strong><span><?= htmlspecialchars($pointsStat['label']) ?></span></li>
                        <li><strong data-countup><?= htmlspecialchars($alliesStat['value']) ?></strong><span><?= htmlspecialchars($alliesStat['label']) ?></span></li>
                    </ul>
                </article>
            </div>
        </div>
    </section>

    <section class="section section--tight impact-milestones">
        <div class="site-shell">
            <div data-reveal="up">
                <span class="section-kicker section-kicker--center">Ruta de la campaña</span>
                <h2 class="section-title">HITOS QUE MARCAN EL AVANCE</h2>
            </div>
            <div class="impact-milestones__grid" data-stagger>
                <?php foreach ($impactMilestones as $milestone): ?>
                    <article class="impact-milestone" data-reveal="up">
                        <div class="impact-milestone__badge">
                            <span class="impact-milestone__icon"><?= site_icon($milestone['icon']) ?></span>
                            <strong><?= htmlspecialchars($milestone['number']) ?></strong>
                        </div>
                        <div>
                            <h3><?= htmlspecialchars($milestone['title']) ?></h3>
                            <p><?= htmlspecialchars($milestone['text']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section impact-stories">
        <div class="site-shell">
            <h2 class="section-title" data-reveal="up">ACCIONES QUE DAN CONTEXTO AL IMPACTO</h2>
            <div class="impact-stories__grid" data-stagger>
                <?php foreach ($impactStories as $story): ?>
                    <article class="impact-story" data-reveal="up">
                        <div class="impact-story__media">
                            <img src="<?= htmlspecialchars($story['image']) ?>" alt="<?= htmlspecialchars($story['alt']) ?>" decoding="async">
                        </div>
                        <div class="impact-story__body">
                            <h3><?= htmlspecialchars($story['title']) ?></h3>
                            <p><?= htmlspecialchars($story['text']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section section--soft impact-process" id="transparencia-campana">
        <div class="site-shell">
            <h2 class="section-title" data-reveal="up">TRANSPARENCIA EN CADA PASO</h2>
            <div class="impact-process__layout">
                <div class="impact-process__steps" data-stagger>
                    <?php foreach ($processSteps as $index => $step): ?>
                        <article class="impact-process__step" data-reveal="up">
                            <span class="impact-process__number"><?= htmlspecialchars($step['number']) ?></span>
                            <span class="impact-process__icon"><?= site_icon($step['icon']) ?></span>
                            <h3><?= htmlspecialchars($step['title']) ?></h3>
                            <p><?= htmlspecialchars($step['text']) ?></p>
                            <?php if ($index < count($processSteps) - 1): ?>
                                <span class="impact-process__connector" aria-hidden="true"></span>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
                <aside class="impact-process__trust" data-reveal="right">
                    <span class="impact-process__trust-icon"><?= site_icon('target') ?></span>
                    <h3>TRANSPARENCIA QUE GENERA CONFIANZA</h3>
                    <p>Compartir avances, historias y resultados ayuda a que más personas comprendan cómo se transforma cada aporte.</p>
                    <a href="#transparencia-campana" class="btn btn--primary btn--block">CONOCER NUESTRO PROCESO</a>
                </aside>
            </div>
        </div>
    </section>

    <section class="section section--tight impact-cta">
        <div class="site-shell">
            <div class="impact-cta__panel" data-reveal="up">
                <div class="impact-cta__copy">
                    <span class="section-kicker">Comparte, invita, transforma.</span>
                    <h2>SI ESTE IMPACTO TE HACE SENTIDO, AYÚDANOS A LLEVAR LA CAMPAÑA MÁS LEJOS</h2>
                    <p>Cada persona que entiende cómo funciona Abrigando Corazones puede abrir nuevas donaciones, voluntarios y aliados.</p>
                </div>
                <div class="impact-cta__actions">
                    <a href="<?= htmlspecialchars($contact['phone_link']) ?>" class="btn btn--primary btn--block" target="_blank" rel="noopener" data-share-whatsapp="impacto">COMPARTIR POR WHATSAPP</a>
                    <a href="<?= BASE_URL ?>como-ayudar" class="btn btn--outline btn--block">QUIERO AYUDAR</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
