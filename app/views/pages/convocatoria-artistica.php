<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php
/** @var array $artCall */
$formUrl = $artCall['form_url'];
$isOpen = !empty($artCall['is_open']);
$daysLeft = (int) ($artCall['days_left'] ?? 0);
/*
 * Contacto directo con quien coordina la convocatoria.
 *
 * El numero y el mensaje viven en app/config/art_call.php: cambiar de persona
 * coordinadora no obliga a tocar la vista. Se limpia el numero de espacios y
 * signos porque wa.me solo acepta digitos.
 */
$coordinadora = $artCall['coordinator'] ?? [];
$coordTelefono = preg_replace('/\D+/', '', (string) ($coordinadora['phone'] ?? ''));
$whatsAppHref = $coordTelefono !== ''
    ? 'https://wa.me/' . $coordTelefono . '?text=' . rawurlencode((string) ($coordinadora['message'] ?? ''))
    : null;
?>

<main class="artcall">

    <!-- ================= AFICHE / PORTADA ================= -->
    <section class="artposter">
        <div class="artposter__paper" aria-hidden="true">
            <span class="blob blob--red"></span>
            <span class="blob blob--gold"></span>
            <span class="blob blob--violet"></span>
            <span class="blob blob--teal"></span>
            <span class="blob blob--orange"></span>
            <span class="artposter__grain"></span>
        </div>

        <div class="artposter__confetti" aria-hidden="true">
            <?php for ($i = 1; $i <= 14; $i++): ?><span class="c<?= $i ?>"></span><?php endfor; ?>
        </div>

        <div class="site-shell artposter__inner">

            <p class="artposter__org">
                <span class="artposter__org-dot" aria-hidden="true"></span>
                <?= htmlspecialchars($artCall['organizer']) ?>
            </p>

            <h1 class="artposter__title">
                <span class="artposter__word artposter__word--1">CONVOCATORIA</span>
                <span class="artposter__word artposter__word--2">
                    <em>ARTÍSTICA</em>
                    <svg class="artposter__underline" viewBox="0 0 600 26" preserveAspectRatio="none" aria-hidden="true">
                        <path d="M4 17 C 120 4, 250 24, 380 11 S 560 6, 596 15" />
                    </svg>
                </span>
                <span class="artposter__ribbon">&hearts;&nbsp; ABRIGANDO CORAZONES &nbsp;&hearts;</span>
            </h1>

            <div class="artposter__body">
                <div class="artposter__claim">
                    <p class="artposter__claim-big">Tu arte deja huella,<br><em>tu corazón abriga.</em></p>
                    <p class="artposter__claim-text">
                        Súmate al gran cierre de campaña y sé parte de una jornada solidaria
                        que llevará abrigo y esperanza a las comunidades altoandinas de
                        <b>Candarave, Tacna.</b>
                    </p>

                    <div class="artposter__actions">
                        <?php if ($isOpen): ?>
                            <a href="<?= htmlspecialchars($formUrl) ?>" class="artbtn artbtn--red" target="_blank" rel="noopener">
                                INSCRIBIR MI TALENTO
                                <span class="artbtn__icon"><?= site_icon('arrow-right') ?></span>
                            </a>
                        <?php endif; ?>
                        <a href="#buscamos-talentos" class="artbtn artbtn--outline">VER CATEGORÍAS</a>
                    </div>
                </div>

                <?php if ($isOpen): ?>
                    <aside class="artstamp" data-artcall-countdown data-deadline="<?= htmlspecialchars($artCall['deadline_iso']) ?>">
                        <span class="artstamp__label">Cierre de convocatoria</span>
                        <span class="artstamp__day"><?= htmlspecialchars(date('d', strtotime($artCall['deadline']))) ?></span>
                        <span class="artstamp__month">Septiembre</span>
                        <p class="artstamp__fallback"><?= htmlspecialchars($artCall['deadline_label']) ?></p>
                        <div class="artstamp__clock" aria-hidden="true">
                            <span><b data-countdown-days><?= $daysLeft ?></b><i>días</i></span>
                            <span><b data-countdown-hours>00</b><i>hrs</i></span>
                            <span><b data-countdown-minutes>00</b><i>min</i></span>
                        </div>
                    </aside>
                <?php else: ?>
                    <aside class="artstamp is-closed">
                        <span class="artstamp__label">Convocatoria cerrada</span>
                        <span class="artstamp__day"><?= htmlspecialchars(date('d', strtotime($artCall['deadline']))) ?></span>
                        <span class="artstamp__month">Septiembre</span>
                        <p class="artstamp__fallback">Escríbenos para la próxima jornada.</p>
                    </aside>
                <?php endif; ?>
            </div>
        </div>

        <div class="artposter__tear" aria-hidden="true"></div>
    </section>

    <!-- ================= MARQUESINA ================= -->
    <div class="artmarquee" aria-hidden="true">
        <div class="artmarquee__track">
            <?php for ($i = 0; $i < 2; $i++): ?>
                <span>Cantantes</span><i>&hearts;</i>
                <span>Danza</span><i>&hearts;</i>
                <span>Folclore</span><i>&hearts;</i>
                <span>Teatro</span><i>&hearts;</i>
                <span>Cultura viva</span><i>&hearts;</i>
                <span>Tu arte deja huella</span><i>&hearts;</i>
            <?php endfor; ?>
        </div>
    </div>

    <!-- ================= LLAMADO / TEXTO GRANDE ================= -->
    <section class="artlead">
        <div class="site-shell artlead__inner">
            <p class="artlead__eyebrow" data-reveal="up">Tu talento puede abrigar corazones</p>
            <p class="artlead__text" data-reveal="up">
                ¿Cantas, bailas, actúas o tienes un talento especial que quieres compartir?
                Buscamos artistas que quieran <mark>unir su talento a una causa que transforma realidades</mark>.
            </p>
            <ul class="artlead__facts" data-stagger>
                <li data-reveal="up">
                    <b>Gratis</b>
                    <span>Participación solidaria, sin costo ni audición previa.</span>
                </li>
                <li data-reveal="up">
                    <b>Certificado</b>
                    <span>Para todos los artistas colaboradores de la jornada.</span>
                </li>
                <li data-reveal="up">
                    <b>Candarave</b>
                    <span>Tu presentación acompaña la entrega de abrigo en zonas altoandinas.</span>
                </li>
            </ul>
        </div>
    </section>

    <!-- ================= CATEGORIAS (COLLAGE) ================= -->
    <section class="artcats" id="buscamos-talentos">
        <div class="site-shell">
            <h2 class="artsection-title" data-reveal="up">
                <span>BUSCAMOS</span><em>TALENTOS</em>
            </h2>

            <div class="artcats__grid" data-stagger>
                <?php foreach ($artCall['categories'] as $index => $category): ?>
                    <article class="artcat artcat--<?= htmlspecialchars($category['tone']) ?>" data-reveal="up">
                        <span class="artcat__num"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="artcat__icon"><?= site_icon($category['icon']) ?></span>
                        <h3><?= htmlspecialchars($category['label']) ?></h3>
                        <p><?= htmlspecialchars($category['text']) ?></p>
                        <span class="artcat__tape" aria-hidden="true"></span>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ================= PASOS ================= -->
    <section class="artsteps">
        <div class="site-shell">
            <h2 class="artsection-title artsection-title--light" data-reveal="up">
                <span>¿CÓMO</span><em>PARTICIPO?</em>
            </h2>
            <ol class="artsteps__list" data-stagger>
                <?php foreach ($artCall['steps'] as $index => $step): ?>
                    <li data-reveal="up">
                        <span class="artsteps__num" aria-hidden="true"><?= $index + 1 ?></span>
                        <div>
                            <h3><?= htmlspecialchars($step['title']) ?></h3>
                            <p><?= htmlspecialchars($step['text']) ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <!-- ================= AFICHE OFICIAL + BENEFICIOS ================= -->
    <section class="artpiece">
        <div class="site-shell artpiece__inner">
            <div class="artpiece__frame" data-reveal="left">
                <?php if (!empty($artCall['poster_url'])): ?>
                    <img
                        src="<?= htmlspecialchars($artCall['poster_url']) ?>"
                        alt="Afiche oficial de la convocatoria artística Abrigando Corazones"
                        loading="lazy" decoding="async">
                <?php else: ?>
                    <?php /* Mientras no se suba la pieza oficial a images/convocatoria/,
                             se muestra esta version tipografica. Nunca queda un hueco. */ ?>
                    <div class="artpiece__mock" role="img" aria-label="Convocatoria artística Abrigando Corazones. Cierre: <?= htmlspecialchars($artCall['deadline_label']) ?>.">
                        <span class="artpiece__mock-org">CPJ TACNA</span>
                        <strong>CONVOCATORIA</strong>
                        <em>ARTÍSTICA</em>
                        <span class="artpiece__mock-brand">&hearts; ABRIGANDO CORAZONES &hearts;</span>
                        <span class="artpiece__mock-claim">Tu arte deja huella,<br>tu corazón abriga.</span>
                        <span class="artpiece__mock-date"><?= htmlspecialchars($artCall['deadline_short']) ?></span>
                    </div>
                <?php endif; ?>
                <span class="artpiece__pin artpiece__pin--l" aria-hidden="true"></span>
                <span class="artpiece__pin artpiece__pin--r" aria-hidden="true"></span>
            </div>

            <div class="artpiece__copy" data-reveal="right">
                <h2>COMPARTE TU TALENTO Y SÉ <em>PROTAGONISTA</em></h2>
                <p class="artpiece__intro">
                    No es un concurso ni una audición: es una jornada solidaria. Cada
                    presentación acompaña la entrega de abrigo a familias que viven el
                    invierno altoandino a más de 3.000 metros de altura.
                </p>
                <ul class="artpiece__list">
                    <?php foreach ($artCall['benefits'] as $benefit): ?>
                        <li>
                            <span class="artpiece__check"><?= site_icon('check') ?></span>
                            <div>
                                <b><?= htmlspecialchars($benefit['title']) ?></b>
                                <span><?= htmlspecialchars($benefit['text']) ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p class="artpiece__accent">Juntos haremos de este día una fiesta de solidaridad y esperanza.</p>
            </div>
        </div>
    </section>

    <!-- ================= TICKET DE INSCRIPCION ================= -->
    <section class="artticket-wrap" id="inscripcion">
        <div class="site-shell">
            <div class="artticket" data-reveal="up">
                <div class="artticket__main">
                    <span class="artticket__kicker"><?= site_icon('sparkles') ?> Inscripciones abiertas</span>
                    <h2>¡TU TALENTO PUEDE<br><em>ABRIGAR CORAZONES!</em></h2>
                    <p>Llena el formulario y súmate al gran cierre de campaña. Es gratuito, toma menos de cinco minutos.</p>

                    <div class="artticket__actions">
                        <?php if ($isOpen): ?>
                            <a href="<?= htmlspecialchars($formUrl) ?>" class="artbtn artbtn--gold" target="_blank" rel="noopener">
                                INSCRIBIRME AHORA
                                <span class="artbtn__icon"><?= site_icon('arrow-right') ?></span>
                            </a>
                        <?php endif; ?>
                        <?php if ($whatsAppHref): ?>
                            <a href="<?= htmlspecialchars($whatsAppHref) ?>" class="artbtn artbtn--ghost" target="_blank" rel="noopener">
                                <span class="artbtn__icon"><?= site_icon('whatsapp') ?></span>
                                ESCRIBIR A LA COORDINACIÓN
                            </a>
                        <?php endif; ?>
                    </div>

                    <p class="artticket__url">
                        <?= site_icon('link') ?>
                        <a href="<?= htmlspecialchars($formUrl) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($formUrl) ?></a>
                    </p>
                </div>

                <div class="artticket__stub">
                    <span class="artticket__seal"><?= site_icon('heart') ?></span>
                    <b>CERTIFICADO<br>DE PARTICIPACIÓN</b>
                    <span class="artticket__stub-note">para todos los<br>artistas colaboradores</span>
                    <span class="artticket__stub-date"><?= htmlspecialchars($artCall['deadline_short']) ?></span>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= CONTACTO DIRECTO ================= -->
    <?php if ($whatsAppHref): ?>
        <?php
        /*
         * Puente para quien ya esta interesado pero todavia no quiere llenar un
         * formulario. Muchas personas prefieren preguntar antes de inscribirse,
         * y sin este paso intermedio se pierden. El mensaje va escrito para que
         * solo tengan que pulsar enviar.
         */
        ?>
        <section class="artwa">
            <div class="site-shell artwa__inner" data-reveal="up">
                <span class="artwa__icono"><?= site_icon('whatsapp') ?></span>

                <div class="artwa__copy">
                    <h2>¿Te interesa pero tienes dudas?</h2>
                    <p>
                        Escríbele directamente a la coordinación de la convocatoria.
                        Te responde una persona del equipo y resuelve lo que necesites
                        antes de inscribirte.
                    </p>
                </div>

                <div class="artwa__accion">
                    <a href="<?= htmlspecialchars($whatsAppHref) ?>" class="artbtn artbtn--verde" target="_blank" rel="noopener">
                        <span class="artbtn__icon"><?= site_icon('whatsapp') ?></span>
                        ESCRIBIR POR WHATSAPP
                    </a>
                    <span class="artwa__numero"><?= htmlspecialchars((string) ($coordinadora['phone'] ?? '')) ?></span>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ================= PREGUNTAS ================= -->
    <section class="artfaq">
        <div class="site-shell">
            <h2 class="artsection-title" data-reveal="up">
                <span>ANTES DE</span><em>INSCRIBIRTE</em>
            </h2>
            <div class="artfaq__list" data-stagger>
                <?php foreach ($artCall['faq'] as $item): ?>
                    <details data-reveal="up">
                        <summary>
                            <span><?= htmlspecialchars($item['question']) ?></span>
                            <i aria-hidden="true"></i>
                        </summary>
                        <p><?= htmlspecialchars($item['answer']) ?></p>
                    </details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
