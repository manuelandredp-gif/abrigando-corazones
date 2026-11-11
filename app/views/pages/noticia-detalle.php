<?php require __DIR__ . '/../layouts/header.php'; ?>

<main>
    <div class="reading-progress" data-reading-progress aria-hidden="true">
        <span data-reading-progress-bar></span>
    </div>

    <article class="section news-article">
        <div class="site-shell news-article__shell">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="<?= BASE_URL ?>">Inicio</a>
                <span>/</span>
                <a href="<?= BASE_URL ?>noticias">Noticias</a>
                <span>/</span>
                <span aria-current="page"><?= htmlspecialchars($article['title']) ?></span>
            </nav>

            <header class="news-article__header" data-reveal="up">
                <p class="news-article__eyebrow">
                    <span class="tag"><?= htmlspecialchars($article['type']) ?></span>
                    <span class="tag tag--outline"><?= htmlspecialchars($article['primary_category_label']) ?></span>
                </p>
                <h1><?= htmlspecialchars($article['title']) ?></h1>
                <p class="news-article__summary"><?= htmlspecialchars($article['summary']) ?></p>
                <?php /* La fuente se distingue del resto de la ficha: es el dato que permite comprobar la nota. */ ?>
                <div class="news-article__meta">
                    <span><?= htmlspecialchars($article['published_label']) ?></span>
                    <span class="news-source">Fuente: <?= htmlspecialchars($article['source_name']) ?></span>
                    <span><?= htmlspecialchars($article['reading_label']) ?></span>
                </div>
            </header>

            <figure class="news-article__hero" data-reveal="up">
                <img src="<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['image_alt']) ?>">
            </figure>

            <div class="news-article__layout">
                <div class="news-article__content" data-stagger>
                    <?php foreach ($article['sections'] as $section): ?>
                        <section class="news-article__section" data-reveal="up">
                            <h2><?= htmlspecialchars($section['title']) ?></h2>
                            <?php foreach ($section['body'] as $paragraph): ?>
                                <p><?= htmlspecialchars($paragraph) ?></p>
                            <?php endforeach; ?>
                        </section>
                    <?php endforeach; ?>

                    <?php if (!empty($article['callout'])): ?>
                        <aside class="news-article__callout" data-reveal="up">
                            <span class="section-kicker">¿Por qué esto importa para Abrigando Corazones?</span>
                            <p><?= htmlspecialchars($article['callout']) ?></p>
                        </aside>
                    <?php endif; ?>

                    <section class="news-article__source" data-reveal="up">
                        <h2>Fuente</h2>
                        <p><strong><?= htmlspecialchars($article['source_name']) ?></strong></p>
                        <?php if (!empty($article['source_url'])): ?>
                            <p class="news-article__source-url"><?= htmlspecialchars(parse_url($article['source_url'], PHP_URL_HOST) ?: $article['source_url']) ?></p>
                            <a href="<?= htmlspecialchars($article['source_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn--secondary">
                                CONSULTAR FUENTE ORIGINAL
                            </a>
                        <?php endif; ?>
                    </section>
                </div>

                <aside class="news-article__aside">
                    <div class="news-article__share" data-article-share data-article-url="<?= htmlspecialchars($article['url']) ?>" data-article-title="<?= htmlspecialchars($article['title']) ?>">
                        <span class="section-kicker">Compartir</span>
                        <div class="social-pills social-pills--stack">
                            <a href="https://wa.me/?text=<?= urlencode($article['title'] . ' ' . $article['url']) ?>" target="_blank" rel="noopener"><?= site_icon('whatsapp') ?> WhatsApp</a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($article['url']) ?>" target="_blank" rel="noopener"><?= site_icon('facebook') ?> Facebook</a>
                            <button type="button" class="social-pills__button" data-copy-article-link><?= site_icon('link') ?> Copiar enlace</button>
                        </div>
                    </div>
                </aside>
            </div>

            <?php if (!empty($article['related'])): ?>
                <section class="news-related">
                    <div class="section-heading">
                        <span class="section-kicker">Relacionadas</span>
                        <h2 class="section-title">Otras historias para seguir el contexto</h2>
                    </div>
                    <div class="news-grid news-grid--newsroom news-grid--related" data-stagger>
                        <?php foreach ($article['related'] as $related): ?>
                            <article class="news-card news-card--newsroom" data-reveal="up">
                                <a href="<?= htmlspecialchars($related['url']) ?>" class="news-card__media">
                                    <img src="<?= htmlspecialchars($related['image']) ?>" alt="<?= htmlspecialchars($related['image_alt']) ?>" loading="lazy" decoding="async">
                                </a>
                                <div class="news-card__body">
                                    <p class="news-card__meta-eyebrow">
                                        <span class="tag"><?= htmlspecialchars($related['type']) ?></span>
                                        <span class="tag tag--outline"><?= htmlspecialchars($related['primary_category_label']) ?></span>
                                    </p>
                                    <h3><a href="<?= htmlspecialchars($related['url']) ?>"><?= htmlspecialchars($related['title']) ?></a></h3>
                                    <p><?= htmlspecialchars($related['excerpt']) ?></p>
                                    <a href="<?= htmlspecialchars($related['url']) ?>" class="news-card__link">LEER HISTORIA <?= site_icon('arrow-right') ?></a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </article>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
