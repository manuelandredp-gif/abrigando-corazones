<?php require __DIR__ . '/../layouts/header.php'; ?>

<main>
    <section class="hero hero--interior">
        <img src="<?= htmlspecialchars($page['hero']['image']) ?>" alt="Montañas altoandinas al fondo" class="hero__bg">
        <div class="hero__shade"></div>
        <div class="site-shell hero__inner hero__inner--interior">
            <div class="hero__copy">
                <span class="section-kicker"><?= htmlspecialchars($page['hero']['eyebrow']) ?></span>
                <h1 class="hero__title">
                    <span><?= htmlspecialchars($page['hero']['title'][0]) ?></span>
                    <span><?= htmlspecialchars($page['hero']['title'][1]) ?></span>
                </h1>
                <p class="hero__text"><?= htmlspecialchars($page['hero']['text']) ?></p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="site-shell cta-panel cta-panel--centered" data-reveal="up">
            <?php /* E2 — Panel 404 expresivo con ilustración y mensaje de marca */ ?>
            <div style="text-align:center;">
                <div aria-hidden="true" style="font-size:clamp(5rem,18vw,9rem);font-weight:900;line-height:1;letter-spacing:-0.04em;color:var(--ac,#c2410c);opacity:0.12;user-select:none;margin-bottom:-0.5em;font-variant-numeric:tabular-nums;">404</div>
                <svg aria-hidden="true" viewBox="0 0 200 80" fill="none" style="width:clamp(120px,40vw,220px);margin:0 auto 1rem;display:block;opacity:0.18;">
                    <polyline points="0,70 30,45 55,58 90,20 120,38 155,10 185,28 200,22" stroke="var(--ac,#c2410c)" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"/>
                    <polyline points="90,20 120,38 155,10" stroke="var(--ac,#c2410c)" stroke-width="5" opacity="0.6" stroke-linecap="round"/>
                </svg>
                <span class="section-kicker section-kicker--center">Error 404</span>
                <h2>Esta ruta no lleva a ningún punto de acopio.</h2>
                <p>La página que buscabas cambió o ya no existe. Pero podemos ayudarte a retomar el camino.</p>
            </div>
            <div class="cta-panel__actions" style="justify-content:center;flex-wrap:wrap;">
                <a href="<?= BASE_URL ?>" class="btn btn--primary" data-prefetch-link>VOLVER AL INICIO</a>
                <a href="<?= BASE_URL ?>puntos-de-acopio" class="btn btn--outline" data-prefetch-link>VER PUNTOS DE ACOPIO</a>
                <a href="<?= BASE_URL ?>como-ayudar" class="btn btn--outline" data-prefetch-link>¿CÓMO AYUDAR?</a>
            </div>
        </div>
    </section>

    <?php
    /*
     * Un 404 sin salidas obliga a usar el boton "atras". Se reutiliza la misma
     * navegacion del encabezado (no hay rutas nuevas ni textos inventados) para
     * que la pagina de error funcione como un indice del sitio.
     */
    $notFoundLinks = array_values(array_filter(
        $navigation ?? [],
        static fn(array $item): bool => ($item['key'] ?? '') !== 'inicio'
    ));

    /*
     * site_icon() indexa por nombre de icono, no por clave de navegacion: sin
     * este puente devuelve cadena vacia y quedan siete circulos en blanco.
     * Si manana se agrega una seccion sin icono, la pastilla no se pinta.
     */
    $notFoundIcons = [
        'quienes-somos'          => 'users',
        'como-ayudar'            => 'heart-handshake',
        'puntos-de-acopio'       => 'pin',
        'aliados'                => 'handshake',
        'impacto'                => 'chart',
        'convocatoria-artistica' => 'palette',
        'noticias'               => 'file-text',
        'contacto'               => 'mail',
        'donar'                  => 'heart',
    ];
    ?>
    <?php if ($notFoundLinks !== []): ?>
        <section class="section section--tight section--soft">
            <div class="site-shell">
                <div data-reveal="up">
                    <span class="section-kicker section-kicker--center">Secciones del sitio</span>
                    <h2 class="section-title">¿A DÓNDE QUERÍAS IR?</h2>
                </div>
                <ul class="notfound-map" data-stagger>
                    <?php foreach ($notFoundLinks as $item): ?>
                        <?php $itemIcon = site_icon($notFoundIcons[$item['key']] ?? ''); ?>
                        <li data-reveal="up">
                            <a href="<?= htmlspecialchars($item['href']) ?>" data-prefetch-link>
                                <?php if ($itemIcon !== ''): ?>
                                    <span class="notfound-map__icon"><?= $itemIcon ?></span>
                                <?php endif; ?>
                                <span class="notfound-map__label"><?= htmlspecialchars($item['label']) ?></span>
                                <span class="notfound-map__arrow"><?= site_icon('arrow-right') ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
