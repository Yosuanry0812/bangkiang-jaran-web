(() => {
    'use strict';

    const film = document.querySelector('[data-resto-film]');
    if (!film) return;

    const photos = JSON.parse(film.dataset.restoPhotos);
    const fallback = JSON.parse(film.dataset.restoFallback);
    const frames = [...film.querySelectorAll('[data-resto-frame]')];
    const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)');
    const connection = navigator.connection;
    const animations = new Set();
    const failed = new Set();
    const pending = new Map();
    const HOLD = 2200;
    const DISSOLVE = 1000;
    let current = 0;
    let layer = 0;
    let generation = 0;
    let ready = false;
    let running = false;
    let inView = film.getBoundingClientRect().bottom > 0;
    let pageAway = false;

    const allowed = () => !reducedMotion.matches && !connection?.saveData && typeof film.animate === 'function';
    const paused = () => !inView || document.hidden || pageAway;
    const reportPlayback = state => {
        if (film.dataset.restoPlayback === state) return;
        film.dataset.restoPlayback = state;
        film.dispatchEvent(new Event('resto-film-playback'));
    };
    const release = animation => {
        animations.delete(animation);
        animation.cancel();
    };
    const animate = (element, keyframes, options) => {
        const animation = element.animate(keyframes, options);
        animations.add(animation);
        if (paused()) animation.pause();
        return animation;
    };
    const drift = (element, index) => animate(element, index % 2 ? [
        { transform: 'scale(1.12) translate3d(1%, -.5%, 0)' },
        { transform: 'scale(1.04) translate3d(-1%, .5%, 0)' },
    ] : [
        { transform: 'scale(1.04) translate3d(-1%, .5%, 0)' },
        { transform: 'scale(1.12) translate3d(1%, -.5%, 0)' },
    ], { duration: 6400, iterations: Infinity, direction: 'alternate', easing: 'linear' });

    // Keep the visible photo in place until the next photo has loaded AND decoded.
    function load(photo) {
        if (pending.has(photo.url)) return pending.get(photo.url);
        const promise = new Promise((resolve, reject) => {
            const image = new Image();
            const timeout = setTimeout(() => finish(false), 10000);
            const finish = success => {
                clearTimeout(timeout);
                image.onload = image.onerror = null;
                if (success) resolve(photo);
                else { failed.add(photo.url); reject(new Error('Photo unavailable')); }
            };
            image.onload = () => image.decode().then(() => finish(true), () => finish(false));
            image.onerror = () => finish(false);
            image.src = photo.url;
        });
        pending.set(photo.url, promise);
        return promise;
    }

    async function nextPhoto(index, token) {
        for (let step = 1; step < photos.length; step++) {
            if (token !== generation) return null;
            const next = (index + step) % photos.length;
            if (failed.has(photos[next].url)) continue;
            try { await load(photos[next]); return next; } catch { /* Try the next real upload. */ }
        }
        return null;
    }

    function describe() {
        frames[layer].alt = photos[current].caption;
        frames[layer].removeAttribute('aria-hidden');
        frames[1 - layer].alt = '';
        frames[1 - layer].setAttribute('aria-hidden', 'true');
    }

    function syncPlayback() {
        animations.forEach(animation => {
            if (paused()) animation.pause();
            else if (animation.playState === 'paused') animation.play();
        });
        if (ready) reportPlayback(allowed() && running ? (paused() ? 'paused' : 'playing') : 'static');
    }

    async function play(token) {
        let camera = drift(frames[layer], current);
        try {
            while (token === generation && photos.length > 1) {
                // Only one upcoming photo is prepared; no all-gallery preload.
                const upcoming = nextPhoto(current, token);
                const hold = animate(frames[layer], [], { duration: HOLD });
                await hold.finished;
                const next = await upcoming;
                if (token !== generation) return;
                if (next === null) {
                    release(hold);
                    return;
                }
                const incoming = frames[1 - layer];
                const outgoing = frames[layer];
                incoming.src = photos[next].url;
                await incoming.decode();
                if (token !== generation) return;
                incoming.style.zIndex = '1';
                outgoing.style.zIndex = '0';
                const nextCamera = drift(incoming, next);
                // The outgoing layer stays opaque to avoid a dark dip mid-dissolve.
                const fade = animate(incoming, [{ opacity: 0 }, { opacity: 1 }], { duration: DISSOLVE, easing: 'ease-in-out', fill: 'forwards' });
                await fade.finished;
                if (token !== generation) return;
                incoming.style.opacity = '1';
                outgoing.style.opacity = '0';
                release(fade);
                release(camera);
                release(hold);
                camera = nextCamera;
                layer = 1 - layer;
                current = next;
                describe();
            }
        } catch (error) {
            if (error.name !== 'AbortError') stop();
        }
    }

    function stop() {
        generation++;
        running = false;
        animations.forEach(animation => animation.cancel());
        animations.clear();
        frames.forEach((frame, index) => {
            frame.style.opacity = index === layer ? '1' : '0';
            frame.style.zIndex = '0';
        });
        film.classList.remove('is-enhanced');
        reportPlayback('static');
    }

    function reconcile() {
        if (!ready) return;
        if (!allowed()) { stop(); return; }
        if (!running) {
            running = true;
            film.classList.add('is-enhanced');
            play(++generation);
        }
        syncPlayback();
    }

    document.addEventListener('visibilitychange', syncPlayback);
    reducedMotion.addEventListener('change', reconcile);
    connection?.addEventListener('change', reconcile);
    window.addEventListener('pagehide', () => { pageAway = true; syncPlayback(); });
    window.addEventListener('pageshow', () => { pageAway = false; syncPlayback(); });
    if ('IntersectionObserver' in window) {
        new IntersectionObserver(entries => {
            inView = entries[0].isIntersecting;
            syncPlayback();
        }).observe(film);
    }

    (async () => {
        // Zero/one photo remains useful. Failed uploads never replace a good frame.
        let available = false;
        for (let index = 0; index < photos.length; index++) {
            try { await load(photos[index]); current = index; available = true; break; } catch { /* Recover initial image. */ }
        }
        if (!available) {
            photos.splice(0, photos.length, fallback);
            try { await load(fallback); } catch { return; }
        }
        frames[layer].src = photos[current].url;
        describe();
        ready = true;
        reconcile();
    })();
})();
