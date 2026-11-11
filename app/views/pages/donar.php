<?php require __DIR__ . '/../layouts/header.php'; ?>

<main>
    <section class="hero hero--interior">
        <img src="<?= htmlspecialchars($page['hero']['image']) ?>" alt="Abrigos y ayuda preparados para donación" class="hero__bg">
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
            <span class="section-kicker section-kicker--center">Tu punto de partida</span>
            <h2 class="section-title">ELIGE POR DÓNDE EMPEZAR</h2>
            <p class="section-subtitle">Cada vía lleva a una acción concreta. Si dudas, escríbenos y te orientamos.</p>
            <div class="donar-grid ac2-donar-grid" data-stagger>
                <article class="donar-card ac2-donar-card" data-reveal="left">
                    <span class="ac2-donar-card__icon" aria-hidden="true"><?= site_icon('coat') ?></span>
                    <span class="section-kicker">Donación en especie</span>
                    <h2>ROPA, FRAZADAS Y ABRIGO EN BUEN ESTADO</h2>
                    <p>Si tienes ropa o abrigo listo para entregar, te ayudamos a identificar qué sí recibir y dónde llevarlo sin complicaciones.</p>
                    <div class="donar-card__actions">
                        <a href="<?= BASE_URL ?>como-ayudar#donar-ropa" class="btn btn--primary">VER QUÉ DONAR</a>
                        <a href="<?= BASE_URL ?>puntos-de-acopio" class="btn btn--outline">VER PUNTOS DE ACOPIO</a>
                    </div>
                </article>

                <article class="donar-card ac2-donar-card" data-reveal="up">
                    <span class="ac2-donar-card__icon" aria-hidden="true"><?= site_icon('users') ?></span>
                    <span class="section-kicker">Voluntariado</span>
                    <h2>SUMA TU TIEMPO A LA CADENA DE SOLIDARIDAD</h2>
                    <p>Si quieres apoyar en clasificación, campañas, difusión o jornadas de entrega, podemos ayudarte a encontrar el rol adecuado.</p>
                    <div class="donar-card__actions">
                        <a href="<?= BASE_URL ?>como-ayudar#voluntariado" class="btn btn--navy">QUIERO SER VOLUNTARIO</a>
                        <a href="<?= BASE_URL ?>voluntariado" class="btn btn--outline">VER MÁS DETALLES</a>
                    </div>
                </article>

                <article class="donar-card donar-card--accent ac2-donar-card" data-reveal="right">
                    <span class="ac2-donar-card__icon" aria-hidden="true"><?= site_icon('handshake') ?></span>
                    <span class="section-kicker">Apoyo coordinado</span>
                    <h2>COLECTAS, ALIANZAS Y PUNTOS DE ACOPIO</h2>
                    <p>Si representas una organización, empresa, colegio o comunidad, podemos orientarte para abrir un punto de acopio o coordinar una colecta.</p>
                    <ul class="donar-list">
                        <li>Coordinación clara con el equipo.</li>
                        <li>Rutas reales para entregar lo recolectado.</li>
                        <li>Sin promesas de logística no confirmada.</li>
                    </ul>
                    <div class="donar-card__actions">
                        <a href="<?= BASE_URL ?>como-ayudar#punto-de-acopio" class="btn btn--primary">ABRIR UN PUNTO</a>
                        <a href="<?= BASE_URL ?>contacto?motivo=Aliado" class="btn btn--outline">COORDINAR APOYO</a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="site-shell">
            <span class="section-kicker section-kicker--center">Tu aporte en contexto</span>
            <h2 class="section-title">CAMINO DE AYUDA</h2>
            <div class="donor-journey ac2-journey" data-stagger>
                <article data-reveal="up"><strong>1</strong><h3>ELIJO CÓMO AYUDAR</h3><p>Identificas si vas a donar ropa, sumarte como voluntario o coordinar una colecta.</p></article>
                <article data-reveal="up"><strong>2</strong><h3>PREPARO MI APORTE</h3><p>Ordenas prendas, defines disponibilidad o conversas con tu organización.</p></article>
                <article data-reveal="up"><strong>3</strong><h3>COORDINO LA RUTA</h3><p>Encuentras el punto correcto, el formulario o el contacto adecuado.</p></article>
                <article data-reveal="up"><strong>4</strong><h3>ENTREGO O PARTICIPO</h3><p>La campaña recibe y organiza la ayuda con un enfoque claro y humano.</p></article>
            </div>
        </div>
    </section>

    <section class="section section--soft">
        <div class="site-shell cta-panel" data-reveal="up">
            <div>
                <span class="section-kicker">Comparte la campaña</span>
                <h2>CADA APORTE TAMBIÉN PUEDE EMPEZAR CON UNA RECOMENDACIÓN</h2>
                <p>Ayúdanos a llegar a más personas compartiendo la campaña o escribiéndonos directamente para coordinar el siguiente paso.</p>
            </div>
            <div class="cta-panel__actions">
                <a href="<?= htmlspecialchars($contact['phone_link']) ?>" target="_blank" rel="noopener" class="btn btn--primary">WHATSAPP</a>
                <a href="<?= BASE_URL ?>contacto" class="btn btn--outline">CONTACTO</a>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
