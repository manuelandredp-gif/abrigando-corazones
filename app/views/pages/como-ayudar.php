<?php
require __DIR__ . '/../layouts/header.php';

$helpTabs = [
    [
        'key' => 'donar-ropa',
        'label' => 'DONA ROPA',
        'title' => "DONA ROPA EN BUEN\nESTADO",
        'description' => 'Tu ropa puede abrigar a una familia que lo necesita. Recibimos prendas limpias y en buen estado para niños, jóvenes y adultos. Nos aseguramos de que cada prenda llegue a quienes más la necesitan.',
        'accent_title' => 'EJEMPLOS DE LO QUE PUEDES DONAR',
        'items' => [
            ['icon' => 'coat', 'label' => 'Casacas y chompas'],
            ['icon' => 'box', 'label' => 'Pantalones y buzos'],
            ['icon' => 'heart', 'label' => 'Mantas y frazadas'],
            ['icon' => 'pin', 'label' => 'Zapatos y botas'],
            ['icon' => 'users', 'label' => 'Gorros y bufandas'],
        ],
        'aside_title' => "¿DÓNDE Y CÓMO\nENTREGAR?",
        'aside_items' => [
            [
                'icon' => 'pin',
                'title' => 'Llévalas a uno de nuestros puntos de acopio.',
                'link_text' => 'Ver puntos de acopio',
                'link_href' => BASE_URL . 'puntos-de-acopio',
            ],
            [
                'icon' => 'heart',
                'title' => 'Coordina una colecta con tu colegio, empresa o comunidad.',
                'link_text' => 'Más información',
                'link_href' => BASE_URL . 'contacto?motivo=Aliado',
            ],
            [
                'icon' => 'mail',
                'title' => '¿No puedes salir de casa?',
                'text' => 'Escríbenos por WhatsApp y te orientamos sobre la mejor forma de realizar tu entrega.',
            ],
        ],
        'primary_cta' => ['label' => 'VER PUNTOS DE ACOPIO', 'href' => BASE_URL . 'puntos-de-acopio', 'variant' => 'navy'],
        'secondary_cta' => ['label' => 'ORGANIZA UNA COLECTA', 'href' => BASE_URL . 'contacto?motivo=Aliado', 'variant' => 'outline'],
        'note' => 'Todas las prendas deben estar limpias, secas y en buen estado.',
    ],
    [
        'key' => 'voluntariado',
        'label' => 'SÉ VOLUNTARIO',
        'title' => "SÉ VOLUNTARIO Y\nSUMA TU TIEMPO",
        'description' => 'Tu tiempo puede cambiar vidas. Como voluntario, ayudas a organizar donaciones, apoyar en campañas, clasificar ropa, asistir en la logística y llevar esperanza a familias de las zonas altoandinas de Tacna.',
        'accent_title' => '¿CÓMO PUEDES AYUDAR?',
        'items' => [
            ['icon' => 'coat', 'label' => 'Clasificación de ropa'],
            ['icon' => 'megaphone', 'label' => 'Apoyo en campañas'],
            ['icon' => 'truck', 'label' => 'Logística y entrega'],
            ['icon' => 'phone', 'label' => 'Difusión en redes'],
            ['icon' => 'check', 'label' => 'Registro y apoyo'],
        ],
        'aside_title' => "¿QUÉ\nNECESITAMOS\nDE TI?",
        'aside_items' => [
            ['icon' => 'users', 'title' => 'Compromiso y responsabilidad'],
            ['icon' => 'clock', 'title' => 'Disponibilidad según tu tiempo'],
            ['icon' => 'heart', 'title' => 'Ganas de ayudar'],
            ['icon' => 'handshake', 'title' => 'Trabajo en equipo'],
        ],
        'primary_cta' => ['label' => 'QUIERO SER VOLUNTARIO', 'href' => BASE_URL . 'voluntariado', 'variant' => 'navy'],
        'secondary_cta' => ['label' => 'MÁS INFORMACIÓN', 'href' => BASE_URL . 'contacto?motivo=Voluntariado', 'variant' => 'outline'],
    ],
    [
        'key' => 'punto-de-acopio',
        'label' => 'ABRE UN PUNTO DE ACOPIO',
        'title' => "ABRE UN PUNTO DE\nACOPIO EN TU COMUNIDAD",
        'description' => 'Negocios, colegios, organizaciones y espacios locales pueden convertirse en puntos de acopio para recibir ropa y frazadas para la campaña.',
        'accent_title' => 'LUGARES IDEALES',
        'items' => [
            ['icon' => 'heart', 'label' => 'Cafeterías o restaurantes'],
            ['icon' => 'house', 'label' => 'Colegios e institutos'],
            ['icon' => 'building', 'label' => 'Empresas y oficinas'],
            ['icon' => 'target', 'label' => 'Universidades'],
            ['icon' => 'handshake', 'label' => 'Organizaciones aliadas'],
        ],
        'aside_title' => "¿CÓMO\nFUNCIONA?",
        'aside_items' => [
            ['icon' => 'users', 'title' => 'Te orientamos en el proceso'],
            ['icon' => 'check', 'title' => 'Recibes material de apoyo'],
            ['icon' => 'box', 'title' => 'Recolectas donaciones en tu espacio'],
            ['icon' => 'truck', 'title' => 'Coordinamos cómo entregar las donaciones'],
        ],
        'primary_cta' => ['label' => 'QUIERO SER PUNTO DE ACOPIO', 'href' => BASE_URL . 'contacto?motivo=Punto%20de%20acopio', 'variant' => 'navy'],
        'secondary_cta' => ['label' => 'SOLICITAR INFORMACIÓN', 'href' => BASE_URL . 'contacto?motivo=Punto%20de%20acopio', 'variant' => 'outline'],
    ],
];

