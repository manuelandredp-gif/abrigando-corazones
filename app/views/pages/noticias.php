<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$featuredStory = $newsFeatured;
$newsCards = array_values(array_filter(
    $newsStories,
    static fn(array $story): bool => $featuredStory === null || $story['slug'] !== $featuredStory['slug']
));
?>

<main>
    <section class="hero hero--interior hero--newsroom">
        <img src="<?= htmlspecialchars($page['hero']['image']) ?>" alt="Familia altoandina junto a su vivienda durante la temporada de bajas temperaturas" class="hero__bg">
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
        <div class="site-shell news-directory news-directory--documentary" data-news-directory>
            <div class="directory-toolbar news-directory__toolbar" data-reveal="up">
                <div class="directory-toolbar__search">
                    <span class="allies-toolbar__search-icon"><?= site_icon('search') ?></span>
                    <input type="search" placeholder="Busca por tema, comunidad o palabra clave..." data-news-search>
                </div>
                <div class="directory-toolbar__meta">
                    <p><strong data-news-count><?= count($newsStories) ?></strong> historias visibles</p>
                    <button type="button" data-news-clear>Ver todas</button>
                </div>
            </div>

            <div class="news-filter-row" data-reveal="up" data-news-filters>
                <button type="button" class="chip-row__item is-active" data-news-filter="all" aria-pressed="true">Todas</button>
                <?php foreach ($newsFilters as $filter): ?>
                    <button type="button" class="chip-row__item" data-news-filter="<?= htmlspecialchars($filter['key']) ?>" aria-pressed="false">
                        <?= htmlspecialchars($filter['label']) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <?php if ($featuredStory): ?>
                <?php /* N4+N5 — Editorial cover: imagen de fondo con gradiente y texto superpuesto + badge de fuente verificada. */ ?>
                <article class="featured-news featured-news--newsroom featured-news--editorial" data-reveal="up" data-news-card data-news-tags="<?= htmlspecialchars(implode(' ', $featuredStory['categories'])) ?>" data-news-searchable="<?= htmlspecialchars($featuredStory['search_blob']) ?>" style="position:relative;overflow:hidden;border-radius:var(--radius-lg,12px);min-height:clamp(340px,50vw,520px);display:flex;align-items:flex-end;">
                    <div class="featured-news__media" style="position:absolute;inset:0;">
                        <img src="<?= htmlspecialchars($featuredStory['image']) ?>" alt="<?= htmlspecialchars($featuredStory['image_alt']) ?>" style="width:100%;height:100%;object-fit:cover;">
                        <div aria-hidden="true" style="position:absolute;inset:0;background:linear-gradient(to top, rgba(10,10,20,0.92) 0%, rgba(10,10,20,0.55) 50%, rgba(10,10,20,0.15) 100%);"></div>
                    </div>
                    <div class="featured-news__copy" style="position:relative;z-index:2;padding:clamp(1.25rem,4vw,2.5rem);color:#fff;width:100%;">
                        <p class="news-card__meta-eyebrow" style="margin-bottom:0.75rem;">
                            <span class="tag" style="background:var(--ac,#c2410c);color:#fff;"><?= htmlspecialchars($featuredStory['type']) ?></span>
                            <span class="tag tag--outline" style="border-color:rgba(255,255,255,0.5);color:#fff;"><?= htmlspecialchars($featuredStory['primary_category_label']) ?></span>
                            <span class="tag" style="background:rgba(255,255,255,0.12);color:#fff;">DESTACADA</span>
                        </p>
                        <h2 style="color:#fff;font-size:clamp(1.3rem,3vw,2rem);line-height:1.2;margin-bottom:0.75rem;"><?= htmlspecialchars($featuredStory['title']) ?></h2>
                        <?php /* La fuente se marca aparte: es lo que hace verificable la nota. */ ?>
                        <p class="featured-news__date" style="color:rgba(255,255,255,0.75);font-size:0.82rem;display:flex;flex-wrap:wrap;gap:0.5rem;align-items:center;margin-bottom:1rem;">
                            <span><?= htmlspecialchars($featuredStory['published_label']) ?></span>
                            <span class="news-source" style="display:inline-flex;align-items:center;gap:0.3rem;">
                                <?php /* N5 — Badge de fuente verificada */ ?>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2.5" aria-hidden="true" style="flex-shrink:0;"><polyline points="20 6 9 17 4 12"/></svg>
                                <?= htmlspecialchars($featuredStory['source_name']) ?>
                            </span>
                            <span><?= htmlspecialchars($featuredStory['reading_label']) ?></span>
                        </p>
                        <p style="color:rgba(255,255,255,0.85);font-size:0.92rem;margin-bottom:1.25rem;max-width:60ch;"><?= htmlspecialchars($featuredStory['summary']) ?></p>
                        <a href="<?= htmlspecialchars($featuredStory['url']) ?>" class="btn btn--primary">LEER HISTORIA</a>
                    </div>
                </article>
            <?php endif; ?>

            <div class="news-grid news-grid--newsroom" data-stagger>
                <?php foreach ($newsCards as $story): ?>
                    <article class="news-card news-card--newsroom" data-reveal="up" data-news-card data-news-tags="<?= htmlspecialchars(implode(' ', $story['categories'])) ?>" data-news-searchable="<?= htmlspecialchars($story['search_blob']) ?>">
                        <a href="<?= htmlspecialchars($story['url']) ?>" class="news-card__media">
                            <img src="<?= htmlspecialchars($story['image']) ?>" alt="<?= htmlspecialchars($story['image_alt']) ?>" loading="lazy" decoding="async">
                        </a>
                        <div class="news-card__body">
                            <p class="news-card__meta-eyebrow">
                                <span class="tag"><?= htmlspecialchars($story['type']) ?></span>
                                <span class="tag tag--outline"><?= htmlspecialchars($story['primary_category_label']) ?></span>
                            </p>
                            <h3><a href="<?= htmlspecialchars($story['url']) ?>"><?= htmlspecialchars($story['title']) ?></a></h3>
                            <p><?= htmlspecialchars($story['excerpt']) ?></p>
                            <?php /* N5 — Badge de fuente verificada en todas las tarjetas */ ?>
                            <p class="news-card__date">
                                <span><?= htmlspecialchars($story['published_label']) ?></span>
                                <span class="news-source" style="display:inline-flex;align-items:center;gap:0.28rem;">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true" style="color:var(--ac,#c2410c);flex-shrink:0;"><polyline points="20 6 9 17 4 12"/></svg>
                                    <?= htmlspecialchars($story['source_name']) ?>
                                </span>
                            </p>
                            <a href="<?= htmlspecialchars($story['url']) ?>" class="news-card__link">LEER HISTORIA <?= site_icon('arrow-right') ?></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="allies-empty" data-news-empty hidden>
                <div class="allies-empty__icon"><?= site_icon('search-x') ?></div>
                <h2>No encontramos historias con estos criterios.</h2>
                <p>Prueba con otra categoría o limpia tu búsqueda para volver a la cobertura completa.</p>
                <button type="button" class="btn btn--secondary" data-news-clear>VER TODAS</button>
            </div>
        </div>
    </section>

    <section class="section section--tight">
        <div class="site-shell news-action-strip" data-reveal="up">
            <div>
                <span class="section-kicker">Después de leer</span>
                <h2>CONVIERTE EL CONTEXTO EN UNA ACCIÓN CONCRETA</h2>
                <p>Si una historia te movió, puedes donar ropa, compartir la campaña o escribirnos para coordinar apoyo desde tu comunidad.</p>
            </div>
            <div class="news-action-strip__actions">
                <a href="<?= BASE_URL ?>como-ayudar#donar-ropa" class="btn btn--primary">DONA ROPA</a>
                <a href="<?= BASE_URL ?>contacto" class="btn btn--outline">COORDINAR APOYO</a>
            </div>
        </div>
    </section>

    <section class="section section--tight">
        <div class="site-shell newsletter-band" data-stagger>
            <div class="newsletter-band__subscribe" data-reveal="up">
                <span class="section-kicker">Boletín de campaña</span>
                <h2>SUSCRÍBETE Y RECIBE CONTEXTO CLARO SOBRE LO QUE ESTÁ PASANDO</h2>
                <p>Si quieres enterarte cuando publiquemos nuevas historias, reportes y acciones de solidaridad, deja tu correo y te escribiremos cuando corresponda.</p>
                <form action="<?= BASE_URL ?>contact/subscribe" method="POST" class="newsletter-form" data-loading-form>
                    <?= \App\Core\Csrf::field() ?>
                <input type="hidden" name="redirect" value="noticias">
                <input type="text" name="website" value="" class="sr-only" tabindex="-1" autocomplete="off" aria-hidden="true">
                    <input type="hidden" name="nombre" value="Suscriptor boletín">
                    <input type="hidden" name="telefono" value="000000000">
                    <input type="hidden" name="como_se_entero" value="Boletín">
                    <input type="email" name="email" placeholder="Escribe tu correo electrónico" aria-label="Correo electrónico para suscribirte al boletín" autocomplete="email" required>
                    <button type="submit" class="btn btn--primary">SUSCRIBIRME</button>
                </form>
                <label class="checkbox-note"><input type="checkbox" checked> <span>Acepto la política de privacidad.</span></label>
            </div>
            <div class="newsletter-band__social" data-reveal="up">
                <span class="section-kicker">Comparte la campaña</span>
                <h2>LAS HISTORIAS TAMBIÉN AYUDAN A MOVILIZAR APOYO</h2>
                <p>Compartir una noticia puede acercar la conversación sobre heladas, prevención y solidaridad a nuevas personas.</p>
                <div class="social-pills">
                    <a href="<?= htmlspecialchars($contact['phone_link']) ?>" target="_blank" rel="noopener"><?= site_icon('whatsapp') ?> WhatsApp</a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(BASE_URL . 'noticias') ?>" target="_blank" rel="noopener"><?= site_icon('facebook') ?> Facebook</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
