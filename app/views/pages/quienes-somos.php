<?php require __DIR__ . '/../layouts/header.php'; ?>

<main>
    <section class="hero hero--interior">
        <img src="<?= htmlspecialchars($page['hero']['image']) ?>" alt="Equipo de Abrigando Corazones en jornada solidaria" class="hero__bg">
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

    <section class="section section--soft">
        <div class="site-shell">
            <div class="about-story" data-stagger>
                <article class="about-story__copy" data-reveal="left">
                    <span class="section-kicker">Nuestro propósito</span>
                    <h2 class="section-title section-title--left">UNA CAMPAÑA QUE CONECTA PERSONAS, ABRIGOS Y COMUNIDADES</h2>
                    <p>Somos una red ciudadana que coordina voluntariado, puntos de acopio, aliados y difusión para que cada prenda llegue con dignidad a las familias que más la necesitan.</p>
                    <p>Más que recolectar ropa, buscamos construir una respuesta organizada, humana y transparente frente al frío que afecta a comunidades altoandinas de Tacna.</p>
                </article>
                <article class="about-story__card about-story__card--pairs" data-reveal="up">
                    <h3>MISIÓN</h3>
                    <p>Movilizar solidaridad, organización y acción comunitaria para llevar abrigo y esperanza a familias de zonas altoandinas.</p>
                    <h3>VISIÓN</h3>
                    <p>Consolidar una red confiable de ayuda ciudadana capaz de responder con empatía y coordinación cada temporada de invierno.</p>
                </article>
                <article class="about-story__card" data-reveal="right">
                    <h3>¿CÓMO TRABAJAMOS?</h3>
                    <ul class="about-list about-list--steps">
                        <li>Articulamos puntos de acopio con horarios claros.</li>
                        <li>Clasificamos donaciones para asegurar utilidad y calidad.</li>
                        <li>Coordinamos aliados, voluntariado y entregas con enfoque humano.</li>
                        <li>Compartimos avances para reforzar confianza y transparencia.</li>
                    </ul>
                </article>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="site-shell">
            <div data-reveal="up">
                <span class="section-kicker section-kicker--center">Nuestra ruta</span>
                <h2 class="section-title">CÓMO HA CRECIDO LA CAMPAÑA</h2>
            </div>
            <div class="about-timeline" data-stagger>
                <article data-reveal="up">
                    <strong>01</strong>
                    <h3>NACE LA IDEA</h3>
                    <p>Identificamos la necesidad de articular ayuda frente al invierno en zonas altoandinas.</p>
                </article>
                <article data-reveal="up">
                    <strong>02</strong>
                    <h3>SE ACTIVAN LOS PRIMEROS APOYOS</h3>
                    <p>Ciudadanos, voluntarios y organizaciones comienzan a coordinar recolección y difusión.</p>
                </article>
                <article data-reveal="up">
                    <strong>03</strong>
                    <h3>SE SUMAN PUNTOS Y ALIADOS</h3>
                    <p>La campaña fortalece su alcance con nuevos espacios de recepción y articulación institucional.</p>
                </article>
                <article data-reveal="up">
                    <strong>04</strong>
                    <h3>LLEGA EL ABRIGO</h3>
                    <p>Cada entrega busca responder con respeto, organización y cercanía a las familias beneficiarias.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="section section--soft">
        <div class="site-shell">
            <div data-reveal="up">
                <span class="section-kicker section-kicker--center">Valores que sostienen la campaña</span>
                <h2 class="section-title">LO QUE NOS MUEVE</h2>
            </div>
            <div class="values-grid" data-stagger>
                <?php foreach ([
                    ['SOLIDARIDAD', 'Actuamos para que la ayuda llegue a tiempo y con propósito.'],
                    ['EMPATÍA', 'Escuchamos necesidades reales y respondemos con humanidad.'],
                    ['RESPETO', 'Cada familia merece atención digna y cercana.'],
                    ['TRANSPARENCIA', 'Mostramos avances, procesos y resultados de manera clara.'],
                    ['COMPROMISO', 'Sostenemos el esfuerzo con responsabilidad compartida.'],
                    ['TRABAJO COLABORATIVO', 'Aliados, voluntarios y ciudadanía hacen posible el impacto.'],
                ] as $value): ?>
                    <article class="value-card" data-reveal="up">
                        <h3><?= htmlspecialchars($value[0]) ?></h3>
                        <p><?= htmlspecialchars($value[1]) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="site-shell cta-panel" data-reveal="up">
            <div>
                <span class="section-kicker">Súmate a esta red</span>
                <h2>DETALLAR EL IMPACTO EMPIEZA POR DAR EL SIGUIENTE PASO</h2>
                <p>Si quieres donar, ser voluntario o impulsar una alianza, estamos listos para coordinar contigo.</p>
            </div>
            <div class="cta-panel__actions">
                <a href="<?= BASE_URL ?>como-ayudar" class="btn btn--primary" data-prefetch-link>QUIERO AYUDAR</a>
                <a href="<?= BASE_URL ?>contacto" class="btn btn--outline" data-prefetch-link>CONTACTAR AL EQUIPO</a>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
