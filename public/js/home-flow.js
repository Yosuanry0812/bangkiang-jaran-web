document.addEventListener('DOMContentLoaded', () => {
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    const saveData = navigator.connection?.saveData === true;
    const walls = [];
    const stack = document.querySelector('[data-overlap-stack]');
    const panels = stack ? [...stack.querySelectorAll('[data-overlap-panel]')].map(element => ({ element, start: 0, height: 0, top: 0 })) : [];
    let layout = { end: 0, height: innerHeight, mobile: innerWidth < 768, nav: 88 };
    let frame = null;
    let measureFrame = null;
    let previousTime = 0;
    let previousScroll = scrollY;
    let scrollTime = performance.now();
    let targetVelocity = 0;
    let velocity = 0;
    let leaving = false;
    let keyboardMode = false;
    const clamp = (value, min, max) => Math.min(max, Math.max(min, value));
    const allowsMotion = () => !reduced.matches && !saveData;

    const imageFallback = image => {
        const fail = () => { image.hidden = true; image.nextElementSibling.hidden = false; };
        image.addEventListener('error', fail, { once: true });
        if (image.complete && !image.naturalWidth) fail();
    };
    document.querySelectorAll('.bj-photo-collage__photo img').forEach(imageFallback);

    const syncWall = wall => {
        const enabled = allowsMotion() && wall.animations.length > 0;
        const reading = wall.paused || wall.focused;
        const active = enabled && wall.visible && !wall.covered && !reading && !document.hidden && !leaving;
        wall.element.classList.toggle('is-static-view', reading);
        wall.element.dataset.columnsMotion = enabled ? (active ? 'running' : 'paused') : 'static';
        wall.animations.forEach(animation => active ? animation.play() : animation.pause());
        wall.controls.hidden = !enabled;
        wall.button.setAttribute('aria-pressed', String(reading));
        wall.button.setAttribute('aria-label', reading ? wall.button.dataset.playLabel : wall.button.dataset.pauseLabel);
        wall.button.querySelector('[data-columns-button-label]').textContent = reading ? wall.button.dataset.playLabel : wall.button.dataset.pauseLabel;
        wall.button.querySelector('.material-symbols-outlined').textContent = reading ? 'play_arrow' : 'pause';
    };

    const buildWall = wall => {
        const progress = wall.animations.map(animation => ((Number(animation.currentTime) || 0) / animation.effect.getTiming().duration) % 1);
        wall.animations.forEach(animation => animation.cancel());
        wall.animations = [];
        wall.lanes.replaceChildren();
        // Measure visible tracks even when resizing while the reader has paused.
        wall.element.classList.remove('is-enhanced', 'is-static-view');
        wall.lanes.hidden = true;
        if (!allowsMotion() || !Element.prototype.animate) { wall.controls.hidden = true; wall.element.dataset.columnsMotion = 'static'; return; }

        wall.element.classList.add('is-enhanced');
        wall.lanes.hidden = false;
        const count = innerWidth < 768 ? 2 : 3;
        const gap = count === 2 ? 10 : 14;
        const width = (wall.viewport.clientWidth - gap * (count - 1)) / count;
        const rows = Math.max(3, Math.ceil(wall.viewport.clientHeight / (width * .75 + gap)) + 1);
        for (let column = 0; column < count; column += 1) {
            const lane = document.createElement('div');
            const track = document.createElement('div');
            const set = document.createElement('div');
            lane.className = 'bj-photo-columns__lane';
            track.className = 'bj-photo-columns__track';
            set.className = 'bj-photo-columns__set';
            for (let row = 0; row < rows; row += 1) {
                const photo = wall.photos[(row * count + column) % wall.photos.length].cloneNode(true);
                const image = photo.querySelector('img');
                image.alt = '';
                imageFallback(image);
                set.append(photo);
            }
            track.append(set);
            lane.append(track);
            wall.lanes.append(lane);
            // CSS height excludes the outer section's scroll-driven scale.
            const distance = parseFloat(getComputedStyle(set).height) + gap;
            const repeat = set.cloneNode(true);
            repeat.querySelectorAll('img').forEach(imageFallback);
            track.append(repeat);
            const speed = count === 2 ? [8, 10][column] : [10, 13, 11][column];
            const duration = distance / speed * 1000;
            const animation = track.animate([{ transform: 'translate3d(0,0,0)' }, { transform: `translate3d(0,${-distance}px,0)` }], { duration, iterations: Infinity, easing: 'linear', direction: column % 2 ? 'reverse' : 'normal' });
            animation.pause();
            animation.currentTime = (progress[column] ?? column * .19) * duration;
            lane.dataset.columnDirection = column % 2 ? 'down' : 'up';
            wall.animations.push(animation);
        }
        syncWall(wall);
    };

    const wallObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            const wall = walls.find(item => item.element === entry.target);
            wall.visible = entry.isIntersecting;
            syncWall(wall);
        });
    }, { threshold: 0 });

    document.querySelectorAll('[data-photo-columns]').forEach(element => {
        const photos = [...element.querySelectorAll('[data-columns-source] figure')];
        photos.forEach(photo => imageFallback(photo.querySelector('img')));
        if (!photos.length) return;
        const wall = { element, photos, viewport: element.querySelector('[data-columns-viewport]'), lanes: element.querySelector('[data-columns-lanes]'), controls: element.querySelector('[data-columns-controls]'), button: element.querySelector('[data-columns-pause]'), animations: [], paused: false, focused: false, visible: false, covered: false, size: '' };
        walls.push(wall);
        buildWall(wall);
        wall.button.addEventListener('click', () => { wall.paused = !wall.paused; syncWall(wall); });
        wall.viewport.addEventListener('focusin', () => { wall.focused = true; syncWall(wall); });
        wall.viewport.addEventListener('focusout', () => { wall.focused = false; syncWall(wall); });
        wallObserver.observe(element);
        new ResizeObserver(() => {
            const size = `${wall.viewport.clientWidth}:${wall.viewport.clientHeight}:${innerWidth < 768}`;
            if (size === wall.size) return;
            wall.size = size;
            requestAnimationFrame(() => buildWall(wall));
        }).observe(wall.viewport);
    });

    const paintPanels = () => {
        if (!stack || !allowsMotion() || stack.classList.contains('is-reading')) return;
        const y = scrollY;
        const topOf = panel => Math.min(Math.max(panel.start - y, panel.top), layout.end - y - panel.height);
        panels.forEach((panel, index) => {
            const next = panels[index + 1];
            const top = topOf(panel);
            const progress = next ? clamp((top + panel.height - topOf(next)) / Math.min(panel.height, layout.height - layout.nav), 0, 1) : 0;
            const visible = top < layout.height && top + panel.height > 0;
            const drift = visible ? clamp(velocity * (layout.mobile ? .0015 : .0035), layout.mobile ? -3 : -7, layout.mobile ? 3 : 7) * (1 - progress) - progress * (layout.mobile ? 5 : 12) : 0;
            panel.element.style.setProperty('--panel-drift', `${drift.toFixed(2)}px`);
            panel.element.style.setProperty('--panel-scale', (1 - progress * (layout.mobile ? .006 : .016)).toFixed(4));
            walls.filter(wall => panel.element.contains(wall.element)).forEach(wall => {
                const covered = progress > .995;
                if (covered !== wall.covered) { wall.covered = covered; syncWall(wall); }
            });
        });
    };
    const tick = now => {
        frame = null;
        if (document.hidden || leaving || !allowsMotion()) return;
        const dt = Math.min((now - (previousTime || now - 16)) / 1000, .064);
        previousTime = now;
        velocity += (targetVelocity - velocity) * (1 - Math.exp(-dt / .12));
        targetVelocity *= Math.exp(-dt / .13);
        if (Math.abs(velocity) < 1 && Math.abs(targetVelocity) < 1) { velocity = 0; targetVelocity = 0; }
        paintPanels();
        if (velocity || targetVelocity) frame = requestAnimationFrame(tick);
    };
    const requestPaint = () => {
        if (!frame && allowsMotion() && !document.hidden && !leaving) { previousTime = 0; frame = requestAnimationFrame(tick); }
    };
    const measure = () => {
        measureFrame = null;
        if (!stack) return;
        layout = { end: 0, height: innerHeight, mobile: innerWidth < 768, nav: Math.min(100, document.getElementById('main-nav')?.offsetHeight || 88) + 8 };
        let start = stack.getBoundingClientRect().top + scrollY;
        const heights = panels.map(panel => panel.element.offsetHeight);
        panels.forEach((panel, index) => {
            panel.start = start;
            panel.height = heights[index];
            panel.top = Math.min(layout.nav, layout.height - panel.height);
            start += panel.height;
            panel.element.style.setProperty('--panel-top', `${panel.top}px`);
            panel.element.style.setProperty('--panel-layer', String(index + 1));
        });
        layout.end = start;
        requestPaint();
    };
    const requestMeasure = () => { if (!measureFrame) measureFrame = requestAnimationFrame(measure); };
    if (stack) {
        const resizeObserver = new ResizeObserver(requestMeasure);
        panels.forEach(panel => resizeObserver.observe(panel.element));
        stack.addEventListener('focusin', event => {
            if (!keyboardMode && !event.target.matches(':focus-visible')) return;
            stack.classList.add('is-reading');
            walls.forEach(wall => { wall.covered = false; syncWall(wall); });
        });
        stack.addEventListener('focusout', () => window.setTimeout(() => {
            if (!stack.contains(document.activeElement)) { stack.classList.remove('is-reading'); requestPaint(); }
        }, 0));
        addEventListener('scroll', () => {
            const now = performance.now();
            targetVelocity = clamp((scrollY - previousScroll) / Math.max((now - scrollTime) / 1000, .016), -1800, 1800);
            previousScroll = scrollY;
            scrollTime = now;
            requestPaint();
        }, { passive: true });
        addEventListener('resize', requestMeasure, { passive: true });
        addEventListener('keydown', event => {
            if (event.key !== 'Tab') return;
            keyboardMode = true;
            if (stack.contains(document.activeElement)) stack.classList.add('is-reading');
        });
        addEventListener('pointerdown', () => {
            keyboardMode = false;
            stack.classList.remove('is-reading');
            requestPaint();
        }, { passive: true });
        addEventListener('load', requestMeasure, { once: true });
        document.fonts?.ready.then(requestMeasure);
    }
    const updatePreference = () => {
        if (frame) cancelAnimationFrame(frame);
        frame = null;
        targetVelocity = 0;
        velocity = 0;
        stack?.classList.toggle('is-overlapping', allowsMotion());
        walls.forEach(buildWall);
        requestMeasure();
    };
    reduced.addEventListener('change', updatePreference);
    document.addEventListener('visibilitychange', () => {
        if (document.hidden && frame) { cancelAnimationFrame(frame); frame = null; }
        walls.forEach(syncWall);
        if (!document.hidden) { previousScroll = scrollY; scrollTime = performance.now(); requestPaint(); }
    });
    addEventListener('pagehide', () => { leaving = true; walls.forEach(syncWall); if (frame) cancelAnimationFrame(frame); frame = null; });
    addEventListener('pageshow', () => { leaving = false; walls.forEach(syncWall); requestMeasure(); });
    updatePreference();
});
