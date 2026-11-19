/**
 * Santa Towing Motion Observer
 * Lightweight IntersectionObserver reproducing React framer-motion scroll reveal.
 */
(function () {
    'use strict';

    function initReveals() {
        var elements = document.querySelectorAll('[data-st-reveal]');
        if (!elements.length) return;

        if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            elements.forEach(function (el) {
                el.classList.add('is-visible');
            });
            return;
        }

        var observer = new IntersectionObserver(
            function (entries, obs) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        obs.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.08, rootMargin: '0px 0px -30px 0px' }
        );

        elements.forEach(function (el) {
            observer.observe(el);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initReveals);
    } else {
        initReveals();
    }
})();
