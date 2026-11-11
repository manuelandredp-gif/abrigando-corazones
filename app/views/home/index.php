<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php
/**
 * PORTADA
 *
 * Escrita de cero. La idea que la ordena: la campaña tiene un dato propio,
 * verificado y devastador —los -17 °C que Senamhi midió en la sierra sur— y
 * todo lo demás cuelga de ahí. Primero el problema con nombre y número, luego
 * la respuesta, luego cómo entrar.
 *
 * Regla que se respeta en toda la vista: no se afirma nada que no venga del
 * controlador o de una fuente citada. Las cifras de campaña se muestran como
 * lo que son, cifras de campaña, no como auditadas.
 */

$deadlineTs   = strtotime($artCall['deadline'] ?? 'now');
$convocatoria = !empty($artCall['is_open']);

// La noticia del frío extremo es el ancla del bloque del dato.
$notaFrio = null;
foreach ($news as $n) {
    if (str_contains($n['slug'], 'frio-extremo')) { $notaFrio = $n; break; }
}
$notaFrio = $notaFrio ?? ($newsFeatured ?? null);

$ultimasNoticias = array_slice($news, 0, 3);

/*
 * Dias de invierno que quedan. Es un dato astronomico verificable (invierno
 * austral: del 21 de junio al 22 de septiembre), no una cifra de campana. Da
 * urgencia real sin inventar nada.
 */
$hoy = new DateTimeImmutable('now');
$finInvierno = new DateTimeImmutable($hoy->format('Y') . '-09-22 23:59:59');
$diasDeInvierno = $hoy < $finInvierno ? (int) $hoy->diff($finInvierno)->days : 0;

/** Fecha relativa legible para las notas. */
$haceCuanto = static function (string $iso) use ($hoy): ?string {
    $ts = strtotime($iso);
    if ($ts === false) {
        return null;
    }
    $dias = (int) $hoy->diff(new DateTimeImmutable(date('Y-m-d', $ts)))->days;
    if ($dias <= 0)  return 'hoy';
    if ($dias === 1) return 'ayer';
    if ($dias < 30)  return 'hace ' . $dias . ' días';
    if ($dias < 60)  return 'hace un mes';
    return 'hace ' . (int) round($dias / 30) . ' meses';
};
?>

