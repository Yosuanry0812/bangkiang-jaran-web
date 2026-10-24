document.addEventListener('DOMContentLoaded', () => {
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    const streams = [];
    let frame = null;
    let lastTime = 0;
    let scrollY = window.scrollY;
    let scrollTime = performance.now();
    let velocity = 0;
    let direction = 1;

    const apply = row => {
        if (!row.width) return;
        row.offset = ((row.offset % row.width) + row.width) % row.width;
        row.track.style.transform = `translate3d(${-row.offset}px,0,0)`;
    };
    const active = state => state.visible && !state.paused && !state.hovered && !state.focused && !state.dragging && !reduced.matches && !document.hidden;
    const tick = now => {
        frame = null;
        const dt = Math.min((now - (lastTime || now)) / 1000, .05);
        lastTime = now;
        velocity *= Math.exp(-dt * 4.5);
        const speed = 22 + Math.min(Math.abs(velocity) * .22, 520);
        streams.forEach(state => {
            if (!active(state)) return;
            state.rows.forEach(row => {
                row.offset += speed * dt * row.direction * direction;
                apply(row);
            });
        });
        if (streams.some(active)) frame = requestAnimationFrame(tick);
    };
    const start = () => {
        if (!frame && streams.some(active)) { lastTime = performance.now(); if (lastTime - scrollTime > 250) velocity = 0; frame = requestAnimationFrame(tick); }
    };

    document.querySelectorAll('[data-photo-stream]:not(.bj-photo-stream--static)').forEach(element => {
        const pause = element.querySelector('[data-stream-pause]');
        if (!pause) return;
        const state = { element, rows: [], visible: false, paused: reduced.matches, hovered: false, focused: false, dragging: false };
        element.querySelectorAll('[data-stream-track]').forEach(track => {
            const row = { track, width: 0, offset: 0, direction: Number(track.dataset.streamDirection) || 1 };
            const viewport = track.parentElement;
            const measure = () => { row.width = track.querySelector('[data-stream-set]').getBoundingClientRect().width; apply(row); };
            new ResizeObserver(measure).observe(track.querySelector('[data-stream-set]'));
            state.rows.push(row);
            let drag = null;
            viewport.addEventListener('pointerdown', event => {
                if (reduced.matches || !event.isPrimary || (event.pointerType === 'mouse' && event.button !== 0)) return;
                drag = { x: event.clientX, y: event.clientY, offset: row.offset };
                state.dragging = true;
                if (event.isTrusted) viewport.setPointerCapture(event.pointerId);
            });
            viewport.addEventListener('pointermove', event => {
                if (!drag) return;
                const dx = event.clientX - drag.x;
                const dy = event.clientY - drag.y;
                if (Math.abs(dx) > 6 && Math.abs(dx) > Math.abs(dy)) { row.offset = drag.offset - dx; apply(row); }
            });
            const endDrag = () => { drag = null; state.dragging = false; start(); };
            viewport.addEventListener('pointerup', endDrag);
            viewport.addEventListener('pointercancel', endDrag);
            viewport.addEventListener('keydown', event => {
                if (!['ArrowLeft', 'ArrowRight'].includes(event.key)) return;
                event.preventDefault(); move(event.key === 'ArrowRight' ? 1 : -1);
            });
        });
        const move = step => {
            state.rows.forEach(row => {
                if (reduced.matches) row.track.parentElement.scrollBy({ left: step * 280, behavior: 'instant' });
                else { row.offset += step * 280; apply(row); }
            });
        };
        const update = () => {
            element.classList.toggle('is-moving', !reduced.matches);
            pause.hidden = reduced.matches;
            pause.setAttribute('aria-pressed', String(state.paused));
            pause.setAttribute('aria-label', state.paused ? pause.dataset.playLabel : pause.dataset.pauseLabel);
            pause.querySelector('span').textContent = state.paused ? 'play_arrow' : 'pause';
            start();
        };
        element.querySelector('[data-stream-prev]').addEventListener('click', () => move(-1));
        element.querySelector('[data-stream-next]').addEventListener('click', () => move(1));
        pause.addEventListener('click', () => { state.paused = !state.paused; update(); });
        element.addEventListener('pointerenter', () => { state.hovered = true; });
        element.addEventListener('pointerleave', () => { state.hovered = false; start(); });
        element.addEventListener('focusin', () => { state.focused = true; });
        element.addEventListener('focusout', () => setTimeout(() => { state.focused = element.contains(document.activeElement); start(); }, 0));
        new IntersectionObserver(entries => { state.visible = entries[0].isIntersecting; start(); }, { threshold: .05 }).observe(element);
        reduced.addEventListener('change', () => { if (reduced.matches) state.paused = true; update(); });
        streams.push(state);
        update();
    });
    if (streams.length) {
        window.addEventListener('scroll', () => {
            const now = performance.now();
            const current = window.scrollY;
            const delta = current - scrollY;
            if (Math.abs(delta) > 1) direction = Math.sign(delta);
            velocity = Math.min(6000, Math.abs(delta / Math.max(now - scrollTime, 8) * 1000));
            scrollY = current; scrollTime = now; start();
        }, { passive: true });
        document.addEventListener('visibilitychange', () => {
            if (document.hidden && frame) { cancelAnimationFrame(frame); frame = null; }
            else start();
        });
    }

    const film = document.querySelector('[data-hero-film]');
    const toggle = document.querySelector('[data-film-toggle]');
    if (film && toggle) {
        let loaded = false;
        let userPaused = reduced.matches || Boolean(navigator.connection?.saveData);
        const update = () => {
            toggle.setAttribute('aria-pressed', String(!film.paused));
            toggle.setAttribute('aria-label', film.paused ? toggle.dataset.playLabel : toggle.dataset.pauseLabel);
            toggle.querySelector('.material-symbols-outlined').textContent = film.paused ? 'play_arrow' : 'pause';
            toggle.querySelector('[data-film-label]').textContent = film.paused ? (document.documentElement.lang === 'en' ? 'Play video' : 'Putar video') : (document.documentElement.lang === 'en' ? 'Pause video' : 'Jeda video');
        };
        const play = () => {
            if (!loaded) { const source = film.querySelector('source'); source.src = source.dataset.videoSrc; film.load(); loaded = true; }
            film.play().catch(update);
        };
        toggle.addEventListener('click', () => { userPaused = !film.paused; if (film.paused) play(); else film.pause(); });
        film.addEventListener('playing', update);
        film.addEventListener('pause', update);
        const showPoster = () => { film.hidden = true; toggle.hidden = true; };
        film.addEventListener('error', showPoster);
        film.querySelector('source').addEventListener('error', showPoster);
        const observer = new IntersectionObserver(entries => { if (!entries[0].isIntersecting || document.hidden) film.pause(); else if (!userPaused) play(); });
        observer.observe(film.closest('section'));
        document.addEventListener('visibilitychange', () => { if (document.hidden) film.pause(); else if (!userPaused && film.closest('section').getBoundingClientRect().bottom > 0) play(); });
        reduced.addEventListener('change', () => { if (reduced.matches) { userPaused = true; film.pause(); } });
        update();
    }
});
