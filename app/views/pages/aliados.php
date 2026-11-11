<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$allyBadgeMap = [
    'voluntariado' => 'Voluntariado',
    'universitarias' => 'Universitaria',
    'sociales' => 'Social',
    'empresariales' => 'Empresarial',
];

$allyIconMap = [
    'voluntariado' => 'users',
    'universitarias' => 'building',
    'sociales' => 'heart-handshake',
    'empresariales' => 'handshake',
];

$featuredOrganization = $organizations[0] ?? null;
foreach ($organizations as $candidate) {
    if (($candidate['slug'] ?? '') === 'cpj-tacna') {
        $featuredOrganization = $candidate;
        break;
    }
}
?>

<main>
    <section class="hero hero--interior hero--allies">
        <img src="<?= htmlspecialchars($page['hero']['image']) ?>" alt="Equipo de voluntariado en comunidad" class="hero__bg">
        <div class="hero__shade"></div>
        <div class="site-shell hero__inner hero__inner--interior">
            <div class="hero__copy">
                <span class="section-kicker">Red que hace posible el cambio</span>
                <h1 class="hero__title">
                    <span><?= htmlspecialchars($page['hero']['title'][0]) ?></span>
                    <span><?= htmlspecialchars($page['hero']['title'][1]) ?></span>
                </h1>
                <p class="hero__text"><?= htmlspecialchars($page['hero']['text']) ?></p>
                <div class="hero__buttons">
                    <a href="#formulario-aliados" class="btn btn--primary">QUIERO SER ALIADO <span class="btn__icon btn__icon--fill"><?= site_icon('heart') ?></span></a>
                </div>
                <ul class="hero__metrics">
                    <li><span class="hero__metric-icon"><?= site_icon('users') ?></span><span>+20 ORGANIZACIONES UNIDAS</span></li>
                    <li><span class="hero__metric-icon"><?= site_icon('handshake') ?></span><span>TRABAJO COLABORATIVO</span></li>
                    <li><span class="hero__metric-icon"><?= site_icon('heart') ?></span><span>IMPACTO REAL</span></li>
                </ul>
            </div>
        </div>
    </section>

    <section class="section section--soft section--allies-intro">
        <div class="site-shell">
            <div class="allies-summary" data-stagger>
                <?php /* Cifras calculadas en el servidor; el contador solo anima el valor final. */ ?>
                <article class="allies-summary__item" data-reveal="up">
                    <strong data-countup><?= count($organizations) ?></strong>
                    <span>organizaciones activas</span>
                </article>
                <article class="allies-summary__item" data-reveal="up">
                    <strong data-countup><?= count($allyFilters) - 1 ?></strong>
                    <span>categorías de alianza</span>
                </article>
                <article class="allies-summary__item" data-reveal="up">
                    <strong>Tacna</strong>
                    <span>red territorial de apoyo</span>
                </article>
            </div>
        </div>
    </section>

    <section class="section section--allies-grid" id="organizaciones">
        <div class="site-shell">
            <div data-reveal="up">
                <span class="section-kicker section-kicker--center">Organizaciones que suman</span>
                <h2 class="section-title">NUESTRAS ORGANIZACIONES ALIADAS</h2>
            </div>

            <?php if (!empty($featuredOrganization)): ?>
                <article class="allies-spotlight" data-reveal="up" data-ally-spotlight>
                    <div class="allies-spotlight__media">
                        <div class="allies-spotlight__logo organization-logo-frame ally-card__logo--<?= htmlspecialchars($featuredOrganization['logo_variant']) ?>">
                            <?php if (!empty($featuredOrganization['logo'])): ?>
                                <img src="<?= htmlspecialchars($featuredOrganization['logo']) ?>" alt="<?= htmlspecialchars($featuredOrganization['logo_alt']) ?>" loading="lazy" decoding="async">
                            <?php else: ?>
                                <div class="organization-logo-placeholder" aria-hidden="true">
                                    <span class="organization-logo-placeholder__icon"><?= site_icon($featuredOrganization['placeholder_icon']) ?></span>
                                    <strong><?= htmlspecialchars(mb_strtoupper($featuredOrganization['short_name'])) ?></strong>
                                    <small>LOGO OFICIAL PENDIENTE</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="allies-spotlight__copy">
                        <span class="allies-spotlight__eyebrow">Aliado destacado de la red</span>
                        <h3><?= htmlspecialchars($featuredOrganization['name']) ?></h3>
                        <p><?= htmlspecialchars($featuredOrganization['overlay_description']) ?></p>
                        <?php if (!empty($featuredOrganization['focus'])): ?>
                            <div class="allies-spotlight__tags" aria-label="Áreas del aliado destacado">
                                <?php foreach (array_slice($featuredOrganization['focus'], 0, 3) as $focus): ?>
                                    <span><?= htmlspecialchars(mb_strtoupper($focus)) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="allies-spotlight__actions">
                        <button type="button" class="btn btn--primary" data-ally-spotlight-target="<?= htmlspecialchars($featuredOrganization['anchor']) ?>">M&Aacute;S INFORMACI&Oacute;N</button>
                    </div>
                </article>
            <?php endif; ?>

            <div class="allies-toolbar" data-reveal="up">
                <div class="allies-toolbar__search">
                    <label class="sr-only" for="allies-search">Buscar organización</label>
                    <span class="allies-toolbar__search-icon"><?= site_icon('search') ?></span>
                    <input id="allies-search" type="search" placeholder="Buscar organización o palabra clave" data-allies-search autocomplete="off">
                </div>
                <div class="allies-toolbar__controls">
                    <label class="allies-toolbar__sort">
                        <span>Ordenar por</span>
                        <select data-allies-sort>
                            <option value="default">Orden actual</option>
                            <option value="name">Nombre A-Z</option>
                            <option value="category">Tipo de aliado</option>
                            <option value="verification">Estado de verificación</option>
                        </select>
                    </label>
                    <label class="allies-toolbar__switch">
                        <input type="checkbox" data-allies-verified-only>
                        <span>Solo verificados</span>
                    </label>
                </div>
            </div>

            <div class="allies-toolbar__meta" data-reveal="up">
                <p class="allies-toolbar__results"><strong data-allies-results-count><?= count($organizations) ?></strong> aliados visibles</p>
                <button type="button" class="allies-toolbar__reset" data-allies-clear-state>Limpiar filtros</button>
            </div>

            <div class="allies-filter-group" data-reveal="up">
                <span class="allies-filter-group__label">Categorías</span>
                <div class="chip-row chip-row--allies" data-allies-filters aria-label="Filtros de organizaciones aliadas">
                    <?php foreach ($allyFilters as $filter): ?>
                        <button type="button" class="chip-row__item<?= $filter['key'] === 'all' ? ' is-active' : '' ?>" data-filter="<?= htmlspecialchars($filter['key']) ?>" aria-pressed="<?= $filter['key'] === 'all' ? 'true' : 'false' ?>">
                            <?= htmlspecialchars($filter['label']) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (!empty($allyAreaFilters)): ?>
                <div class="allies-filter-group allies-filter-group--areas" data-reveal="up">
                    <span class="allies-filter-group__label">Áreas</span>
                    <div class="chip-row chip-row--allies chip-row--areas" data-allies-area-filters aria-label="Filtros por áreas de acción">
                        <?php foreach ($allyAreaFilters as $filter): ?>
                            <button type="button" class="chip-row__item chip-row__item--subtle" data-area-filter="<?= htmlspecialchars($filter['key']) ?>" aria-pressed="false">
                                <?= htmlspecialchars(mb_strtoupper($filter['label'])) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="ally-grid ally-grid--premium" data-allies-grid data-stagger>
                <?php foreach ($organizations as $index => $organization): ?>
                    <?php
                    $contextId = $organization['anchor'] . '-context';
                    $matchesPending = $organization['verification'] === 'pending';
                    ?>
                    <article
                        id="<?= htmlspecialchars($organization['anchor']) ?>"
                        class="ally-card ally-card--premium organization-card"
                        data-reveal="up"
                        data-organization-card
                        data-order-index="<?= $index ?>"
                        data-organization-name="<?= htmlspecialchars($organization['name']) ?>"
                        data-organization-short-name="<?= htmlspecialchars($organization['short_name']) ?>"
                        data-category="<?= htmlspecialchars($organization['category']) ?>"
                        data-category-display="<?= htmlspecialchars($organization['category_display']) ?>"
                        data-filter-tags="<?= htmlspecialchars(implode(' ', $organization['filter_tags'])) ?>"
                        data-focus-tags="<?= htmlspecialchars(implode(' ', $organization['focus_slugs'])) ?>"
                        data-verification="<?= htmlspecialchars($organization['verification']) ?>"
                        data-verification-rank="<?= htmlspecialchars((string) $organization['verification_rank']) ?>"
                        data-description="<?= htmlspecialchars($organization['description']) ?>"
                        data-overlay-description="<?= htmlspecialchars($organization['description']) ?>"
                        data-source-label="<?= htmlspecialchars((string) ($organization['source_label'] ?? '')) ?>"
                        data-status-badge="<?= htmlspecialchars($organization['status_badge']) ?>"
                        aria-label="<?= htmlspecialchars($organization['name']) ?>"
                    >
                        <div class="organization-card__surface">
                            <div class="organization-card__front">
                                <div class="organization-logo-frame ally-card__logo ally-card__logo--<?= htmlspecialchars($organization['logo_variant']) ?>">
                                    <?php if (!empty($organization['logo'])): ?>
                                        <img src="<?= htmlspecialchars($organization['logo']) ?>" alt="<?= htmlspecialchars($organization['logo_alt']) ?>" loading="lazy" decoding="async">
                                    <?php else: ?>
                                        <div class="organization-logo-placeholder" aria-hidden="true">
                                            <span class="organization-logo-placeholder__icon"><?= site_icon($organization['placeholder_icon']) ?></span>
                                            <strong><?= htmlspecialchars(mb_strtoupper($organization['short_name'])) ?></strong>
                                            <small>LOGO OFICIAL PENDIENTE</small>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="organization-card__body">
                                    <span class="ally-card__badge">
                                        <span class="ally-card__badge-icon" aria-hidden="true"><?= site_icon($allyIconMap[$organization['category']] ?? 'heart') ?></span>
                                        <?= htmlspecialchars($allyBadgeMap[$organization['category']] ?? 'Institucional') ?>
                                    </span>
                                    <strong class="organization-card__name"><?= htmlspecialchars($organization['name']) ?></strong>
                                    <?php if ($organization['subtitle'] !== ''): ?>
                                        <span class="organization-card__subtitle"><?= htmlspecialchars($organization['subtitle']) ?></span>
                                    <?php endif; ?>
                                     <p class="organization-card__excerpt"><?= htmlspecialchars($organization['summary_short']) ?></p>

                                    <?php /*
                                        Aporte concreto declarado por la propia organizacion en el
                                        formulario de apoyo. Antes todas mostraban el mismo texto
                                        generico y no se sabia que habia hecho cada una.
                                    */ ?>
                                    <?php if (!empty($organization['support_type'])): ?>
                                        <p class="ally-support">
                                            <span class="ally-support__type"><?= site_icon('handshake') ?><?= htmlspecialchars($organization['support_type']) ?></span>
                                            <?php if (!empty($organization['support_note'])): ?>
                                                <span class="ally-support__note"><?= htmlspecialchars($organization['support_note']) ?></span>
                                            <?php endif; ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if (!empty($organization['focus'])): ?>
                                        <div class="organization-card__visible-tags" aria-label="Áreas de acción">
                                            <?php foreach (array_slice($organization['focus'], 0, 2) as $focus): ?>
                                                <span><?= htmlspecialchars(mb_strtoupper($focus)) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="organization-card__mini-meta">
                                        <?php if ($matchesPending): ?>
                                            <span class="organization-card__mini-status organization-card__mini-status--pending"><?= htmlspecialchars($organization['status_badge']) ?></span>
                                        <?php else: ?>
                                            <span class="organization-card__mini-status organization-card__mini-status--<?= htmlspecialchars($organization['verification']) ?>">
                                                <span class="organization-card__status-dot" aria-hidden="true"></span>
                                                <?= htmlspecialchars($organization['verification_label']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="organization-card__footer">
                                    <div class="organization-card__footer-actions">
                                        <button
                                            type="button"
                                            class="organization-card__toggle"
                                            data-organization-action
                                            data-organization-detail
                                        >
                                            <span>M&Aacute;S INFORMACI&Oacute;N</span>
                                            <span class="organization-card__toggle-icon"><?= site_icon('arrow-right') ?></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div
                                id="<?= htmlspecialchars($contextId) ?>"
                                class="organization-card__context"
                                data-organization-context
                                aria-hidden="true"
                            >
                                <span class="organization-card__eyebrow">CONOCE A NUESTRO ALIADO</span>
                                <h3 class="organization-card__context-name"><?= htmlspecialchars($organization['name']) ?></h3>
                                <p class="organization-card__context-category"><?= htmlspecialchars($organization['category_display']) ?></p>
                                <p class="organization-card__description"><?= htmlspecialchars($organization['overlay_description']) ?></p>

                                <div class="organization-card__meta">
                                    <?php if (!empty($organization['focus'])): ?>
                                        <div class="organization-card__focus organization-context__areas" aria-label="Áreas de acción">
                                            <?php foreach ($organization['focus'] as $focus): ?>
                                                <?php $focusClass = strtolower(str_replace([' ', 'á', 'é', 'í', 'ó', 'ú', 'ñ'], ['-', 'a', 'e', 'i', 'o', 'u', 'n'], $focus)); ?>
                                                <span class="organization-card__focus-tag organization-card__focus-tag--<?= htmlspecialchars($focusClass) ?>">
                                                    <?= htmlspecialchars(mb_strtoupper($focus)) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($matchesPending): ?>
                                        <span class="organization-card__status-badge organization-card__status-badge--pending"><?= htmlspecialchars($organization['status_badge']) ?></span>
                                    <?php else: ?>
                                        <span class="organization-card__status organization-card__status--<?= htmlspecialchars($organization['verification']) ?>">
                                            <span class="organization-card__status-dot" aria-hidden="true"></span>
                                            <span><?= htmlspecialchars(mb_strtoupper($organization['verification_label'])) ?></span>
                                        </span>
                                    <?php endif; ?>

                                    <?php if (!$matchesPending && !empty($organization['source_label'])): ?>
                                        <p class="organization-card__source">Fuente institucional: <?= htmlspecialchars($organization['source_label']) ?></p>
                                    <?php endif; ?>

                                    <div class="organization-card__context-actions">
                                        <button type="button" class="organization-card__context-link" data-organization-detail>M&Aacute;S INFORMACI&Oacute;N</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="allies-load-more" data-reveal="up">
                <button type="button" class="btn btn--outline" data-allies-load-more hidden>VER MÁS ALIADOS</button>
            </div>

            <div class="allies-empty" data-allies-empty hidden>
                <div class="allies-empty__icon"><?= site_icon('search-x') ?></div>
                <h3>NO ENCONTRAMOS ALIADOS<br>EN ESTA CATEGORÍA</h3>
                <p>Prueba con otro filtro para conocer a las organizaciones que forman parte de Abrigando Corazones.</p>
                <button type="button" class="btn btn--outline" data-filter-reset>VER TODAS</button>
            </div>

            <p class="ally-note" data-reveal="up">¿Eres una organización y quieres ser parte? <a href="#formulario-aliados">Contáctanos</a> y construyamos alianzas que transformen vidas.</p>
        </div>
    </section>

    <div class="ally-detail-modal" data-ally-modal hidden aria-hidden="true">
        <div class="ally-detail-modal__backdrop" data-ally-modal-close></div>
        <div class="ally-detail-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="ally-modal-title">
            <button type="button" class="ally-detail-modal__close" data-ally-modal-close aria-label="Cerrar detalle"><?= site_icon('close') ?></button>
            <div class="ally-detail-modal__content">
                <span class="ally-detail-modal__eyebrow">INFORMACI&Oacute;N DEL ALIADO</span>
                <h3 id="ally-modal-title" class="ally-detail-modal__title"></h3>
                <p class="ally-detail-modal__type"></p>
                <p class="ally-detail-modal__description"></p>
                <div class="ally-detail-modal__areas" data-ally-modal-areas></div>
                <div class="ally-detail-modal__meta">
                    <span class="ally-detail-modal__status" data-ally-modal-status></span>
                    <p class="ally-detail-modal__source" data-ally-modal-source></p>
                </div>
            </div>
        </div>
    </div>

    <section class="section section--soft">
        <div class="site-shell">
            <div class="allies-story" data-stagger>
                <div class="allies-story__copy" data-reveal="left">
                    <span class="section-kicker">Juntos llegamos más lejos</span>
                    <h2 class="section-title section-title--left">JUNTOS<br>LLEGAMOS MÁS LEJOS</h2>
                    <p>La campaña crece gracias al trabajo conjunto entre ciudadanos, voluntariados, universidades, organizaciones, empresas e instituciones.</p>
                </div>
                <div class="allies-story__benefits" data-reveal="right">
                    <article>
                        <span>01</span>
                        <div>
                            <h3>AMPLIFICAMOS EL IMPACTO</h3>
                            <p>Coordinamos esfuerzos para llevar abrigo y esperanza a más familias.</p>
                        </div>
                    </article>
                    <article>
                        <span>02</span>
                        <div>
                            <h3>CONECTAMOS COMUNIDADES</h3>
                            <p>Articulamos redes locales con objetivos compartidos y acciones concretas.</p>
                        </div>
                    </article>
                    <article>
                        <span>03</span>
                        <div>
                            <h3>MOVILIZAMOS VOLUNTARIOS</h3>
                            <p>Activamos equipos que clasifican, organizan y entregan con compromiso real.</p>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="section section--ally-cta">
        <div class="site-shell">
            <div class="ally-cta" data-stagger>
                <div class="ally-cta__copy" data-reveal="left">
                    <span class="section-kicker">Nueva alianza</span>
                    <h2>¿TU ORGANIZACIÓN QUIERE<br>SUMARSE A ABRIGANDO CORAZONES?</h2>
                    <p>Convirtamos tu institución, empresa, colectivo o voluntariado en parte de esta cadena de solidaridad.</p>
                </div>
                <div class="ally-cta__actions" data-reveal="right">
                    <a href="#formulario-aliados" class="btn btn--primary">QUIERO SER ALIADO <span class="btn__icon btn__icon--fill"><?= site_icon('heart') ?></span></a>
                    <a href="<?= BASE_URL ?>contacto" class="btn btn--outline">CONTACTAR AL EQUIPO</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="formulario-aliados">
        <div class="site-shell ally-form-layout">
            <div class="ally-form-layout__photo" data-reveal="left">
                <img src="<?= BASE_URL ?>images/hero_bg.webp" alt="Voluntariado Abrigando Corazones">
                <span class="scarf-heart scarf-heart--overlay">
                    <span class="scarf-heart__heart"><img src="<?= BASE_URL ?>images/logo_heart.webp" alt=""></span>
                    <span class="scarf-heart__scarf"></span>
                </span>
            </div>
            <div class="ally-form-layout__content" data-reveal="right">
                <span class="section-kicker">Formulario de alianza</span>
                <h2>SÚMATE COMO ALIADO</h2>
                <p>Instituciones que se unen, comunidades que se transforman.</p>
                <form action="<?= BASE_URL ?>contact/subscribe" method="POST" class="ally-form" data-loading-form>
                    <?= \App\Core\Csrf::field() ?>
                    <input type="hidden" name="redirect" value="contacto">
                    <input type="text" name="website" value="" class="sr-only" tabindex="-1" autocomplete="off" aria-hidden="true">
                    <label class="sr-only" for="ally-nombre">Nombre de la organización</label>
                    <input id="ally-nombre" type="text" name="nombre" placeholder="Nombre de la organización" autocomplete="organization" required>
                    <label class="sr-only" for="ally-contacto">Teléfono o WhatsApp de contacto</label>
                    <input id="ally-contacto" type="tel" name="telefono" placeholder="Teléfono / WhatsApp de contacto" aria-label="Teléfono o WhatsApp de contacto" autocomplete="tel" required>
                    <label class="sr-only" for="ally-email">Correo electrónico</label>
                    <input id="ally-email" type="email" name="email" placeholder="Correo electrónico" autocomplete="email" required>
                    <label class="sr-only" for="ally-tipo">Tipo de organización</label>
                    <select id="ally-tipo" name="como_se_entero" required>
                        <option value="">Tipo de organización</option>
                        <option value="Social">Social</option>
                        <option value="Universitaria">Universitaria</option>
                        <option value="Empresarial">Empresarial</option>
                        <option value="Voluntariado">Voluntariado</option>
                    </select>
                    <label class="sr-only" for="ally-mensaje">¿Cómo quieres colaborar con Abrigando Corazones?</label>
                    <textarea id="ally-mensaje" name="mensaje" placeholder="¿Cómo quieres colaborar con Abrigando Corazones?"></textarea>
                    <button type="submit" class="btn btn--primary" data-submit-label="ENVIAR SOLICITUD DE ALIANZA">ENVIAR SOLICITUD DE ALIANZA <span class="btn__icon btn__icon--fill"><?= site_icon('heart') ?></span></button>
                </form>
                <div class="ally-contact-card">
                    <h3>También puedes contactarnos</h3>
                    <p><span><?= site_icon('phone') ?></span><?= htmlspecialchars($contact['phone']) ?></p>
                    <?php if (!empty($contact['email'])): ?>
                        <p><span><?= site_icon('mail') ?></span><?= htmlspecialchars($contact['email']) ?></p>
                    <?php endif; ?>
                    <p><span><?= site_icon('pin') ?></span><?= htmlspecialchars($contact['location']) ?></p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
