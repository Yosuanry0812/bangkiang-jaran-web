document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-photo-exhibition]').forEach(exhibition => {
        const prints = [...exhibition.querySelectorAll('[data-exhibition-print]')];
        exhibition.querySelectorAll('.bj-exhibition__image img').forEach(image => {
            const showError = () => {
                image.hidden = true;
                image.nextElementSibling.hidden = false;
            };
            image.addEventListener('error', showError, { once: true });
            if (image.complete && !image.naturalWidth) showError();
        });
        exhibition.querySelectorAll('[data-exhibition-thumb]').forEach((button, index) => {
            const image = button.querySelector('img');
            const showError = () => {
                image.hidden = true;
                button.classList.add('has-error');
                button.dataset.photoNumber = String(index + 1);
            };
            image.addEventListener('error', showError, { once: true });
            if (image.complete && !image.naturalWidth) showError();
        });
        if (prints.length < 2) return;

        const scene = exhibition.querySelector('[data-exhibition-scene]');
        const counter = exhibition.querySelector('[data-exhibition-counter]');
        const thumbnails = [...exhibition.querySelectorAll('[data-exhibition-thumb]')];
        let current = 0;
        let gesture = null;
        let suppressClick = false;

        const render = () => {
            prints.forEach((print, index) => {
                let offset = (index - current + prints.length) % prints.length;
                if (offset > prints.length / 2) offset -= prints.length;
                const distance = Math.abs(offset);
                print.style.setProperty('--print-offset', String(offset));
                print.style.setProperty('--print-distance', String(distance));
                print.style.zIndex = String(prints.length - distance);
                print.classList.toggle('is-current', offset === 0);
                print.toggleAttribute('data-distant', distance > 2);
                // Visible side prints remain selectable. Distant prints leave the accessibility tree.
                print.setAttribute('aria-hidden', String(distance > 2));
                print.querySelector('[data-exhibition-select]').tabIndex = distance > 0 && distance <= 2 ? 0 : -1;
            });
            thumbnails.forEach((button, index) => button.setAttribute('aria-pressed', String(index === current)));
            counter.textContent = `${current + 1} / ${prints.length}`;
        };
        const select = index => {
            current = (index + prints.length) % prints.length;
            render();
            const strip = thumbnails[current].parentElement;
            const button = thumbnails[current];
            strip.scrollLeft = Math.max(0, button.offsetLeft - strip.offsetLeft - strip.clientWidth / 2 + button.clientWidth / 2);
        };

        exhibition.classList.add('is-ready');
        exhibition.querySelector('[data-exhibition-navigation]').hidden = false;
        exhibition.querySelector('[data-exhibition-thumbnails]').hidden = false;
        render();
        exhibition.querySelector('[data-exhibition-prev]').addEventListener('click', () => select(current - 1));
        exhibition.querySelector('[data-exhibition-next]').addEventListener('click', () => select(current + 1));
        thumbnails.forEach((button, index) => button.addEventListener('click', () => select(index)));
        scene.addEventListener('click', event => {
            if (suppressClick && event.detail > 0) { event.preventDefault(); return; }
            const button = event.target.closest('[data-exhibition-select]');
            if (!button) return;
            select(Number(button.dataset.exhibitionSelect));
            // The selected overlay becomes hidden; keep keyboard focus on the gallery.
            scene.focus({ preventScroll: true });
        });
        scene.addEventListener('keydown', event => {
            if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
            event.preventDefault();
            select(event.key === 'Home' ? 0 : event.key === 'End' ? prints.length - 1 : current + (event.key === 'ArrowRight' ? 1 : -1));
            if (event.target !== scene) scene.focus({ preventScroll: true });
        });
        scene.addEventListener('pointerdown', event => {
            if (!event.isPrimary || (event.pointerType === 'mouse' && event.button !== 0)) return;
            suppressClick = false;
            gesture = { id: event.pointerId, x: event.clientX, y: event.clientY };
        });
        scene.addEventListener('pointermove', event => {
            if (!gesture || event.pointerId !== gesture.id) return;
            const dx = event.clientX - gesture.x;
            const dy = event.clientY - gesture.y;
            if (Math.abs(dy) > 12 && Math.abs(dy) > Math.abs(dx)) { gesture = null; return; }
            if (Math.abs(dx) > 12 && Math.abs(dx) > Math.abs(dy)) {
                suppressClick = true;
                if (!scene.hasPointerCapture(event.pointerId)) scene.setPointerCapture(event.pointerId);
            }
        });
        scene.addEventListener('pointerup', event => {
            if (!gesture || event.pointerId !== gesture.id) return;
            const dx = event.clientX - gesture.x;
            const dy = event.clientY - gesture.y;
            gesture = null;
            if (scene.hasPointerCapture(event.pointerId)) scene.releasePointerCapture(event.pointerId);
            if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) select(current + (dx < 0 ? 1 : -1));
            window.setTimeout(() => { suppressClick = false; }, 0);
        });
        scene.addEventListener('pointercancel', () => { gesture = null; });
        scene.addEventListener('lostpointercapture', () => { gesture = null; });
        scene.addEventListener('pointerleave', () => { if (!suppressClick) gesture = null; });
    });
});
