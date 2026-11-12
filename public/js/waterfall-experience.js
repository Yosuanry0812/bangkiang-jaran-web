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

    const film = document.querySelector('[data-hero-film], [data-resto-film]');
    if (film) {
        const photoFilm = film.hasAttribute('data-resto-film');
        const hero = film.closest('[data-hero-intro]') || film.closest('section');
        const source = film.querySelector('source');
        const connection = navigator.connection;
        const copy = hero.querySelector('.bj-home-hero__copy, .bj-resto-hero-copy');
        const title = hero.querySelector('[data-hero-typewriter]');
        let introStarted = hero.classList.contains('is-intro-complete');
        let introElapsed = 0;
        let introClock = null;
        let introTimer;
        let completionTimer;
        let letterCount = 0;
        if (!photoFilm || film.dataset.restoPlayback) hero.setAttribute('data-intro-managed', '');
        if (title) {
            const label = title.cloneNode(true);
            label.querySelectorAll('br').forEach(br => br.replaceWith(' '));
            title.setAttribute('aria-label', label.textContent.replace(/\s+/g, ' ').trim());
            const walker = document.createTreeWalker(title, NodeFilter.SHOW_TEXT);
            const nodes = [];
            while (walker.nextNode()) nodes.push(walker.currentNode);
            nodes.forEach(node => {
                const fragment = document.createDocumentFragment();
                node.textContent.split(/(\s+)/).forEach(word => {
                    if (!word.trim()) { fragment.append(document.createTextNode(word)); return; }
                    const wrapper = document.createElement('span');
                    wrapper.className = 'bj-type-word';
                    wrapper.setAttribute('aria-hidden', 'true');
                    Array.from(word).forEach(letter => {
                        const span = document.createElement('span');
                        span.className = 'bj-type-letter';
                        span.style.setProperty('--letter-index', letterCount++);
                        span.textContent = letter;
                        wrapper.append(span);
                    });
                    fragment.append(wrapper);
                });
                node.replaceWith(fragment);
            });
        }
        const typeDuration = 120 + letterCount * 22 + 180;
        hero.style.setProperty('--type-duration', `${typeDuration}ms`);
        const completeIntro = () => {
            clearTimeout(completionTimer);
            hero.classList.remove('is-intro-pending', 'is-intro-typing');
            hero.classList.add('is-intro-complete');
            copy.inert = false;
        };
        const revealIntro = (immediate = false) => {
            clearTimeout(introTimer);
            if (immediate) { introStarted = true; completeIntro(); return; }
            if (introStarted) return;
            introStarted = true;
            hero.classList.remove('is-intro-pending');
            hero.classList.add('is-intro-typing');
            completionTimer = setTimeout(completeIntro, typeDuration + 850);
        };
        const pauseIntroClock = () => {
            clearTimeout(introTimer);
            if (introClock !== null) introElapsed += performance.now() - introClock;
            introClock = null;
        };
        const startIntroClock = () => {
            if (introStarted || introClock !== null) return;
            introClock = performance.now();
            introTimer = setTimeout(() => {
                pauseIntroClock();
                if (allowed()) revealIntro();
            }, Math.max(0, 5000 - introElapsed));
        };
        copy.inert = hero.classList.contains('is-intro-pending');
        let visible = false;
        let leaving = false;
        let failed = false;
        let pending = false;
        const allowed = () => visible && !leaving && !failed && !document.hidden
            && !reduced.matches && !connection?.saveData
            && (!photoFilm || film.dataset.restoPlayback === 'playing');
        const sync = () => {
            if (photoFilm) {
                if (film.dataset.restoPlayback) hero.setAttribute('data-intro-managed', '');
                if (allowed()) startIntroClock();
                else pauseIntroClock();
                if (reduced.matches || connection?.saveData || film.dataset.restoPlayback === 'static') revealIntro(true);
                return;
            }
            if (!allowed()) {
                pauseIntroClock();
                film.pause();
                hero.classList.remove('is-film-playing');
                if (reduced.matches || connection?.saveData) revealIntro(true);
                return;
            }
            if (!source.src) {
                source.src = source.dataset.videoSrc;
                film.load();
            }
            if (pending || !film.paused) return;
            pending = true;
            film.play().catch(error => {
                if (error.name === 'AbortError' && allowed()) requestAnimationFrame(sync);
                else if (error.name !== 'AbortError') revealIntro(true);
                // Autoplay can be denied by device policy; the poster stays visible.
            }).finally(() => { pending = false; });
        };
        if (photoFilm) {
            film.addEventListener('resto-film-playback', sync);
        } else {
            film.muted = true;
            film.defaultMuted = true;
            film.loop = true;
            film.addEventListener('playing', () => {
                if (!allowed()) { sync(); return; }
                hero.classList.add('is-film-ready', 'is-film-playing');
                startIntroClock();
            });
            film.addEventListener('pause', () => { pauseIntroClock(); hero.classList.remove('is-film-playing'); });
            film.addEventListener('waiting', pauseIntroClock);
            const showPoster = () => {
                failed = true;
                film.pause();
                hero.classList.remove('is-film-ready', 'is-film-playing');
                revealIntro(true);
            };
            film.addEventListener('error', showPoster);
            source.addEventListener('error', showPoster);
        }
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(entries => {
                visible = entries[0].isIntersecting;
                sync();
            }, { threshold: 0 }).observe(hero);
        } else { visible = true; sync(); }
        document.addEventListener('visibilitychange', sync);
        reduced.addEventListener('change', sync);
        connection?.addEventListener('change', sync);
        addEventListener('pagehide', () => { leaving = true; sync(); });
        addEventListener('pageshow', () => { leaving = false; sync(); });
        // Retry only after a real user gesture when autoplay was denied.
        addEventListener('pointerdown', sync, { passive: true });
        addEventListener('keydown', sync);
    }
});
