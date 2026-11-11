<?php
require __DIR__ . '/../layouts/header.php';

$allowedReasons = ['Dona ropa', 'Voluntariado', 'Punto de acopio', 'Aliado', 'Convocatoria artística', 'Consulta', 'Otro'];
$initialReason = trim((string) ($_GET['motivo'] ?? ''));
if (!in_array($initialReason, $allowedReasons, true)) {
    $initialReason = 'Consulta';
}
?>

<main>
    <section class="hero hero--interior">
        <img src="<?= htmlspecialchars($page['hero']['image']) ?>" alt="Equipo de Abrigando Corazones listo para coordinar ayuda" class="hero__bg">
        <div class="hero__shade"></div>
        <div class="site-shell hero__inner hero__inner--interior">
            <div class="hero__copy">
                <span class="section-kicker">Estamos para escucharte</span>
                <h1 class="hero__title">
                    <span>HABLEMOS.</span>
                    <span>CADA AYUDA CUENTA.</span>
                </h1>
                <p class="hero__text">Si quieres donar, sumarte como voluntario, abrir un punto de acopio o coordinar una alianza, este es el mejor lugar para empezar.</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="site-shell">
            <div class="contact-reasons ac2-reasons" id="motivo-contacto" data-contact-reasons data-reveal="up">
                <div class="ac2-reasons__head">
                    <span class="section-kicker">Paso 1 · Motivo de contacto</span>
                    <p class="ac2-reasons__hint">Elige una opción y se aplicará al formulario de abajo. Si no eliges ninguna, tu mensaje llega como consulta general.</p>
                </div>
                <div class="help-selector__choices" role="group" aria-label="Motivo de contacto">
                    <button type="button" class="help-selector__choice" data-contact-reason="Dona ropa" aria-pressed="false">DONAR ROPA</button>
                    <button type="button" class="help-selector__choice" data-contact-reason="Voluntariado" aria-pressed="false">VOLUNTARIADO</button>
                    <button type="button" class="help-selector__choice" data-contact-reason="Punto de acopio" aria-pressed="false">PUNTO DE ACOPIO</button>
                    <button type="button" class="help-selector__choice" data-contact-reason="Aliado" aria-pressed="false">ALIANZA</button>
                    <button type="button" class="help-selector__choice" data-contact-reason="Convocatoria artística" aria-pressed="false">CONVOCATORIA ARTÍSTICA</button>
                    <button type="button" class="help-selector__choice" data-contact-reason="Consulta" aria-pressed="false">CONSULTA</button>
                    <button type="button" class="help-selector__choice" data-contact-reason="Otro" aria-pressed="false">OTRO</button>
                </div>
            </div>

            <div class="contact-page contact-page--editorial contact-page--message" data-stagger>
                <div class="contact-page__lead" data-reveal="left">
                    <span class="section-kicker">Paso 2 · Canales directos</span>
                    <h2>COORDINEMOS EL SIGUIENTE PASO DE FORMA CLARA Y CERCANA</h2>
                    <p>Cuéntanos qué necesitas y el equipo correcto recibirá tu solicitud. Mientras más claro sea tu mensaje, más rápido podremos orientarte.</p>
                    <ul>
                        <li><span><?= site_icon('phone') ?></span><span><?= htmlspecialchars($contact['phone']) ?></span></li>
                        <?php if (!empty($contact['email'])): ?>
                            <li><span><?= site_icon('mail') ?></span><span><?= htmlspecialchars($contact['email']) ?></span></li>
                        <?php endif; ?>
                        <li><span><?= site_icon('pin') ?></span><span><?= htmlspecialchars($contact['location']) ?></span></li>
                    </ul>
                    <div class="cta-panel__actions">
                        <a href="<?= htmlspecialchars($contact['phone_link']) ?>" target="_blank" rel="noopener" class="btn btn--primary">ESCRÍBENOS POR WHATSAPP</a>
                    </div>
                </div>

                <form action="<?= BASE_URL ?>contact/subscribe" method="POST" class="contact-page__form ac2-form" data-loading-form data-reveal="right" id="contacto-form">
                    <?= \App\Core\Csrf::field() ?>
                    <div class="ac2-form__head">
                        <span class="section-kicker">Paso 3 · Escríbenos</span>
                        <p class="contact-page__form-note">Cuatro campos y listo. El motivo se toma de <a href="#motivo-contacto">tu selección de arriba</a>.</p>
                    </div>
                    <div class="form-intent-card" aria-label="Consejo para enviar el mensaje">
                        <strong>Mientras más concreto sea tu mensaje, más rápido podremos ayudarte.</strong>
                        <span>Ejemplo: “Quiero donar 2 bolsas de ropa en Tacna Centro” o “Mi negocio puede ser punto de acopio”.</span>
                    </div>
                    <input type="hidden" name="redirect" value="contacto">
                    <input type="text" name="website" value="" class="sr-only" tabindex="-1" autocomplete="off" aria-hidden="true">
                    <input type="hidden" name="como_se_entero" value="<?= htmlspecialchars($initialReason) ?>" data-contact-reason-select data-contact-initial-reason="<?= htmlspecialchars($initialReason) ?>">
                    <label class="ac2-field" for="contacto-nombre">
                        <span class="ac2-field__label">Nombre completo <em aria-hidden="true">*</em></span>
                        <input id="contacto-nombre" type="text" name="nombre" placeholder="Ej.: María Quispe" autocomplete="name" minlength="3" required>
                    </label>
                    <label class="ac2-field" for="contacto-telefono">
                        <span class="ac2-field__label">Teléfono o WhatsApp <em aria-hidden="true">*</em></span>
                        <input id="contacto-telefono" type="tel" inputmode="tel" name="telefono" placeholder="Ej.: 900 000 000" autocomplete="tel" required>
                    </label>
                    <label class="ac2-field" for="contacto-email">
                        <span class="ac2-field__label">Correo electrónico <em aria-hidden="true">*</em></span>
                        <input id="contacto-email" type="email" name="email" placeholder="nombre@correo.com" autocomplete="email" required>
                    </label>
                    <label class="ac2-field ac2-field--message" for="contacto-mensaje">
                        <span class="ac2-field__label">Tu mensaje <em aria-hidden="true">*</em></span>
                        <textarea id="contacto-mensaje" name="mensaje" rows="5" maxlength="600" placeholder="Escribe tu mensaje. Ej.: quiero donar ropa, coordinar una colecta, abrir un punto de acopio o resolver una duda." required></textarea>
                    </label>
                    <button type="submit" class="btn btn--primary btn--block" data-submit-label="ENVIAR MENSAJE">ENVIAR MENSAJE</button>
                    <p class="ac2-form__foot">Los campos con <em aria-hidden="true">*</em><span class="sr-only">asterisco</span> son obligatorios.</p>
                </form>
            </div>
        </div>
    </section>

    <section class="section section--soft">
        <div class="site-shell">
            <span class="section-kicker section-kicker--center">Dudas frecuentes</span>
            <h2 class="section-title">ANTES DE ESCRIBIRNOS, TAL VEZ ESTO TE AYUDE</h2>
            <div class="faq-grid" data-stagger>
                <?php foreach ([
                    ['¿Puedo coordinar una colecta con mi empresa o colegio?', 'Sí. Podemos ayudarte a organizar una colecta o una alianza de apoyo.'],
                    ['¿Cómo sé cuál es el punto de acopio más cercano?', 'En la sección de puntos de acopio encontrarás referencias, horarios y enlaces directos a Maps.'],
                    ['¿Puedo apoyar solo con algunas horas?', 'Sí. El voluntariado también puede coordinarse para jornadas puntuales según disponibilidad.'],
                    ['¿Y si mi consulta no encaja en una categoría?', 'Escríbenos igual. Revisaremos tu mensaje y te orientaremos con el siguiente paso.'],
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
