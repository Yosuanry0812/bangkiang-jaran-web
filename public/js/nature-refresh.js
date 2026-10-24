document.addEventListener('DOMContentLoaded', () => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    document.querySelectorAll('[data-photo-deck]').forEach(deck => {
        const slides = [...deck.querySelectorAll('[data-deck-slide]')];
        if (slides.length < 2) return;
        const stage = deck.querySelector('[data-deck-stage]');
        const pause = deck.querySelector('[data-deck-pause]');
        const counter = deck.querySelector('[data-deck-counter]');
        let current = 0;
        let paused = reducedMotion.matches;
        let inView = false;
        let hovered = false;
        let timer;
        let pointerStart = null;

        const render = (manual = false) => {
            slides.forEach((slide, index) => {
                let offset = (index - current + slides.length) % slides.length;
                if (offset > slides.length / 2) offset -= slides.length;
                slide.style.setProperty('--deck-offset', String(offset));
                slide.style.setProperty('--deck-distance', String(Math.abs(offset)));
                slide.classList.toggle('is-active', offset === 0);
                slide.setAttribute('aria-hidden', String(offset !== 0));
                slide.style.zIndex = String(slides.length - Math.abs(offset));
            });
            counter.setAttribute('aria-live', manual ? 'polite' : 'off');
            counter.textContent = `${current + 1} / ${slides.length}`;
        };
        const schedule = () => {
            window.clearTimeout(timer);
            if (paused || reducedMotion.matches || !inView || hovered || document.hidden || deck.contains(document.activeElement)) return;
            timer = window.setTimeout(() => { current = (current + 1) % slides.length; render(); schedule(); }, 5500);
        };
        const move = step => { current = (current + step + slides.length) % slides.length; render(true); schedule(); };
        const updatePause = () => {
            pause.setAttribute('aria-pressed', String(paused));
            pause.setAttribute('aria-label', paused ? pause.dataset.playLabel : pause.dataset.pauseLabel);
            pause.querySelector('span').textContent = paused ? 'play_arrow' : 'pause';
            schedule();
        };
        deck.classList.add('is-ready');
        render();
        updatePause();
        deck.querySelector('[data-deck-prev]').addEventListener('click', () => move(-1));
        deck.querySelector('[data-deck-next]').addEventListener('click', () => move(1));
        pause.addEventListener('click', () => { paused = !paused; updatePause(); });
        stage.addEventListener('keydown', event => {
            if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
            event.preventDefault();
            if (event.key === 'Home') current = 0;
            else if (event.key === 'End') current = slides.length - 1;
            else current = (current + (event.key === 'ArrowRight' ? 1 : -1) + slides.length) % slides.length;
            render(true); schedule();
        });
        stage.addEventListener('pointerdown', event => {
            if (!event.isPrimary || (event.pointerType === 'mouse' && event.button !== 0)) return;
            pointerStart = { x: event.clientX, y: event.clientY };
            stage.setPointerCapture(event.pointerId);
        });
        stage.addEventListener('pointerup', event => {
            if (!pointerStart) return;
            const deltaX = event.clientX - pointerStart.x;
            const deltaY = event.clientY - pointerStart.y;
            pointerStart = null;
            if (Math.abs(deltaX) > 45 && Math.abs(deltaX) > Math.abs(deltaY)) move(deltaX < 0 ? 1 : -1);
        });
        stage.addEventListener('pointercancel', () => { pointerStart = null; });
        deck.addEventListener('pointerenter', () => { hovered = true; schedule(); });
        deck.addEventListener('pointerleave', () => { hovered = false; schedule(); });
        deck.addEventListener('focusin', schedule);
        deck.addEventListener('focusout', () => window.setTimeout(schedule, 0));
        document.addEventListener('visibilitychange', schedule);
        reducedMotion.addEventListener('change', () => { if (reducedMotion.matches) paused = true; updatePause(); });
        new IntersectionObserver(entries => { inView = entries[0].isIntersecting; schedule(); }, { threshold: 0.2 }).observe(deck);
    });

    const filters = [...document.querySelectorAll('[data-history-filter]')];
    const cards = [...document.querySelectorAll('[data-history-status]')];
    filters.forEach(button => button.addEventListener('click', () => {
        const filter = button.dataset.historyFilter;
        filters.forEach(item => item.setAttribute('aria-pressed', String(item === button)));
        cards.forEach(card => { card.hidden = filter !== 'all' && card.dataset.historyStatus !== filter; });
        const visible = cards.filter(card => !card.hidden).length;
        const empty = document.querySelector('[data-history-filter-empty]');
        if (empty) empty.hidden = visible > 0;
        const count = document.querySelector('[data-history-count]');
        if (count) count.textContent = String(visible);
    }));
});
