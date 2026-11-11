<?php
$whatsAppCopyMap = [
    'inicio' => 'Hola, quiero ayudar a Abrigando Corazones.',
    'quienes-somos' => 'Hola, quisiera conocer mejor la campaña Abrigando Corazones.',
    'como-ayudar' => 'Hola, quisiera información sobre cómo ayudar en Abrigando Corazones.',
    'voluntariado' => 'Hola, quisiera sumarme como voluntario en Abrigando Corazones.',
    'puntos-de-acopio' => 'Hola, quisiera información sobre los puntos de acopio.',
    'aliados' => 'Hola, quisiera información para sumarme como aliado a Abrigando Corazones.',
    'impacto' => 'Hola, quisiera conocer más sobre el impacto de Abrigando Corazones.',
    'noticias' => 'Hola, quisiera compartir una noticia de Abrigando Corazones.',
    'contacto' => 'Hola, quisiera comunicarme con el equipo de Abrigando Corazones.',
    'convocatoria-artistica' => 'Hola, quisiera participar en la convocatoria artística de Abrigando Corazones.',
    'donar' => 'Hola, quisiera coordinar una donación para Abrigando Corazones.',
    '404' => 'Hola, necesito ayuda para encontrar una sección de Abrigando Corazones.',
];
$whatsAppFloatingMessage = $whatsAppCopyMap[$pageKey ?? ''] ?? 'Hola, quisiera información sobre Abrigando Corazones.';
$floatingWhatsAppHref = 'https://wa.me/51921193346?text=' . rawurlencode($whatsAppFloatingMessage);
?>
<footer class="site-footer">
    <div class="site-shell site-footer__grid">
        <div class="site-footer__brand">
            <a class="brand brand--footer" href="<?= BASE_URL ?>" aria-label="Abrigando Corazones inicio" data-prefetch-link>
                <img src="<?= BASE_URL ?>images/logo_heart.webp" alt="Logotipo Abrigando Corazones" class="brand__mark">
            </a>
            <p>Llevamos abrigo, esperanza y solidaridad a las familias de las zonas altoandinas de Tacna.</p>
        </div>

        <div>
            <h3>CONTÁCTANOS</h3>
            <ul class="footer-list">
                <li>
                    <a href="<?= htmlspecialchars($contact['phone_link']) ?>" target="_blank" rel="noopener">
                        <span class="footer-icon"><?= site_icon('phone') ?></span>
                        <span><?= htmlspecialchars($contact['phone']) ?></span>
                    </a>
                </li>
                <?php if (!empty($contact['email'])): ?>
                    <li>
                        <a href="mailto:<?= htmlspecialchars($contact['email']) ?>">
                            <span class="footer-icon"><?= site_icon('mail') ?></span>
                            <span><?= htmlspecialchars($contact['email']) ?></span>
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <span class="footer-icon"><?= site_icon('pin') ?></span>
                    <span><?= htmlspecialchars($contact['location']) ?></span>
                </li>
            </ul>
        </div>

        <div>
            <h3>ENLACES</h3>
            <ul class="footer-list footer-list--links">
                <?php foreach ($navigation as $item): ?>
                    <li><a href="<?= htmlspecialchars($item['href']) ?>" data-prefetch-link><?= htmlspecialchars($item['label']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div>
            <h3>SÍGUENOS</h3>
            <p class="footer-note">en nuestras redes</p>
            <div class="footer-socials">
                <?php foreach (($contact['socials'] ?? []) as $social): ?>
                    <a href="<?= htmlspecialchars($social['href']) ?>" target="_blank" rel="noopener" aria-label="<?= htmlspecialchars($social['label']) ?>">
                        <?= site_icon($social['icon']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="site-shell site-footer__bottom">
        <p>© 2026 Abrigando Corazones. Todos los derechos reservados.</p>
        <p>Hecho con cuidado en Tacna.</p>
    </div>
</footer>

<a href="<?= htmlspecialchars($floatingWhatsAppHref) ?>" class="floating-whatsapp" target="_blank" rel="noopener" aria-label="¿Necesitas ayuda? Escríbenos por WhatsApp">
    <span class="floating-whatsapp__icon"><?= site_icon('whatsapp') ?></span>
    <span class="floating-whatsapp__label">¿Necesitas ayuda?</span>
</a>

<button type="button" class="back-top" data-back-top aria-label="Volver arriba">
    <?= site_icon('arrow-right') ?>
</button>

<?php if (($pageKey ?? '') === 'puntos-de-acopio'): ?>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<?php endif; ?>
<?php /*
   main.js ya usaba window.BASE_URL para el respaldo de imagenes y para el
   endpoint de metricas, pero nunca se definia: caia siempre en '/'. Eso
   funciona con el sitio en la raiz del dominio y se rompe si algun dia vive en
   un subdirectorio. Se define aqui, antes de cargar el script.
*/ ?>
<script nonce="<?= htmlspecialchars(CSP_NONCE, ENT_QUOTES, 'UTF-8') ?>">window.BASE_URL = <?= json_encode(BASE_URL, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;</script>
<script src="<?= BASE_URL ?>js/main.js?v=<?= urlencode((string) ($scriptVersion ?? time())) ?>"></script>
</body>
</html>