<main class="pi">

    <!-- ══════════════ 1 · APERTURA ══════════════ -->
    <section class="pi-hero">
        <?php
        /*
         * 8 - Direccion de arte, no solo redimensionado. La foto original es
         * cuadrada y encuadrada para 16:9; en vertical se recortaba a la cara y
         * se perdia el paisaje nevado, que es lo que da contexto a la escena.
         * El recorte movil es la MISMA fotografia con otro encuadre.
         */
        $heroMovil = BASE_URL . 'images/hero_bg-movil.webp';
        ?>
        <picture class="pi-hero__marco">
            <source media="(max-width: 700px)" srcset="<?= htmlspecialchars($heroMovil) ?>">
            <img src="<?= htmlspecialchars($page['hero']['image']) ?>"
                 alt="Voluntaria entregando una manta de abrigo a una niña en una comunidad altoandina de Tacna"
                 class="pi-hero__foto" fetchpriority="high" decoding="async">
        </picture>
        <div class="pi-hero__velo" aria-hidden="true"></div>
        <div class="pi-hero__vineta" aria-hidden="true"></div>
        <div class="pi-hero__grano" aria-hidden="true"></div>

        <div class="site-shell pi-hero__inner">
            <p class="pi-hero__estado">
                <span class="pi-hero__pulso" aria-hidden="true"></span>
                Campaña activa
                <?php if ($diasDeInvierno > 0): ?>
                    <b>· quedan <?= $diasDeInvierno ?> días de invierno</b>
                <?php else: ?>
                    <b>· Invierno <?= date('Y') ?></b>
                <?php endif; ?>
                <?php /* P1 — Badge de temperatura animado */ ?>
                <span aria-label="Temperatura mínima: menos 17 grados Celsius" style="display:inline-flex;align-items:center;gap:0.3rem;background:rgba(220,38,38,0.18);border:1px solid rgba(220,38,38,0.4);color:#fca5a5;border-radius:99px;padding:0.15rem 0.65rem 0.15rem 0.45rem;font-size:0.78rem;font-weight:700;letter-spacing:0.03em;backdrop-filter:blur(4px);">
                    <span aria-hidden="true" style="width:7px;height:7px;border-radius:50%;background:#ef4444;display:inline-block;animation:pulse-red 1.4s ease-in-out infinite;flex-shrink:0;"></span>
                    −17 °C
                </span>
                <style>@keyframes pulse-red{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.45;transform:scale(1.5)}}</style>
            </p>

            <h1 class="pi-hero__titulo" data-lineas>
                <span>Nadie debería</span>
                <span>pasar la noche</span>
                <em>
                    con frío.
                    <svg class="pi-hero__trazo" viewBox="0 0 300 16" preserveAspectRatio="none" aria-hidden="true">
                        <path d="M4 11 C 70 4, 150 15, 230 7 S 285 5, 296 10"/>
                    </svg>
                </em>
            </h1>

            <p class="pi-hero__bajada">
                Reunimos abrigo para las familias de las zonas altoandinas de
                <strong>Candarave, Tacna</strong>, donde el invierno se vive a más de
                3.000 metros de altura.
            </p>

            <div class="pi-hero__acciones">
                <a href="<?= BASE_URL ?>como-ayudar#donar-ropa" class="btn btn--primary" data-prefetch-link>
                    QUIERO DONAR ROPA
                    <span class="btn__icon btn__icon--fill"><?= site_icon('heart') ?></span>
                </a>
                <a href="<?= BASE_URL ?>puntos-de-acopio" class="btn btn--ghost-light" data-prefetch-link>
                    <span class="btn__icon"><?= site_icon('pin') ?></span>
                    DÓNDE LO LLEVO
                </a>
            </div>

            <?php /* Cifras de estructura, no de resultado: son verificables mirando el sitio. */ ?>
            <dl class="pi-hero__marcador">
                <div>
                    <dt>Puntos de acopio activos</dt>
                    <dd data-countup><?= (int) $pointsSummary['active_count'] ?></dd>
                </div>
                <div>
                    <dt>Distritos con punto</dt>
                    <dd data-countup><?= (int) $pointsSummary['district_count'] ?></dd>
                </div>
                <div>
                    <dt>Organizaciones aliadas</dt>
                    <dd data-countup><?= count($organizations) ?></dd>
                </div>
            </dl>
        </div>

        <a href="#el-frio" class="pi-hero__baja" aria-label="Ir al contenido">
            <span aria-hidden="true"></span>
        </a>
    </section>

    <!-- ══════════════ 2 · EL DATO ══════════════ -->
    <section class="pi-frio" id="el-frio">
        <div class="site-shell pi-frio__inner">
            <p class="pi-frio__intro" data-reveal="up">
                En las noches de invierno, en la sierra sur del Perú, el termómetro
                puede llegar a
            </p>

            <div class="pi-frio__medida" data-reveal="up">
                <?php /* Termometro que se vacia sincronizado con el contador. */ ?>
                <div class="pi-frio__termo" aria-hidden="true">
                    <span class="pi-frio__mercurio"></span>
                </div>

                <p class="pi-frio__cifra">
                    <span data-countup data-countup-slow>&minus;17</span><i>°C</i>
                    <span class="pi-frio__escarcha" aria-hidden="true"></span>
                </p>

                <div class="pi-frio__vaho" aria-hidden="true"><span></span><span></span><span></span></div>
            </div>

            <div class="pi-frio__pie" data-reveal="up">
                <p>
                    Es la temperatura mínima proyectada para sectores por encima de los
                    <strong>4.000 metros</strong> de altitud. A esa altura viven familias
                    enteras en viviendas que no fueron construidas para retener el calor.
                </p>
                <?php if ($notaFrio): ?>
                    <p class="pi-frio__fuente">
                        <?= site_icon('link') ?>
                        Fuente: <strong><?= htmlspecialchars($notaFrio['source_name']) ?></strong>
                        · <a href="<?= htmlspecialchars($notaFrio['url']) ?>" data-prefetch-link>ver la nota completa</a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ══════════════ 3 · LA RESPUESTA ══════════════ -->
    <section class="pi-respuesta">
        <div class="site-shell">
            <div class="pi-respuesta__cabeza" data-reveal="up">
                <span class="section-kicker">La respuesta</span>
                <h2 class="section-title section-title--left">Una red que convierte ropa guardada en abrigo real</h2>
                <p class="section-subtitle">
                    No es una colecta suelta. Es una cadena: puntos de acopio que reciben,
                    voluntarios que clasifican, organizaciones que mueven y comunidades que
                    reciben antes de que caiga lo más duro del invierno.
                </p>
            </div>

            <ol class="pi-cadena" data-stagger>
                <?php /* 17 - Punto que recorre la cadena: muestra el trayecto de la donacion. */ ?>
                <li class="pi-cadena__riel" aria-hidden="true"><span></span></li>
                <?php
                $cadena = [
                    ['icon' => 'box',      'titulo' => 'Recibimos',   'texto' => 'Negocios e instituciones abren sus puertas como puntos de acopio en Tacna.'],
                    ['icon' => 'users',    'titulo' => 'Clasificamos', 'texto' => 'Voluntarios revisan cada prenda: limpia, seca y útil para el frío de altura.'],
                    ['icon' => 'truck',    'titulo' => 'Llevamos',    'texto' => 'Las organizaciones aliadas aportan logística para subir la ayuda a la zona.'],
                    ['icon' => 'coat',     'titulo' => 'Abrigamos',   'texto' => 'La entrega se hace en comunidad, acompañada de una jornada solidaria.'],
                ];
                foreach ($cadena as $i => $paso): ?>
                    <li data-reveal="up">
                        <span class="pi-cadena__num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="pi-cadena__icono"><?= site_icon($paso['icon']) ?></span>
                        <h3><?= htmlspecialchars($paso['titulo']) ?></h3>
                        <p><?= htmlspecialchars($paso['texto']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <!-- ══════════════ 4 · CIFRAS DE CAMPAÑA ══════════════ -->
    <section class="pi-cifras">
        <div class="site-shell">
            <ul class="pi-cifras__lista" data-stagger>
                <?php foreach ($stats as $s): ?>
                    <li data-reveal="up">
                        <span class="pi-cifras__icono"><?= site_icon($s['icon']) ?></span>
                        <strong data-countup><?= htmlspecialchars($s['value']) ?></strong>
                        <span class="pi-cifras__etq"><?= htmlspecialchars($s['label']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php /*
                Honestidad: estas cifras las reporta la campaña y hoy no llevan
                fecha de corte ni fuente publicada. Se dice, en vez de
                presentarlas como auditadas.
            */ ?>
            <aside class="pi-cifras__nota" data-reveal="up">
                <span class="pi-cifras__nota-icono"><?= site_icon('help-circle') ?></span>
                <div>
                    <strong>De dónde salen estas cifras</strong>
                    <p>
                        Son totales acumulados que reporta la campaña. Todavía no publicamos
                        la fecha de corte ni el desglose de cada una; lo estamos preparando en
                        <a href="<?= BASE_URL ?>impacto" data-prefetch-link>la página de impacto</a>.
                    </p>
                </div>
            </aside>
        </div>
    </section>

    <!-- ══════════════ 5 · CÓMO ENTRAR ══════════════ -->
    <section class="pi-caminos" id="como-ayudar">
        <div class="site-shell">
            <div class="pi-caminos__cabeza" data-reveal="up">
                <span class="section-kicker section-kicker--center">Tres formas de entrar</span>
                <h2 class="section-title">Elige la que te quede a mano</h2>
            </div>

            <div class="pi-caminos__grid" data-stagger>
                <?php foreach ($homeHelp as $i => $item): ?>
                    <?php /* 27 - La opcion central va destacada: guia la decision en vez
                             de dejar tres alternativas idénticas y que elija el azar. */ ?>
                    <article class="pi-camino pi-camino--<?= htmlspecialchars($item['tone']) ?><?= $i === 1 ? ' pi-camino--eje' : '' ?>" data-reveal="up">
                        <?php /* 25 - La ilustracion sale del marco y se recorta arriba:
                                 el icono en cuadrito gris era lo mas debil de la pagina. */ ?>
                        <span class="pi-camino__arte" aria-hidden="true">
                            <img src="<?= htmlspecialchars($item['icon']) ?>" alt="" loading="lazy" decoding="async">
                        </span>
                        <div class="pi-camino__cuerpo">
                            <h3><?= htmlspecialchars($item['title']) ?></h3>
                            <p><?= htmlspecialchars($item['text']) ?></p>
                            <a href="<?= htmlspecialchars($item['href']) ?>" class="pi-camino__link" data-prefetch-link>
                                <span><?= htmlspecialchars($item['button']) ?></span>
                                <?= site_icon('arrow-right') ?>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ══════════════ 6 · CONVOCATORIA ARTÍSTICA ══════════════ -->
    <?php require __DIR__ . '/../shared/_convocatoria.php'; ?>

    <!-- ══════════════ 7 · DÓNDE LLEVARLO ══════════════ -->
    <section class="pi-puntos">
        <div class="site-shell">
            <div class="section-head" data-reveal="up">
                <div>
                    <span class="section-kicker">Dónde llevarlo</span>
                    <h2 class="section-title section-title--left">
                        <?= (int) $pointsSummary['active_count'] ?> puntos de acopio abiertos en Tacna
                    </h2>
                </div>
                <a href="<?= BASE_URL ?>puntos-de-acopio" class="section-link" data-prefetch-link>
                    VER EL MAPA COMPLETO <?= site_icon('arrow-right') ?>
                </a>
            </div>

            <div class="pi-puntos__grid" data-stagger>
                <?php foreach ($homePoints as $p): ?>
                    <article class="pi-punto" data-reveal="up">
                        <?php
                        /*
                         * 29 - Mini-mapa real, sin JavaScript ni clave de API.
                         *
                         * Se calcula la tesela de OpenStreetMap que contiene la
                         * coordenada del punto y se pide como una simple imagen.
                         * La posicion del pin se saca de la parte decimal de la
                         * tesela, asi que cae en el lugar exacto y no al centro.
                         * Solo se dibuja si la coordenada esta verificada: un
                         * mapa con el pin mal puesto es peor que no tener mapa.
                         */
                        $mapa = null;
                        if (!empty($p['coordinates_verified']) && $p['latitude'] !== null && $p['longitude'] !== null) {
                            $z = 16;
                            $n = 2 ** $z;
                            $latRad = deg2rad((float) $p['latitude']);
                            $xExacto = (((float) $p['longitude'] + 180) / 360) * $n;
                            $yExacto = (1 - log(tan($latRad) + 1 / cos($latRad)) / M_PI) / 2 * $n;
                            $mapa = [
                                    // CartoDB y no tile.openstreetmap.org: el servidor de OSM
                                // bloquea el hotlinking desde origenes desconocidos y las
                                // teselas llegaban vacias. Este es el mismo CDN que ya usa
                                // el mapa de la pagina de puntos, y esta permitido en la CSP.
                                'url' => sprintf('https://a.basemaps.cartocdn.com/light_all/%d/%d/%d@2x.png', $z, (int) $xExacto, (int) $yExacto),
                                'x' => round(($xExacto - floor($xExacto)) * 100, 2),
                                'y' => round(($yExacto - floor($yExacto)) * 100, 2),
                            ];
                        }
                        ?>
                        <?php if (!empty($p['photo']) || $mapa): ?>
                            <div class="pi-punto__medios">
                                <?php if (!empty($p['photo'])): ?>
                                    <div class="pi-punto__foto">
                                        <img src="<?= htmlspecialchars($p['photo']) ?>" alt="<?= htmlspecialchars($p['photo_alt']) ?>" loading="lazy" decoding="async">
                                    </div>
                                <?php endif; ?>

                                <?php if ($mapa): ?>
                                    <a class="pi-punto__mini" href="<?= htmlspecialchars($p['maps_url']) ?>"
                                       target="_blank" rel="noopener"
                                       aria-label="Ver <?= htmlspecialchars($p['display_name']) ?> en el mapa">
                                        <img src="<?= htmlspecialchars($mapa['url']) ?>" alt="" loading="lazy" decoding="async">
                                        <span class="pi-punto__pin" style="left: <?= $mapa['x'] ?>%; top: <?= $mapa['y'] ?>%"></span>
                                        <span class="pi-punto__mini-cinta">Ver mapa</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <div class="pi-punto__cuerpo">
                            <?php /* 31 - El estado "abierto ahora" late; antes los tres chips
                                     se veian igual y no se distinguia de un cierre definitivo. */ ?>
                            <?php /* PA2/P3 — Estado con indicador pulsante para "abierto ahora" */ ?>
                            <span class="pi-punto__estado pi-punto__estado--<?= htmlspecialchars($p['public_status']['state']) ?>" style="display:inline-flex;align-items:center;gap:0.35rem;">
                                <?php if (($p['public_status']['state'] ?? '') === 'open'): ?>
                                    <i class="pi-punto__pulso" aria-hidden="true"></i>
                                    <?php /* Dot verde pulsante en "abierto ahora" */ ?>
                                    <span aria-hidden="true" style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;animation:pulse-green 1.6s ease-in-out infinite;flex-shrink:0;"></span>
                                    <style>@keyframes pulse-green{0%,100%{opacity:1;box-shadow:0 0 0 0 rgba(34,197,94,.6)}50%{opacity:.8;box-shadow:0 0 0 5px rgba(34,197,94,0)}}</style>
                                <?php else: ?>
                                    <span aria-hidden="true" style="width:8px;height:8px;border-radius:50%;background:currentColor;opacity:0.4;display:inline-block;flex-shrink:0;"></span>
                                <?php endif; ?>
                                <?= htmlspecialchars($p['public_status']['label']) ?>
                            </span>

                            <h3><?= htmlspecialchars($p['display_name']) ?></h3>

                            <?php /* 30 - Distancia real, solo si la persona concede permiso.
                                     Es el dato que de verdad busca quien mira esta tarjeta. */ ?>
                            <p class="pi-punto__dir"
                               <?php if ($p['latitude'] !== null && $p['longitude'] !== null): ?>
                                   data-punto-lat="<?= htmlspecialchars((string) $p['latitude']) ?>"
                                   data-punto-lon="<?= htmlspecialchars((string) $p['longitude']) ?>"
                               <?php endif; ?>>
                                <?= site_icon('pin') ?>
                                <span><?= htmlspecialchars($p['address']) ?></span>
                                <b class="pi-punto__dist" hidden></b>
                            </p>

                            <p class="pi-punto__hora">
                                <?= site_icon('clock') ?>
                                <span><?= htmlspecialchars($p['reception_days_label']) ?> · <?= htmlspecialchars($p['reception_schedule_label']) ?></span>
                            </p>

                            <?php
                            /*
                             * 32 - Franja de 24 horas con el tramo de recepcion sombreado.
                             * Se entiende de un vistazo sin leer el horario.
                             */
                            $ventanas = [];
                            foreach (($p['reception_schedule'] ?? []) as $dia) {
                                foreach ($dia as $v) {
                                    if (isset($v['start'], $v['end'])) {
                                        $ventanas[$v['start'] . $v['end']] = $v;
                                    }
                                }
                            }
                            ?>
                            <?php if ($ventanas !== []): ?>
                                <div class="pi-punto__franja" aria-hidden="true">
                                    <?php foreach ($ventanas as $v):
                                        $ini = (int) substr($v['start'], 0, 2) * 60 + (int) substr($v['start'], 3, 2);
                                        $fin = (int) substr($v['end'], 0, 2) * 60 + (int) substr($v['end'], 3, 2);
                                        $izq = round($ini / 1440 * 100, 2);
                                        $ancho = round(max($fin - $ini, 15) / 1440 * 100, 2);
                                    ?>
                                        <span style="left: <?= $izq ?>%; width: <?= $ancho ?>%"></span>
                                    <?php endforeach; ?>
                                    <em>0h</em><em>12h</em><em>24h</em>
                                </div>
                            <?php endif; ?>

                            <div class="pi-punto__acciones">
                                <a href="<?= BASE_URL ?>puntos-de-acopio/<?= htmlspecialchars($p['slug']) ?>" class="pi-punto__link" data-prefetch-link>
                                    Ver detalles <?= site_icon('arrow-right') ?>
                                </a>
                                <?php /* 33 - Como llegar directo, sin pasar por la ficha. */ ?>
                                <a href="<?= htmlspecialchars($p['maps_url']) ?>" class="pi-punto__mapa" target="_blank" rel="noopener">
                                    <?= site_icon('locate-fixed') ?> Cómo llegar
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ══════════════ 8 · QUIÉNES RESPALDAN ══════════════ -->
    <section class="pi-aliados">
        <div class="site-shell">
            <div class="pi-aliados__cabeza" data-reveal="up">
                <span class="section-kicker">Quiénes respaldan</span>
                <h2 class="section-title section-title--left">
                    <?= count($organizations) ?> organizaciones sostienen esta campaña
                </h2>
                <a href="<?= BASE_URL ?>aliados" class="section-link" data-prefetch-link>
                    CONOCERLAS TODAS <?= site_icon('arrow-right') ?>
                </a>
            </div>

            <?php
            /*
             * 34 - Marquesina en vez de grilla: 26 organizaciones no caben en 8
             * casillas, y una grilla recortada da la impresión de que son ocho.
             * La lista se duplica para que el bucle no tenga costura.
             */
            $conLogo = array_values(array_filter($organizations, static fn(array $o): bool => !empty($o['logo'])));
            $restantes = count($organizations) - count($conLogo);
            ?>
            <div class="pi-aliados__cinta" data-reveal="up">
                <ul class="pi-aliados__pista">
                    <?php for ($vuelta = 0; $vuelta < 2; $vuelta++): ?>
                        <?php foreach ($conLogo as $o): ?>
                            <li <?= $vuelta === 1 ? 'aria-hidden="true"' : '' ?>>
                                <a href="<?= BASE_URL ?>aliados#<?= htmlspecialchars($o['anchor']) ?>"
                                   data-prefetch-link
                                   <?= $vuelta === 1 ? 'tabindex="-1"' : '' ?>>
                                    <img src="<?= htmlspecialchars($o['logo']) ?>"
                                         alt="<?= htmlspecialchars($o['logo_alt']) ?>" loading="lazy" decoding="async">
                                    <?php /* 35 - El aporte real, que ya está en los datos y no se veía. */ ?>
                                    <span class="pi-aliados__ficha">
                                        <b><?= htmlspecialchars($o['name']) ?></b>
                                        <?php if (!empty($o['support_type'])): ?>
                                            <em><?= htmlspecialchars($o['support_type']) ?></em>
                                        <?php endif; ?>
                                        <?php if (!empty($o['support_note'])): ?>
                                            <i><?= htmlspecialchars($o['support_note']) ?></i>
                                        <?php endif; ?>
                                    </span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endfor; ?>
                </ul>
            </div>

            <?php /* 37 - Salida explícita al directorio con las que no caben. */ ?>
            <?php if ($restantes > 0): ?>
                <p class="pi-aliados__resto" data-reveal="up">
                    <a href="<?= BASE_URL ?>aliados" data-prefetch-link>
                        y <strong><?= $restantes ?></strong> organizaciones más en el directorio
                        <?= site_icon('arrow-right') ?>
                    </a>
                </p>
            <?php endif; ?>
        </div>
    </section>

    <!-- ══════════════ 9 · CONTEXTO ══════════════ -->
    <section class="pi-noticias">
        <div class="site-shell">
            <div class="section-head" data-reveal="up">
                <div>
                    <span class="section-kicker">Por qué urge</span>
                    <h2 class="section-title section-title--left">Lo que está pasando allá arriba</h2>
                </div>
                <a href="<?= BASE_URL ?>noticias" class="section-link" data-prefetch-link>
                    TODAS LAS NOTAS <?= site_icon('arrow-right') ?>
                </a>
            </div>

            <div class="pi-noticias__grid" data-stagger>
                <?php foreach ($ultimasNoticias as $i => $n): ?>
                    <?php
                    $cuando = $haceCuanto($n['published_time'] ?? '');
                    $dias = isset($n['published_time']) ? (int) ((time() - strtotime($n['published_time'])) / 86400) : 999;
                    // 40 - El dominio de la fuente: la credibilidad entra por el ojo
                    // antes de que nadie haga clic.
                    $dominio = !empty($n['source_url']) ? parse_url($n['source_url'], PHP_URL_HOST) : null;
                    ?>
                    <?php /* 38 - La primera nota al doble: jerarquía editorial en vez de tres iguales. */ ?>
                    <article class="pi-nota<?= $i === 0 ? ' pi-nota--principal' : '' ?>" data-reveal="up">
                        <a href="<?= htmlspecialchars($n['url']) ?>" data-prefetch-link>
                            <div class="pi-nota__foto">
                                <img src="<?= htmlspecialchars($n['image']) ?>" alt="<?= htmlspecialchars($n['image_alt']) ?>" loading="lazy" decoding="async">
                                <?php /* 41 - Solo si de verdad es reciente. */ ?>
                                <?php if ($dias >= 0 && $dias < 7): ?>
                                    <span class="pi-nota__nuevo">Actualizado</span>
                                <?php endif; ?>
                            </div>
                            <div class="pi-nota__cuerpo">
                                <span class="pi-nota__cat"><?= htmlspecialchars($n['primary_category_label']) ?></span>
                                <h3><?= htmlspecialchars($n['title']) ?></h3>
                                <p><?= htmlspecialchars($n['summary']) ?></p>
                                <span class="pi-nota__fuente">
                                    <?php if ($dominio): ?>
                                        <b class="pi-nota__dominio"><?= htmlspecialchars($dominio) ?></b>
                                    <?php endif; ?>
                                    <span><?= htmlspecialchars($n['source_name']) ?></span>
                                    <?php /* 39 - Fecha relativa: comunica que el sitio está vivo. */ ?>
                                    <?php if ($cuando): ?>
                                        <i><?= htmlspecialchars($cuando) ?></i>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ══════════════ 10 · CIERRE ══════════════ -->
    <section class="pi-cierre" id="sumate">
        <div class="site-shell pi-cierre__inner">
            <div class="pi-cierre__copy" data-reveal="up">
                <span class="section-kicker">Da el paso</span>
                <h2>Una casaca que no usas puede ser la noche en que alguien no pasa frío.</h2>
                <p>
                    Déjanos tus datos y te decimos exactamente qué donar, dónde dejarlo y
                    cuándo. Sin compromiso: si prefieres solo preguntar, también está bien.
                </p>

                <ul class="pi-cierre__lista">
                    <li><?= site_icon('check') ?><span>Te respondemos por WhatsApp o correo</span></li>
                    <li><?= site_icon('check') ?><span>Te indicamos el punto de acopio más cercano</span></li>
                    <li><?= site_icon('check') ?><span>Tus datos solo se usan para coordinar la ayuda</span></li>
                </ul>

                <a href="<?= htmlspecialchars($contact['phone_link']) ?>" class="pi-cierre__wa" target="_blank" rel="noopener">
                    <?= site_icon('whatsapp') ?>
                    <span>O escríbenos directo: <strong><?= htmlspecialchars($contact['phone']) ?></strong></span>
                </a>
            </div>

            <form action="<?= BASE_URL ?>contact/subscribe" method="POST" class="pi-form" data-loading-form data-reveal="up" data-metrics-label="portada-sumate">
                <?= \App\Core\Csrf::field() ?>
                <input type="hidden" name="redirect" value="/">
                <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" class="sr-only">

                <div class="pi-form__cabeza">
                    <p class="pi-form__titulo">Quiero sumarme</p>
                    <?php /* 43 - Progreso: en formularios de mas de tres campos reduce el abandono. */ ?>
                    <p class="pi-form__paso" data-form-progreso aria-live="polite">
                        <span><b>0</b> de 4</span>
                        <span class="pi-form__barra"><i style="width:0%"></i></span>
                    </p>
                </div>

                <label class="pi-form__campo">
                    <span>Nombre completo</span>
                    <input type="text" name="nombre" required minlength="3" autocomplete="name" placeholder="Ej.: María Fernández">
                </label>

                <label class="pi-form__campo">
                    <span>Teléfono o WhatsApp</span>
                    <input type="tel" name="telefono" required inputmode="tel" autocomplete="tel" placeholder="Ej.: 987 654 321">
                </label>

                <label class="pi-form__campo">
                    <span>Correo electrónico</span>
                    <input type="email" name="email" required autocomplete="email" placeholder="Ej.: correo@ejemplo.com">
                </label>

                <label class="pi-form__campo">
                    <span>¿Cómo quieres ayudar?</span>
                    <select name="como_se_entero" required>
                        <option value="">Elige una opción</option>
                        <option value="Dona ropa">Quiero donar ropa</option>
                        <option value="Sé voluntario">Quiero ser voluntario</option>
                        <option value="Punto de acopio">Quiero abrir un punto de acopio</option>
                        <option value="Aliado">Quiero sumar mi organización</option>
                        <option value="Convocatoria artística">Quiero participar con mi talento artístico</option>
                        <option value="Consulta">Solo tengo una consulta</option>
                    </select>
                </label>

                <button type="submit" class="btn btn--primary btn--block" data-submit-label="ENVIANDO...">
                    ENVIAR Y RECIBIR LOS PUNTOS CERCANOS
                    <span class="btn__icon btn__icon--fill"><?= site_icon('heart') ?></span>
                </button>

                <?php
                /*
                 * 46 - Aviso de privacidad. No es cosmetico: el formulario pide
                 * nombre, telefono y correo, y hasta ahora no decia que se hacia
                 * con ellos. Lo que se afirma aqui es exactamente lo que hace el
                 * codigo: guardar el registro y avisar por correo al equipo.
                 */
                ?>
                <p class="pi-form__privacidad">
                    <?= site_icon('check') ?>
                    <span>
                        Tus datos se guardan para coordinar la ayuda y se envían por correo
                        al equipo de la campaña. No se comparten con terceros ni se usan para
                        publicidad. Escríbenos si quieres que los eliminemos.
                    </span>
                </p>
                <?php /* 45 - Estado de exito sin recargar. Si el JS falla, el envio
                         normal recarga con ?success=true y el aviso de siempre. */ ?>
                <div class="pi-form__ok" data-form-ok hidden role="status" aria-live="polite">
                    <span class="pi-form__ok-marca"><?= site_icon('check') ?></span>
                    <strong>Recibido</strong>
                    <p>Gracias por sumarte. Te escribimos muy pronto con los puntos de acopio más cercanos.</p>
                </div>
            </form>
        </div>
    </section>

</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
