/**
 * Santa Towing Main Interactions
 * Vanilla JS replacing React state for NavBar scroll, Mobile Drawer, Accordions,
 * Hero Slider, Category Tabs, Gallery Switcher, Newsletter AJAX, and Copy URL.
 */
(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    ready(function () {
        var cfg = window.santaTowingConfig || {};

        /* -------------------------------------------------------------
         * 1. NavBar Scroll Transition (Home top-bar offset -> scrolled white)
         * ----------------------------------------------------------- */
        var header = document.getElementById('st-main-header');
        if (header) {
            var isHome = header.getAttribute('data-is-home') === '1';

            var applyHeaderState = function () {
                if (!isHome) return;
                var scrolled = window.scrollY > 40;
                if (scrolled) {
                    header.classList.remove('bg-secondary/90', 'text-white', 'py-4', 'lg:translate-y-9', 'border-white/10');
                    header.classList.add('bg-white/95', 'shadow-sm', 'border-gray-100', 'py-3.5', 'text-dark', 'translate-y-0');
                } else {
                    header.classList.add('bg-secondary/90', 'text-white', 'py-4', 'lg:translate-y-9', 'border-white/10');
                    header.classList.remove('bg-white/95', 'shadow-sm', 'border-gray-100', 'py-3.5', 'text-dark');
                }
            };

            applyHeaderState();
            window.addEventListener('scroll', applyHeaderState, { passive: true });
        }

        /* -------------------------------------------------------------
         * 2. Mobile Drawer Open / Close + Body Lock
         * ----------------------------------------------------------- */
        var drawer = document.getElementById('st-mobile-drawer');
        var drawerToggle = document.getElementById('st-mobile-menu-toggle');
        var drawerIconMenu = drawerToggle ? drawerToggle.querySelector('.st-icon-open') : null;
        var drawerIconClose = drawerToggle ? drawerToggle.querySelector('.st-icon-close') : null;

        function setDrawerState(open) {
            if (!drawer || !drawerToggle) return;
            drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
            drawerToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (open) {
                drawer.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                if (drawerIconMenu) drawerIconMenu.classList.add('hidden');
                if (drawerIconClose) drawerIconClose.classList.remove('hidden');
            } else {
                drawer.classList.add('hidden');
                document.body.style.overflow = '';
                if (drawerIconMenu) drawerIconMenu.classList.remove('hidden');
                if (drawerIconClose) drawerIconClose.classList.add('hidden');
            }
        }

        if (drawerToggle && drawer) {
            drawer.setAttribute('aria-hidden', drawer.classList.contains('hidden') ? 'true' : 'false');

            drawerToggle.addEventListener('click', function () {
                var isOpen = !drawer.classList.contains('hidden');
                setDrawerState(!isOpen);
            });
            drawer.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    setDrawerState(false);
                });
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !drawer.classList.contains('hidden')) {
                    setDrawerState(false);
                }
            });
        }

        /* -------------------------------------------------------------
         * 2b. Desktop / Tablet Dropdown Menu Click & Keyboard Accessibility
         * ----------------------------------------------------------- */
        var dropdowns = document.querySelectorAll('.st-dropdown');
        function closeAllDropdowns(except) {
            dropdowns.forEach(function (dd) {
                if (dd === except) return;
                var btn = dd.querySelector('button');
                var menu = dd.querySelector('.st-dropdown-menu');
                if (btn) btn.setAttribute('aria-expanded', 'false');
                if (menu) {
                    menu.classList.remove('opacity-100', 'visible');
                    menu.classList.add('opacity-0', 'invisible');
                }
            });
        }
        dropdowns.forEach(function (dd) {
            var btn = dd.querySelector('button');
            var menu = dd.querySelector('.st-dropdown-menu');
            if (!btn || !menu) return;
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                var open = btn.getAttribute('aria-expanded') === 'true';
                closeAllDropdowns(dd);
                btn.setAttribute('aria-expanded', open ? 'false' : 'true');
                menu.classList.toggle('opacity-100', !open);
                menu.classList.toggle('visible', !open);
                menu.classList.toggle('opacity-0', open);
                menu.classList.toggle('invisible', open);
            });
        });
        document.addEventListener('click', function (e) {
            if (!e.target.closest || !e.target.closest('.st-dropdown')) {
                closeAllDropdowns(null);
            }
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeAllDropdowns(null);
            }
        });

        /* -------------------------------------------------------------
         * 3. Accessible Accordion Toggles (Mobile Drawer & FAQs)
         * ----------------------------------------------------------- */
        document.querySelectorAll('[data-st-accordion-toggle]').forEach(function (btn) {
            var targetId = btn.getAttribute('data-st-accordion-toggle');
            var panel = targetId ? document.getElementById(targetId) : btn.nextElementSibling;
            var icon = btn.querySelector('.st-accordion-icon, .st-chevron, svg');
            if (icon && btn.getAttribute('aria-expanded') === 'true') {
                icon.classList.add('rotate-180');
            }

            btn.addEventListener('click', function () {
                if (!panel) return;
                var expanded = btn.getAttribute('aria-expanded') === 'true';

                btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                panel.classList.toggle('is-open', !expanded);
                if (icon) {
                    icon.classList.toggle('rotate-180', !expanded);
                }
            });
        });

        /* -------------------------------------------------------------
         * 4. Hero Slider
         * ----------------------------------------------------------- */
        var slider = document.getElementById('st-hero-slider');
        if (slider) {
            var slides = slider.querySelectorAll('[data-st-slide]');
            var dots = slider.querySelectorAll('[data-st-slide-dot]');
            var prevBtn = slider.querySelector('[data-st-slide-prev]');
            var nextBtn = slider.querySelector('[data-st-slide-next]');
            var pauseBtn = slider.querySelector('[data-st-slide-pause]');
            var current = 0;
            var paused = false;
            var timer = null;

            function showSlide(index) {
                if (!slides.length) return;
                current = (index + slides.length) % slides.length;
                slides.forEach(function (s, idx) {
                    if (idx === current) {
                        s.classList.remove('opacity-0', 'pointer-events-none', 'z-0');
                        s.classList.add('opacity-100', 'z-10');
                    } else {
                        s.classList.add('opacity-0', 'pointer-events-none', 'z-0');
                        s.classList.remove('opacity-100', 'z-10');
                    }
                });
                dots.forEach(function (d, idx) {
                    if (idx === current) {
                        d.classList.add('w-8', 'bg-accent', 'shadow-sm');
                        d.classList.remove('w-2.5', 'bg-white/40');
                    } else {
                        d.classList.remove('w-8', 'bg-accent', 'shadow-sm');
                        d.classList.add('w-2.5', 'bg-white/40');
                    }
                });
                scheduleNext();
            }

            function scheduleNext() {
                if (timer) clearTimeout(timer);
                if (paused) return;
                timer = setTimeout(function () {
                    showSlide(current + 1);
                }, current === 0 ? 8000 : 5000);
            }

            if (prevBtn) prevBtn.addEventListener('click', function () { showSlide(current - 1); });
            if (nextBtn) nextBtn.addEventListener('click', function () { showSlide(current + 1); });
            dots.forEach(function (d) {
                d.addEventListener('click', function () {
                    showSlide(parseInt(d.getAttribute('data-st-slide-dot'), 10) || 0);
                });
            });
            if (pauseBtn) {
                pauseBtn.addEventListener('click', function () {
                    paused = !paused;
                    pauseBtn.setAttribute('aria-pressed', paused ? 'true' : 'false');
                    var pIcon = pauseBtn.querySelector('[data-st-icon-pause]');
                    var playIcon = pauseBtn.querySelector('[data-st-icon-play]');
                    if (pIcon) pIcon.classList.toggle('hidden', paused);
                    if (playIcon) playIcon.classList.toggle('hidden', !paused);
                    scheduleNext();
                });
            }
            slider.setAttribute('tabindex', slider.getAttribute('tabindex') || '0');
            slider.addEventListener('keydown', function (e) {
                if (e.key === 'ArrowLeft') { e.preventDefault(); showSlide(current - 1); }
                else if (e.key === 'ArrowRight') { e.preventDefault(); showSlide(current + 1); }
            });
            showSlide(0);
        }

        /* -------------------------------------------------------------
         * 5. Scroll To Footer Arrow
         * ----------------------------------------------------------- */
        document.querySelectorAll('[data-st-scroll-footer]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
            });
        });

        /* -------------------------------------------------------------
         * 6. Category Filters (Home Featured Cars & CPT Archives)
         * ----------------------------------------------------------- */
        document.querySelectorAll('[data-st-filter-group]').forEach(function (group) {
            var buttons = group.querySelectorAll('[data-st-filter-btn]');
            var cards = group.querySelectorAll('[data-st-category]');

            function applyFilter(cat) {
                buttons.forEach(function (b) {
                    var active = b.getAttribute('data-st-filter-btn') === cat;
                    if (active) {
                        b.classList.add('bg-accent', 'text-white', 'shadow-md');
                        b.classList.remove('text-white/80', 'text-dark/70');
                    } else {
                        b.classList.remove('bg-accent', 'text-white', 'shadow-md');
                        if (!b.classList.contains('text-white/80')) {
                            b.classList.add('text-dark/70');
                        }
                    }
                });
                var visibleCount = 0;
                cards.forEach(function (card) {
                    var cCat = card.getAttribute('data-st-category') || '';
                    var show = cat === 'All' || cCat === cat || cCat.toLowerCase().indexOf(cat.toLowerCase()) !== -1;
                    card.classList.toggle('hidden', !show);
                    if (show) visibleCount++;
                });
                var emptyBox = group.querySelector('[data-st-filter-empty]');
                if (!emptyBox) {
                    emptyBox = document.createElement('div');
                    emptyBox.setAttribute('data-st-filter-empty', '1');
                    emptyBox.className = 'hidden text-center py-8 text-sm font-medium text-slate-500';
                    emptyBox.textContent = 'No items match this filter.';
                    group.appendChild(emptyBox);
                }
                emptyBox.classList.toggle('hidden', visibleCount > 0);
            }

            buttons.forEach(function (b) {
                b.addEventListener('click', function () {
                    applyFilter(b.getAttribute('data-st-filter-btn'));
                });
            });

            if (buttons.length && buttons[0].getAttribute('data-st-filter-btn') !== 'All') {
                applyFilter(buttons[0].getAttribute('data-st-filter-btn'));
            }
        });

        /* -------------------------------------------------------------
         * 7. Detail Page Interactive Tabs & Galleries
         * ----------------------------------------------------------- */
        document.querySelectorAll('[data-st-tabs]').forEach(function (tabsWrap) {
            var btns = tabsWrap.querySelectorAll('[data-st-tab-btn]');
            var panels = tabsWrap.querySelectorAll('[data-st-tab-panel]');
            btns.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var target = btn.getAttribute('data-st-tab-btn');
                    btns.forEach(function (b) {
                        var isAct = b === btn;
                        b.classList.toggle('text-accent', isAct);
                        b.classList.toggle('border-accent', isAct);
                        b.classList.toggle('bg-white', isAct);
                        b.classList.toggle('text-slate-600', !isAct);
                        b.classList.toggle('border-transparent', !isAct);
                    });
                    panels.forEach(function (p) {
                        p.classList.toggle('hidden', p.getAttribute('data-st-tab-panel') !== target);
                    });
                });
            });
        });

        document.querySelectorAll('[data-st-gallery]').forEach(function (gal) {
            var mainImg = gal.querySelector('[data-st-gallery-main]');
            var thumbs = gal.querySelectorAll('[data-st-gallery-thumb]');
            thumbs.forEach(function (th) {
                th.addEventListener('click', function () {
                    var src = th.getAttribute('data-st-gallery-thumb');
                    if (mainImg && src) mainImg.setAttribute('src', src);
                    thumbs.forEach(function (other) {
                        var active = other === th;
                        other.classList.toggle('border-accent', active);
                        other.classList.toggle('ring-2', active);
                        other.classList.toggle('opacity-75', !active);
                    });
                });
            });
        });

        /* -------------------------------------------------------------
         * 8. Footer Newsletter AJAX Subscription
         * ----------------------------------------------------------- */
        var newsletterForm = document.getElementById('st-footer-newsletter-form');
        if (newsletterForm && cfg.ajaxUrl) {
            newsletterForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var emailInput = newsletterForm.querySelector('input[type="email"]');
                var submitBtn = newsletterForm.querySelector('button[type="submit"]');
                var successBox = document.getElementById('st-footer-newsletter-success');
                if (!emailInput || !emailInput.value.trim()) return;

                if (submitBtn) submitBtn.disabled = true;

                var formData = new FormData();
                formData.append('action', 'st_newsletter_subscribe');
                formData.append('nonce', cfg.nonce || '');
                formData.append('email', emailInput.value.trim());

                fetch(cfg.ajaxUrl, { method: 'POST', body: formData })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        if (res && res.success) {
                            emailInput.value = '';
                            newsletterForm.classList.add('hidden');
                            if (successBox) {
                                successBox.classList.remove('hidden');
                                successBox.classList.add('flex');
                            }
                        } else if (emailInput) {
                            emailInput.setCustomValidity((res && res.data && res.data.message) ? res.data.message : 'Please enter a valid email address.');
                            emailInput.reportValidity();
                            emailInput.setCustomValidity('');
                        }
                    })
                    .catch(function () {
                        if (emailInput) {
                            emailInput.setCustomValidity('Unable to connect right now. Please try again.');
                            emailInput.reportValidity();
                            emailInput.setCustomValidity('');
                        }
                    })
                    .finally(function () {
                        if (submitBtn) submitBtn.disabled = false;
                    });
            });
        }

        /* -------------------------------------------------------------
         * 9. Reading Progress Bar & Copy Link & Sitemap Live Filter
         * ----------------------------------------------------------- */
        var progressBar = document.getElementById('st-reading-progress');
        if (progressBar) {
            window.addEventListener('scroll', function () {
                var docH = document.documentElement.scrollHeight - window.innerHeight;
                var pct = docH > 0 ? Math.min(100, Math.max(0, (window.scrollY / docH) * 100)) : 0;
                progressBar.style.width = pct + '%';
            }, { passive: true });
        }

        document.querySelectorAll('[data-st-copy-url]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var url = btn.getAttribute('data-st-copy-url') || window.location.href;
                function showCopied() {
                    var existingBadge = btn.querySelector('.st-copy-feedback');
                    if (existingBadge) existingBadge.remove();
                    var badge = document.createElement('span');
                    badge.className = 'st-copy-feedback absolute -top-8 left-1/2 -translate-x-1/2 bg-dark text-white text-[11px] font-bold px-2 py-0.5 rounded whitespace-nowrap pointer-events-none shadow';
                    badge.textContent = 'Copied!';
                    btn.appendChild(badge);
                    btn.classList.add('bg-primary', 'text-white');
                    setTimeout(function () {
                        if (badge.parentNode) badge.parentNode.removeChild(badge);
                        btn.classList.remove('bg-primary', 'text-white');
                    }, 1800);
                }

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(showCopied).catch(function () {
                        fallbackCopy(url, showCopied);
                    });
                } else {
                    fallbackCopy(url, showCopied);
                }
            });
        });

        function fallbackCopy(text, onDone) {
            try {
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.setAttribute('readonly', '');
                ta.style.position = 'fixed';
                ta.style.left = '-9999px';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                if (typeof onDone === 'function') onDone();
            } catch (err) {}
        }

        var sitemapFilter = document.getElementById('st-sitemap-filter');
        if (sitemapFilter) {
            var sitemapEmpty = document.getElementById('st-sitemap-empty');
            var sitemapCountEl = document.getElementById('st-sitemap-count');
            sitemapFilter.addEventListener('input', function () {
                var q = sitemapFilter.value.trim().toLowerCase();
                var totalVisible = 0;
                document.querySelectorAll('.st-sitemap-section').forEach(function (sec) {
                    var visibleInSec = 0;
                    sec.querySelectorAll('.st-sitemap-item').forEach(function (item) {
                        var text = item.getAttribute('data-st-filter-text') || '';
                        var match = !q || text.indexOf(q) !== -1;
                        item.classList.toggle('hidden', !match);
                        if (match) {
                            visibleInSec++;
                            totalVisible++;
                        }
                    });
                    sec.classList.toggle('hidden', visibleInSec === 0);
                });
                if (sitemapEmpty) {
                    sitemapEmpty.classList.toggle('hidden', totalVisible > 0);
                }
                if (sitemapCountEl) {
                    var allTotal = sitemapCountEl.getAttribute('data-total-routes') || totalVisible;
                    sitemapCountEl.textContent = q
                        ? (totalVisible + ' of ' + allTotal + ' Matching Routes')
                        : (allTotal + ' Total Verified Routes');
                }
            });
        }
    });
})();
