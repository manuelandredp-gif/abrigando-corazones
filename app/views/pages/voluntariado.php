<?php require __DIR__ . '/../layouts/header.php'; ?>

<main>
    <section class="hero hero--interior">
        <img src="<?= htmlspecialchars($page['hero']['image']) ?>" alt="Voluntarios de Abrigando Corazones en jornada de apoyo" class="hero__bg">
        <div class="hero__shade"></div>
        <div class="site-shell hero__inner hero__inner--interior">
            <div class="hero__copy">
                <span class="section-kicker"><?= htmlspecialchars($page['hero']['eyebrow']) ?></span>
                <h1 class="hero__title">
                    <span><?= htmlspecialchars($page['hero']['title'][0]) ?></span>
                    <span><?= htmlspecialchars($page['hero']['title'][1]) ?></span>
                </h1>
                <p class="hero__text"><?= htmlspecialchars($page['hero']['text']) ?></p>
                <div class="hero__buttons">
                    <a href="#formulario-voluntariado" class="btn btn--primary">QUIERO SUMARME</a>
                    <a href="<?= BASE_URL ?>como-ayudar" class="btn btn--ghost-light">VER OTRAS FORMAS DE AYUDAR</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section section--soft">
        <div class="site-shell">
            <div class="about-story" data-stagger>
                <article class="about-story__copy" data-reveal="left">
                    <span class="section-kicker">Por qué ser voluntario</span>
                    <h2 class="section-title section-title--left">UNA CAMPAÑA HUMANA TAMBIÉN NECESITA TIEMPO, CUIDADO Y COORDINACIÓN</h2>
                    <p>El voluntariado permite que cada donación se reciba, clasifique, organice y entregue con respeto. Es una forma directa de convertir intención solidaria en ayuda concreta.</p>
                    <p>Hay roles en campo, logística, comunicación y coordinación para personas con distintos tiempos y habilidades.</p>
                </article>
                <article class="about-story__card" data-reveal="up">
                    <h3>¿CÓMO FUNCIONA?</h3>
                    <ul class="about-list">
                        <li>Te registras con tus datos básicos y disponibilidad.</li>
                        <li>Identificamos las áreas donde mejor puedes aportar.</li>
                        <li>Coordinamos contigo según jornadas y necesidades reales.</li>
                        <li>Te acompañamos para que tu experiencia sea clara y útil.</li>
                    </ul>
                </article>
                <article class="about-story__card" data-reveal="right">
                    <h3>LO QUE APORTAS</h3>
                    <p>Tiempo, orden, empatía, comunicación y compromiso. Detrás de cada entrega hay personas que ayudan a que todo ocurra con cuidado y transparencia.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="site-shell">
            <span class="section-kicker section-kicker--center">Áreas de voluntariado</span>
            <h2 class="section-title">ENCUENTRA DÓNDE PUEDES APORTAR</h2>
            <p class="section-subtitle">Tres frentes de trabajo. Elige el que más se parezca a lo que puedes aportar.</p>
            <div class="volunteer-groups ac2-groups" data-stagger>
                <?php
                $volunteerGroups = [
                    'CAMPO' => ['Entrega', 'Recepción', 'Transporte'],
                    'LOGÍSTICA' => ['Clasificación', 'Inventario', 'Coordinación'],
                    'COMUNICACIÓN' => ['Difusión', 'Fotografía', 'Video', 'Diseño', 'Redes'],
                ];
                ?>
                <?php foreach ($volunteerGroups as $group => $areas): ?>
                    <article class="volunteer-group" data-reveal="up">
                        <h3><?= htmlspecialchars($group) ?></h3>
                        <div class="volunteer-group__chips">
                            <?php foreach ($areas as $area): ?>
                                <span><?= htmlspecialchars($area) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section section--soft">
        <div class="site-shell volunteer-wizard ac2-wizard" data-volunteer-wizard data-reveal="up">
            <div class="volunteer-wizard__intro">
                <span class="section-kicker">Recomendador rápido</span>
                <h2 class="section-title section-title--left">ENCUENTRA TU FORMA DE AYUDAR</h2>
                <p>Responde tres preguntas y te sugerimos áreas donde tu tiempo puede generar más impacto.</p>
            </div>
            <div class="volunteer-wizard__grid ac2-wizard__grid">
                <label for="wizard-mode" class="ac2-wizard__field">
                    <span><i class="ac2-wizard__num" aria-hidden="true">1</i>¿Prefieres trabajo en campo o digital?</span>
                    <select id="wizard-mode" data-volunteer-answer="mode">
                        <option value="">Elige una opción</option>
                        <option value="campo">Campo</option>
                        <option value="digital">Digital</option>
                        <option value="mixto">Ambos</option>
                    </select>
                </label>
                <label for="wizard-time" class="ac2-wizard__field">
                    <span><i class="ac2-wizard__num" aria-hidden="true">2</i>¿Cuánto tiempo puedes dedicar?</span>
                    <select id="wizard-time" data-volunteer-answer="time">
                        <option value="">Elige una opción</option>
                        <option value="breve">Jornadas puntuales</option>
                        <option value="medio">Algunas horas por semana</option>
                        <option value="amplio">Disponibilidad amplia</option>
                    </select>
                </label>
                <label for="wizard-skill" class="ac2-wizard__field">
                    <span><i class="ac2-wizard__num" aria-hidden="true">3</i>¿Qué habilidad quieres aportar?</span>
                    <select id="wizard-skill" data-volunteer-answer="skill">
                        <option value="">Elige una opción</option>
                        <option value="organizacion">Organización</option>
                        <option value="movilidad">Movilidad y apoyo físico</option>
                        <option value="contenido">Contenido y difusión</option>
                    </select>
                </label>
            </div>
            <div class="volunteer-wizard__result" data-volunteer-result role="status" aria-live="polite">
                Completa las tres respuestas para recibir sugerencias.
            </div>
            <p class="ac2-wizard__next">Con el área sugerida en mente, <a href="#formulario-voluntariado">completa tu inscripción</a> y elígela en el formulario.</p>
        </div>
    </section>

    <section class="section" id="formulario-voluntariado">
        <div class="site-shell contact-page contact-page--volunteer" data-stagger>
            <div class="contact-page__lead" data-reveal="left">
                <span class="section-kicker">Inscripción</span>
                <h2>INSCRÍBETE Y COORDINEMOS TU PARTICIPACIÓN</h2>
                <p>Cuéntanos cómo te gustaría sumarte, tu disponibilidad y la mejor forma de contactarte para próximas jornadas.</p>
                <ul>
                    <li><span><?= site_icon('users') ?></span><span>Roles en campo, logística, comunicación y coordinación.</span></li>
                    <li><span><?= site_icon('clock') ?></span><span>Participación según disponibilidad real.</span></li>
                    <li><span><?= site_icon('phone') ?></span><span>Contacto directo por WhatsApp para coordinar.</span></li>
                </ul>
            </div>
            <form action="<?= BASE_URL ?>contact/subscribe" method="POST" class="contact-page__form ac2-form" data-loading-form data-reveal="right">
                <?= \App\Core\Csrf::field() ?>
                <div class="ac2-form__head">
                    <span class="section-kicker">Formulario</span>
                    <p class="contact-page__form-note">Cuatro campos. Te escribimos por WhatsApp o correo cuando haya una jornada compatible.</p>
                </div>
                <input type="hidden" name="redirect" value="voluntariado">
                <input type="text" name="website" value="" class="sr-only" tabindex="-1" autocomplete="off" aria-hidden="true">
                <label class="ac2-field" for="voluntariado-nombre">
                    <span class="ac2-field__label">Nombre completo <em aria-hidden="true">*</em></span>
                    <input id="voluntariado-nombre" type="text" name="nombre" placeholder="Ej.: María Quispe" autocomplete="name" minlength="3" required>
                </label>
                <label class="ac2-field" for="voluntariado-telefono">
                    <span class="ac2-field__label">Teléfono o WhatsApp <em aria-hidden="true">*</em></span>
                    <input id="voluntariado-telefono" type="tel" inputmode="tel" name="telefono" placeholder="Ej.: 900 000 000" autocomplete="tel" required>
                </label>
                <label class="ac2-field" for="voluntariado-email">
                    <span class="ac2-field__label">Correo electrónico <em aria-hidden="true">*</em></span>
                    <input id="voluntariado-email" type="email" name="email" placeholder="nombre@correo.com" autocomplete="email" required>
                </label>
                <label class="ac2-field" for="voluntariado-area">
                    <span class="ac2-field__label">Área de interés principal <em aria-hidden="true">*</em></span>
                    <select id="voluntariado-area" name="como_se_entero" required>
                        <option value="">Elige un área</option>
                        <option value="Recepción">Recepción</option>
                        <option value="Clasificación">Clasificación</option>
                        <option value="Inventario">Inventario</option>
                        <option value="Transporte">Transporte</option>
                        <option value="Difusión">Difusión</option>
                        <option value="Fotografía y video">Fotografía y video</option>
                        <option value="Diseño y redes">Diseño y redes</option>
                        <option value="Coordinación">Coordinación</option>
                    </select>
                </label>
                <button type="submit" class="btn btn--primary btn--block" data-submit-label="QUIERO SUMARME">QUIERO SUMARME</button>
                <p class="ac2-form__foot">Los campos con <em aria-hidden="true">*</em><span class="sr-only">asterisco</span> son obligatorios.</p>
            </form>
        </div>
    </section>

    <section class="section section--tight">
        <div class="site-shell">
            <h2 class="section-title">PREGUNTAS FRECUENTES</h2>
            <div class="faq-grid" data-stagger>
                <?php foreach ([
                    ['¿Necesito experiencia previa?', 'No. Podemos orientarte según tus habilidades y el tipo de jornada disponible.'],
                    ['¿Puedo ayudar solo algunas horas?', 'Sí. También necesitamos apoyo puntual en campañas, clasificación y difusión.'],
                    ['¿Hay tareas fuera de campo?', 'Sí. Hay roles de comunicación, organización, registro y coordinación.'],
                    ['¿Cómo me contactarán?', 'Usaremos tu WhatsApp o correo para coordinar cuando existan actividades compatibles contigo.'],
                ] as $item): ?>
                    <details class="faq-item" data-reveal="up">
                        <summary><span><?= htmlspecialchars($item[0]) ?></span><span class="faq-item__chevron"><?= site_icon('chevron-down') ?></span></summary>
                        <p><?= htmlspecialchars($item[1]) ?></p>
                    </details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
