document.addEventListener('DOMContentLoaded', () => {
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    const connection = navigator.connection;
    const slideshows = [];
    let photoOpen = false;
    let leaving = false;

    document.querySelectorAll('[data-photo-slideshow]').forEach(element => {
        const photos = JSON.parse(element.dataset.slideshowPhotos);
        let visible = element.querySelector('img');
        const message = element.querySelector('p');
        const link = element.querySelector('[data-photo-open]');
        const failed = new Set();
        let index = Math.max(0, photos.findIndex(photo => photo.url === visible.src));
        let timer = null;
        let changing = false;
        let inView = false;
        let focused = false;
        const allowed = () => typeof element.animate === 'function' && !reduced.matches && !connection?.saveData && !document.hidden && !leaving && !photoOpen && inView && !focused;
        const describe = photo => {
            link.href = photo.url;
            link.dataset.photoCaption = photo.caption;
            link.setAttribute('aria-label', `${document.documentElement.lang === 'en' ? 'Enlarge photo: ' : 'Perbesar foto: '}${photo.caption}`);
        };
        const fallback = () => {
            if (!visible.naturalWidth) { visible.hidden = true; message.hidden = false; }
        };
        visible.addEventListener('error', fallback, { once: true });
        if (visible.complete) fallback();

        const sync = () => {
            clearTimeout(timer);
            timer = null;
            element.dataset.slideshowState = allowed() && photos.length > 1 ? 'playing' : 'static';
            if (allowed() && photos.length > 1 && !changing) timer = setTimeout(next, 4200 + Number(element.dataset.slideshowIndex) * 350);
        };
        async function next() {
            timer = null;
            if (!allowed() || changing) return;
            changing = true;
            try {
                for (let step = 1; step < photos.length; step++) {
                    const candidate = (index + step) % photos.length;
                    const photo = photos[candidate];
                    if (failed.has(photo.url)) continue;
                    const incoming = new Image();
                    incoming.alt = photo.caption;
                    incoming.width = 1200;
                    incoming.height = 800;
                    incoming.decoding = 'async';
                    incoming.src = photo.url;
                    try { await incoming.decode(); } catch { failed.add(photo.url); continue; }
                    if (!allowed()) return;
                    incoming.style.opacity = '0';
                    visible.after(incoming);
                    // Keep the outgoing image opaque throughout the dissolve.
                    const fade = incoming.animate([{ opacity: 0 }, { opacity: 1 }], { duration: 850, easing: 'ease-in-out', fill: 'forwards' });
                    const stop = () => fade.finish();
                    reduced.addEventListener('change', stop, { once: true });
                    await fade.finished;
                    reduced.removeEventListener('change', stop);
                    incoming.style.opacity = '1';
                    fade.cancel();
                    visible.remove();
                    visible = incoming;
                    message.hidden = true;
                    index = candidate;
                    describe(photo);
                    break;
                }
            } finally { changing = false; sync(); }
        }
        element.addEventListener('focusin', () => { focused = true; sync(); });
        element.addEventListener('focusout', () => { focused = false; sync(); });
        new IntersectionObserver(entries => { inView = entries[0].isIntersecting; sync(); }, { threshold: .15 }).observe(element);
        reduced.addEventListener('change', sync);
        connection?.addEventListener('change', sync);
        slideshows.push(sync);
    });
    document.addEventListener('visibilitychange', () => slideshows.forEach(sync => sync()));
    document.addEventListener('bj-photo-viewer-change', event => { photoOpen = event.detail.open; slideshows.forEach(sync => sync()); });
    window.addEventListener('pagehide', () => { leaving = true; slideshows.forEach(sync => sync()); });
    window.addEventListener('pageshow', () => { leaving = false; slideshows.forEach(sync => sync()); });
});
