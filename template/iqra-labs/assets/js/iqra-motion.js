/**
 * =========================================================================
 * MANSAPA Iqra' Labs — iqra-motion.js  [FINAL PRODUCTION v2]
 * Place: template/mansapa iqra labs/assets/js/iqra-motion.js
 *
 * Behaviours:
 *  1. Scroll Reveal   — IntersectionObserver on .scroll-reveal
 *  2. Navbar scroll   — solid bg when scrolled >60px
 *  3. Search FX       — scale + glow on focus
 *  4. Topic card delay— staggered entrance on desktop
 *  5. Quick-badge     — highlights active keyword in URL
 *  6. Smooth anchors  — scrollIntoView for # links
 * =========================================================================
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        /* ── 1. SCROLL REVEAL ──────────────────────────────────────────── */
        var revealEls = document.querySelectorAll('.scroll-reveal');

        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries, obs) {
                entries.forEach(function (entry, i) {
                    if (entry.isIntersecting) {
                        var delay = parseInt(entry.target.dataset.delay || (i % 6) * 80);
                        setTimeout(function () {
                            entry.target.classList.add('revealed');
                        }, delay);
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.10, rootMargin: '0px 0px -40px 0px' });

            revealEls.forEach(function (el) { observer.observe(el); });
        } else {
            // Graceful fallback
            revealEls.forEach(function (el) { el.classList.add('revealed'); });
        }


        /* ── 2. NAVBAR SCROLL BEHAVIOUR ───────────────────────────────── */
        var navbar = document.getElementById('mainNavbar');
        if (navbar) {
            function onScroll() {
                if (window.scrollY > 60) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            }
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll(); // run once on load
        }


        /* ── 3. SEARCH INPUT FOCUS FX ─────────────────────────────────── */
        var searchInput = document.getElementById('search-input');
        var searchGlass = document.querySelector('.search-glass');

        if (searchInput && searchGlass) {
            searchInput.addEventListener('focus', function () {
                searchGlass.style.transform = 'translateY(-3px)';
            });
            searchInput.addEventListener('blur', function () {
                searchGlass.style.transform = '';
            });
        }


        /* ── 4. TOPIC CARD STAGGER (desktop only) ─────────────────────── */
        if (window.innerWidth >= 1024) {
            var topicCards = document.querySelectorAll('.topic-grid .topic-card');
            topicCards.forEach(function (card, i) {
                card.style.transitionDelay = (i * 35) + 'ms';
            });
        }


        /* ── 5. QUICK BADGE ACTIVE STATE ──────────────────────────────── */
        var currentKeyword = new URLSearchParams(window.location.search).get('keywords');
        if (currentKeyword) {
            document.querySelectorAll('.qtag').forEach(function (tag) {
                var tagUrl    = new URL(tag.href, window.location.origin);
                var tagKw     = tagUrl.searchParams.get('keywords');
                if (tagKw && tagKw.toLowerCase() === currentKeyword.toLowerCase()) {
                    tag.style.background   = 'var(--gold)';
                    tag.style.color        = 'var(--navy)';
                    tag.style.borderColor  = 'var(--gold)';
                    tag.style.fontWeight   = '700';
                }
            });
        }


        /* ── 6. SMOOTH ANCHOR SCROLL ──────────────────────────────────── */
        document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
            anchor.addEventListener('click', function (e) {
                var targetId = anchor.getAttribute('href');
                if (targetId === '#') return;
                var target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });


        /* ── 7. OPAC DEDICATED PAGE INTERACTIONS ─────────────────────── */
        var carousel   = document.getElementById('opacBooksCarousel');
        var btnPrev    = document.getElementById('opacSliderPrev');
        var btnNext    = document.getElementById('opacSliderNext');
        var heroInput  = document.getElementById('opac-hero-input');

        if (heroInput && window.location.search.indexOf('p=opac') !== -1) {
            heroInput.focus();
        }

        if (carousel && btnPrev && btnNext) {
            var scrollDistance = 280;

            btnPrev.addEventListener('click', function () {
                carousel.scrollBy({ left: -scrollDistance, behavior: 'smooth' });
            });

            btnNext.addEventListener('click', function () {
                carousel.scrollBy({ left: scrollDistance, behavior: 'smooth' });
            });

            // Keyboard navigation
            carousel.addEventListener('keydown', function (e) {
                if (e.key === 'ArrowLeft') {
                    carousel.scrollBy({ left: -scrollDistance, behavior: 'smooth' });
                } else if (e.key === 'ArrowRight') {
                    carousel.scrollBy({ left: scrollDistance, behavior: 'smooth' });
                }
            });
        }

        /* ── 8. HERO OPAC CTA DIRECT REDIRECT ────────────────────────── */
        var heroOpacCta = document.getElementById('hero-opac-cta');
        if (heroOpacCta) {
            heroOpacCta.addEventListener('click', function (e) {
                // Ensure direct redirect to the dedicated OPAC page on click
                if (!e.ctrlKey && !e.metaKey && !e.shiftKey && e.button === 0) {
                    e.preventDefault();
                    window.location.href = 'index.php?p=opac';
                }
            });
        }

    }); // DOMContentLoaded

}());
