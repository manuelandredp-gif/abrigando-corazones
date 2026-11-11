<?php
/**
 * Bloque de la convocatoria artistica para la portada.
 * Se oculta solo cuando la convocatoria ya cerro.
 *
 * @var array $artCall
 */
if (empty($artCall) || empty($artCall['is_open'])) {
    return;
}

$deadlineTimestamp = strtotime($artCall['deadline']);
$deadlineDay = date('d', $deadlineTimestamp);
$spanishMonths = [
    1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre',
];
$deadlineMonth = $spanishMonths[(int) date('n', $deadlineTimestamp)];
$daysLeft = (int) ($artCall['days_left'] ?? 0);
?>
<section class="arthome">
    <div class="arthome__bg" aria-hidden="true"></div>
    <div class="site-shell arthome__inner" data-reveal="up" data-stagger>
        <div data-reveal="up">
            <span class="arthome__kicker">
                <?= site_icon('sparkles') ?> Convocatoria abierta
            </span>
            <h2>TU ARTE DEJA HUELLA,<br><em>tu corazón abriga.</em></h2>
            <p class="arthome__text">
                ¿Cantas, bailas, actúas o tienes un talento especial que quieres compartir?
                Súmate a la jornada artística solidaria que llevará alegría y esperanza a las
                comunidades altoandinas de Candarave, Tacna.
            </p>

            <ul class="arthome__chips" data-stagger>
                <?php foreach ($artCall['categories'] as $category): ?>
                    <li data-reveal="up"><?= site_icon($category['icon']) ?><span><?= htmlspecialchars($category['label']) ?></span></li>
                <?php endforeach; ?>
            </ul>

            <div class="arthome__actions">
                <a href="<?= htmlspecialchars($artCall['form_url']) ?>" class="artbtn artbtn--gold" target="_blank" rel="noopener">
                    INSCRIBIR MI TALENTO
                    <span class="artbtn__icon"><?= site_icon('arrow-right') ?></span>
                </a>
                <a href="<?= BASE_URL ?>convocatoria-artistica" class="artbtn artbtn--ghost" data-prefetch-link>
                    VER LA CONVOCATORIA
                </a>

                <?php
                /* Mismo atajo desde la portada: quien ya esta decidido no
                   tiene por que pasar por la pagina completa para preguntar. */
                $coordTel = preg_replace('/\D+/', '', (string) ($artCall['coordinator']['phone'] ?? ''));
                ?>
                <?php if ($coordTel !== ''): ?>
                    <a href="https://wa.me/<?= htmlspecialchars($coordTel) ?>?text=<?= rawurlencode((string) ($artCall['coordinator']['message'] ?? '')) ?>"
                       class="artbtn artbtn--ghost" target="_blank" rel="noopener">
                        <span class="artbtn__icon"><?= site_icon('whatsapp') ?></span>
                        PREGUNTAR POR WHATSAPP
                    </a>
                <?php endif; ?>
            </div>

            <?php if (!empty($artCall['organizer'])): ?>
                <p class="arthome__credit">
                    <?= site_icon('handshake') ?>
                    <span>Organiza <strong><?= htmlspecialchars($artCall['organizer']) ?></strong></span>
                </p>
            <?php endif; ?>
        </div>

        <aside class="arthome__stamp" aria-label="Cierre de la convocatoria" data-reveal="up">
            <span class="arthome__stamp-label">Cierre</span>
            <span class="arthome__stamp-day"><?= htmlspecialchars($deadlineDay) ?></span>
            <span class="arthome__stamp-month"><?= htmlspecialchars($deadlineMonth) ?></span>
            <p class="arthome__stamp-note">
                <?php if ($daysLeft > 0): ?>
                    Quedan <strong data-countup><?= $daysLeft ?></strong> <?= $daysLeft === 1 ? 'día' : 'días' ?> para inscribirte.
                <?php else: ?>
                    <strong>Último día</strong> para inscribirte.
                <?php endif; ?>
            </p>
            <span class="arthome__stamp-seal">Certificado incluido</span>
        </aside>
    </div>
</section>
