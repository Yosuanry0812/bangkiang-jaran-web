/* Beranda: jalur foto menyamping lewat scroll, bobot judul, parallax, tombol tiket. */
(() => {
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches || navigator.connection?.saveData;
    const wide = matchMedia('(min-width: 901px)');

    const root = document.querySelector('.bj-rv');
    if (root) root.classList.add('bj-rv--js');

    // Judul: kata naik satu per satu dari balik garis; elemen lain muncul berurutan.
    const inView = new ('IntersectionObserver' in window ? IntersectionObserver : function () { this.observe = () => {}; this.unobserve = () => {}; })(entries => entries.forEach(entry => {
        if (entry.isIntersecting) { entry.target.classList.add('is-in'); inView.unobserve(entry.target); }
    }), { rootMargin: '0px 0px -10% 0px', threshold: 0.1 });
    if (!reduce && root) {
        root.querySelectorAll('h2').forEach(heading => {
            if (heading.children.length || !heading.textContent.trim()) return;
            const text = heading.textContent.trim();
            heading.setAttribute('aria-label', text);
            heading.textContent = '';
            text.split(/\s+/).forEach((word, index) => {
                const mask = document.createElement('span');
                mask.className = 'bj-rv-sw';
                mask.setAttribute('aria-hidden', 'true');
                const inner = document.createElement('span');
                inner.textContent = word;
                inner.style.setProperty('--i', index);
                mask.appendChild(inner);
                heading.appendChild(mask);
                heading.appendChild(document.createTextNode(' '));
            });
            heading.classList.add('bj-rv-split');
            inView.observe(heading);
        });
        const groups = ['.bj-rv-stub', '.bj-rv-faq__items details', '.bj-rv-route__origins button', '.bj-rv-tickets__copy > *', '.bj-rv-route__panel > *', '.bj-rv-close > *', '.bj-rv-wall__title small'];
        groups.forEach(selector => root.querySelectorAll(selector).forEach((el, index) => {
            el.setAttribute('data-rv-in', '');
            el.style.setProperty('--i', index);
            inView.observe(el);
        }));
    }

    // Bilah progres scroll di tepi atas.
    const bar = document.createElement('div');
    bar.className = 'bj-rv-progress';
    bar.setAttribute('aria-hidden', 'true');
    if (root) root.appendChild(bar);
    const path = document.querySelector('.bj-rv-steps__path');
    if (!reduce && 'IntersectionObserver' in window) {
        const seen = new IntersectionObserver(entries => entries.forEach(entry => {
            if (entry.isIntersecting) { entry.target.classList.add('is-seen'); seen.unobserve(entry.target); }
        }), { rootMargin: '0px 0px -12% 0px', threshold: 0.12 });
        document.querySelectorAll('[data-rv-panel]').forEach(panel => seen.observe(panel));
    } else {
        document.querySelectorAll('[data-rv-panel]').forEach(panel => panel.classList.add('is-seen'));
    }

    // Jalur: pada layar lebar, scroll vertikal menggeser foto ke samping. Layar kecil memakai geser jari biasa.
    const trail = document.querySelector('[data-rv-trail]');
    const track = trail?.querySelector('[data-rv-track]');
    let travel = 0;
    const measureTrail = () => {
        if (!trail || !track) return;
        const pinned = wide.matches && !reduce;
        trail.classList.toggle('is-pinned', pinned);
        track.style.transform = '';
        if (!pinned) { trail.style.removeProperty('--rv-trail-height'); travel = 0; return; }
        travel = Math.max(0, track.scrollWidth - innerWidth);
        trail.style.setProperty('--rv-trail-height', `${travel + innerHeight}px`);
    };

    const weights = [...document.querySelectorAll('[data-rv-weight]')];
    const parallax = [...document.querySelectorAll('[data-rv-parallax]')];
    const fab = document.querySelector('[data-rv-fab]');
    const hero = document.getElementById('hero-section');
    const cta = document.querySelector('[data-rv-cta]');
    let ticking = false;

    const update = () => {
        ticking = false;
        const vh = innerHeight;
        if (trail && travel > 0) {
            const rect = trail.getBoundingClientRect();
            const progress = Math.min(1, Math.max(0, -rect.top / Math.max(1, rect.height - vh)));
            track.style.transform = `translate3d(${-Math.round(progress * travel)}px,0,0)`;
        }
        if (!reduce) {
            weights.forEach(el => {
                const r = el.getBoundingClientRect();
                if (r.bottom < 0 || r.top > vh) return;
                const p = Math.min(1, Math.max(0, (vh - r.top) / (vh * 0.7)));
                const from = Number(el.dataset.rvWeight) || 200;
                const to = Number(el.dataset.rvWeightTo) || 600;
                el.style.fontVariationSettings = `'wght' ${Math.round(from + (to - from) * p)}`;
            });
            parallax.forEach(el => {
                const r = el.parentElement.getBoundingClientRect();
                if (r.bottom > 0 && r.top < vh) {
                    const p = (r.top + r.height / 2 - vh / 2) / vh;
                    el.style.transform = `translate3d(0, ${Math.round(p * -50)}px, 0)`;
                }
            });
        }
        const docHeight = document.documentElement.scrollHeight - vh;
        bar.style.transform = 'scaleX(' + (docHeight > 0 ? Math.min(1, Math.max(0, scrollY / docHeight)) : 0).toFixed(4) + ')';
        if (path && !reduce) {
            const pr = path.getBoundingClientRect();
            path.style.setProperty('--rv-line', Math.min(1, Math.max(0, (vh * 0.7 - pr.top) / pr.height)).toFixed(3));
        }
        if (trail && travel > 0) {
            trail.querySelectorAll('[data-rv-panel] img').forEach(img => {
                const pr = img.closest('[data-rv-panel]').getBoundingClientRect();
                img.style.setProperty('--rv-px', Math.round(((pr.left + pr.width / 2 - innerWidth / 2) / innerWidth) * -70) + 'px');
            });
        }
        if (fab && hero) {
            const pastHero = hero.getBoundingClientRect().bottom < vh * 0.25;
            const rect = cta ? cta.getBoundingClientRect() : null;
            const atCta = rect && rect.top < vh && rect.bottom > 0;
            fab.classList.toggle('is-visible', pastHero && !atCta);
        }
    };
    const request = () => { if (!ticking) { ticking = true; requestAnimationFrame(update); } };
    const remeasure = () => { measureTrail(); request(); };
    addEventListener('scroll', request, { passive: true });
    addEventListener('resize', remeasure, { passive: true });
    addEventListener('load', remeasure);
    wide.addEventListener?.('change', remeasure);
    measureTrail();
    update();
})();
