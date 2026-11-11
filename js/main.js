document.addEventListener('DOMContentLoaded', function () {
    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.body.classList.add('is-loaded');
    if (!prefersReducedMotion) {
        document.body.classList.add('has-motion');
    }

    /**
     * Aisla cada modulo: si uno falla, los demas siguen funcionando.
     */
    function safe(fn, name) {
        try {
            fn();
        } catch (error) {
            if (window.console && console.warn) {
                console.warn('[abrigando] modulo "' + name + '" fallo:', error);
            }
        }
    }

    safe(initHeaderScroll, 'headerScroll');
    safe(initScrollProgress, 'scrollProgress');
    safe(initMobileMenu, 'mobileMenu');
    safe(initSmartPrefetch, 'smartPrefetch');
    safe(initRevealAnimations, 'revealAnimations');
    safe(initCountUp, 'countUp');
    safe(initProgressBars, 'progressBars');
    safe(initHeroParallax, 'heroParallax');
    safe(initAlliesFilters, 'alliesFilters');
    safe(initOrganizationCards, 'organizationCards');
    safe(initHelpTabs, 'helpTabs');
    safe(initHelpExperience, 'helpExperience');
    safe(initVolunteerWizard, 'volunteerWizard');
    safe(initCollectionPointsDirectoryV2, 'collectionPoints');
    safe(initPointDetailPage, 'pointDetail');
    safe(initNewsDirectory, 'newsDirectory');
    safe(initNewsArticleDetail, 'newsArticleDetail');
    safe(initContactReasonSelector, 'contactReason');
    safe(initShareButtons, 'shareButtons');
    safe(initBackTop, 'backTop');
    safe(initStoryVideo, 'storyVideo');
    safe(initLoadingForms, 'loadingForms');
    safe(initFormEnhancements, 'formEnhancements');
    safe(initPremiumMicroInteractions, 'premiumMicro');
    safe(initTextareaCounters, 'textareaCounters');
    safe(initImageFallbacks, 'imageFallbacks');
    safe(initImageFadeIn, 'imageFadeIn');
    safe(initMetrics, 'metrics');
    safe(initPortada, 'portada');
    safe(initDistancias, 'distancias');
    safe(initBarraLectura, 'barraLectura');
    safe(initFormPortada, 'formPortada');
    safe(initRedAnimaciones, 'redAnimaciones');
    safe(initToasts, 'toasts');
    safe(initArtCallCountdown, 'artCallCountdown');

    function normalizeText(value) {
        return (value || '')
            .toString()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    }

    function initHeaderScroll() {
        var header = document.getElementById('site-header');
        if (!header) return;
        var ticking = false;

        function updateHeaderState() {
            var y = window.scrollY || document.documentElement.scrollTop;
            header.classList.toggle('is-scrolled', y > 20);
            ticking = false;
        }

        function onScroll() {
            if (ticking) return;
            ticking = true;
            window.requestAnimationFrame(updateHeaderState);
        }

        updateHeaderState();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    function initMobileMenu() {
        var menuToggle = document.querySelector('[data-menu-toggle]');
        var mobileMenu = document.getElementById('mobile-menu');
        if (!menuToggle || !mobileMenu) return;

        function closeMenu() {
            menuToggle.classList.remove('is-open');
            mobileMenu.classList.remove('is-open');
            menuToggle.setAttribute('aria-expanded', 'false');
        }

        menuToggle.addEventListener('click', function () {
            var open = menuToggle.classList.toggle('is-open');
            mobileMenu.classList.toggle('is-open', open);
            menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        mobileMenu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                closeMenu();
            });
        });

        document.addEventListener('click', function (event) {
            if (!mobileMenu.classList.contains('is-open')) return;
            if (mobileMenu.contains(event.target) || menuToggle.contains(event.target)) return;
            closeMenu();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape' || !mobileMenu.classList.contains('is-open')) return;
            closeMenu();
        });
    }

    function initSmartPrefetch() {
        var links = Array.prototype.slice.call(document.querySelectorAll('[data-prefetch-link]'));
        if (!links.length || !('relList' in HTMLLinkElement.prototype)) return;

        var prefetched = {};

        function prefetch(href) {
            if (!href || prefetched[href]) return;

            var url;
            try {
                url = new URL(href, window.location.href);
            } catch (error) {
                return;
            }

            if (url.origin !== window.location.origin) return;
            prefetched[href] = true;

            var link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = url.href;
            link.as = 'document';
            document.head.appendChild(link);
        }

        links.forEach(function (link) {
            var href = link.getAttribute('href');
            link.addEventListener('mouseenter', function () {
                prefetch(href);
            }, { passive: true });
            link.addEventListener('focus', function () {
                prefetch(href);
            }, { passive: true });
        });
    }

    function initRevealAnimations() {
        var revealItems = Array.prototype.slice.call(document.querySelectorAll('[data-reveal]'));
        var staggerParents = document.querySelectorAll('[data-stagger]');

        staggerParents.forEach(function (parent) {
            Array.prototype.slice.call(parent.children).forEach(function (child, index) {
                if (child.hasAttribute('data-reveal')) {
                    child.style.setProperty('--stagger-index', index);
                }
            });
        });

        if (!revealItems.length || prefersReducedMotion || !('IntersectionObserver' in window)) {
            revealItems.forEach(function (item) {
                item.classList.add('reveal-visible');
            });
            return;
        }

        // Umbral bajo: un bloque mas alto que el viewport nunca alcanza 0.15
        // de visibilidad y se quedaria invisible para siempre.
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px -6% 0px' });

        revealItems.forEach(function (item) {
            observer.observe(item);
        });

        // Red de seguridad: si algo quedo oculto (observer roto, layout raro,
        // JS a medio cargar) se revela igual a los 6 segundos.
        window.setTimeout(function () {
            revealItems.forEach(function (item) {
                item.classList.add('reveal-visible');
            });
        }, 6000);
    }

    /**
     * Contadores animados para las cifras de campana.
     * El valor final ya esta en el HTML: el JS solo lo anima.
     */
    function initCountUp() {
        var counters = Array.prototype.slice.call(document.querySelectorAll('[data-countup]'));
        if (!counters.length) return;

        if (prefersReducedMotion || !('IntersectionObserver' in window)) return;

        function animate(node) {
            var finalText = node.textContent.trim();
            var match = finalText.match(/^([^\d]*)([\d.,]+)(.*)$/);
            if (!match) return;

            var prefix = match[1];
            var suffix = match[3];
            var digits = match[2].replace(/[.,]/g, '');
            var target = parseInt(digits, 10);
            if (!isFinite(target) || target <= 0) return;

            // 24 - El numero cambia de cantidad de digitos mientras cuenta y la
            // fila entera temblaba de lado a lado. Se congela el ancho final
            // antes de empezar.
            var anchoFinal = node.getBoundingClientRect().width;
            if (anchoFinal > 0) {
                node.style.minWidth = Math.ceil(anchoFinal) + 'px';
            }

            var grouped = match[2].indexOf(',') !== -1 || match[2].indexOf('.') !== -1;
            var started = null;
            // El -17 de la portada es el numero protagonista de la pagina:
            // se le da mas recorrido para que la caida se lea como caida.
            var duration = node.hasAttribute('data-countup-slow') ? 2400 : 1100;

            function format(value) {
                return grouped ? value.toLocaleString('es-PE') : String(value);
            }

            function step(timestamp) {
                if (started === null) started = timestamp;
                var progress = Math.min((timestamp - started) / duration, 1);
                var eased = 1 - Math.pow(1 - progress, 3);
                node.textContent = prefix + format(Math.round(target * eased)) + suffix;
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                } else {
                    node.textContent = finalText;
                }
            }

            window.requestAnimationFrame(step);
        }

        var counterObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                counterObserver.unobserve(entry.target);
                animate(entry.target);
            });
        }, { threshold: 0.05 });

        counters.forEach(function (node) {
            counterObserver.observe(node);
        });
    }

    /**
     * Barra de progreso de lectura en el borde superior.
     */
    function initScrollProgress() {
        var bar = document.querySelector('[data-scroll-progress]');
        if (!bar) return;

        var ticking = false;

        function update() {
            var doc = document.documentElement;
            var scrollable = doc.scrollHeight - window.innerHeight;
            var ratio = scrollable > 0 ? (window.scrollY || doc.scrollTop || 0) / scrollable : 0;
            bar.style.transform = 'scaleX(' + Math.min(Math.max(ratio, 0), 1) + ')';
            ticking = false;
        }

        function onScroll() {
            if (ticking) return;
            ticking = true;
            window.requestAnimationFrame(update);
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onScroll, { passive: true });
        update();
    }

    /**
     * Cuenta regresiva del cierre de la convocatoria artistica.
     * El texto de respaldo ya viene renderizado desde PHP.
     */
    function initArtCallCountdown() {
        var root = document.querySelector('[data-artcall-countdown]');
        if (!root) return;

        var deadline = Date.parse(root.getAttribute('data-deadline') || '');
        if (!isFinite(deadline)) return;

        var valueNodes = {
            days: root.querySelector('[data-countdown-days]'),
            hours: root.querySelector('[data-countdown-hours]'),
            minutes: root.querySelector('[data-countdown-minutes]'),
        };

        if (!valueNodes.days) return;

        function pad(value) {
            return value < 10 ? '0' + value : String(value);
        }

        function tick() {
            var remaining = deadline - Date.now();

            if (remaining <= 0) {
                root.classList.add('is-closed');
                return;
            }

            var totalMinutes = Math.floor(remaining / 60000);
            var days = Math.floor(totalMinutes / 1440);
            var hours = Math.floor((totalMinutes % 1440) / 60);
            var minutes = totalMinutes % 60;

            valueNodes.days.textContent = String(days);
            if (valueNodes.hours) valueNodes.hours.textContent = pad(hours);
            if (valueNodes.minutes) valueNodes.minutes.textContent = pad(minutes);
            root.classList.add('is-live');
        }

        tick();
        window.setInterval(tick, 30000);
    }

    function initProgressBars() {
        var bars = document.querySelectorAll('[data-progress-fill]');
        if (!bars.length) return;

        if (prefersReducedMotion || !('IntersectionObserver' in window)) {
            bars.forEach(function (bar) {
                var target = bar.style.getPropertyValue('--progress-target');
                bar.style.transform = 'scaleX(1)';
                if (bar.parentElement) {
                    bar.parentElement.classList.add('is-visible');
                }
                if (target) {
                    bar.style.width = target;
                }
            });
            return;
        }

        var progressObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                entry.target.parentElement.classList.add('is-visible');
                progressObserver.unobserve(entry.target);
            });
        }, { threshold: 0.35 });

        bars.forEach(function (bar) {
            progressObserver.observe(bar);
        });
    }

    function initHeroParallax() {
        if (prefersReducedMotion || window.innerWidth < 1024) return;

        var hero = document.querySelector('.hero--home .hero__bg');
        if (!hero) return;

        function onScroll() {
            var offset = Math.min((window.scrollY || 0) * 0.08, 28);
            hero.style.transform = 'translateY(' + offset + 'px) scale(1)';
        }

        window.addEventListener('scroll', onScroll, { passive: true });
    }

    function initAlliesFilters() {
        var filterBar = document.querySelector('[data-allies-filters]');
        var grid = document.querySelector('[data-allies-grid]');
        var cards = Array.prototype.slice.call(document.querySelectorAll('[data-allies-grid] [data-organization-card]'));
        var emptyState = document.querySelector('[data-allies-empty]');
        var resetButton = document.querySelector('[data-filter-reset]');
        var clearStateButton = document.querySelector('[data-allies-clear-state]');
        var searchInput = document.querySelector('[data-allies-search]');
        var sortSelect = document.querySelector('[data-allies-sort]');
        var verifiedToggle = document.querySelector('[data-allies-verified-only]');
        var resultsCount = document.querySelector('[data-allies-results-count]');
        var loadMoreButton = document.querySelector('[data-allies-load-more]');
        var areaBar = document.querySelector('[data-allies-area-filters]');
        var modal = document.querySelector('[data-ally-modal]');
        var spotlightButton = document.querySelector('[data-ally-spotlight-target]');

        if (!filterBar || !grid || !cards.length) return;

        grid.setAttribute('data-allies-enhanced', 'true');

        var storageKey = 'abrigando-allies-state-v1';
        var touchLikeDevice = window.matchMedia('(hover: none)').matches;
        var allFilterButtons = Array.prototype.slice.call(filterBar.querySelectorAll('[data-filter]'));
        var areaButtons = areaBar ? Array.prototype.slice.call(areaBar.querySelectorAll('[data-area-filter]')) : [];
        var modalTitle = modal ? modal.querySelector('#ally-modal-title') : null;
        var modalType = modal ? modal.querySelector('.ally-detail-modal__type') : null;
        var modalDescription = modal ? modal.querySelector('.ally-detail-modal__description') : null;
        var modalAreas = modal ? modal.querySelector('[data-ally-modal-areas]') : null;
        var modalStatus = modal ? modal.querySelector('[data-ally-modal-status]') : null;
        var modalSource = modal ? modal.querySelector('[data-ally-modal-source]') : null;
        var state = {
            category: 'all',
            areas: [],
            search: '',
            sort: 'default',
            verifiedOnly: false,
            visibleLimit: getInitialVisibleLimit(),
            openCardId: null
        };
        var lastFocusedElement = null;
        var areaLabelMap = {};

        areaButtons.forEach(function (button) {
            areaLabelMap[button.getAttribute('data-area-filter')] = button.textContent.trim();
        });

        cards.forEach(function (card, index) {
            card.dataset.orderIndex = card.dataset.orderIndex || String(index);
        });

        function normalizeText(value) {
            return (value || '')
                .toString()
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim();
        }

        function getInitialVisibleLimit() {
            if (window.innerWidth < 640) return 6;
            if (window.innerWidth < 1024) return 8;
            return 9;
        }

        function getStepVisibleLimit() {
            if (window.innerWidth < 640) return 4;
            if (window.innerWidth < 1024) return 4;
            return 3;
        }

        function safelyParseState() {
            try {
                var raw = window.localStorage.getItem(storageKey);
                return raw ? JSON.parse(raw) : null;
            } catch (error) {
                return null;
            }
        }

        function persistState() {
            try {
                window.localStorage.setItem(storageKey, JSON.stringify({
                    category: state.category,
                    areas: state.areas,
                    search: state.search,
                    sort: state.sort,
                    verifiedOnly: state.verifiedOnly,
                    visibleLimit: state.visibleLimit
                }));
            } catch (error) {
                return;
            }
        }

        function restoreState() {
            var saved = safelyParseState();
            if (!saved) return;

            state.category = saved.category || 'all';
            state.areas = Array.isArray(saved.areas) ? saved.areas.filter(Boolean) : [];
            state.search = saved.search || '';
            state.sort = saved.sort || 'default';
            state.verifiedOnly = !!saved.verifiedOnly;
            state.visibleLimit = Math.max(saved.visibleLimit || getInitialVisibleLimit(), getInitialVisibleLimit());
        }

        function getActiveCardButtons() {
            return cards.filter(function (card) {
                return !card.hidden;
            }).map(function (card) {
                return card.querySelector('[data-organization-action], [data-organization-toggle]');
            }).filter(Boolean);
        }

        function setCardState(card, open, options) {
            options = options || {};
            var button = card.querySelector('[data-organization-action], [data-organization-toggle]');
            var context = card.querySelector('[data-organization-context]');

            card.classList.toggle('is-open', open);
            card.setAttribute('data-card-open', open ? 'true' : 'false');
            card.setAttribute('data-card-pinned', open && !!options.pinned ? 'true' : 'false');

            if (button) {
                button.setAttribute('aria-expanded', open ? 'true' : 'false');
            }

            if (context) {
                context.setAttribute('aria-hidden', open ? 'false' : 'true');
            }

            if (open && options.track !== false) {
                state.openCardId = card.id;
            } else if ((!open || options.track !== false) && state.openCardId === card.id) {
                state.openCardId = null;
            }
        }

        function closeAllCards(exceptCard) {
            cards.forEach(function (card) {
                if (card !== exceptCard) {
                    setCardState(card, false);
                }
            });
        }

        function openCard(card, options) {
            options = options || {};
            if (!card || card.hidden) return;

            closeAllCards(card);
            setCardState(card, true, { pinned: true });

            if (options.scrollIntoView) {
                card.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'center' });
            }
        }

        function getCardAreaSlugs(card) {
            return (card.getAttribute('data-focus-tags') || '').split(/\s+/).filter(Boolean);
        }

        function getCardSearchBlob(card) {
            return normalizeText([
                card.getAttribute('data-organization-name'),
                card.getAttribute('data-organization-short-name'),
                card.getAttribute('data-category-display'),
                card.getAttribute('data-description'),
                card.getAttribute('data-overlay-description'),
                card.getAttribute('data-source-label'),
                card.getAttribute('data-filter-tags'),
                card.getAttribute('data-focus-tags')
            ].join(' '));
        }

        function matchesVerifiedOnly(card) {
            if (!state.verifiedOnly) return true;
            var verification = card.getAttribute('data-verification') || '';
            return verification === 'verified' || verification === 'partial' || verification === 'verified_limited';
        }

        function matchesCategory(card) {
            if (state.category === 'all') return true;
            var tags = (card.getAttribute('data-filter-tags') || '').split(/\s+/).filter(Boolean);
            return tags.indexOf(state.category) !== -1;
        }

        function matchesAreas(card) {
            if (!state.areas.length) return true;
            var focusTags = getCardAreaSlugs(card);
            return state.areas.every(function (area) {
                return focusTags.indexOf(area) !== -1;
            });
        }

        function matchesSearch(card) {
            if (!state.search) return true;
            return getCardSearchBlob(card).indexOf(normalizeText(state.search)) !== -1;
        }

        function compareCards(a, b) {
            if (state.sort === 'name') {
                return (a.getAttribute('data-organization-name') || '').localeCompare(b.getAttribute('data-organization-name') || '', 'es', { sensitivity: 'base' });
            }

            if (state.sort === 'category') {
                var categoryCompare = (a.getAttribute('data-category-display') || '').localeCompare(b.getAttribute('data-category-display') || '', 'es', { sensitivity: 'base' });
                if (categoryCompare !== 0) return categoryCompare;
                return (a.getAttribute('data-organization-name') || '').localeCompare(b.getAttribute('data-organization-name') || '', 'es', { sensitivity: 'base' });
            }

            if (state.sort === 'verification') {
                var rankCompare = Number(a.getAttribute('data-verification-rank') || 99) - Number(b.getAttribute('data-verification-rank') || 99);
                if (rankCompare !== 0) return rankCompare;
                return (a.getAttribute('data-organization-name') || '').localeCompare(b.getAttribute('data-organization-name') || '', 'es', { sensitivity: 'base' });
            }

            return Number(a.getAttribute('data-order-index') || 0) - Number(b.getAttribute('data-order-index') || 0);
        }

        function animateVisibleCards(visibleCards) {
            visibleCards.forEach(function (card, index) {
                if (prefersReducedMotion) {
                    card.classList.remove('is-entering');
                    card.style.transitionDelay = '';
                    return;
                }

                card.style.transitionDelay = Math.min(index * 34, 220) + 'ms';
                card.classList.add('is-entering');
                window.setTimeout(function () {
                    card.classList.remove('is-entering');
                    card.style.transitionDelay = '';
                }, 240);
            });
        }

        function renderCategoryButtons() {
            allFilterButtons.forEach(function (button) {
                var isActive = button.getAttribute('data-filter') === state.category;
                button.classList.toggle('is-active', isActive);
                button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });
        }

        function renderAreaButtons() {
            areaButtons.forEach(function (button) {
                var key = button.getAttribute('data-area-filter');
                var isActive = state.areas.indexOf(key) !== -1;
                button.classList.toggle('is-active', isActive);
                button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });
        }

        function updateResultsMeta(count) {
            if (resultsCount) {
                resultsCount.textContent = String(count);
            }
        }

        function updateLoadMoreButton(totalVisible) {
            if (!loadMoreButton) return;

            var collapsedCount = Math.max(totalVisible - state.visibleLimit, 0);
            loadMoreButton.hidden = collapsedCount <= 0;
            loadMoreButton.textContent = collapsedCount > 0 ? 'VER MÁS ALIADOS (' + collapsedCount + ')' : 'VER MÁS ALIADOS';
        }

        function updateHashForCard(card) {
            if (!card || !card.id) return;
            if (window.location.hash === '#' + card.id) return;
            history.replaceState(null, '', '#' + card.id);
        }

        function clearHashIfNeeded(card) {
            if (!card || !card.id) return;
            if (window.location.hash !== '#' + card.id) return;
            history.replaceState(null, '', window.location.pathname + window.location.search);
        }

        function buildAreaTags(areaSlugs) {
            return areaSlugs.map(function (slug) {
                var tag = document.createElement('span');
                tag.className = 'ally-detail-modal__area-tag';
                tag.textContent = areaLabelMap[slug] || slug.toUpperCase();
                return tag;
            });
        }

        function openModal(card) {
            if (!modal || !card) return;

            lastFocusedElement = document.activeElement;
            modal.hidden = false;
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('has-modal-open');

            if (modalTitle) modalTitle.textContent = card.getAttribute('data-organization-name') || '';
            if (modalType) modalType.textContent = card.getAttribute('data-category-display') || '';
            if (modalDescription) modalDescription.textContent = card.getAttribute('data-description') || card.getAttribute('data-overlay-description') || '';
            if (modalStatus) modalStatus.textContent = card.getAttribute('data-status-badge') || '';
            if (modalSource) {
                var source = card.getAttribute('data-source-label') || '';
                modalSource.textContent = source ? 'Fuente institucional: ' + source : 'Perfil institucional en actualización';
            }
            if (modalAreas) {
                modalAreas.innerHTML = '';
                var areaNodes = buildAreaTags(getCardAreaSlugs(card));
                if (!areaNodes.length) {
                    var fallbackTag = document.createElement('span');
                    fallbackTag.className = 'ally-detail-modal__area-tag ally-detail-modal__area-tag--muted';
                    fallbackTag.textContent = 'Perfil en actualización';
                    areaNodes = [fallbackTag];
                }
                areaNodes.forEach(function (node) {
                    modalAreas.appendChild(node);
                });
            }

            window.setTimeout(function () {
                var closeButton = modal.querySelector('[data-ally-modal-close]');
                if (closeButton) closeButton.focus();
            }, 20);
        }

        function closeModal() {
            if (!modal || modal.hidden) return;
            modal.hidden = true;
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('has-modal-open');

            if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
                lastFocusedElement.focus();
            }
        }

        function syncControls() {
            if (searchInput) searchInput.value = state.search;
            if (sortSelect) sortSelect.value = state.sort;
            if (verifiedToggle) verifiedToggle.checked = state.verifiedOnly;
            renderCategoryButtons();
            renderAreaButtons();
        }

        function applyFilters(options) {
            options = options || {};

            var matchingCards = cards.filter(function (card) {
                return matchesCategory(card) && matchesAreas(card) && matchesSearch(card) && matchesVerifiedOnly(card);
            });

            matchingCards.sort(compareCards);
            matchingCards.forEach(function (card) {
                grid.appendChild(card);
            });

            var cardsToAnimate = [];
            cards.forEach(function (card) {
                var shouldShow = matchingCards.indexOf(card) !== -1;
                var withinLimit = shouldShow && matchingCards.indexOf(card) < state.visibleLimit;

                card.classList.remove('is-leaving');
                card.hidden = !withinLimit;
                card.classList.toggle('is-collapsed', shouldShow && !withinLimit);

                if (withinLimit) {
                    cardsToAnimate.push(card);
                } else {
                    setCardState(card, false);
                }
            });

            if (state.openCardId) {
                var currentCard = document.getElementById(state.openCardId);
                if (!currentCard || currentCard.hidden) {
                    state.openCardId = null;
                }
            }

            if (emptyState) {
                emptyState.hidden = matchingCards.length !== 0;
            }

            updateResultsMeta(matchingCards.length);
            updateLoadMoreButton(matchingCards.length);
            persistState();

            if (options.animate !== false) {
                animateVisibleCards(cardsToAnimate);
            }
        }

        function resetState() {
            state.category = 'all';
            state.areas = [];
            state.search = '';
            state.sort = 'default';
            state.verifiedOnly = false;
            state.visibleLimit = getInitialVisibleLimit();
            state.openCardId = null;
            syncControls();
            closeAllCards(null);
            applyFilters({ animate: true });
        }

        restoreState();
        syncControls();

        allFilterButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var nextFilter = button.getAttribute('data-filter') || 'all';
                state.category = nextFilter;
                state.visibleLimit = getInitialVisibleLimit();
                renderCategoryButtons();
                applyFilters({ animate: true });
            });
        });

        areaButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var key = button.getAttribute('data-area-filter');
                var index = state.areas.indexOf(key);

                if (index === -1) {
                    state.areas.push(key);
                } else {
                    state.areas.splice(index, 1);
                }

                state.visibleLimit = getInitialVisibleLimit();
                renderAreaButtons();
                applyFilters({ animate: true });
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                state.search = searchInput.value || '';
                state.visibleLimit = getInitialVisibleLimit();
                applyFilters({ animate: false });
            });
        }

        if (sortSelect) {
            sortSelect.addEventListener('change', function () {
                state.sort = sortSelect.value || 'default';
                applyFilters({ animate: false });
            });
        }

        if (verifiedToggle) {
            verifiedToggle.addEventListener('change', function () {
                state.verifiedOnly = !!verifiedToggle.checked;
                state.visibleLimit = getInitialVisibleLimit();
                applyFilters({ animate: true });
            });
        }

        if (loadMoreButton) {
            loadMoreButton.addEventListener('click', function () {
                state.visibleLimit += getStepVisibleLimit();
                applyFilters({ animate: true });
            });
        }

        if (resetButton) {
            resetButton.addEventListener('click', function () {
                resetState();
            });
        }

        if (clearStateButton) {
            clearStateButton.addEventListener('click', function () {
                resetState();
            });
        }

        cards.forEach(function (card) {
            var button = card.querySelector('[data-organization-action], [data-organization-toggle]');
            var detailButton = card.querySelector('[data-organization-detail]');

            if (button) {
                button.addEventListener('click', function () {
                    if (button.hasAttribute('data-organization-detail')) return;
                    var isPinned = card.getAttribute('data-card-pinned') === 'true';
                    var shouldOpen = !card.classList.contains('is-open') || !isPinned;
                    closeAllCards(card);
                    setCardState(card, shouldOpen, { pinned: shouldOpen });
                    if (shouldOpen) {
                        updateHashForCard(card);
                    } else {
                        clearHashIfNeeded(card);
                    }
                });

                button.addEventListener('keydown', function (event) {
                    if (['ArrowRight', 'ArrowDown', 'ArrowLeft', 'ArrowUp'].indexOf(event.key) === -1) return;

                    var activeButtons = getActiveCardButtons();
                    var currentIndex = activeButtons.indexOf(button);
                    if (currentIndex === -1) return;

                    event.preventDefault();
                    var nextIndex = currentIndex;

                    if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
                        nextIndex = Math.min(activeButtons.length - 1, currentIndex + 1);
                    } else {
                        nextIndex = Math.max(0, currentIndex - 1);
                    }

                    activeButtons[nextIndex].focus();
                });
            }

            if (detailButton) {
                detailButton.addEventListener('click', function () {
                    openModal(card);
                });
            }

            card.addEventListener('focusout', function (event) {
                var nextTarget = event.relatedTarget;
                if (!nextTarget || !card.contains(nextTarget)) {
                    setCardState(card, false);
                }
            });
        });

        if (spotlightButton) {
            spotlightButton.addEventListener('click', function () {
                var targetId = spotlightButton.getAttribute('data-ally-spotlight-target') || '';
                var targetCard = targetId ? document.getElementById(targetId) : null;
                if (targetCard) {
                    openModal(targetCard);
                }
            });
        }

        if (modal) {
            Array.prototype.slice.call(modal.querySelectorAll('[data-ally-modal-close]')).forEach(function (button) {
                button.addEventListener('click', function () {
                    closeModal();
                });
            });
        }

        document.addEventListener('click', function (event) {
            cards.forEach(function (card) {
                if (!card.contains(event.target)) {
                    setCardState(card, false);
                }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') return;

            if (modal && !modal.hidden) {
                closeModal();
                return;
            }

            closeAllCards(null);
        });

        window.addEventListener('hashchange', function () {
            var hash = window.location.hash.replace('#', '');
            if (!hash) return;

            var targetCard = document.getElementById(hash);
            if (targetCard && targetCard.hasAttribute('data-organization-card')) {
                if (targetCard.hidden) {
                    state.visibleLimit = Math.max(state.visibleLimit, cards.length);
                    applyFilters({ animate: false });
                }
                openCard(targetCard, { scrollIntoView: true });
            }
        });

        window.addEventListener('resize', function () {
            state.visibleLimit = Math.max(state.visibleLimit, getInitialVisibleLimit());
            applyFilters({ animate: false });
        });

        applyFilters({ animate: false });

        var initialHash = window.location.hash.replace('#', '');
        if (initialHash) {
            var initialCard = document.getElementById(initialHash);
            if (initialCard && initialCard.hasAttribute('data-organization-card')) {
                state.visibleLimit = Math.max(state.visibleLimit, cards.length);
                applyFilters({ animate: false });
                openCard(initialCard, { scrollIntoView: false });
            }
        }
    }

    function initOrganizationCards() {
        if (document.querySelector('[data-allies-grid][data-allies-enhanced="true"]')) return;

        var cards = Array.prototype.slice.call(document.querySelectorAll('.organization-card'));
        if (!cards.length) return;
        var touchLikeDevice = window.matchMedia('(hover: none)').matches;

        function setCardState(card, open) {
            var button = card.querySelector('[data-organization-toggle]');
            var context = card.querySelector('[data-organization-context]');

            card.classList.toggle('is-open', open);

            if (button) {
                button.setAttribute('aria-expanded', open ? 'true' : 'false');
            }

            if (context) {
                context.setAttribute('aria-hidden', open ? 'false' : 'true');
            }
        }

        function closeAll(exceptCard) {
            cards.forEach(function (card) {
                if (card !== exceptCard) {
                    setCardState(card, false);
                }
            });
        }

        cards.forEach(function (card) {
            var button = card.querySelector('[data-organization-toggle]');

            if (!button) return;

            button.addEventListener('click', function () {
                var shouldOpen = !card.classList.contains('is-open');
                closeAll(card);
                setCardState(card, shouldOpen);
            });

            if (!touchLikeDevice) {
                card.addEventListener('mouseenter', function () {
                    closeAll(card);
                    setCardState(card, true);
                });

                card.addEventListener('mouseleave', function () {
                    setCardState(card, false);
                });
            }

            card.addEventListener('focusout', function (event) {
                var nextTarget = event.relatedTarget;
                if (!nextTarget || !card.contains(nextTarget)) {
                    setCardState(card, false);
                }
            });
        });

        document.addEventListener('click', function (event) {
            cards.forEach(function (card) {
                if (!card.contains(event.target)) {
                    setCardState(card, false);
                }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') return;
            closeAll(null);
        });
    }

    function initBackTop() {
        var backTop = document.querySelector('[data-back-top]');
        if (!backTop) return;

        function onScroll() {
            var y = window.scrollY || document.documentElement.scrollTop;
            backTop.classList.toggle('is-visible', y > 420);
        }

        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });

        backTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: prefersReducedMotion ? 'auto' : 'smooth' });
        });
    }

    /**
     * El control con aspecto de reproductor no reproduce nada: lleva a la
     * pagina de Impacto. Se respeta el comportamiento existente, pero la ruta
     * pasa por BASE_URL: estaba fijada a origin + '/impacto', que se rompe si
     * el sitio se instala en un subdirectorio.
     *
     * PENDIENTE DE DECISION DEL CLIENTE: o se sube el video real, o este
     * control no deberia parecer un reproductor.
     */
    function initStoryVideo() {
        var destino = (window.BASE_URL || '/') + 'impacto';

        document.querySelectorAll('.video-card__play').forEach(function (button) {
            button.addEventListener('click', function () {
                window.location.href = destino;
            });
        });
    }

    function initHelpTabs() {
        var root = document.querySelector('[data-help-tabs]');
        if (!root) return;

        var buttons = Array.prototype.slice.call(root.querySelectorAll('[data-help-tab]'));
        var panels = Array.prototype.slice.call(root.querySelectorAll('[data-help-panel]'));
        if (!buttons.length || !panels.length) return;

        var hashMap = {
            'donar-ropa': 'donar-ropa',
            'voluntariado': 'voluntariado',
            'punto-de-acopio': 'punto-de-acopio'
        };

        function getPanel(key) {
            return panels.find(function (panel) {
                return panel.getAttribute('data-help-panel') === key;
            });
        }

        function getButton(key) {
            return buttons.find(function (button) {
                return button.getAttribute('data-help-tab') === key;
            });
        }

        function activateTab(key, options) {
            options = options || {};
            var targetButton = getButton(key);
            var targetPanel = getPanel(key);
            if (!targetButton || !targetPanel) return;

            buttons.forEach(function (button) {
                var active = button === targetButton;
                button.classList.toggle('is-active', active);
                button.setAttribute('aria-selected', active ? 'true' : 'false');
                button.tabIndex = active ? 0 : -1;
            });

            panels.forEach(function (panel) {
                var active = panel === targetPanel;
                panel.hidden = !active;
                panel.classList.toggle('is-active', active);
                if (active && !prefersReducedMotion) {
                    panel.classList.remove('is-entering');
                    window.requestAnimationFrame(function () {
                        panel.classList.add('is-entering');
                    });
                } else {
                    panel.classList.remove('is-entering');
                }
            });

            if (options.updateHash !== false) {
                var newHash = hashMap[key] || key;
                if (window.location.hash !== '#' + newHash) {
                    history.replaceState(null, '', window.location.pathname + window.location.search + '#' + newHash);
                }
            }
        }

        function focusByOffset(currentButton, offset) {
            var currentIndex = buttons.indexOf(currentButton);
            if (currentIndex === -1) return;
            var nextIndex = (currentIndex + offset + buttons.length) % buttons.length;
            buttons[nextIndex].focus();
        }

        buttons.forEach(function (button, index) {
            button.addEventListener('click', function () {
                activateTab(button.getAttribute('data-help-tab'));
            });

            button.addEventListener('keydown', function (event) {
                switch (event.key) {
                    case 'ArrowRight':
                    case 'Right':
                        event.preventDefault();
                        focusByOffset(button, 1);
                        break;
                    case 'ArrowLeft':
                    case 'Left':
                        event.preventDefault();
                        focusByOffset(button, -1);
                        break;
                    case 'Home':
                        event.preventDefault();
                        buttons[0].focus();
                        break;
                    case 'End':
                        event.preventDefault();
                        buttons[buttons.length - 1].focus();
                        break;
                    case 'Enter':
                    case ' ':
                    case 'Spacebar':
                        event.preventDefault();
                        activateTab(button.getAttribute('data-help-tab'));
                        break;
                }
            });

            if (index !== 0) {
                button.tabIndex = -1;
            }
        });

        var initialHash = window.location.hash.replace('#', '');
        var initialTab = hashMap[initialHash] ? initialHash : buttons[0].getAttribute('data-help-tab');
        activateTab(initialTab, { updateHash: false });

        window.addEventListener('hashchange', function () {
            var nextHash = window.location.hash.replace('#', '');
            if (!hashMap[nextHash]) return;
            activateTab(nextHash, { updateHash: false });
        });
    }

    function initHelpExperience() {
        var checklist = document.querySelector('[data-donation-checklist]');
        if (!checklist) return;

        if (checklist) {
            var inputs = Array.prototype.slice.call(checklist.querySelectorAll('input[type="checkbox"]'));
            var status = checklist.querySelector('[data-checklist-status]');

            function updateChecklist() {
                var completed = inputs.length > 0 && inputs.every(function (input) {
                    return input.checked;
                });

                checklist.classList.toggle('is-complete', completed);

                if (status) {
                    status.textContent = completed
                        ? '¡Tu donación está lista para llevar!'
                        : 'Marca cada paso antes de entregar tu aporte.';
                }
            }

            inputs.forEach(function (input) {
                input.addEventListener('change', updateChecklist);
            });

            updateChecklist();
        }
    }

    /**
     * Asistente que recomienda areas de voluntariado.
     *
     * IMPORTANTE: las areas sugeridas aqui deben existir palabra por palabra
     * como <option> del formulario de inscripcion Y en la lista blanca de
     * ContactController::isValidContactData(). Antes recomendaba 'Entrega',
     * 'Redes', 'Video' y 'Diseño', que no figuran en el desplegable: a quien
     * recibia esa recomendacion le era imposible elegirla.
     */
    function initVolunteerWizard() {
        var wizard = document.querySelector('[data-volunteer-wizard]');
        if (!wizard) return;

        var selects = Array.prototype.slice.call(wizard.querySelectorAll('[data-volunteer-answer]'));
        var result = wizard.querySelector('[data-volunteer-result]');

        function getSuggestions(values) {
            var suggestions = [];

            if (values.mode === 'campo' || values.mode === 'mixto') {
                suggestions.push('Recepción', 'Transporte');
            }

            if (values.mode === 'digital' || values.mode === 'mixto') {
                suggestions.push('Difusión', 'Diseño y redes');
            }

            if (values.skill === 'organizacion') {
                suggestions.push('Clasificación', 'Inventario', 'Coordinación');
            }

            if (values.skill === 'movilidad') {
                suggestions.push('Transporte', 'Coordinación');
            }

            if (values.skill === 'contenido') {
                suggestions.push('Fotografía y video', 'Diseño y redes');
            }

            if (values.time === 'amplio') {
                suggestions.push('Coordinación');
            }

            return suggestions.filter(function (item, index, array) {
                return array.indexOf(item) === index;
            }).slice(0, 3);
        }

        function updateResult() {
            var values = {};
            selects.forEach(function (select) {
                values[select.getAttribute('data-volunteer-answer')] = select.value;
            });

            if (!values.mode || !values.time || !values.skill) {
                result.textContent = 'Completa las tres respuestas para recibir sugerencias.';
                return;
            }

            var suggestions = getSuggestions(values);
            result.innerHTML = suggestions.length
                ? '<strong>Áreas sugeridas:</strong> ' + suggestions.join(', ') + '.'
                : 'Te recomendamos explorar roles de coordinación y apoyo general según disponibilidad.';
        }

        selects.forEach(function (select) {
            select.addEventListener('change', updateResult);
        });

        updateResult();
    }

    function initPointsDirectory() {
        var directory = document.querySelector('[data-points-directory]');
        if (!directory) return;

        var input = directory.querySelector('[data-point-search]');
        var clear = directory.querySelector('[data-point-clear]');
        var count = directory.querySelector('[data-point-count]');
        var empty = document.querySelector('[data-point-empty]');
        var cards = Array.prototype.slice.call(document.querySelectorAll('[data-point-card]'));
        var filters = Array.prototype.slice.call(directory.querySelectorAll('[data-point-filter]'));
        var debounceTimer = null;
        var activeFilter = 'all';

        function showToast(message, type) {
            var toast = document.createElement('div');
            toast.className = 'toast toast--' + (type === 'error' ? 'error' : 'success');
            toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
            toast.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
            toast.setAttribute('data-toast', '');
            toast.innerHTML = '<span class="toast__icon">' + (type === 'error' ? '✕' : '✓') + '</span><span>' + message + '</span>';
            document.body.appendChild(toast);
            window.setTimeout(function () {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity .3s ease';
                window.setTimeout(function () {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 320);
            }, 2200);
        }

        function getLimaParts() {
            var formatter = new Intl.DateTimeFormat('en-US', {
                timeZone: 'America/Lima',
                weekday: 'long',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });

            var values = {};
            formatter.formatToParts(new Date()).forEach(function (part) {
                values[part.type] = part.value;
            });

            return {
                weekday: (values.weekday || '').toLowerCase(),
                time: (values.hour || '00') + ':' + (values.minute || '00')
            };
        }

        function formatTimeLabel(time) {
            var segments = (time || '00:00').split(':');
            var hour = Number(segments[0] || 0);
            var minute = Number(segments[1] || 0);
            var period = hour >= 12 ? 'p. m.' : 'a. m.';
            var displayHour = hour % 12;
            if (displayHour === 0) displayHour = 12;
            return displayHour + ':' + String(minute).padStart(2, '0') + ' ' + period;
        }

        function getScheduleForDay(schedule, dayKey) {
            if (!schedule) return [];
            var exactDay = schedule[dayKey];
            if (Array.isArray(exactDay)) return exactDay;
            var fallback = schedule['default'];
            return Array.isArray(fallback) ? fallback : [];
        }

        function computeStatus(days, schedule) {
            if (!days.length || !schedule || !Object.keys(schedule).length) {
                return {
                    state: 'pending',
                    label: 'HORARIO DE ACOPIO POR CONFIRMAR',
                    detail: 'Consulta antes de acercarte.'
                };
            }

            var limaNow = getLimaParts();
            var today = limaNow.weekday;
            var currentTime = limaNow.time;
            var todayActive = days.indexOf(today) !== -1;
            var todayWindows = todayActive ? getScheduleForDay(schedule, today) : [];

            for (var i = 0; i < todayWindows.length; i += 1) {
                var range = todayWindows[i];
                if (currentTime >= range.start && currentTime < range.end) {
                    return {
                        state: 'open',
                        label: 'ABIERTO PARA RECIBIR DONACIONES',
                        detail: 'Cierra a las ' + formatTimeLabel(range.end) + '.'
                    };
                }
            }

            for (var j = 0; j < todayWindows.length; j += 1) {
                var nextRange = todayWindows[j];
                if (currentTime < nextRange.start) {
                    return {
                        state: 'closed',
                        label: 'CERRADO POR AHORA',
                        detail: 'Abre hoy a las ' + formatTimeLabel(nextRange.start) + '.'
                    };
                }
            }

            var week = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            var todayIndex = week.indexOf(today);

            for (var offset = 1; offset <= 7; offset += 1) {
                var nextIndex = (todayIndex + offset) % week.length;
                var nextDay = week[nextIndex];
                if (days.indexOf(nextDay) === -1) continue;
                var nextDayRanges = getScheduleForDay(schedule, nextDay);
                if (!nextDayRanges.length) continue;
                return {
                    state: 'closed',
                    label: 'CERRADO POR AHORA',
                    detail: offset === 1
                        ? 'Abre mañana a las ' + formatTimeLabel(nextDayRanges[0].start) + '.'
                        : 'Vuelve pronto a las ' + formatTimeLabel(nextDayRanges[0].start) + '.'
                };
            }

            return {
                state: 'closed',
                label: 'CERRADO POR AHORA',
                detail: 'Revisa los días de recepción antes de acercarte.'
            };
        }

        function applyStatus(card) {
            var days = (card.getAttribute('data-point-days') || '').split(',').filter(Boolean);
            var rawSchedule = card.getAttribute('data-point-schedule') || '{}';
            var schedule = {};

            try {
                schedule = JSON.parse(rawSchedule);
            } catch (error) {
                schedule = {};
            }

            var computed = computeStatus(days, schedule);
            var badge = card.querySelector('[data-point-status-badge]');
            var detail = card.querySelector('[data-point-status-detail]');

            card.setAttribute('data-point-state', computed.state);

            if (badge) {
                badge.textContent = computed.label;
                badge.classList.remove('pickup-badge--open', 'pickup-badge--closed', 'pickup-badge--pending');
                badge.classList.add('pickup-badge--' + computed.state);
            }

            if (detail) {
                detail.textContent = computed.detail;
            }
        }

        function renderFilterButtons() {
            filters.forEach(function (button) {
                var isActive = button.getAttribute('data-point-filter') === activeFilter;
                button.classList.toggle('is-active', isActive);
                button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });
        }

        function matchesFilter(card) {
            if (activeFilter === 'all') return true;
            if (activeFilter === 'open-now') {
                return card.getAttribute('data-point-state') === 'open';
            }

            var tags = (card.getAttribute('data-point-filter-tags') || '').split(/\s+/).filter(Boolean);
            return tags.indexOf(activeFilter) !== -1;
        }

        function applyFilter() {
            var query = normalizeText(input && input.value ? input.value : '');
            var visible = 0;

            cards.forEach(function (card) {
                applyStatus(card);
                var haystack = normalizeText(card.getAttribute('data-point-searchable') || '');
                var searchMatch = !query || haystack.indexOf(query) !== -1;
                var filterMatch = matchesFilter(card);
                var matches = searchMatch && filterMatch;
                card.hidden = !matches;
                if (matches) visible += 1;
            });

            if (count) count.textContent = String(visible);
            if (empty) empty.hidden = visible !== 0;
        }

        function queueFilter() {
            window.clearTimeout(debounceTimer);
            debounceTimer = window.setTimeout(applyFilter, 180);
        }

        filters.forEach(function (button) {
            button.addEventListener('click', function () {
                activeFilter = button.getAttribute('data-point-filter') || 'all';
                renderFilterButtons();
                applyFilter();
            });
        });

        cards.forEach(function (card) {
            var toggle = card.querySelector('[data-point-toggle]');
            var copyButton = card.querySelector('[data-point-copy]');
            var shareButton = card.querySelector('[data-point-share]');

            if (toggle) {
                toggle.addEventListener('click', function () {
                    var targetId = toggle.getAttribute('data-point-toggle');
                    var panel = targetId ? document.getElementById(targetId) : null;
                    if (!panel) return;

                    var isOpen = !panel.hidden;
                    panel.hidden = isOpen;
                    toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                });
            }

            if (copyButton) {
                copyButton.addEventListener('click', function () {
                    var address = card.getAttribute('data-point-address') || '';
                    if (!address) {
                        showToast('No se pudo copiar la dirección.', 'error');
                        return;
                    }

                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(address).then(function () {
                            showToast('Dirección copiada.', 'success');
                        }).catch(function () {
                            showToast('No se pudo copiar la dirección.', 'error');
                        });
                        return;
                    }

                    var helper = document.createElement('textarea');
                    helper.value = address;
                    helper.setAttribute('readonly', 'readonly');
                    helper.style.position = 'absolute';
                    helper.style.left = '-9999px';
                    document.body.appendChild(helper);
                    helper.select();

                    try {
                        document.execCommand('copy');
                        showToast('Dirección copiada.', 'success');
                    } catch (error) {
                        showToast('No se pudo copiar la dirección.', 'error');
                    }

                    document.body.removeChild(helper);
                });
            }

            if (shareButton) {
                shareButton.addEventListener('click', function () {
                    var name = card.getAttribute('data-point-name') || 'Punto de acopio';
                    var address = card.getAttribute('data-point-address') || '';
                    var mapsUrl = card.getAttribute('data-point-maps') || '';
                    var shareText = 'Puedes donar abrigo en ' + name + '. ' + address + (mapsUrl ? ' ' + mapsUrl : '');

                    if (navigator.share) {
                        navigator.share({
                            title: name,
                            text: shareText,
                            url: mapsUrl || window.location.href
                        }).catch(function () {
                            return;
                        });
                        return;
                    }

                    window.open('https://wa.me/?text=' + encodeURIComponent(shareText), '_blank', 'noopener');
                });
            }
        });

        if (input) {
            input.addEventListener('input', queueFilter);
        }

        if (clear) {
            clear.addEventListener('click', function () {
                if (input) input.value = '';
                activeFilter = 'all';
                renderFilterButtons();
                applyFilter();
            });
        }

        renderFilterButtons();
        applyFilter();
    }

    function initCollectionPointsDirectory() {
        var directory = document.querySelector('[data-points-directory]');
        if (!directory) return;

        var input = directory.querySelector('[data-point-search]');
        var clear = directory.querySelector('[data-point-clear]');
        var count = directory.querySelector('[data-point-count]');
        var empty = document.querySelector('[data-point-empty]');
        var cards = Array.prototype.slice.call(document.querySelectorAll('[data-point-card]'));
        var filters = Array.prototype.slice.call(directory.querySelectorAll('[data-point-filter]'));
        var geolocateButton = directory.querySelector('[data-point-geolocate]');
        var nearFilterButton = directory.querySelector('[data-point-near-filter]');
        var nearestDistanceValue = document.querySelector('[data-nearest-distance-value]');
        var nearestDistanceLabel = document.querySelector('[data-nearest-distance-label]');
        var nearbyBlock = document.querySelector('[data-point-nearby]');
        var nearbyList = document.querySelector('[data-point-nearby-list]');
        var payloadNode = document.querySelector('[data-point-map-payload]');
        var overlayNode = document.querySelector('[data-point-map-overlay]');
        var mapCanvas = document.querySelector('[data-collection-points-map]');
        var activeFilter = 'all';
        var debounceTimer = null;
        var userCoords = null;
        var nearestPointId = null;
        var selectedPointId = null;
        var map = null;
        var mapReady = false;
        var pointsLayer = null;
        var userMarker = null;
        var userCircle = null;
        var markerIndex = {};
        var pointPayload = [];
        var overlayPayload = null;

        function showToast(message, type) {
            var toast = document.createElement('div');
            toast.className = 'toast toast--' + (type === 'error' ? 'error' : 'success');
            toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
            toast.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
            toast.setAttribute('data-toast', '');
            toast.innerHTML = '<span class="toast__icon">' + (type === 'error' ? '!' : 'OK') + '</span><span>' + message + '</span>';
            document.body.appendChild(toast);
            window.setTimeout(function () {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity .3s ease';
                window.setTimeout(function () {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 320);
            }, 2500);
        }

        function getLimaParts() {
            var formatter = new Intl.DateTimeFormat('en-US', {
                timeZone: 'America/Lima',
                weekday: 'long',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
            var values = {};
            formatter.formatToParts(new Date()).forEach(function (part) {
                values[part.type] = part.value;
            });

            return {
                weekday: (values.weekday || '').toLowerCase(),
                time: (values.hour || '00') + ':' + (values.minute || '00')
            };
        }

        function formatTimeLabel(time) {
            var segments = (time || '00:00').split(':');
            var hour = Number(segments[0] || 0);
            var minute = Number(segments[1] || 0);
            var period = hour >= 12 ? 'p. m.' : 'a. m.';
            var displayHour = hour % 12;
            if (displayHour === 0) displayHour = 12;
            return displayHour + ':' + String(minute).padStart(2, '0') + ' ' + period;
        }

        function getScheduleForDay(schedule, dayKey) {
            if (!schedule) return [];
            var exactDay = schedule[dayKey];
            if (Array.isArray(exactDay)) return exactDay;
            var fallback = schedule.default;
            return Array.isArray(fallback) ? fallback : [];
        }

        function computeStatus(days, schedule) {
            if (!days.length || !schedule || !Object.keys(schedule).length) {
                return {
                    state: 'pending',
                    label: 'HORARIO DE ACOPIO POR CONFIRMAR',
                    detail: 'Consulta antes de acercarte.'
                };
            }

            var week = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            var limaNow = getLimaParts();
            var today = limaNow.weekday;
            var currentTime = limaNow.time;
            var todayWindows = days.indexOf(today) !== -1 ? getScheduleForDay(schedule, today) : [];

            for (var i = 0; i < todayWindows.length; i += 1) {
                var range = todayWindows[i];
                if (currentTime >= range.start && currentTime < range.end) {
                    return {
                        state: 'open',
                        label: 'ABIERTO PARA RECIBIR DONACIONES',
                        detail: 'Cierra a las ' + formatTimeLabel(range.end) + '.'
                    };
                }
            }

            for (var j = 0; j < todayWindows.length; j += 1) {
                var nextRange = todayWindows[j];
                if (currentTime < nextRange.start) {
                    return {
                        state: 'closed',
                        label: 'CERRADO POR AHORA',
                        detail: 'Abre hoy a las ' + formatTimeLabel(nextRange.start) + '.'
                    };
                }
            }

            var todayIndex = week.indexOf(today);
            for (var offset = 1; offset <= 7; offset += 1) {
                var nextIndex = (todayIndex + offset) % week.length;
                var nextDay = week[nextIndex];
                if (days.indexOf(nextDay) === -1) continue;
                var nextRanges = getScheduleForDay(schedule, nextDay);
                if (!nextRanges.length) continue;
                return {
                    state: 'closed',
                    label: 'CERRADO POR AHORA',
                    detail: offset === 1
                        ? 'Abre mañana a las ' + formatTimeLabel(nextRanges[0].start) + '.'
                        : 'Vuelve pronto a las ' + formatTimeLabel(nextRanges[0].start) + '.'
                };
            }

            return {
                state: 'closed',
                label: 'CERRADO POR AHORA',
                detail: 'Revisa los días de recepción antes de acercarte.'
            };
        }

        function formatDistance(distanceKm) {
            if (distanceKm == null || !isFinite(distanceKm)) return '';
            if (distanceKm < 1) {
                return Math.max(50, Math.round(distanceKm * 1000 / 10) * 10) + ' m de ti';
            }
            return distanceKm.toFixed(distanceKm < 10 ? 1 : 0) + ' km de ti';
        }

        function haversineDistance(fromLat, fromLng, toLat, toLng) {
            var toRadians = Math.PI / 180;
            var dLat = (toLat - fromLat) * toRadians;
            var dLng = (toLng - fromLng) * toRadians;
            var a = Math.sin(dLat / 2) * Math.sin(dLat / 2)
                + Math.cos(fromLat * toRadians) * Math.cos(toLat * toRadians)
                * Math.sin(dLng / 2) * Math.sin(dLng / 2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return 6371 * c;
        }

        function getCardCoordinates(card) {
            var latitude = Number(card.getAttribute('data-point-latitude'));
            var longitude = Number(card.getAttribute('data-point-longitude'));
            if (!isFinite(latitude) || !isFinite(longitude)) return null;
            return { lat: latitude, lng: longitude };
        }

        function getCardDistance(card) {
            var raw = card.getAttribute('data-point-distance-value');
            if (raw == null || raw === '') return null;
            var numeric = Number(raw);
            return isFinite(numeric) ? numeric : null;
        }

        function setCardDistance(card, distanceKm) {
            var node = card.querySelector('[data-point-distance]');
            if (distanceKm == null || !isFinite(distanceKm)) {
                card.removeAttribute('data-point-distance-value');
                if (node) {
                    node.hidden = true;
                    node.textContent = '';
                }
                return;
            }

            card.setAttribute('data-point-distance-value', String(distanceKm));
            if (node) {
                node.hidden = false;
                node.textContent = formatDistance(distanceKm);
            }
        }

        function applyStatus(card) {
            var days = (card.getAttribute('data-point-days') || '').split(',').filter(Boolean);
            var rawSchedule = card.getAttribute('data-point-schedule') || '{}';
            var schedule = {};

            try {
                schedule = JSON.parse(rawSchedule);
            } catch (error) {
                schedule = {};
            }

            var computed = computeStatus(days, schedule);
            var badge = card.querySelector('[data-point-status-badge]');
            var detail = card.querySelector('[data-point-status-detail]');

            card.setAttribute('data-point-state', computed.state);

            if (badge) {
                badge.textContent = computed.label;
                badge.classList.remove('pickup-badge--open', 'pickup-badge--closed', 'pickup-badge--pending');
                badge.classList.add('pickup-badge--' + computed.state);
            }

            if (detail) {
                detail.textContent = computed.detail;
            }
        }

        function updateNearestSummary(distanceKm, pointName) {
            if (!nearestDistanceValue || !nearestDistanceLabel) return;
            if (distanceKm == null || !pointName) {
                nearestDistanceValue.textContent = 'Explora';
                nearestDistanceLabel.textContent = 'Activa tu ubicación para encontrar el punto más cercano.';
                return;
            }

            nearestDistanceValue.textContent = distanceKm < 1
                ? Math.round(distanceKm * 1000) + ' m'
                : distanceKm.toFixed(distanceKm < 10 ? 1 : 0) + ' km';
            nearestDistanceLabel.textContent = pointName + ' es el punto más cercano a tu ubicación actual.';
        }

        function renderFilterButtons() {
            filters.forEach(function (button) {
                var key = button.getAttribute('data-point-filter') || 'all';
                var active = key === activeFilter;
                button.classList.toggle('is-active', active);
                button.setAttribute('aria-pressed', active ? 'true' : 'false');
            });

            if (nearFilterButton) {
                nearFilterButton.disabled = userCoords == null;
            }
        }

        function matchesFilter(card) {
            if (activeFilter === 'all') return true;
            if (activeFilter === 'open-now') {
                return card.getAttribute('data-point-state') === 'open';
            }
            if (activeFilter === 'near-me') {
                return userCoords != null && getCardDistance(card) != null;
            }

            var tags = (card.getAttribute('data-point-filter-tags') || '').split(/\s+/).filter(Boolean);
            return tags.indexOf(activeFilter) !== -1;
        }

        function createMarkerIcon(state, options) {
            var classes = ['pickup-marker'];
            classes.push('pickup-marker--' + (state || 'closed'));
            if (options && options.nearest) classes.push('pickup-marker--closest');
            if (options && options.selected) classes.push('is-selected');
            if (options && options.hovered) classes.push('is-hovered');
            if (options && options.user) classes.push('pickup-marker--user');

            return L.divIcon({
                className: 'pickup-marker-wrap',
                html: '<span class="' + classes.join(' ') + '"></span>',
                iconSize: options && options.user ? [24, 24] : [22, 22],
                iconAnchor: options && options.user ? [12, 12] : [11, 11],
                popupAnchor: [0, -12]
            });
        }

        function buildPopupHtml(point, card) {
            var mapsUrl = point.maps_url || (card ? card.getAttribute('data-point-maps') : '') || '#';
            var distanceKm = card ? getCardDistance(card) : null;
            var hasPhoto = !!point.photo;
            var imageHtml = hasPhoto
                ? '<img src="' + point.photo + '" alt="' + (point.photo_alt || point.name) + '" loading="lazy" decoding="async">'
                : '<div class="pickup-popup__fallback"><span>' + point.short_name + '</span></div>';

            return ''
                + '<article class="pickup-popup">'
                + '<div class="pickup-popup__media">' + imageHtml + '</div>'
                + '<div class="pickup-popup__body">'
                + '<span class="pickup-popup__eyebrow">' + point.district + '</span>'
                + '<h3>' + point.name + '</h3>'
                + '<p class="pickup-popup__status pickup-popup__status--' + point.state + '">' + point.status_label + '</p>'
                + '<p class="pickup-popup__address">' + point.address + '</p>'
                + (distanceKm != null ? '<p class="pickup-popup__distance">' + formatDistance(distanceKm) + '</p>' : '')
                + '<a href="' + mapsUrl + '" target="_blank" rel="noopener noreferrer" class="pickup-popup__link">VER EN GOOGLE MAPS</a>'
                + '</div>'
                + '</article>';
        }

        function updateMarkerIcon(pointId) {
            var entry = markerIndex[pointId];
            if (!entry) return;
            entry.marker.setIcon(createMarkerIcon(entry.card.getAttribute('data-point-state'), {
                selected: Number(pointId) === Number(selectedPointId),
                hovered: entry.hovered === true,
                nearest: Number(pointId) === Number(nearestPointId)
            }));
        }

        function refreshPopup(pointId) {
            var entry = markerIndex[pointId];
            if (!entry) return;
            entry.point.state = entry.card.getAttribute('data-point-state') || entry.point.state;
            entry.point.status_label = entry.card.querySelector('[data-point-status-badge]') ? entry.card.querySelector('[data-point-status-badge]').textContent : entry.point.status_label;
            entry.marker.bindPopup(buildPopupHtml(entry.point, entry.card), {
                closeButton: true,
                autoPanPadding: [24, 24]
            });
        }

        function setHovered(pointId, hovered) {
            var entry = markerIndex[pointId];
            if (!entry) return;
            entry.hovered = hovered;
            updateMarkerIcon(pointId);
        }

        function highlightCard(pointId, shouldScroll) {
            cards.forEach(function (card) {
                var isActive = Number(card.getAttribute('data-point-id')) === Number(pointId);
                card.classList.toggle('is-selected', isActive);
                if (isActive && shouldScroll) {
                    card.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'nearest' });
                }
            });
        }

        function focusPoint(pointId, options) {
            selectedPointId = pointId;
            highlightCard(pointId, !(options && options.skipScroll));

            Object.keys(markerIndex).forEach(function (id) {
                updateMarkerIcon(id);
            });

            var entry = markerIndex[pointId];
            if (!entry || !map || !mapReady) return;

            map.flyTo(entry.marker.getLatLng(), Math.max(map.getZoom(), 14), {
                animate: !prefersReducedMotion,
                duration: prefersReducedMotion ? 0 : 0.85
            });
            refreshPopup(pointId);
            entry.marker.openPopup();
        }

        function renderNearbyList(sortedCards) {
            if (!nearbyBlock || !nearbyList) return;
            nearbyList.innerHTML = '';

            if (!userCoords || !sortedCards.length) {
                nearbyBlock.hidden = true;
                return;
            }

            sortedCards.slice(0, 3).forEach(function (card, index) {
                var item = document.createElement('li');
                item.className = 'pickup-nearby__item';
                var button = document.createElement('button');
                button.type = 'button';
                button.className = 'pickup-nearby__button';
                button.innerHTML = '<span class="pickup-nearby__rank">' + (index + 1) + '</span><span class="pickup-nearby__copy"><strong>' + (card.getAttribute('data-point-name') || '') + '</strong><small>' + formatDistance(getCardDistance(card)) + '</small></span>';
                button.addEventListener('click', function () {
                    focusPoint(Number(card.getAttribute('data-point-id')));
                });
                item.appendChild(button);
                nearbyList.appendChild(item);
            });

            nearbyBlock.hidden = false;
        }

        function updateGeoDistances() {
            var closest = null;

            cards.forEach(function (card) {
                var coordinates = getCardCoordinates(card);
                var badge = card.querySelector('[data-point-nearest-badge]');

                if (!userCoords || !coordinates) {
                    setCardDistance(card, null);
                    card.classList.remove('is-nearest');
                    if (badge) badge.hidden = true;
                    return;
                }

                var distanceKm = haversineDistance(userCoords.lat, userCoords.lng, coordinates.lat, coordinates.lng);
                setCardDistance(card, distanceKm);

                if (!closest || distanceKm < closest.distance) {
                    closest = {
                        id: Number(card.getAttribute('data-point-id')),
                        distance: distanceKm,
                        card: card
                    };
                }
            });

            nearestPointId = closest ? closest.id : null;

            cards.forEach(function (card) {
                var isNearest = closest && Number(card.getAttribute('data-point-id')) === closest.id;
                card.classList.toggle('is-nearest', !!isNearest);
                var badge = card.querySelector('[data-point-nearest-badge]');
                if (badge) {
                    badge.hidden = !isNearest;
                }
            });

            updateNearestSummary(closest ? closest.distance : null, closest ? closest.card.getAttribute('data-point-name') : '');
            renderNearbyList(cards.filter(function (card) {
                return getCardDistance(card) != null;
            }).sort(function (a, b) {
                return getCardDistance(a) - getCardDistance(b);
            }));

            Object.keys(markerIndex).forEach(function (id) {
                updateMarkerIcon(id);
                refreshPopup(id);
            });
        }

        function syncMarkerVisibility() {
            if (!mapReady || !pointsLayer) return;
            Object.keys(markerIndex).forEach(function (id) {
                var entry = markerIndex[id];
                if (!entry.card.hidden && !pointsLayer.hasLayer(entry.marker)) {
                    pointsLayer.addLayer(entry.marker);
                }
                if (entry.card.hidden && pointsLayer.hasLayer(entry.marker)) {
                    pointsLayer.removeLayer(entry.marker);
                }
            });
        }

        function sortVisibleCards() {
            var grid = document.querySelector('[data-point-grid]');
            if (!grid) return;

            cards.slice().sort(function (a, b) {
                var hiddenA = a.hidden ? 1 : 0;
                var hiddenB = b.hidden ? 1 : 0;
                if (hiddenA !== hiddenB) return hiddenA - hiddenB;

                if (activeFilter === 'near-me' && userCoords) {
                    var distanceA = getCardDistance(a);
                    var distanceB = getCardDistance(b);
                    if (distanceA != null && distanceB != null && distanceA !== distanceB) {
                        return distanceA - distanceB;
                    }
                    if (distanceA != null && distanceB == null) return -1;
                    if (distanceA == null && distanceB != null) return 1;
                }

                return Number(a.getAttribute('data-point-id')) - Number(b.getAttribute('data-point-id'));
            }).forEach(function (card) {
                grid.appendChild(card);
            });
        }

        function initializeMap() {
            if (!mapCanvas || typeof L === 'undefined') return;

            try {
                pointPayload = payloadNode ? JSON.parse(payloadNode.textContent || '[]') : [];
            } catch (error) {
                pointPayload = [];
            }

            map = L.map(mapCanvas, {
                scrollWheelZoom: true,
                zoomControl: true
            });
            pointsLayer = L.layerGroup().addTo(map);
            mapReady = true;

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            var bounds = [];

            pointPayload.forEach(function (point) {
                if (!point.coordinates_verified || !isFinite(Number(point.latitude)) || !isFinite(Number(point.longitude))) {
                    return;
                }

                var pointId = Number(point.id);
                var card = cards.find(function (candidate) {
                    return Number(candidate.getAttribute('data-point-id')) === pointId;
                });
                if (!card) return;

                var marker = L.marker([Number(point.latitude), Number(point.longitude)], {
                    icon: createMarkerIcon(card.getAttribute('data-point-state'))
                });

                marker.on('click', function () {
                    focusPoint(pointId);
                });
                marker.on('mouseover', function () {
                    setHovered(pointId, true);
                });
                marker.on('mouseout', function () {
                    setHovered(pointId, false);
                });

                markerIndex[pointId] = {
                    marker: marker,
                    point: point,
                    card: card,
                    hovered: false
                };

                refreshPopup(pointId);
                pointsLayer.addLayer(marker);
                bounds.push([Number(point.latitude), Number(point.longitude)]);
            });

            if (bounds.length) {
                map.fitBounds(bounds, { padding: [34, 34] });
                if (bounds.length === 1) {
                    map.setZoom(15);
                }
            } else {
                map.setView([-18.0146, -70.2512], 13);
            }

            window.setTimeout(function () {
                map.invalidateSize();
            }, 150);
        }

        function applyFilters() {
            var query = normalizeText(input && input.value ? input.value : '');
            var visible = 0;

            cards.forEach(function (card) {
                applyStatus(card);
                var haystack = normalizeText(card.getAttribute('data-point-searchable') || '');
                var searchMatch = !query || haystack.indexOf(query) !== -1;
                var filterMatch = matchesFilter(card);
                var matches = searchMatch && filterMatch;
                card.hidden = !matches;
                if (matches) visible += 1;
            });

            if (count) count.textContent = String(visible);
            if (empty) empty.hidden = visible !== 0;

            sortVisibleCards();
            syncMarkerVisibility();
            renderFilterButtons();
        }

        function queueFilter() {
            window.clearTimeout(debounceTimer);
            debounceTimer = window.setTimeout(applyFilters, 180);
        }

        function attachCardInteractions() {
            cards.forEach(function (card) {
                var pointId = Number(card.getAttribute('data-point-id'));
                var toggle = card.querySelector('[data-point-toggle]');
                var copyButton = card.querySelector('[data-point-copy]');
                var shareButton = card.querySelector('[data-point-share]');

                card.addEventListener('mouseenter', function () {
                    setHovered(pointId, true);
                });
                card.addEventListener('mouseleave', function () {
                    setHovered(pointId, false);
                });
                card.addEventListener('focusin', function () {
                    setHovered(pointId, true);
                });
                card.addEventListener('focusout', function () {
                    setHovered(pointId, false);
                });
                card.addEventListener('click', function (event) {
                    if (event.target.closest('button, a, input, select, textarea, summary')) return;
                    focusPoint(pointId, { skipScroll: true });
                });
                card.addEventListener('keydown', function (event) {
                    if (event.target !== card) return;
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        focusPoint(pointId, { skipScroll: true });
                    }
                });

                if (toggle) {
                    toggle.addEventListener('click', function () {
                        var targetId = toggle.getAttribute('data-point-toggle');
                        var panel = targetId ? document.getElementById(targetId) : null;
                        if (!panel) return;
                        var isOpen = !panel.hidden;
                        panel.hidden = isOpen;
                        toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                    });
                }

                if (copyButton) {
                    copyButton.addEventListener('click', function () {
                        var address = card.getAttribute('data-point-address') || '';
                        if (!address) {
                            showToast('No se pudo copiar la dirección.', 'error');
                            return;
                        }

                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(address).then(function () {
                                showToast('Dirección copiada.', 'success');
                            }).catch(function () {
                                showToast('No se pudo copiar la dirección.', 'error');
                            });
                            return;
                        }

                        var helper = document.createElement('textarea');
                        helper.value = address;
                        helper.setAttribute('readonly', 'readonly');
                        helper.style.position = 'absolute';
                        helper.style.left = '-9999px';
                        document.body.appendChild(helper);
                        helper.select();

                        try {
                            document.execCommand('copy');
                            showToast('Dirección copiada.', 'success');
                        } catch (error) {
                            showToast('No se pudo copiar la dirección.', 'error');
                        }

                        document.body.removeChild(helper);
                    });
                }

                if (shareButton) {
                    shareButton.addEventListener('click', function () {
                        var name = card.getAttribute('data-point-name') || 'Punto de acopio';
                        var address = card.getAttribute('data-point-address') || '';
                        var mapsUrl = card.getAttribute('data-point-maps') || '';
                        var shareText = 'Punto de acopio Abrigando Corazones: ' + name + ' ' + address + (mapsUrl ? ' ' + mapsUrl : '');

                        if (navigator.share) {
                            navigator.share({
                                title: name,
                                text: shareText,
                                url: mapsUrl || window.location.href
                            }).catch(function () {
                                return;
                            });
                            return;
                        }

                        window.open('https://wa.me/?text=' + encodeURIComponent(shareText), '_blank', 'noopener');
                    });
                }
            });
        }

        function handleGeolocationSuccess(position) {
            userCoords = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            if (geolocateButton) {
                var successLabel = geolocateButton.getAttribute('data-label-original') || 'ENCONTRAR EL PUNTO MÁS CERCANO';
                geolocateButton.disabled = false;
                geolocateButton.classList.remove('is-loading');
                geolocateButton.innerHTML = '<span>' + successLabel + '</span>';
            }

            if (map && mapReady) {
                if (userMarker) {
                    map.removeLayer(userMarker);
                }
                if (userCircle) {
                    map.removeLayer(userCircle);
                }

                userMarker = L.marker([userCoords.lat, userCoords.lng], {
                    icon: createMarkerIcon('open', { user: true })
                }).addTo(map);
                userMarker.bindPopup('<div class="pickup-popup__user">Tu ubicación actual</div>');

                userCircle = L.circle([userCoords.lat, userCoords.lng], {
                    radius: 250,
                    color: '#2563eb',
                    fillColor: '#2563eb',
                    fillOpacity: 0.12,
                    weight: 1.4
                }).addTo(map);

                map.flyTo([userCoords.lat, userCoords.lng], Math.max(map.getZoom(), 13), {
                    animate: !prefersReducedMotion,
                    duration: prefersReducedMotion ? 0 : 0.9
                });
            }

            updateGeoDistances();
            if (nearFilterButton) {
                nearFilterButton.disabled = false;
            }
            activeFilter = 'near-me';
            applyFilters();
        }

        function handleGeolocationError() {
            userCoords = null;
            if (geolocateButton) {
                var errorLabel = geolocateButton.getAttribute('data-label-original') || 'ENCONTRAR EL PUNTO MÁS CERCANO';
                geolocateButton.disabled = false;
                geolocateButton.classList.remove('is-loading');
                geolocateButton.innerHTML = '<span>' + errorLabel + '</span>';
            }
            if (nearFilterButton) {
                nearFilterButton.disabled = true;
            }
            if (activeFilter === 'near-me') {
                activeFilter = 'all';
            }
            updateGeoDistances();
            applyFilters();
            showToast('No pudimos acceder a tu ubicación. Puedes explorar todos los puntos manualmente.', 'error');
        }

        filters.forEach(function (button) {
            button.addEventListener('click', function () {
                var nextFilter = button.getAttribute('data-point-filter') || 'all';
                if (nextFilter === 'near-me' && userCoords == null) {
                    return;
                }
                activeFilter = nextFilter;
                applyFilters();
            });
        });

        if (input) {
            input.addEventListener('input', queueFilter);
        }

        if (clear) {
            clear.addEventListener('click', function () {
                if (input) input.value = '';
                activeFilter = 'all';
                applyFilters();
            });
        }

        if (geolocateButton) {
            geolocateButton.addEventListener('click', function () {
                if (!navigator.geolocation) {
                    showToast('Tu navegador no soporta geolocalización. Puedes explorar los puntos manualmente.', 'error');
                    return;
                }

                navigator.geolocation.getCurrentPosition(handleGeolocationSuccess, handleGeolocationError, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 120000
                });
            });
        }

        if (geolocateButton) {
            geolocateButton.addEventListener('click', function () {
                if (!navigator.geolocation) return;
                var originalLabel = geolocateButton.getAttribute('data-label-original') || geolocateButton.textContent.trim();
                geolocateButton.setAttribute('data-label-original', originalLabel);
                geolocateButton.disabled = true;
                geolocateButton.classList.add('is-loading');
                geolocateButton.innerHTML = '<span>BUSCANDO TU UBICACIÓN...</span>';
            }, true);
        }

        attachCardInteractions();
        initializeMap();
        updateGeoDistances();
        applyFilters();
    }

    function initCollectionPointsDirectoryV2() {
        var directory = document.querySelector('[data-points-directory]');
        if (!directory) return;

        var input = directory.querySelector('[data-point-search]');
        var viewportInput = directory.querySelector('[data-point-search-viewport]');
        var clear = directory.querySelector('[data-point-clear]');
        var count = directory.querySelector('[data-point-count]');
        var countLabels = Array.prototype.slice.call(directory.querySelectorAll('[data-point-count-label], [data-point-count-kpi-label]'));
        var empty = document.querySelector('[data-point-empty]');
        var cards = Array.prototype.slice.call(document.querySelectorAll('[data-point-card]'));
        var filters = Array.prototype.slice.call(directory.querySelectorAll('[data-point-filter]'));
        var geoButtons = Array.prototype.slice.call(directory.querySelectorAll('[data-point-geolocate], [data-point-geolocate-secondary], [data-point-geolocate-empty]'));
        var nearFilterButton = directory.querySelector('[data-point-near-filter]');
        var nearestDistanceLabel = document.querySelector('[data-nearest-distance-label]');
        var nearbyBlock = document.querySelector('[data-point-nearby]');
        var nearbyList = document.querySelector('[data-point-nearby-list]');
        var nearbyEmpty = document.querySelector('[data-point-nearby-empty]');
        var openKpi = directory.querySelector('[data-point-open-kpi]');
        var fitVisibleButtons = Array.prototype.slice.call(directory.querySelectorAll('[data-point-fit-visible]'));
        var showAllButton = directory.querySelector('[data-point-show-all]');
        var helpToggle = directory.querySelector('[data-point-map-help-toggle]');
        var helpPanel = directory.querySelector('[data-point-map-help]');
        var payloadNode = document.querySelector('[data-point-map-payload]');
        var mapCanvas = document.querySelector('[data-collection-points-map]');
        var activeFilter = 'all';
        var debounceTimer = null;
        var userCoords = null;
        var nearestPointId = null;
        var selectedPointId = null;
        var map = null;
        var mapReady = false;
        var pointsLayer = null;
        var userMarker = null;
        var userCircle = null;
        var markerIndex = {};
        var pointPayload = [];

        function showToast(message, type) {
            var toast = document.createElement('div');
            toast.className = 'toast toast--' + (type === 'error' ? 'error' : 'success');
            toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
            toast.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
            toast.setAttribute('data-toast', '');
            toast.innerHTML = '<span class="toast__icon">' + (type === 'error' ? '!' : 'OK') + '</span><span>' + message + '</span>';
            document.body.appendChild(toast);
            window.setTimeout(function () {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity .3s ease';
                window.setTimeout(function () {
                    if (toast.parentNode) toast.parentNode.removeChild(toast);
                }, 320);
            }, 2500);
        }

        function getLimaParts() {
            var formatter = new Intl.DateTimeFormat('en-US', {
                timeZone: 'America/Lima',
                weekday: 'long',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
            var values = {};
            formatter.formatToParts(new Date()).forEach(function (part) {
                values[part.type] = part.value;
            });
            return {
                weekday: (values.weekday || '').toLowerCase(),
                time: (values.hour || '00') + ':' + (values.minute || '00')
            };
        }

        function formatTimeLabel(time) {
            var segments = (time || '00:00').split(':');
            var hour = Number(segments[0] || 0);
            var minute = Number(segments[1] || 0);
            var period = hour >= 12 ? 'p. m.' : 'a. m.';
            var displayHour = hour % 12;
            if (displayHour === 0) displayHour = 12;
            return displayHour + ':' + String(minute).padStart(2, '0') + ' ' + period;
        }

        function getScheduleForDay(schedule, dayKey) {
            if (!schedule) return [];
            var exactDay = schedule[dayKey];
            if (Array.isArray(exactDay)) return exactDay;
            var fallback = schedule['default'];
            return Array.isArray(fallback) ? fallback : [];
        }

        function computeStatus(days, schedule) {
            if (!days.length || !schedule || !Object.keys(schedule).length) {
                return { state: 'pending', label: 'HORARIO DE ACOPIO POR CONFIRMAR', detail: 'Consulta antes de acercarte.' };
            }

            var week = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            var limaNow = getLimaParts();
            var today = limaNow.weekday;
            var currentTime = limaNow.time;
            var todayWindows = days.indexOf(today) !== -1 ? getScheduleForDay(schedule, today) : [];

            for (var i = 0; i < todayWindows.length; i += 1) {
                var range = todayWindows[i];
                if (currentTime >= range.start && currentTime < range.end) {
                    return { state: 'open', label: 'ABIERTO PARA RECIBIR DONACIONES', detail: 'Cierra a las ' + formatTimeLabel(range.end) + '.' };
                }
            }

            for (var j = 0; j < todayWindows.length; j += 1) {
                var nextRange = todayWindows[j];
                if (currentTime < nextRange.start) {
                    return { state: 'closed', label: 'CERRADO POR AHORA', detail: 'Abre hoy a las ' + formatTimeLabel(nextRange.start) + '.' };
                }
            }

            var todayIndex = week.indexOf(today);
            for (var offset = 1; offset <= 7; offset += 1) {
                var nextIndex = (todayIndex + offset) % week.length;
                var nextDay = week[nextIndex];
                if (days.indexOf(nextDay) === -1) continue;
                var nextRanges = getScheduleForDay(schedule, nextDay);
                if (!nextRanges.length) continue;
                return {
                    state: 'closed',
                    label: 'CERRADO POR AHORA',
                    detail: offset === 1 ? 'Abre mañana a las ' + formatTimeLabel(nextRanges[0].start) + '.' : 'Vuelve pronto a las ' + formatTimeLabel(nextRanges[0].start) + '.'
                };
            }

            return { state: 'closed', label: 'CERRADO POR AHORA', detail: 'Revisa los días de recepción antes de acercarte.' };
        }

        function formatDistance(distanceKm) {
            if (distanceKm == null || !isFinite(distanceKm)) return '';
            if (distanceKm < 1) return Math.max(50, Math.round(distanceKm * 1000 / 10) * 10) + ' m de ti';
            return distanceKm.toFixed(distanceKm < 10 ? 1 : 0) + ' km de ti';
        }

        function haversineDistance(fromLat, fromLng, toLat, toLng) {
            var toRadians = Math.PI / 180;
            var dLat = (toLat - fromLat) * toRadians;
            var dLng = (toLng - fromLng) * toRadians;
            var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(fromLat * toRadians) * Math.cos(toLat * toRadians) * Math.sin(dLng / 2) * Math.sin(dLng / 2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return 6371 * c;
        }

        function getCardCoordinates(card) {
            var latitude = Number(card.getAttribute('data-point-latitude'));
            var longitude = Number(card.getAttribute('data-point-longitude'));
            if (!isFinite(latitude) || !isFinite(longitude)) return null;
            return { lat: latitude, lng: longitude };
        }

        function getCardDistance(card) {
            var raw = card.getAttribute('data-point-distance-value');
            if (raw == null || raw === '') return null;
            var numeric = Number(raw);
            return isFinite(numeric) ? numeric : null;
        }

        function setCardDistance(card, distanceKm) {
            var node = card.querySelector('[data-point-distance]');
            if (distanceKm == null || !isFinite(distanceKm)) {
                card.removeAttribute('data-point-distance-value');
                if (node) {
                    node.hidden = true;
                    node.textContent = '';
                }
                return;
            }
            card.setAttribute('data-point-distance-value', String(distanceKm));
            if (node) {
                node.hidden = false;
                node.textContent = formatDistance(distanceKm);
            }
        }

        function applyStatus(card) {
            var days = (card.getAttribute('data-point-days') || '').split(',').filter(Boolean);
            var rawSchedule = card.getAttribute('data-point-schedule') || '{}';
            var schedule = {};
            try {
                schedule = JSON.parse(rawSchedule);
            } catch (error) {
                schedule = {};
            }
            var computed = computeStatus(days, schedule);
            var badge = card.querySelector('[data-point-status-badge]');
            var detail = card.querySelector('[data-point-status-detail]');
            card.setAttribute('data-point-state', computed.state);
            if (badge) {
                badge.textContent = computed.label;
                badge.classList.remove('pickup-badge--open', 'pickup-badge--closed', 'pickup-badge--pending');
                badge.classList.add('pickup-badge--' + computed.state);
            }
            if (detail) detail.textContent = computed.detail;
        }

        function updateNearestSummary(distanceKm, pointName) {
            if (!nearestDistanceLabel) return;
            if (distanceKm == null || !pointName) {
                nearestDistanceLabel.textContent = 'Explora los puntos más cercanos a tu ubicación.';
                return;
            }
            nearestDistanceLabel.textContent = pointName + ' está a ' + (distanceKm < 1 ? Math.round(distanceKm * 1000) + ' m' : distanceKm.toFixed(distanceKm < 10 ? 1 : 0) + ' km') + ' de tu ubicación.';
        }

        function renderFilterButtons() {
            filters.forEach(function (button) {
                var key = button.getAttribute('data-point-filter') || 'all';
                var active = key === activeFilter;
                button.classList.toggle('is-active', active);
                button.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
            if (nearFilterButton) nearFilterButton.disabled = userCoords == null;
        }

        function matchesFilter(card) {
            if (activeFilter === 'all') return true;
            if (activeFilter === 'open-now') return card.getAttribute('data-point-state') === 'open';
            if (activeFilter === 'near-me') return userCoords != null && getCardDistance(card) != null;
            var tags = (card.getAttribute('data-point-filter-tags') || '').split(/\s+/).filter(Boolean);
            return tags.indexOf(activeFilter) !== -1;
        }

        function createMarkerIcon(state, options) {
            var classes = ['pickup-marker', 'pickup-marker--' + (state || 'closed')];
            if (options && options.nearest) classes.push('pickup-marker--closest');
            if (options && options.selected) classes.push('is-selected');
            if (options && options.hovered) classes.push('is-hovered');
            if (options && options.user) classes.push('pickup-marker--user');
            return L.divIcon({
                className: 'pickup-marker-wrap',
                html: '<span class="' + classes.join(' ') + '"></span>',
                iconSize: options && options.user ? [24, 24] : [22, 22],
                iconAnchor: options && options.user ? [12, 12] : [11, 11],
                popupAnchor: [0, -12]
            });
        }

        function buildPopupHtml(point, card) {
            var mapsUrl = point.maps_url || (card ? card.getAttribute('data-point-maps') : '') || '#';
            var distanceKm = card ? getCardDistance(card) : null;
            var hasPhoto = !!point.photo;
            var imageHtml = hasPhoto ? '<img src="' + point.photo + '" alt="' + (point.photo_alt || point.name) + '" loading="lazy" decoding="async">' : '<div class="pickup-popup__fallback"><span>' + point.short_name + '</span></div>';
            return '<article class="pickup-popup"><div class="pickup-popup__media">' + imageHtml + '</div><div class="pickup-popup__body"><span class="pickup-popup__eyebrow">' + point.district + '</span><h3>' + point.name + '</h3><p class="pickup-popup__status pickup-popup__status--' + point.state + '">' + point.status_label + '</p><p class="pickup-popup__address">' + point.address + '</p>' + (distanceKm != null ? '<p class="pickup-popup__distance">' + formatDistance(distanceKm) + '</p>' : '') + '<a href="' + mapsUrl + '" target="_blank" rel="noopener noreferrer" class="pickup-popup__link">VER EN GOOGLE MAPS</a></div></article>';
        }

        function updateMarkerIcon(pointId) {
            var entry = markerIndex[pointId];
            if (!entry) return;
            entry.marker.setIcon(createMarkerIcon(entry.card.getAttribute('data-point-state'), {
                selected: Number(pointId) === Number(selectedPointId),
                hovered: entry.hovered === true,
                nearest: Number(pointId) === Number(nearestPointId)
            }));
        }

        function refreshPopup(pointId) {
            var entry = markerIndex[pointId];
            if (!entry) return;
            entry.point.state = entry.card.getAttribute('data-point-state') || entry.point.state;
            entry.point.status_label = entry.card.querySelector('[data-point-status-badge]') ? entry.card.querySelector('[data-point-status-badge]').textContent : entry.point.status_label;
            entry.marker.bindPopup(buildPopupHtml(entry.point, entry.card), { closeButton: true, autoPanPadding: [24, 24] });
        }

        function setHovered(pointId, hovered) {
            var entry = markerIndex[pointId];
            if (!entry) return;
            entry.hovered = hovered;
            updateMarkerIcon(pointId);
        }

        function highlightCard(pointId, shouldScroll) {
            cards.forEach(function (card) {
                var isActive = Number(card.getAttribute('data-point-id')) === Number(pointId);
                card.classList.toggle('is-selected', isActive);
                if (isActive && shouldScroll) {
                    card.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'nearest' });
                }
            });
        }

        function focusPoint(pointId, options) {
            selectedPointId = pointId;
            highlightCard(pointId, !(options && options.skipScroll));
            Object.keys(markerIndex).forEach(function (id) {
                updateMarkerIcon(id);
            });
            var entry = markerIndex[pointId];
            if (!entry || !map || !mapReady) return;
            map.flyTo(entry.marker.getLatLng(), Math.max(map.getZoom(), 14), {
                animate: !prefersReducedMotion,
                duration: prefersReducedMotion ? 0 : 0.85
            });
            refreshPopup(pointId);
            entry.marker.openPopup();
        }

        function updateNearbyVisibility(showResults) {
            if (nearbyBlock) nearbyBlock.hidden = !showResults;
            if (nearbyEmpty) nearbyEmpty.hidden = showResults;
        }

        function renderNearbyList(sortedCards) {
            if (!nearbyBlock || !nearbyList) return;
            nearbyList.innerHTML = '';
            if (!userCoords || !sortedCards.length) {
                updateNearbyVisibility(false);
                return;
            }
            sortedCards.slice(0, 3).forEach(function (card, index) {
                var item = document.createElement('li');
                item.className = 'pickup-nearby__item';
                var button = document.createElement('button');
                button.type = 'button';
                button.className = 'pickup-nearby__button';
                button.innerHTML = '<span class="pickup-nearby__rank">' + (index + 1) + '</span><span class="pickup-nearby__copy"><strong>' + (card.getAttribute('data-point-name') || '') + '</strong><small>' + formatDistance(getCardDistance(card)) + '</small></span>';
                button.addEventListener('click', function () {
                    focusPoint(Number(card.getAttribute('data-point-id')));
                });
                item.appendChild(button);
                nearbyList.appendChild(item);
            });
            updateNearbyVisibility(true);
        }

        function updateGeoDistances() {
            var closest = null;
            cards.forEach(function (card) {
                var coordinates = getCardCoordinates(card);
                var badge = card.querySelector('[data-point-nearest-badge]');
                if (!userCoords || !coordinates) {
                    setCardDistance(card, null);
                    card.classList.remove('is-nearest');
                    if (badge) badge.hidden = true;
                    return;
                }
                var distanceKm = haversineDistance(userCoords.lat, userCoords.lng, coordinates.lat, coordinates.lng);
                setCardDistance(card, distanceKm);
                if (!closest || distanceKm < closest.distance) {
                    closest = { id: Number(card.getAttribute('data-point-id')), distance: distanceKm, card: card };
                }
            });
            nearestPointId = closest ? closest.id : null;
            cards.forEach(function (card) {
                var isNearest = closest && Number(card.getAttribute('data-point-id')) === closest.id;
                card.classList.toggle('is-nearest', !!isNearest);
                var badge = card.querySelector('[data-point-nearest-badge]');
                if (badge) badge.hidden = !isNearest;
            });
            updateNearestSummary(closest ? closest.distance : null, closest ? closest.card.getAttribute('data-point-name') : '');
            renderNearbyList(cards.filter(function (card) {
                return getCardDistance(card) != null && !card.hidden;
            }).sort(function (a, b) {
                return getCardDistance(a) - getCardDistance(b);
            }));
            Object.keys(markerIndex).forEach(function (id) {
                updateMarkerIcon(id);
                refreshPopup(id);
            });
        }

        function syncMarkerVisibility() {
            if (!mapReady || !pointsLayer) return;
            Object.keys(markerIndex).forEach(function (id) {
                var entry = markerIndex[id];
                if (!entry.card.hidden && !pointsLayer.hasLayer(entry.marker)) pointsLayer.addLayer(entry.marker);
                if (entry.card.hidden && pointsLayer.hasLayer(entry.marker)) pointsLayer.removeLayer(entry.marker);
            });
        }

        function fitVisibleMarkers(options) {
            if (!mapReady || !map) return;
            var visibleLatLngs = Object.keys(markerIndex).reduce(function (collection, id) {
                var entry = markerIndex[id];
                if (!entry.card.hidden) collection.push(entry.marker.getLatLng());
                return collection;
            }, []);
            if (!visibleLatLngs.length) return;
            if (visibleLatLngs.length === 1) {
                map.flyTo(visibleLatLngs[0], Math.max(map.getZoom(), 15), {
                    animate: !prefersReducedMotion,
                    duration: prefersReducedMotion ? 0 : 0.75
                });
                return;
            }
            map.flyToBounds(L.latLngBounds(visibleLatLngs), {
                padding: [26, 26],
                animate: !(options && options.instant) && !prefersReducedMotion,
                duration: prefersReducedMotion ? 0 : 0.75
            });
        }

        function sortVisibleCards() {
            var grid = document.querySelector('[data-point-grid]');
            if (!grid) return;
            cards.slice().sort(function (a, b) {
                var hiddenA = a.hidden ? 1 : 0;
                var hiddenB = b.hidden ? 1 : 0;
                if (hiddenA !== hiddenB) return hiddenA - hiddenB;
                if (activeFilter === 'near-me' && userCoords) {
                    var distanceA = getCardDistance(a);
                    var distanceB = getCardDistance(b);
                    if (distanceA != null && distanceB != null && distanceA !== distanceB) return distanceA - distanceB;
                    if (distanceA != null && distanceB == null) return -1;
                    if (distanceA == null && distanceB != null) return 1;
                }
                return Number(a.getAttribute('data-point-id')) - Number(b.getAttribute('data-point-id'));
            }).forEach(function (card) {
                grid.appendChild(card);
            });
        }

        function setGeoButtonsState(loading) {
            geoButtons.forEach(function (button) {
                if (!button) return;
                if (!button.hasAttribute('data-original-label')) button.setAttribute('data-original-label', button.innerHTML);
                button.disabled = loading;
                button.classList.toggle('is-loading', loading);
                button.innerHTML = loading
                    ? (button.hasAttribute('data-point-geolocate-secondary') ? '<span>...</span>' : '<span>BUSCANDO TU UBICACIÓN...</span>')
                    : button.getAttribute('data-original-label');
            });
        }

        function updateCounters(visibleCards) {
            var visibleCount = visibleCards.length;
            var openCount = visibleCards.filter(function (card) {
                return card.getAttribute('data-point-state') === 'open';
            }).length;
            if (count) count.textContent = String(visibleCount);
            countLabels.forEach(function (label) {
                label.textContent = visibleCount === 1 ? 'PUNTO VISIBLE' : 'PUNTOS VISIBLES';
            });
            if (openKpi) {
                openKpi.textContent = String(openCount);
                var statusLabel = openKpi.nextElementSibling;
                if (statusLabel) statusLabel.textContent = openCount === 1 ? 'ABIERTO AHORA' : 'ABIERTOS AHORA';
            }
        }

        function initializeMap() {
            if (!mapCanvas || typeof L === 'undefined') return;
            try {
                pointPayload = payloadNode ? JSON.parse(payloadNode.textContent || '[]') : [];
            } catch (error) {
                pointPayload = [];
            }
            try {
                overlayPayload = overlayNode ? JSON.parse(overlayNode.textContent || 'null') : null;
            } catch (error) {
                overlayPayload = null;
            }
            map = L.map(mapCanvas, { scrollWheelZoom: true, zoomControl: true });
            pointsLayer = L.layerGroup().addTo(map);
            mapReady = true;
            var mapFrame = mapCanvas.closest('.pickup-map__frame--reference');
            var tileFallbackActivated = false;
            var tileLoadSettled = false;
            var tileErrorCount = 0;
            var tileLayer = null;
            var imageOverlay = null;

            function markMapReady() {
                tileLoadSettled = true;
                if (mapFrame) mapFrame.classList.add('is-map-ready');
                window.requestAnimationFrame(function () { map.invalidateSize(); });
            }

            function createPrimaryTileLayer() {
                return L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    subdomains: 'abcd',
                    maxZoom: 20,
                    attribution: '&copy; OpenStreetMap contributors &copy; CARTO'
                });
            }

            function createFallbackTileLayer() {
                return L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
                    maxZoom: 19,
                    attribution: '&copy; Esri &copy; OpenStreetMap'
                });
            }

            function bindTileEvents(layer) {
                layer.on('load', function () {
                    markMapReady();
                });
                layer.on('tileerror', function () {
                    tileErrorCount += 1;
                    if (!tileFallbackActivated && tileErrorCount >= 4) {
                        tileFallbackActivated = true;
                        try {
                            map.removeLayer(layer);
                        } catch (error) {
                            // ignore removal issues and continue with fallback
                        }
                        tileLayer = createFallbackTileLayer();
                        bindTileEvents(tileLayer);
                        tileLayer.addTo(map);
                    }
                });
            }

            tileLayer = createPrimaryTileLayer();
            bindTileEvents(tileLayer);
            tileLayer.addTo(map);
            if (overlayPayload && overlayPayload.image && Array.isArray(overlayPayload.bounds) && overlayPayload.bounds.length === 2) {
                imageOverlay = L.imageOverlay(overlayPayload.image, overlayPayload.bounds, {
                    opacity: 0.96,
                    interactive: false,
                    className: 'pickup-map__image-overlay'
                });
                imageOverlay.addTo(map);
            }
            var bounds = [];
            pointPayload.forEach(function (point) {
                if (!point.coordinates_verified || !isFinite(Number(point.latitude)) || !isFinite(Number(point.longitude))) return;
                var pointId = Number(point.id);
                var card = cards.find(function (candidate) {
                    return Number(candidate.getAttribute('data-point-id')) === pointId;
                });
                if (!card) return;
                var marker = L.marker([Number(point.latitude), Number(point.longitude)], { icon: createMarkerIcon(card.getAttribute('data-point-state')) });
                marker.on('click', function () { focusPoint(pointId); });
                marker.on('mouseover', function () { setHovered(pointId, true); });
                marker.on('mouseout', function () { setHovered(pointId, false); });
                markerIndex[pointId] = { marker: marker, point: point, card: card, hovered: false };
                refreshPopup(pointId);
                pointsLayer.addLayer(marker);
                bounds.push([Number(point.latitude), Number(point.longitude)]);
            });
            if (bounds.length) {
                map.fitBounds(bounds, { padding: [34, 34] });
                if (bounds.length === 1) map.setZoom(15);
                window.requestAnimationFrame(function () { map.invalidateSize(); });
                window.setTimeout(function () { map.invalidateSize(); }, 160);
                window.setTimeout(function () { map.invalidateSize(); }, 520);
                window.setTimeout(function () {
                    if (!tileLoadSettled) markMapReady();
                    map.invalidateSize();
                }, 1200);
            } else {
                map.setView([-18.0146, -70.2512], 13);
                markMapReady();
            }

            window.addEventListener('load', function () {
                if (map) map.invalidateSize();
            }, { once: true });

            if ('ResizeObserver' in window) {
                var resizeObserver = new ResizeObserver(function () {
                    if (!map) return;
                    window.requestAnimationFrame(function () {
                        map.invalidateSize();
                    });
                });
                resizeObserver.observe(mapCanvas);
            }
        }

        function applyFilters() {
            var query = normalizeText(input && input.value ? input.value : '');
            var visibleCards = [];
            if (viewportInput && input && viewportInput.value !== input.value) viewportInput.value = input.value;
            cards.forEach(function (card) {
                applyStatus(card);
                var haystack = normalizeText(card.getAttribute('data-point-searchable') || '');
                var searchMatch = !query || haystack.indexOf(query) !== -1;
                var filterMatch = matchesFilter(card);
                var matches = searchMatch && filterMatch;
                card.hidden = !matches;
                if (matches) visibleCards.push(card);
            });
            updateCounters(visibleCards);
            if (empty) empty.hidden = visibleCards.length !== 0;
            sortVisibleCards();
            syncMarkerVisibility();
            renderFilterButtons();
            updateGeoDistances();
            fitVisibleMarkers({ instant: true });
        }

        function queueFilter() {
            window.clearTimeout(debounceTimer);
            debounceTimer = window.setTimeout(applyFilters, 170);
        }

        function attachCardInteractions() {
            cards.forEach(function (card) {
                var pointId = Number(card.getAttribute('data-point-id'));
                var toggle = card.querySelector('[data-point-toggle]');
                var copyButton = card.querySelector('[data-point-copy]');
                var shareButton = card.querySelector('[data-point-share]');

                card.addEventListener('mouseenter', function () { setHovered(pointId, true); });
                card.addEventListener('mouseleave', function () { setHovered(pointId, false); });
                card.addEventListener('focusin', function () { setHovered(pointId, true); });
                card.addEventListener('focusout', function () { setHovered(pointId, false); });
                card.addEventListener('click', function (event) {
                    if (event.target.closest('button, a, input, select, textarea, summary')) return;
                    focusPoint(pointId, { skipScroll: true });
                });
                card.addEventListener('keydown', function (event) {
                    if (event.target !== card) return;
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        focusPoint(pointId, { skipScroll: true });
                    }
                });

                if (toggle) {
                    toggle.addEventListener('click', function () {
                        var targetId = toggle.getAttribute('data-point-toggle');
                        var panel = targetId ? document.getElementById(targetId) : null;
                        if (!panel) return;
                        var isOpen = !panel.hidden;
                        panel.hidden = isOpen;
                        toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                    });
                }

                if (copyButton) {
                    copyButton.addEventListener('click', function () {
                        var address = card.getAttribute('data-point-address') || '';
                        if (!address) {
                            showToast('No se pudo copiar la dirección.', 'error');
                            return;
                        }
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(address).then(function () {
                                showToast('Dirección copiada.', 'success');
                            }).catch(function () {
                                showToast('No se pudo copiar la dirección.', 'error');
                            });
                            return;
                        }
                        var helper = document.createElement('textarea');
                        helper.value = address;
                        helper.setAttribute('readonly', 'readonly');
                        helper.style.position = 'absolute';
                        helper.style.left = '-9999px';
                        document.body.appendChild(helper);
                        helper.select();
                        try {
                            document.execCommand('copy');
                            showToast('Dirección copiada.', 'success');
                        } catch (error) {
                            showToast('No se pudo copiar la dirección.', 'error');
                        }
                        document.body.removeChild(helper);
                    });
                }

                if (shareButton) {
                    shareButton.addEventListener('click', function () {
                        var name = card.getAttribute('data-point-name') || 'Punto de acopio';
                        var address = card.getAttribute('data-point-address') || '';
                        var mapsUrl = card.getAttribute('data-point-maps') || '';
                        var shareText = 'Punto de acopio Abrigando Corazones: ' + name + ' ' + address + (mapsUrl ? ' ' + mapsUrl : '');
                        if (navigator.share) {
                            navigator.share({
                                title: name,
                                text: shareText,
                                url: mapsUrl || window.location.href
                            }).catch(function () {
                                return;
                            });
                            return;
                        }
                        window.open('https://wa.me/?text=' + encodeURIComponent(shareText), '_blank', 'noopener');
                    });
                }
            });
        }

        function handleGeolocationSuccess(position) {
            userCoords = { lat: position.coords.latitude, lng: position.coords.longitude };
            setGeoButtonsState(false);
            if (map && mapReady) {
                if (userMarker) map.removeLayer(userMarker);
                if (userCircle) map.removeLayer(userCircle);
                userMarker = L.marker([userCoords.lat, userCoords.lng], { icon: createMarkerIcon('open', { user: true }) }).addTo(map);
                userMarker.bindPopup('<div class="pickup-popup__user">Tu ubicación actual</div>');
                userCircle = L.circle([userCoords.lat, userCoords.lng], {
                    radius: 250,
                    color: '#2563eb',
                    fillColor: '#2563eb',
                    fillOpacity: 0.12,
                    weight: 1.4
                }).addTo(map);
            }
            activeFilter = 'near-me';
            applyFilters();
            fitVisibleMarkers();
            showToast('Mostrando los puntos de acopio más cercanos a tu ubicación.', 'success');
        }

        function handleGeolocationError() {
            userCoords = null;
            setGeoButtonsState(false);
            if (nearFilterButton) nearFilterButton.disabled = true;
            if (activeFilter === 'near-me') activeFilter = 'all';
            updateGeoDistances();
            applyFilters();
            showToast('No pudimos acceder a tu ubicación. Puedes explorar todos los puntos manualmente.', 'error');
        }

        filters.forEach(function (button) {
            button.addEventListener('click', function () {
                var nextFilter = button.getAttribute('data-point-filter') || 'all';
                if (nextFilter === 'near-me' && userCoords == null) return;
                activeFilter = nextFilter;
                applyFilters();
            });
        });

        if (input) input.addEventListener('input', queueFilter);
        if (viewportInput) {
            viewportInput.addEventListener('input', function () {
                if (input) input.value = viewportInput.value;
                queueFilter();
            });
        }

        if (clear) {
            clear.addEventListener('click', function () {
                if (input) input.value = '';
                if (viewportInput) viewportInput.value = '';
                activeFilter = 'all';
                applyFilters();
            });
        }

        geoButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                if (!navigator.geolocation) {
                    showToast('Tu navegador no soporta geolocalización. Puedes explorar los puntos manualmente.', 'error');
                    return;
                }
                setGeoButtonsState(true);
                navigator.geolocation.getCurrentPosition(handleGeolocationSuccess, handleGeolocationError, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 300000
                });
            });
        });

        fitVisibleButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                fitVisibleMarkers();
            });
        });

        if (showAllButton) {
            showAllButton.addEventListener('click', function () {
                activeFilter = 'all';
                if (input) input.value = '';
                if (viewportInput) viewportInput.value = '';
                applyFilters();
            });
        }

        if (helpToggle && helpPanel) {
            helpToggle.addEventListener('click', function () {
                var expanded = helpToggle.getAttribute('aria-expanded') === 'true';
                helpToggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                helpPanel.hidden = expanded;
            });
        }

        attachCardInteractions();
        initializeMap();
        updateGeoDistances();
        updateNearbyVisibility(false);
        applyFilters();

        window.addEventListener('pageshow', function () {
            setGeoButtonsState(false);
        });
    }

    function initNewsDirectory() {
        var directory = document.querySelector('[data-news-directory]');
        if (!directory) return;

        var input = directory.querySelector('[data-news-search]');
        var clearButtons = Array.prototype.slice.call(directory.querySelectorAll('[data-news-clear]'));
        var count = directory.querySelector('[data-news-count]');
        var empty = directory.querySelector('[data-news-empty]');
        var filterBar = directory.querySelector('[data-news-filters]');
        var cards = Array.prototype.slice.call(directory.querySelectorAll('[data-news-card]'));
        var activeFilter = 'all';
        var debounceTimer = null;

        function updateButtons() {
            if (!filterBar) return;
            filterBar.querySelectorAll('[data-news-filter]').forEach(function (button) {
                var active = button.getAttribute('data-news-filter') === activeFilter;
                button.classList.toggle('is-active', active);
                button.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
        }

        function applyFilter() {
            var query = normalizeText(input && input.value ? input.value : '');
            var visible = 0;

            cards.forEach(function (card) {
                var tags = (card.getAttribute('data-news-tags') || '').split(/\s+/).filter(Boolean);
                var haystack = normalizeText(card.getAttribute('data-news-searchable') || '');
                var categoryMatch = activeFilter === 'all' || tags.indexOf(activeFilter) !== -1;
                var searchMatch = !query || haystack.indexOf(query) !== -1;
                var matches = categoryMatch && searchMatch;
                card.hidden = !matches;
                if (matches) visible += 1;
            });

            if (count) count.textContent = String(visible);
            if (empty) empty.hidden = visible !== 0;
        }

        function queueFilter() {
            window.clearTimeout(debounceTimer);
            debounceTimer = window.setTimeout(applyFilter, 250);
        }

        if (input) input.addEventListener('input', queueFilter);

        if (clearButtons.length) {
            clearButtons.forEach(function (clear) {
                clear.addEventListener('click', function () {
                    activeFilter = 'all';
                    if (input) input.value = '';
                    updateButtons();
                    window.clearTimeout(debounceTimer);
                    applyFilter();
                });
            });
        }

        if (filterBar) {
            filterBar.querySelectorAll('[data-news-filter]').forEach(function (button) {
                button.addEventListener('click', function () {
                    activeFilter = button.getAttribute('data-news-filter') || 'all';
                    updateButtons();
                    applyFilter();
                });
            });
        }

        updateButtons();
        applyFilter();
    }

    function initPointDetailPage() {
        var detailMap = document.querySelector('[data-point-detail-map]');
        var detailPayloadNode = document.querySelector('[data-point-detail-map-payload]');
        var copyButton = document.querySelector('[data-detail-copy-address]');
        var shareButton = document.querySelector('[data-detail-share]');

        if (copyButton) {
            copyButton.addEventListener('click', function () {
                var address = copyButton.getAttribute('data-address') || '';
                if (!address) return;
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(address).then(function () {
                        showLocalToast('Direccion copiada.', 'success');
                    }).catch(function () {
                        showLocalToast('No se pudo copiar la direccion.', 'error');
                    });
                    return;
                }
                var helper = document.createElement('textarea');
                helper.value = address;
                helper.setAttribute('readonly', 'readonly');
                helper.style.position = 'absolute';
                helper.style.left = '-9999px';
                document.body.appendChild(helper);
                helper.select();
                try {
                    document.execCommand('copy');
                    showLocalToast('Direccion copiada.', 'success');
                } catch (error) {
                    showLocalToast('No se pudo copiar la direccion.', 'error');
                }
                document.body.removeChild(helper);
            });
        }

        if (shareButton) {
            shareButton.addEventListener('click', function () {
                var title = shareButton.getAttribute('data-share-title') || document.title;
                var text = shareButton.getAttribute('data-share-text') || title;
                if (navigator.share) {
                    navigator.share({ title: title, text: text, url: window.location.href }).catch(function () {});
                    return;
                }
                window.open('https://wa.me/?text=' + encodeURIComponent(text + ' ' + window.location.href), '_blank', 'noopener');
            });
        }

        if (!detailMap || !detailPayloadNode || typeof L === 'undefined') return;

        var payload = null;
        try {
            payload = JSON.parse(detailPayloadNode.textContent || 'null');
        } catch (error) {
            payload = null;
        }
        if (!payload || !payload.coordinates_verified || !isFinite(Number(payload.latitude)) || !isFinite(Number(payload.longitude))) {
            detailMap.classList.add('is-map-pending');
            return;
        }

        var detailLeaflet = L.map(detailMap, { scrollWheelZoom: false, zoomControl: true }).setView([Number(payload.latitude), Number(payload.longitude)], 16);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            subdomains: 'abcd',
            maxZoom: 20,
            attribution: '&copy; OpenStreetMap contributors &copy; CARTO'
        }).addTo(detailLeaflet);
        L.marker([Number(payload.latitude), Number(payload.longitude)]).addTo(detailLeaflet).bindPopup('<strong>' + payload.name + '</strong><br>' + payload.address);
        window.setTimeout(function () {
            detailLeaflet.invalidateSize();
        }, 180);
    }

    function showLocalToast(message, type) {
        var toast = document.createElement('div');
        toast.className = 'toast toast--' + (type === 'error' ? 'error' : 'success');
        toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
        toast.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
        toast.setAttribute('data-toast', '');
        toast.innerHTML = '<span class="toast__icon">' + (type === 'error' ? '!' : 'OK') + '</span><span>' + message + '</span>';
        document.body.appendChild(toast);
        window.setTimeout(function () {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity .3s ease';
            window.setTimeout(function () {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, 320);
        }, 2600);
    }

    function initNewsArticleDetail() {
        var progress = document.querySelector('[data-reading-progress]');
        var progressBar = document.querySelector('[data-reading-progress-bar]');
        var shareBox = document.querySelector('[data-article-share]');
        if (!progress && !shareBox) return;

        if (progress && progressBar) {
            function updateReadingProgress() {
                var documentHeight = document.documentElement.scrollHeight - window.innerHeight;
                var percent = documentHeight <= 0 ? 0 : Math.min((window.scrollY / documentHeight) * 100, 100);
                progressBar.style.width = percent + '%';
            }

            updateReadingProgress();
            window.addEventListener('scroll', updateReadingProgress, { passive: true });
            window.addEventListener('resize', updateReadingProgress);
        }

        if (!shareBox) return;

        var copyButton = shareBox.querySelector('[data-copy-article-link]');
        var articleUrl = shareBox.getAttribute('data-article-url') || window.location.href;
        var originalLabel = copyButton ? copyButton.innerHTML : '';

        function copyFallback() {
            var helper = document.createElement('textarea');
            helper.value = articleUrl;
            helper.setAttribute('readonly', 'readonly');
            helper.style.position = 'absolute';
            helper.style.left = '-9999px';
            document.body.appendChild(helper);
            helper.select();
            document.execCommand('copy');
            document.body.removeChild(helper);
        }

        if (copyButton) {
            copyButton.addEventListener('click', function () {
                var done = function () {
                    copyButton.innerHTML = 'ENLACE COPIADO';
                    window.setTimeout(function () {
                        copyButton.innerHTML = originalLabel;
                    }, 1400);
                };

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(articleUrl).then(done).catch(function () {
                        copyFallback();
                        done();
                    });
                    return;
                }

                copyFallback();
                done();
            });
        }
    }

    function initContactReasonSelector() {
        var container = document.querySelector('[data-contact-reasons]');
        var select = document.querySelector('[data-contact-reason-select]');
        if (!container || !select) return;

        var buttons = Array.prototype.slice.call(container.querySelectorAll('[data-contact-reason]'));

        function setActive(value) {
            buttons.forEach(function (button) {
                var active = button.getAttribute('data-contact-reason') === value;
                button.classList.toggle('is-active', active);
                button.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
        }

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                var value = button.getAttribute('data-contact-reason') || '';
                select.value = value;
                setActive(value);
                select.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });

        select.addEventListener('change', function () {
            setActive(select.value);
        });

        var initialReason = select.getAttribute('data-contact-initial-reason') || '';
        if (initialReason) {
            select.value = initialReason;
        } else if (!select.value) {
            select.value = 'Consulta';
        }

        setActive(select.value);
    }

    function initShareButtons() {
        var whatsappButtons = Array.prototype.slice.call(document.querySelectorAll('[data-share-whatsapp]'));
        if (!whatsappButtons.length) return;

        whatsappButtons.forEach(function (button) {
            var baseHref = button.getAttribute('href') || '';
            if (!baseHref) return;

            var separator = baseHref.indexOf('?') === -1 ? '?' : '&';
            var message = 'Conoce Abrigando Corazones y descubre cómo ayudar: ' + window.location.href;
            button.href = baseHref + separator + 'text=' + encodeURIComponent(message);
        });
    }

    function initToasts() {
        document.querySelectorAll('[data-toast]').forEach(function (toast) {
            setTimeout(function () {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity .3s ease';
                setTimeout(function () {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 320);
            }, 3200);
        });
    }

    function initLoadingForms() {
        document.querySelectorAll('[data-loading-form]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (event.defaultPrevented) return;
                if (form.dataset.submitting === 'true') {
                    event.preventDefault();
                    return;
                }

                var submit = form.querySelector('button[type="submit"]');
                if (!submit || submit.disabled) return;

                var submitLabel = submit.getAttribute('data-submit-label') || submit.textContent.trim();
                form.dataset.submitting = 'true';
                submit.disabled = true;
                submit.classList.add('is-loading');
                submit.setAttribute('aria-busy', 'true');
                submit.innerHTML = '<span class="btn__spinner" aria-hidden="true"></span><span>ENVIANDO...</span>';
                submit.setAttribute('data-original-label', submitLabel);
            });
        });
    }

    function initFormEnhancements() {
        var params = new URLSearchParams(window.location.search);
        var successState = params.get('success');

        document.querySelectorAll('[data-loading-form]').forEach(function (form) {
            var submit = form.querySelector('button[type="submit"]');
            var feedback = document.createElement('div');
            feedback.className = 'form-feedback';
            feedback.setAttribute('aria-live', 'polite');
            feedback.hidden = true;
            form.appendChild(feedback);

            if (successState === 'true') {
                feedback.hidden = false;
                feedback.classList.add('form-feedback--success');
                feedback.textContent = '¡GRACIAS POR SUMARTE! Hemos recibido correctamente tu información. Pronto podremos comunicarnos contigo.';
            } else if (successState === 'error') {
                feedback.hidden = false;
                feedback.classList.add('form-feedback--error');
                feedback.textContent = 'No pudimos registrar tus datos en este momento. Por favor, inténtalo nuevamente.';
            }

            form.addEventListener('submit', function (event) {
                var firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    event.preventDefault();
                    if (submit) {
                        submit.disabled = false;
                        submit.classList.remove('is-loading');
                        submit.removeAttribute('aria-busy');
                        if (submit.hasAttribute('data-original-label')) {
                            submit.textContent = submit.getAttribute('data-original-label');
                        }
                    }
                    form.dataset.submitting = 'false';
                    feedback.hidden = false;
                    feedback.className = 'form-feedback form-feedback--error';
                    feedback.textContent = 'Completa los campos requeridos antes de enviar.';
                    firstInvalid.focus({ preventScroll: false });
                    firstInvalid.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'center' });
                }
            }, true);
        });
    }

    function initPremiumMicroInteractions() {
        document.addEventListener('pointerdown', function (event) {
            var button = event.target.closest('.btn, button, .pickup-card__microbutton, .organization-card__toggle');
            if (!button || button.disabled) return;
            button.classList.add('is-pressing');
        }, { passive: true });

        document.addEventListener('pointerup', function (event) {
            var button = event.target.closest('.btn, button, .pickup-card__microbutton, .organization-card__toggle');
            if (!button) return;
            window.setTimeout(function () {
                button.classList.remove('is-pressing');
            }, 140);
        }, { passive: true });

        document.addEventListener('pointercancel', function () {
            document.querySelectorAll('.is-pressing').forEach(function (button) {
                button.classList.remove('is-pressing');
            });
        }, { passive: true });

        function focusHashTarget() {
            if (!window.location.hash || window.location.hash.length < 2) return;
            var target = document.getElementById(decodeURIComponent(window.location.hash.slice(1)));
            if (!target) return;
            target.classList.remove('js-hash-focus');
            void target.offsetWidth;
            target.classList.add('js-hash-focus');
            if (!target.hasAttribute('tabindex')) {
                target.setAttribute('tabindex', '-1');
                target.setAttribute('data-temp-focus', 'true');
            }
            target.focus({ preventScroll: true });
        }

        window.addEventListener('hashchange', focusHashTarget);
        window.setTimeout(focusHashTarget, 120);

        document.querySelectorAll('img').forEach(function (image) {
            if (!image.hasAttribute('decoding')) {
                image.setAttribute('decoding', 'async');
            }
        });
    }

    function initTextareaCounters() {
        document.querySelectorAll('textarea[required]').forEach(function (textarea) {
            if (textarea.dataset.counterReady === 'true') return;
            textarea.dataset.counterReady = 'true';
            var counter = document.createElement('div');
            counter.className = 'field-counter';
            counter.setAttribute('aria-live', 'polite');
            textarea.insertAdjacentElement('afterend', counter);

            function updateCounter() {
                var length = textarea.value.trim().length;
                counter.textContent = length < 20
                    ? 'Agrega un poco más de contexto para orientarte mejor.'
                    : length + ' caracteres. Mensaje claro.';
                counter.classList.toggle('is-good', length >= 20);
            }

            textarea.addEventListener('input', updateCounter);
            updateCounter();
        });
    }

    /**
     * Aparicion suave de las fotografias.
     *
     * Sin esto las imagenes "saltan" al terminar de descargarse, sobre todo en
     * conexiones lentas, que es justo el caso de buena parte del publico de la
     * campana. La opacidad inicial la pone el CSS SOLO bajo .has-motion, que
     * es una clase que agrega este mismo script: si el JS no corre, las
     * imagenes se ven normales desde el primer momento.
     */
    /**
     * Registro de clics para el reporte diario.
     *
     * Solo se envia una etiqueta legible del elemento y su destino: nada de
     * datos personales, ni de posicion del raton, ni de contenido escrito por
     * el usuario. Se usa sendBeacon para que el envio no retrase la navegacion
     * cuando el clic abre otra pagina.
     */
    /**
     * Comportamientos propios de la portada.
     *
     * Todo lo de aqui es decorativo: si algo falla, la pagina sigue completa y
     * legible. Por eso ninguna pieza esconde contenido, solo lo acompana.
     */
    function initPortada() {
        var frio = document.querySelector('.pi-frio');

        /* 11 y 12 · El numero baja de verdad y el termometro se vacia con el.
           Se dispara cuando la seccion entra en pantalla, no al cargar. */
        if (frio && !prefersReducedMotion && 'IntersectionObserver' in window) {
            var obsFrio = new IntersectionObserver(function (entradas) {
                entradas.forEach(function (e) {
                    if (!e.isIntersecting) return;
                    frio.classList.add('is-midiendo');
                    obsFrio.unobserve(e.target);
                });
            }, { threshold: 0.25 });
            obsFrio.observe(frio);
        } else if (frio) {
            frio.classList.add('is-midiendo');
        }

        /* 14 · El fondo se enfria segun el avance del scroll dentro de la
           seccion. Se escribe en una variable CSS y el color lo calcula el CSS
           con color-mix, para no tocar estilos desde JS. */
        if (frio && !prefersReducedMotion) {
            var pendiente = false;

            var pintar = function () {
                var caja = frio.getBoundingClientRect();
                var total = caja.height + window.innerHeight;
                var recorrido = window.innerHeight - caja.top;
                var avance = Math.min(Math.max(recorrido / total, 0), 1);
                frio.style.setProperty('--pi-frio-avance', avance.toFixed(3));
                pendiente = false;
            };

            var alScroll = function () {
                if (pendiente) return;
                pendiente = true;
                window.requestAnimationFrame(pintar);
            };

            window.addEventListener('scroll', alScroll, { passive: true });
            window.addEventListener('resize', alScroll, { passive: true });
            pintar();
        }
    }

    /**
     * 30 - Distancia desde el visitante hasta cada punto de acopio.
     *
     * Es el dato que de verdad busca quien mira esa lista: no "dónde está",
     * sino "cuál me queda cerca". Solo se calcula si la persona concede el
     * permiso de ubicación, no se pide al entrar y la coordenada NUNCA sale
     * del navegador: el cálculo es local y no se envía a ningún servidor.
     */
    function initDistancias() {
        var tarjetas = Array.prototype.slice.call(
            document.querySelectorAll('.pi-punto__dir[data-punto-lat]')
        );

        if (!tarjetas.length || !navigator.geolocation) return;

        var seccion = document.querySelector('.pi-puntos');
        if (!seccion) return;

        // Boton discreto: pedir la ubicación de golpe al cargar es intrusivo.
        var boton = document.createElement('button');
        boton.type = 'button';
        boton.className = 'pi-puntos__ubicar';
        boton.innerHTML = '<span>Ver cuál me queda más cerca</span>';

        var cabeza = seccion.querySelector('.section-head');
        if (!cabeza) return;
        cabeza.appendChild(boton);

        function distanciaKm(lat1, lon1, lat2, lon2) {
            var R = 6371;
            var dLat = (lat2 - lat1) * Math.PI / 180;
            var dLon = (lon2 - lon1) * Math.PI / 180;
            var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        boton.addEventListener('click', function () {
            boton.disabled = true;
            boton.innerHTML = '<span>Calculando...</span>';

            navigator.geolocation.getCurrentPosition(function (pos) {
                var lat = pos.coords.latitude;
                var lon = pos.coords.longitude;

                tarjetas.forEach(function (nodo) {
                    var d = distanciaKm(
                        lat, lon,
                        parseFloat(nodo.getAttribute('data-punto-lat')),
                        parseFloat(nodo.getAttribute('data-punto-lon'))
                    );

                    var etiqueta = nodo.querySelector('.pi-punto__dist');
                    if (!etiqueta) return;

                    etiqueta.textContent = d < 1
                        ? 'a ' + Math.round(d * 1000) + ' m de ti'
                        : 'a ' + d.toFixed(1) + ' km de ti';
                    etiqueta.hidden = false;
                });

                boton.remove();
            }, function () {
                boton.disabled = false;
                boton.innerHTML = '<span>No se pudo obtener tu ubicación</span>';
            }, { timeout: 8000, maximumAge: 300000 });
        });
    }

    /**
     * 47 - Barra de progreso de lectura en el borde superior.
     */
    function initBarraLectura() {
        var barra = document.querySelector('[data-scroll-progress]');
        if (!barra) return;

        var pendiente = false;

        function pintar() {
            var doc = document.documentElement;
            var recorrible = doc.scrollHeight - window.innerHeight;
            var razon = recorrible > 0 ? (window.scrollY || doc.scrollTop || 0) / recorrible : 0;
            barra.style.transform = 'scaleX(' + Math.min(Math.max(razon, 0), 1).toFixed(4) + ')';
            pendiente = false;
        }

        function alScroll() {
            if (pendiente) return;
            pendiente = true;
            window.requestAnimationFrame(pintar);
        }

        window.addEventListener('scroll', alScroll, { passive: true });
        window.addEventListener('resize', alScroll, { passive: true });
        pintar();
    }

    /**
     * 42, 43, 44 y 45 - Formulario de la portada.
     *
     * Todo lo de aqui es mejora encima de un formulario que YA funciona sin
     * JavaScript: si algo falla, el envio normal sigue su curso y el servidor
     * responde con el aviso de siempre.
     */
    function initFormPortada() {
        var form = document.querySelector('.pi-form');
        if (!form) return;

        var campos = Array.prototype.slice.call(form.querySelectorAll('.pi-form__campo'));
        var progreso = form.querySelector('[data-form-progreso]');
        var exito = form.querySelector('[data-form-ok]');

        function esValido(control) {
            if (!control) return false;
            if (control.value.trim() === '') return false;
            return typeof control.checkValidity === 'function' ? control.checkValidity() : true;
        }

        // 42 y 43 - Marca por campo y contador de avance.
        function revisar() {
            var completos = 0;

            campos.forEach(function (campo) {
                var control = campo.querySelector('input, select');
                var ok = esValido(control);
                campo.classList.toggle('is-ok', ok);
                if (ok) completos++;
            });

            if (progreso) {
                var total = campos.length || 1;
                var num = progreso.querySelector('b');
                var barra = progreso.querySelector('i');
                if (num) num.textContent = String(completos);
                if (barra) barra.style.width = Math.round(completos / total * 100) + '%';
            }
        }

        form.addEventListener('input', revisar);
        form.addEventListener('change', revisar);
        form.addEventListener('blur', revisar, true);
        revisar();

        // 45 - Confirmacion sin recargar. Si fetch no existe o falla, se deja
        // que el navegador envie el formulario como toda la vida.
        if (!exito || typeof window.fetch !== 'function' || typeof FormData !== 'function') return;

        form.addEventListener('submit', function (evento) {
            if (!form.checkValidity()) return;

            evento.preventDefault();

            var boton = form.querySelector('button[type="submit"]');
            if (boton) boton.disabled = true;
            form.setAttribute('data-submitting', 'true');

            fetch(form.getAttribute('action'), {
                method: 'POST',
                body: new FormData(form),
                credentials: 'same-origin',
                redirect: 'follow'
            }).then(function (respuesta) {
                // El controlador redirige con ?success=true o ?success=error.
                var ok = respuesta.url && respuesta.url.indexOf('success=true') !== -1;

                if (!ok) {
                    throw new Error('respuesta_no_exitosa');
                }

                exito.hidden = false;
                form.removeAttribute('data-submitting');
            }).catch(function () {
                // Sin trucos: se reenvia por la via normal.
                form.removeAttribute('data-submitting');
                if (boton) boton.disabled = false;
                form.submit();
            });
        });
    }

    /**
     * Red de seguridad de las animaciones de entrada.
     *
     * Los titulares de hero entran con una animacion que arranca en opacity 0.
     * Si por lo que sea la animacion no llega a ejecutarse -navegador que las
     * bloquea, pestana en segundo plano que congela el compositor, extension
     * que interfiere- el texto se quedaria invisible para siempre.
     *
     * A los 3 segundos se marca el documento como "ya entrado" y el CSS fuerza
     * el estado final de todo lo que anima al aparecer. Es el mismo criterio
     * que la red de 6 segundos del sistema de revelados: el contenido nunca
     * puede depender de que una animacion termine.
     */
    function initRedAnimaciones() {
        window.setTimeout(function () {
            document.body.classList.add('animaciones-listas');
        }, 3000);
    }

    function initMetrics() {
        var BASE = window.BASE_URL || '/';
        var endpoint = BASE + 'metricas/evento';

        function enviar(carga) {
            try {
                var cuerpo = JSON.stringify(carga);

                if (navigator.sendBeacon) {
                    navigator.sendBeacon(endpoint, new Blob([cuerpo], { type: 'application/json' }));
                    return;
                }

                fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: cuerpo,
                    keepalive: true
                }).catch(function () {});
            } catch (e) {
                // medir nunca debe entorpecer la navegacion
            }
        }

        function etiquetaDe(el) {
            var texto = (el.getAttribute('aria-label') || el.textContent || '').replace(/\s+/g, ' ').trim();

            if (!texto) {
                var img = el.querySelector('img[alt]');
                if (img) texto = img.getAttribute('alt') || '';
            }

            return texto.slice(0, 90) || 'sin etiqueta';
        }

        document.addEventListener('click', function (event) {
            var el = event.target.closest('a, button');
            if (!el) return;

            // No se miden los controles internos de interfaz: abrir el menu o
            // desplegar una pregunta no dice nada sobre el interes real.
            if (el.hasAttribute('data-menu-toggle') || el.closest('[data-no-metrics]')) return;

            var destino = el.getAttribute('href') || null;

            enviar({
                ruta: window.location.pathname,
                tipo: 'click',
                etiqueta: etiquetaDe(el),
                destino: destino
            });
        }, { passive: true });

        document.addEventListener('submit', function (event) {
            var form = event.target;
            if (!form || form.tagName !== 'FORM') return;

            enviar({
                ruta: window.location.pathname,
                tipo: 'envio_formulario',
                etiqueta: form.getAttribute('data-metrics-label') || form.getAttribute('id') || 'formulario',
                destino: form.getAttribute('action') || null
            });
        }, { passive: true });
    }

    function initImageFadeIn() {
        if (prefersReducedMotion) return;

        var images = Array.prototype.slice.call(document.images);
        if (!images.length) return;

        function reveal(img) {
            img.classList.add('is-loaded');
        }

        images.forEach(function (img) {
            img.classList.add('img-fade');

            if (img.complete && img.naturalWidth > 0) {
                reveal(img);
                return;
            }

            img.addEventListener('load', function () { reveal(img); }, { once: true });
            img.addEventListener('error', function () { reveal(img); }, { once: true });
        });

        // Red de seguridad: si algun evento no llega, a los 5 segundos se
        // muestran todas igual. Nunca una foto invisible por un fallo de carga.
        window.setTimeout(function () {
            images.forEach(reveal);
        }, 5000);
    }

    function initImageFallbacks() {
        document.querySelectorAll('img').forEach(function (image) {
            image.addEventListener('error', function () {
                if (image.dataset.fallbackApplied === 'true') return;
                image.dataset.fallbackApplied = 'true';
                image.classList.add('image-fallback-ready');
                image.alt = image.alt || 'Imagen institucional de Abrigando Corazones';
                image.src = (window.BASE_URL || '/') + 'images/logo_heart.webp';
            }, { once: true });
        });
    }
});