$activeHelpKey = 'donar-ropa';
?>

<main>
    <section class="hero hero--interior">
        <img src="<?= htmlspecialchars($page['hero']['image']) ?>" alt="Voluntaria acompañando a una niña en comunidad altoandina" class="hero__bg">
        <div class="hero__shade"></div>
        <div class="site-shell hero__inner hero__inner--interior">
            <div class="hero__copy">
                <h1 class="hero__title">
                    <span><?= htmlspecialchars($page['hero']['title'][0]) ?></span>
                    <span><?= htmlspecialchars($page['hero']['title'][1]) ?></span>
                </h1>
                <p class="hero__text"><?= htmlspecialchars($page['hero']['text']) ?></p>
                <ul class="hero__metrics">
                    <li><span class="hero__metric-icon"><?= site_icon('target') ?></span><span>Transparencia y confianza</span></li>
                    <li><span class="hero__metric-icon"><?= site_icon('chart') ?></span><span>Impacto real y medible</span></li>
                    <li><span class="hero__metric-icon"><?= site_icon('users') ?></span><span>100% orientado a las familias</span></li>
                </ul>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="site-shell">
            <div class="help-experience ac2-help" data-help-tabs data-reveal="up">
                <div class="help-experience__head ac2-help__head">
                    <span class="section-kicker section-kicker--center">Elige tu forma de ayudar</span>
                    <h2 class="section-title">3 FORMAS DE HACER LA DIFERENCIA</h2>
                    <p class="section-subtitle">Toca una opción para ver qué implica, qué necesitamos y cómo dar el siguiente paso.</p>
                </div>

                <div class="help-tabs" role="tablist" aria-label="Formas de ayudar">
                    <?php foreach ($helpTabs as $index => $tab): ?>
                        <?php
                        $isActive = $tab['key'] === $activeHelpKey;
                        $tabId = 'help-tab-' . $tab['key'];
                        $panelId = 'help-panel-' . $tab['key'];
                        ?>
                        <button
                            type="button"
                            id="<?= htmlspecialchars($tabId) ?>"
                            class="help-tabs__button<?= $isActive ? ' is-active' : '' ?>"
                            role="tab"
                            aria-selected="<?= $isActive ? 'true' : 'false' ?>"
                            aria-controls="<?= htmlspecialchars($panelId) ?>"
                            tabindex="<?= $isActive ? '0' : '-1' ?>"
                            data-help-tab="<?= htmlspecialchars($tab['key']) ?>"
                            data-help-target="<?= htmlspecialchars($panelId) ?>"
                        >
                            <span><?= htmlspecialchars($tab['label']) ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>

                <?php foreach ($helpTabs as $tab): ?>
                    <?php
                    $isActive = $tab['key'] === $activeHelpKey;
                    $tabId = 'help-tab-' . $tab['key'];
                    $panelId = 'help-panel-' . $tab['key'];
                    ?>
                    <section
                        id="<?= htmlspecialchars($panelId) ?>"
                        class="help-panel<?= $isActive ? ' is-active' : '' ?>"
                        role="tabpanel"
                        aria-labelledby="<?= htmlspecialchars($tabId) ?>"
                        data-help-panel="<?= htmlspecialchars($tab['key']) ?>"
                        <?= $isActive ? '' : 'hidden' ?>
                    >
                        <div class="help-panel__grid help-panel__grid--<?= htmlspecialchars($tab['key']) ?>">
                            <div class="help-panel__copy">
                                <h3><?= nl2br(htmlspecialchars($tab['title'])) ?></h3>
                                <p><?= htmlspecialchars($tab['description']) ?></p>
                                <div class="help-panel__cluster">
                                    <h4><?= htmlspecialchars($tab['accent_title']) ?></h4>
                                    <div class="help-badge-grid">
                                        <?php foreach ($tab['items'] as $item): ?>
                                            <article class="help-badge-card">
                                                <span class="help-badge-card__icon"><?= site_icon($item['icon']) ?></span>
                                                <strong><?= htmlspecialchars($item['label']) ?></strong>
                                            </article>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php if (!empty($tab['note'])): ?>
                                        <div class="help-inline-note">
                                            <span class="help-inline-note__icon"><?= site_icon('heart') ?></span>
                                            <p><?= htmlspecialchars($tab['note']) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="help-panel__visual">
                                <?php if ($tab['key'] === 'donar-ropa'): ?>
                                    <div class="help-visual help-visual--ropa" aria-hidden="true">
                                        <div class="help-visual__soft-card help-visual__soft-card--top"></div>
                                        <div class="help-visual__soft-card help-visual__soft-card--bottom"></div>
                                        <div class="help-visual__box">
                                            <img src="<?= BASE_URL ?>images/logo_heart.webp" alt="">
                                            <strong>ABRIGANDO<br>CORAZONES</strong>
                                        </div>
                                        <div class="help-visual__stack">
                                            <span class="help-visual__fabric help-visual__fabric--navy"></span>
                                            <span class="help-visual__fabric help-visual__fabric--orange"></span>
                                            <span class="help-visual__fabric help-visual__fabric--sand"></span>
                                            <span class="help-visual__fabric help-visual__fabric--light"></span>
                                        </div>
                                        <div class="help-visual__heart-knit"></div>
                                        <div class="help-visual__mini-box"></div>
                                    </div>
                                <?php elseif ($tab['key'] === 'voluntariado'): ?>
                                    <div class="help-visual help-visual--volunteer" aria-hidden="true">
                                        <div class="help-visual__poster">
                                            <img src="<?= BASE_URL ?>images/logo_heart.webp" alt="">
                                            <strong>ABRIGANDO<br>CORAZONES</strong>
                                            <span>Abrigamos cuerpos, calentamos corazones.</span>
                                        </div>
                                        <div class="help-visual__team">
                                            <div class="volunteer-figure volunteer-figure--left">
                                                <span class="volunteer-figure__head"></span>
                                                <span class="volunteer-figure__body"></span>
                                                <span class="volunteer-figure__arm volunteer-figure__arm--left"></span>
                                                <span class="volunteer-figure__arm volunteer-figure__arm--right"></span>
                                            </div>
                                            <div class="volunteer-figure volunteer-figure--center">
                                                <span class="volunteer-figure__head"></span>
                                                <span class="volunteer-figure__body"></span>
                                                <span class="volunteer-figure__arm volunteer-figure__arm--left"></span>
                                                <span class="volunteer-figure__arm volunteer-figure__arm--right"></span>
                                            </div>
                                            <div class="volunteer-figure volunteer-figure--right">
                                                <span class="volunteer-figure__head"></span>
                                                <span class="volunteer-figure__body"></span>
                                                <span class="volunteer-figure__arm volunteer-figure__arm--left"></span>
                                                <span class="volunteer-figure__arm volunteer-figure__arm--right"></span>
                                                <span class="volunteer-figure__board"></span>
                                            </div>
                                        </div>
                                        <div class="help-visual__sorting-box">
                                            <strong>ABRIGANDO<br>CORAZONES</strong>
                                        </div>
                                        <div class="help-visual__folds help-visual__folds--left">
                                            <span></span><span></span><span></span>
                                        </div>
                                        <div class="help-visual__folds help-visual__folds--right">
                                            <span></span><span></span><span></span>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="help-visual help-visual--point" aria-hidden="true">
                                        <div class="help-visual__stall-sign">
                                            <img src="<?= BASE_URL ?>images/logo_heart.webp" alt="">
                                            <strong>ABRIGANDO<br>CORAZONES</strong>
                                            <span>Dona ropa, abriga vidas</span>
                                        </div>
                                        <div class="help-visual__stall-awning"></div>
                                        <div class="help-visual__stall-body">
                                            <div class="help-visual__stall-window"></div>
                                            <div class="help-visual__stall-counter">
                                                <span class="help-visual__fabric help-visual__fabric--navy"></span>
                                                <span class="help-visual__fabric help-visual__fabric--sand"></span>
                                                <span class="help-visual__fabric help-visual__fabric--orange"></span>
                                            </div>
                                            <div class="help-visual__stall-brand">
                                                <img src="<?= BASE_URL ?>images/logo_heart.webp" alt="">
                                                <strong>ABRIGANDO<br>CORAZONES</strong>
                                            </div>
                                            <div class="help-visual__stall-icons">
                                                <span><?= site_icon('coat') ?></span>
                                                <span><?= site_icon('house') ?></span>
                                                <span><?= site_icon('heart') ?></span>
                                                <span><?= site_icon('users') ?></span>
                                            </div>
                                        </div>
                                        <div class="help-visual__plant"></div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <aside class="help-panel__aside">
                                <h3><?= nl2br(htmlspecialchars($tab['aside_title'])) ?></h3>
                                <ul class="help-aside-list">
                                    <?php foreach ($tab['aside_items'] as $item): ?>
                                        <li>
                                            <span class="help-aside-list__icon"><?= site_icon($item['icon']) ?></span>
                                            <div>
                                                <strong><?= htmlspecialchars($item['title']) ?></strong>
                                                <?php if (!empty($item['link_href']) && !empty($item['link_text'])): ?>
                                                    <a href="<?= htmlspecialchars($item['link_href']) ?>"><?= htmlspecialchars($item['link_text']) ?></a>
                                                <?php elseif (!empty($item['text'])): ?>
                                                    <p><?= htmlspecialchars($item['text']) ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <div class="help-panel__actions">
                                    <a href="<?= htmlspecialchars($tab['primary_cta']['href']) ?>" class="btn btn--<?= htmlspecialchars($tab['primary_cta']['variant']) ?> btn--block"><?= htmlspecialchars($tab['primary_cta']['label']) ?></a>
                                    <a href="<?= htmlspecialchars($tab['secondary_cta']['href']) ?>" class="btn btn--<?= htmlspecialchars($tab['secondary_cta']['variant']) ?> btn--block"><?= htmlspecialchars($tab['secondary_cta']['label']) ?></a>
                                </div>
                            </aside>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section section--soft">
        <div class="site-shell yes-no-grid ac2-yesno" data-stagger>
            <article class="yes-no-card yes-no-card--yes" data-reveal="left">
                <h3><span><?= site_icon('check') ?></span>QUÉ SÍ DONAR</h3>
                <ul>
                    <li>Ropa de abrigo para niños, jóvenes y adultos.</li>
                    <li>Mantas, frazadas, colchas y sacos de dormir.</li>
                    <li>Ropa de algodón y lana en buen estado.</li>
                    <li>Zapatos, botas o zapatillas en buen estado.</li>
                    <li>Gorros, guantes, bufandas y medias nuevas o en buen estado.</li>
                    <li>Ropa de bebé, mantas y accesorios de invierno.</li>
                </ul>
            </article>
            <article class="yes-no-card yes-no-card--no" data-reveal="right">
                <h3><span><?= site_icon('x') ?></span>QUÉ NO DONAR</h3>
                <ul>
                    <li>Ropa sucia, rota o húmeda.</li>
                    <li>Ropa interior usada.</li>
                    <li>Prendas de verano o incompletas.</li>
                    <li>Calzado en mal estado.</li>
                    <li>Juguetes rotos o incompletos.</li>
                    <li>Electrodomésticos o muebles.</li>
                </ul>
            </article>
        </div>
        <div class="site-shell">
            <div class="donation-checklist" data-donation-checklist data-reveal="up">
                <div class="donation-checklist__head">
                    <span class="section-kicker">Checklist de preparación</span>
                    <h2 class="section-title section-title--left">ANTES DE ENTREGAR TU DONACIÓN</h2>
                </div>
                <div class="donation-checklist__items ac2-checklist__items">
                    <label><input type="checkbox"> <span>¿Está limpia?</span></label>
                    <label><input type="checkbox"> <span>¿Está seca?</span></label>
                    <label><input type="checkbox"> <span>¿Está completa?</span></label>
                    <label><input type="checkbox"> <span>¿Está en buen estado?</span></label>
                </div>
                <p class="donation-checklist__status" data-checklist-status role="status" aria-live="polite">Marca cada paso antes de entregar tu aporte.</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="site-shell">
            <h2 class="section-title">PROCESO DE DONACIÓN</h2>
            <p class="section-subtitle">Así hacemos llegar tu ayuda a quien más la necesita.</p>
            <div class="process-row ac2-process" data-stagger>
                <article data-reveal="up"><span>1</span><h3>ENTREGA</h3><p>Llevas tu donación a un punto de acopio o coordinas con el equipo la forma de entregarla.</p></article>
                <article data-reveal="up"><span>2</span><h3>CLASIFICACIÓN</h3><p>Nuestro equipo revisa, clasifica y selecciona las prendas para asegurar calidad y utilidad.</p></article>
                <article data-reveal="up"><span>3</span><h3>DISTRIBUCIÓN</h3><p>Transportamos las donaciones a las comunidades altoandinas de Tacna.</p></article>
                <article data-reveal="up"><span>4</span><h3>ENTREGA A FAMILIAS</h3><p>Las entregamos con respeto y cercanía a las familias que más lo necesitan.</p></article>
            </div>
        </div>
    </section>

    <section class="section section--tight">
        <div class="site-shell">
            <h2 class="section-title">PREGUNTAS FRECUENTES</h2>
            <div class="faq-grid" data-stagger>
                <?php foreach ($faqHome as $item): ?>
                    <details class="faq-item" data-reveal="up">
                        <summary><span><?= htmlspecialchars($item['question']) ?></span><span class="faq-item__chevron"><?= site_icon('chevron-down') ?></span></summary>
                        <p><?= htmlspecialchars($item['answer']) ?></p>
                    </details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="contact-banner" hidden aria-hidden="true">
        <div class="site-shell contact-banner__inner">
            <div class="contact-banner__lead">
                <span class="scarf-heart">
                    <span class="scarf-heart__heart"><img src="<?= BASE_URL ?>images/logo_heart.webp" alt=""></span>
                    <span class="scarf-heart__scarf"></span>
                </span>
                <div>
                    <h2>SÚMATE A ESTA<br>CADENA DE SOLIDARIDAD</h2>
                    <p>Inscríbete como voluntario, organiza una colecta o recibe más información de cómo ayudar.</p>
                </div>
            </div>
            <form action="<?= BASE_URL ?>contact/subscribe" method="POST" class="contact-banner__form" data-loading-form>
                <?= \App\Core\Csrf::field() ?>
                <input type="hidden" name="redirect" value="contacto">
                <input type="text" name="website" value="" class="sr-only" tabindex="-1" autocomplete="off" aria-hidden="true">
                <input type="text" name="nombre" placeholder="Nombre completo" aria-label="Nombre completo" autocomplete="name" required>
                <input type="tel" name="telefono" placeholder="Teléfono / WhatsApp" aria-label="Teléfono o WhatsApp" autocomplete="tel" required>
                <input type="email" name="email" placeholder="Correo electrónico" aria-label="Correo electrónico" autocomplete="email" required>
                <select name="como_se_entero" aria-label="¿Cómo deseas ayudar?" required>
                    <option value="">¿Cómo deseas ayudar?</option>
                    <option value="Dona ropa">Donar ropa</option>
                    <option value="Voluntariado">Voluntariado</option>
                    <option value="Colecta">Organizar una colecta</option>
                    <option value="Punto de acopio">Punto de acopio</option>
                </select>
                <button type="submit" class="btn btn--primary btn--block" data-submit-label="INSCRIBIRME">INSCRIBIRME</button>
            </form>
            <div class="contact-banner__whatsapp">
                <h3>¿PREFIERES WHATSAPP?</h3>
                <p>Escríbenos y te ayudamos a coordinar tu apoyo.</p>
                <strong><?= htmlspecialchars($contact['phone']) ?></strong>
                <a href="<?= htmlspecialchars($contact['phone_link']) ?>" target="_blank" rel="noopener" class="btn btn--outline btn--block">ESCRIBIR POR WHATSAPP</a>
            </div>
        </div>
    </section>

    <section class="section section--tight" hidden aria-hidden="true">
        <div class="site-shell cta-panel" data-reveal="up">
            <div>
                <span class="section-kicker">Comparte la campaña</span>
                <h2>CADA PERSONA QUE CONOCE LA CAMPAÑA PUEDE ACTIVAR OTRA CADENA DE AYUDA</h2>
                <p>Si todavía no puedes donar ropa, también puedes ampliar el impacto compartiendo esta causa con tu comunidad.</p>
            </div>
            <div class="cta-panel__actions">
                <a href="<?= htmlspecialchars($contact['phone_link']) ?>" target="_blank" rel="noopener" class="btn btn--primary">COMPARTIR POR WHATSAPP</a>
                <a href="<?= BASE_URL ?>impacto" class="btn btn--outline">VER IMPACTO</a>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
