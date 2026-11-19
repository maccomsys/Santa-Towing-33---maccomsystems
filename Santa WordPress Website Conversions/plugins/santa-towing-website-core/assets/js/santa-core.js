/**
 * Santa Towing Website Core — Frontend Interactive Controller (Vanilla JS)
 * Handles:
 * - Hero Slider controls, keyboard navigation & autoplay
 * - Catalog / Gallery / FAQ category filter tabs with empty states
 * - FAQ Accordion toggles
 * - Gallery Lightbox (Prev/Next navigation, Arrow keys, Escape, scroll lock)
 * - Career job application box open/close/Escape
 * - Service Area 16-Region / 232-Town search & dependent dropdowns
 * - AJAX Form submission with client-side validation, CV extension/size validation,
 *   safe JSON parsing, prefilled field preservation on reset, & WhatsApp dispatch handoff
 */
(function () {
    'use strict';

    var cfg = window.stwcConfig || {};

    function initHeroSliders() {
        var sliders = document.querySelectorAll('[data-stwc-hero-slider]');
        sliders.forEach(function (slider) {
            var slides = slider.querySelectorAll('.stwc-hero-slide');
            var copies = slider.querySelectorAll('.stwc-hero-copy');
            var dots = slider.querySelectorAll('.stwc-hero-dot');
            var prevBtn = slider.querySelector('.stwc-hero-prev');
            var nextBtn = slider.querySelector('.stwc-hero-next');
            if (!slides.length) return;

            var current = 0;
            var total = slides.length;
            var autoplay = slider.getAttribute('data-autoplay') === '1';
            var intervalMs = parseInt(slider.getAttribute('data-interval') || '6000', 10);
            var timer = null;

            function goTo(index) {
                current = (index + total) % total;
                slides.forEach(function (s, i) {
                    if (i === current) {
                        s.classList.remove('opacity-0', 'pointer-events-none', 'z-0');
                        s.classList.add('opacity-100', 'z-10');
                    } else {
                        s.classList.add('opacity-0', 'pointer-events-none', 'z-0');
                        s.classList.remove('opacity-100', 'z-10');
                    }
                });
                copies.forEach(function (c, i) {
                    if (i === current) {
                        c.classList.remove('hidden');
                        c.classList.add('block');
                    } else {
                        c.classList.add('hidden');
                        c.classList.remove('block');
                    }
                });
                dots.forEach(function (d, i) {
                    if (i === current) {
                        d.classList.add('bg-accent', 'w-8');
                        d.classList.remove('bg-white/40');
                    } else {
                        d.classList.remove('bg-accent', 'w-8');
                        d.classList.add('bg-white/40');
                    }
                });
            }

            if (prevBtn) prevBtn.addEventListener('click', function () { goTo(current - 1); });
            if (nextBtn) nextBtn.addEventListener('click', function () { goTo(current + 1); });
            dots.forEach(function (dot) {
                dot.addEventListener('click', function () {
                    var idx = parseInt(dot.getAttribute('data-slide-index') || '0', 10);
                    goTo(idx);
                });
            });

            slider.setAttribute('tabindex', slider.getAttribute('tabindex') || '0');
            slider.addEventListener('keydown', function (e) {
                if (e.key === 'ArrowLeft') { e.preventDefault(); goTo(current - 1); }
                else if (e.key === 'ArrowRight') { e.preventDefault(); goTo(current + 1); }
            });

            function startAutoplay() {
                if (timer) window.clearInterval(timer);
                if (autoplay && total > 1) {
                    timer = window.setInterval(function () { goTo(current + 1); }, intervalMs);
                }
            }

            if (autoplay && total > 1) {
                startAutoplay();
                slider.addEventListener('mouseenter', function () {
                    if (timer) { window.clearInterval(timer); timer = null; }
                });
                slider.addEventListener('mouseleave', startAutoplay);
            }
        });
    }

    function initFilterScopes() {
        var scopes = document.querySelectorAll('[data-stwc-filter-scope]');
        scopes.forEach(function (scope) {
            var buttons = scope.querySelectorAll('.stwc-filter-btn');
            var items = scope.querySelectorAll('.stwc-filter-item');
            if (!buttons.length || !items.length) return;

            buttons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var val = btn.getAttribute('data-filter-value') || 'All';
                    buttons.forEach(function (b) {
                        b.classList.remove('bg-primary', 'text-white', 'is-active');
                        b.classList.add('bg-white', 'text-dark');
                    });
                    btn.classList.add('bg-primary', 'text-white', 'is-active');
                    btn.classList.remove('bg-white', 'text-dark');

                    var visibleCount = 0;
                    items.forEach(function (item) {
                        var cat = item.getAttribute('data-category') || '';
                        var show = val === 'All' || cat.toLowerCase() === val.toLowerCase();
                        item.classList.toggle('is-hidden', !show);
                        if (show) visibleCount++;
                    });

                    var emptyEl = scope.querySelector('.stwc-filter-empty');
                    if (!emptyEl && items[0] && items[0].parentNode) {
                        emptyEl = document.createElement('div');
                        emptyEl.className = 'stwc-filter-empty hidden';
                        emptyEl.textContent = 'No items match this category.';
                        items[0].parentNode.appendChild(emptyEl);
                    }
                    if (emptyEl) {
                        emptyEl.classList.toggle('hidden', visibleCount > 0);
                    }
                });
            });
        });
    }

    function initFaqAccordions() {
        document.addEventListener('click', function (e) {
            var trigger = e.target.closest('.stwc-faq-trigger');
            if (!trigger) return;

            var card = trigger.parentElement;
            if (!card) return;
            var panel = card.querySelector('.stwc-faq-panel');
            var chevron = trigger.querySelector('.stwc-faq-chevron');
            var isExpanded = trigger.getAttribute('aria-expanded') === 'true';

            trigger.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
            if (panel) panel.classList.toggle('hidden', isExpanded);
            if (chevron) chevron.classList.toggle('rotate-180', !isExpanded);
        });
    }

    function initLightbox() {
        document.addEventListener('click', function (e) {
            var item = e.target.closest('[data-stwc-lightbox]');
            if (!item) return;

            var container = item.closest('.stwc-gallery-grid, [data-stwc-filter-scope]') || document;
            var galleryNodes = Array.prototype.slice.call(container.querySelectorAll('[data-stwc-lightbox]')).filter(function (el) {
                return !el.classList.contains('is-hidden');
            });
            if (!galleryNodes.length) galleryNodes = [item];
            var currentIndex = Math.max(0, galleryNodes.indexOf(item));

            var backdrop = document.createElement('div');
            backdrop.className = 'stwc-lightbox-backdrop';
            backdrop.setAttribute('role', 'dialog');
            backdrop.setAttribute('aria-modal', 'true');
            backdrop.innerHTML =
                '<div class="stwc-lightbox-dialog">' +
                '<button type="button" class="stwc-lightbox-close" aria-label="Close">&times;</button>' +
                (galleryNodes.length > 1 ? '<button type="button" class="stwc-lightbox-nav stwc-lightbox-prev" aria-label="Previous image">&#8249;</button>' : '') +
                '<img src="" alt="" />' +
                (galleryNodes.length > 1 ? '<button type="button" class="stwc-lightbox-nav stwc-lightbox-next" aria-label="Next image">&#8250;</button>' : '') +
                '<div class="stwc-lightbox-caption"></div>' +
                (galleryNodes.length > 1 ? '<div class="stwc-lightbox-counter"></div>' : '') +
                '</div>';

            var imgEl = backdrop.querySelector('img');
            var capEl = backdrop.querySelector('.stwc-lightbox-caption');
            var ctrEl = backdrop.querySelector('.stwc-lightbox-counter');
            var prevOverflow = document.body.style.overflow;
            document.body.style.overflow = 'hidden';

            function showAt(idx) {
                currentIndex = (idx + galleryNodes.length) % galleryNodes.length;
                var target = galleryNodes[currentIndex];
                var url = target.getAttribute('data-stwc-lightbox') || '';
                var caption = target.getAttribute('data-stwc-caption') || '';
                imgEl.src = url;
                imgEl.alt = caption;
                capEl.textContent = caption;
                if (ctrEl) ctrEl.textContent = (currentIndex + 1) + ' / ' + galleryNodes.length;
            }

            function closeBox() {
                document.removeEventListener('keydown', onKeyDown);
                document.body.style.overflow = prevOverflow;
                if (backdrop.parentNode) backdrop.parentNode.removeChild(backdrop);
            }

            function onKeyDown(ev) {
                if (ev.key === 'Escape') { closeBox(); }
                else if (ev.key === 'ArrowLeft' && galleryNodes.length > 1) { showAt(currentIndex - 1); }
                else if (ev.key === 'ArrowRight' && galleryNodes.length > 1) { showAt(currentIndex + 1); }
            }

            backdrop.addEventListener('click', function (ev) {
                if (ev.target === backdrop || ev.target.classList.contains('stwc-lightbox-close')) {
                    closeBox();
                } else if (ev.target.classList.contains('stwc-lightbox-prev')) {
                    showAt(currentIndex - 1);
                } else if (ev.target.classList.contains('stwc-lightbox-next')) {
                    showAt(currentIndex + 1);
                }
            });

            document.addEventListener('keydown', onKeyDown);
            showAt(currentIndex);
            document.body.appendChild(backdrop);
        });
    }

    function initAreaDirectorySearch() {
        var dirs = document.querySelectorAll('[data-stwc-area-directory]');
        dirs.forEach(function (dir) {
            var input = dir.querySelector('.stwc-area-search');
            var cards = dir.querySelectorAll('.stwc-region-card');
            if (!input || !cards.length) return;

            input.addEventListener('input', function () {
                var q = input.value.trim().toLowerCase();
                var matchedRegions = 0;
                cards.forEach(function (card) {
                    var regionName = (card.getAttribute('data-region') || '').toLowerCase();
                    var chips = card.querySelectorAll('.stwc-town-chip');
                    var anyMatch = !q || regionName.indexOf(q) !== -1;

                    chips.forEach(function (chip) {
                        var town = (chip.getAttribute('data-town') || '').toLowerCase();
                        var match = !q || regionName.indexOf(q) !== -1 || town.indexOf(q) !== -1;
                        chip.style.display = match ? '' : 'none';
                        if (match) anyMatch = true;
                    });

                    card.style.display = anyMatch ? '' : 'none';
                    if (anyMatch) matchedRegions++;
                });

                var emptyNotice = dir.querySelector('.stwc-area-empty');
                if (!emptyNotice) {
                    emptyNotice = document.createElement('div');
                    emptyNotice.className = 'stwc-area-empty stwc-filter-empty hidden';
                    emptyNotice.textContent = 'No Ghana regions or towns match your search.';
                    dir.appendChild(emptyNotice);
                }
                emptyNotice.classList.toggle('hidden', matchedRegions > 0);
            });
        });
    }

    function initRegionAreaCascade() {
        var regions = Array.isArray(cfg.ghanaRegions) ? cfg.ghanaRegions : [];
        var towingForms = document.querySelectorAll('.stwc-towing-form');

        towingForms.forEach(function (form) {
            var regionSel = form.querySelector('.stwc-region-select');
            var areaSel = form.querySelector('.stwc-area-select');
            if (!regionSel || !areaSel) return;

            regionSel.addEventListener('change', function () {
                var chosen = regionSel.value;
                areaSel.innerHTML = '';

                if (!chosen) {
                    areaSel.disabled = true;
                    var defOpt = document.createElement('option');
                    defOpt.value = '';
                    defOpt.textContent = (cfg.i18n && cfg.i18n.selectRegion) || 'Select Region First...';
                    areaSel.appendChild(defOpt);
                    return;
                }

                var found = regions.find(function (r) {
                    return r.region && r.region.toLowerCase() === chosen.toLowerCase();
                });

                var placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = (cfg.i18n && cfg.i18n.selectArea) || 'Select Area / Town...';
                areaSel.appendChild(placeholder);

                if (found && Array.isArray(found.areas)) {
                    found.areas.forEach(function (areaName) {
                        var opt = document.createElement('option');
                        opt.value = areaName;
                        opt.textContent = areaName;
                        areaSel.appendChild(opt);
                    });
                }
                areaSel.disabled = false;
            });
        });
    }

    function initCareersApplyButtons() {
        document.addEventListener('click', function (e) {
            var closeBtn = e.target.closest('.stwc-close-apply');
            if (closeBtn) {
                var boxToClose = closeBtn.closest('.stwc-career-form-box');
                if (boxToClose) boxToClose.classList.add('hidden');
                return;
            }

            var btn = e.target.closest('.stwc-open-apply');
            if (!btn) return;
            var board = btn.closest('.stwc-careers-board');
            if (!board) return;

            var jobTitle = btn.getAttribute('data-job-title') || '';
            var box = board.querySelector('.stwc-career-form-box');
            if (!box) return;

            var titleSpan = box.querySelector('.stwc-selected-job-title');
            var titleInput = box.querySelector('.stwc-job-title-input');
            if (titleSpan) titleSpan.textContent = jobTitle;
            if (titleInput) {
                titleInput.value = jobTitle;
                titleInput.setAttribute('data-stwc-default', jobTitle);
            }

            box.classList.remove('hidden');
            if (typeof box.scrollIntoView === 'function') {
                box.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.stwc-career-form-box:not(.hidden)').forEach(function (box) {
                    box.classList.add('hidden');
                });
            }
        });
    }

    function validateFormClient(form, feedback) {
        function fail(message, field) {
            if (feedback) {
                feedback.classList.remove('hidden');
                feedback.classList.add('is-error');
                feedback.textContent = message;
            }
            if (field && typeof field.focus === 'function') field.focus();
            return false;
        }

        var requiredFields = form.querySelectorAll('[required]');
        for (var i = 0; i < requiredFields.length; i++) {
            var fld = requiredFields[i];
            if (fld.disabled) continue;
            var val = (fld.value || '').trim();
            if (!val) {
                return fail('Please fill in all required fields before submitting.', fld);
            }
        }

        var emailInputs = form.querySelectorAll('input[type="email"]');
        for (var j = 0; j < emailInputs.length; j++) {
            var em = (emailInputs[j].value || '').trim();
            if (em && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) {
                return fail('Please enter a valid email address.', emailInputs[j]);
            }
        }

        var phoneInputs = form.querySelectorAll('input[type="tel"]');
        for (var k = 0; k < phoneInputs.length; k++) {
            var ph = (phoneInputs[k].value || '').trim();
            if (ph && ph.replace(/\D/g, '').length < 7) {
                return fail('Please enter a valid phone number (at least 7 digits).', phoneInputs[k]);
            }
        }

        var fileInput = form.querySelector('input[type="file"]');
        if (fileInput && fileInput.files && fileInput.files[0]) {
            var f = fileInput.files[0];
            if (f.size > 5 * 1024 * 1024) {
                return fail((cfg.i18n && cfg.i18n.fileTooLarge) || 'File must be 5MB or smaller.', fileInput);
            }
            if (!/\.(pdf|doc|docx)$/i.test(f.name || '')) {
                return fail('Resume / CV must be a PDF, DOC, or DOCX file.', fileInput);
            }
        }

        return true;
    }

    function initAjaxForms() {
        var forms = document.querySelectorAll('.stwc-ajax-form');
        forms.forEach(function (form) {
            // Snapshot hidden / prefilled context defaults so form.reset() does not wipe item/job metadata
            var preservedFields = {};
            form.querySelectorAll('input[name="inquiryKind"], input[name="itemId"], input[name="itemTitle"], input[name="jobTitle"]').forEach(function (el) {
                preservedFields[el.name] = el.value;
            });

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                var action = form.getAttribute('data-action');
                var feedback = form.querySelector('.stwc-form-feedback');
                var submitBtn = form.querySelector('.stwc-submit-btn');
                if (!action || !cfg.ajaxUrl) return;

                if (feedback) {
                    feedback.classList.add('hidden');
                    feedback.classList.remove('is-error');
                    feedback.innerHTML = '';
                }

                if (!validateFormClient(form, feedback)) {
                    return;
                }

                var originalHtml = submitBtn ? submitBtn.innerHTML : '';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = (cfg.i18n && cfg.i18n.submitting) || 'Sending...';
                }

                var fd = new FormData(form);
                fd.append('action', action);
                fd.append('nonce', cfg.nonce || '');

                fetch(cfg.ajaxUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: fd
                })
                    .then(function (res) {
                        return res.text().then(function (rawText) {
                            var parsed = {};
                            try {
                                parsed = rawText ? JSON.parse(rawText) : {};
                            } catch (parseErr) {
                                parsed = { success: false, data: { message: 'Server returned an unexpected response.' } };
                            }
                            return { ok: res.ok, body: parsed };
                        });
                    })
                    .then(function (result) {
                        var payload = result.body || {};
                        if (feedback) {
                            feedback.classList.remove('hidden');
                            if (payload.success && payload.data) {
                                feedback.classList.remove('is-error');
                                var msg = document.createElement('p');
                                msg.textContent = payload.data.message || 'Submitted successfully.';
                                feedback.appendChild(msg);

                                if (payload.data.whatsapp_url) {
                                    var waLink = document.createElement('a');
                                    waLink.href = payload.data.whatsapp_url;
                                    waLink.target = '_blank';
                                    waLink.rel = 'noopener noreferrer';
                                    waLink.className = 'inline-flex items-center gap-2 mt-3 bg-primary text-white px-5 py-2.5 rounded-full font-bold text-xs hover:bg-black transition-colors';
                                    waLink.textContent = 'Continue on WhatsApp Now \u2192';
                                    feedback.appendChild(waLink);
                                }

                                form.reset();

                                // Restore preserved context values and reset dependent region->area dropdown
                                Object.keys(preservedFields).forEach(function (fieldName) {
                                    var target = form.querySelector('[name="' + fieldName + '"]');
                                    if (target) {
                                        var overrideDef = target.getAttribute('data-stwc-default');
                                        target.value = overrideDef !== null ? overrideDef : preservedFields[fieldName];
                                    }
                                });
                                var areaSel = form.querySelector('.stwc-area-select');
                                if (areaSel) {
                                    areaSel.innerHTML = '';
                                    areaSel.disabled = true;
                                    var defOpt = document.createElement('option');
                                    defOpt.value = '';
                                    defOpt.textContent = (cfg.i18n && cfg.i18n.selectRegion) || 'Select Region First...';
                                    areaSel.appendChild(defOpt);
                                }
                            } else {
                                feedback.classList.add('is-error');
                                feedback.textContent = (payload.data && payload.data.message) || 'Please check all required fields.';
                            }
                        }
                    })
                    .catch(function () {
                        if (feedback) {
                            feedback.classList.remove('hidden');
                            feedback.classList.add('is-error');
                            feedback.textContent = (cfg.i18n && cfg.i18n.networkError) || 'Connection error. Please call us directly.';
                        }
                    })
                    .finally(function () {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalHtml;
                        }
                    });
            });
        });
    }

    function initFaqLiveSearch() {
        var inputs = document.querySelectorAll('[data-stwc-faq-search]');
        inputs.forEach(function (inp) {
            if (inp.getAttribute('data-stwc-bound') === '1') return;
            inp.setAttribute('data-stwc-bound', '1');

            var wrap = inp.closest('.stwc-faq-search-wrap') || inp.parentElement;
            var statusEl = wrap ? wrap.querySelector('.stwc-faq-search-status') : null;

            inp.addEventListener('input', function () {
                var q = (inp.value || '').trim().toLowerCase();
                var scopes = document.querySelectorAll('[data-stwc-filter-scope="faqs"]');
                var totalShown = 0;

                scopes.forEach(function (scope) {
                    var items = scope.querySelectorAll('.stwc-filter-item');
                    var scopeShown = 0;
                    items.forEach(function (el) {
                        var txt = (el.textContent || '').toLowerCase();
                        var match = !q || txt.indexOf(q) !== -1;
                        el.classList.toggle('is-hidden', !match);
                        el.style.display = match ? '' : 'none';
                        if (match) {
                            scopeShown++;
                            totalShown++;
                        }
                    });
                    var listEl = scope.querySelector('.stwc-faq-list');
                    var emptyEl = scope.querySelector('.stwc-filter-empty');
                    if (!emptyEl && listEl) {
                        emptyEl = document.createElement('div');
                        emptyEl.className = 'stwc-filter-empty hidden';
                        emptyEl.textContent = 'No frequently asked questions match your search.';
                        listEl.appendChild(emptyEl);
                    }
                    if (emptyEl) {
                        emptyEl.classList.toggle('hidden', scopeShown > 0);
                    }
                });

                if (statusEl) {
                    if (q) {
                        statusEl.classList.remove('hidden');
                        statusEl.textContent = totalShown + ' matching question' + (totalShown === 1 ? '' : 's') + ' found';
                    } else {
                        statusEl.classList.add('hidden');
                        statusEl.textContent = '';
                    }
                }
            });
        });
    }

    function initBodyWorksShowcases() {
        var showcases = document.querySelectorAll('[data-stwc-bw-showcase]');
        showcases.forEach(function (box) {
            if (box.getAttribute('data-stwc-bound') === '1') return;
            box.setAttribute('data-stwc-bound', '1');

            var tabs = box.querySelectorAll('[data-stwc-bw-tab]');
            var beforeImg = box.querySelector('.stwc-bw-before-img');
            var beforeCap = box.querySelector('.stwc-bw-before-cap');
            var afterImg = box.querySelector('.stwc-bw-after-img');
            var afterCap = box.querySelector('.stwc-bw-after-cap');

            tabs.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    tabs.forEach(function (b) {
                        b.classList.remove('bg-primary', 'text-white', 'is-active');
                        b.classList.add('bg-white', 'text-dark', 'border', 'border-dark/15');
                        b.setAttribute('aria-selected', 'false');
                    });
                    btn.classList.add('bg-primary', 'text-white', 'is-active');
                    btn.classList.remove('bg-white', 'text-dark', 'border', 'border-dark/15');
                    btn.setAttribute('aria-selected', 'true');

                    var bImg = btn.getAttribute('data-before-img') || '';
                    var bCap = btn.getAttribute('data-before-cap') || '';
                    var aImg = btn.getAttribute('data-after-img') || '';
                    var aCap = btn.getAttribute('data-after-cap') || '';

                    if (beforeImg && bImg) beforeImg.src = bImg;
                    if (beforeCap) beforeCap.textContent = bCap;
                    if (afterImg && aImg) afterImg.src = aImg;
                    if (afterCap) afterCap.textContent = aCap;
                });
            });
        });
    }

    function initBeforeAfterSliders() {
        var stages = document.querySelectorAll('[data-stwc-before-after]');
        stages.forEach(function (stage) {
            if (stage.getAttribute('data-stwc-bound') === '1') return;
            stage.setAttribute('data-stwc-bound', '1');

            var range = stage.querySelector('.stwc-compare-range');
            var clip = stage.querySelector('.stwc-before-clip');
            var beforeInnerImg = clip ? clip.querySelector('img') : null;
            var divider = stage.querySelector('.stwc-compare-divider');
            if (!range || !clip) return;

            function syncDimensions() {
                if (beforeInnerImg && stage.clientWidth > 0) {
                    beforeInnerImg.style.width = stage.clientWidth + 'px';
                    beforeInnerImg.style.maxWidth = 'none';
                }
            }

            function updateSplit(val) {
                var pct = Math.min(95, Math.max(5, parseFloat(val) || 50));
                clip.style.width = pct + '%';
                if (divider) divider.style.left = pct + '%';
            }

            syncDimensions();
            updateSplit(range.value || 50);

            range.addEventListener('input', function () {
                updateSplit(range.value);
            });
            window.addEventListener('resize', syncDimensions);
        });
    }

    function bootAll() {
        initHeroSliders();
        initFilterScopes();
        initFaqAccordions();
        initFaqLiveSearch();
        initBodyWorksShowcases();
        initBeforeAfterSliders();
        initLightbox();
        initAreaDirectorySearch();
        initRegionAreaCascade();
        initCareersApplyButtons();
        initAjaxForms();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootAll);
    } else {
        bootAll();
    }

    if (window.jQuery) {
        window.jQuery(window).on('elementor/frontend/init', function () {
            if (window.elementorFrontend && window.elementorFrontend.hooks) {
                window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function () {
                    initHeroSliders();
                    initFilterScopes();
                    initFaqLiveSearch();
                    initBodyWorksShowcases();
                    initBeforeAfterSliders();
                    initAreaDirectorySearch();
                    initRegionAreaCascade();
                    initAjaxForms();
                });
            }
        });
    }
})();
