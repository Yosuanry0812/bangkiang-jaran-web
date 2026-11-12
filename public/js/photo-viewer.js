document.addEventListener('DOMContentLoaded', () => {
    const viewer = document.getElementById('bj-photo-viewer');
    if (!viewer || typeof viewer.showModal !== 'function') return;

    const image = viewer.querySelector('img');
    const stage = viewer.querySelector('[data-photo-stage]');
    const status = viewer.querySelector('[data-photo-status]');
    let opener = null;
    let scrollPosition = 0;
    let previousBodyStyle = null;

    const loaded = () => {
        if (!viewer.open || !image.naturalWidth) return;
        image.hidden = false;
        status.hidden = true;
    };
    image.addEventListener('load', loaded);
    image.addEventListener('error', () => {
        if (!viewer.open) return;
        image.hidden = true;
        status.textContent = viewer.dataset.errorLabel;
        status.hidden = false;
    });

    // One listener for every photo; the original link still works without JS.
    document.addEventListener('click', event => {
        const link = event.target.closest('a[data-photo-open]');
        if (!link || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        // Animated copies are pointer-only; focusing them would hide the lanes
        // and pause the gallery when the photo closes.
        opener = link.closest('[data-columns-lanes]') ? null : link;
        image.hidden = true;
        status.textContent = viewer.dataset.loadingLabel;
        status.hidden = false;
        const caption = link.dataset.photoCaption || '';
        image.alt = caption;
        viewer.setAttribute('aria-label', caption || (document.documentElement.lang === 'en' ? 'Full photo' : 'Foto lengkap'));
        scrollPosition = window.scrollY;
        previousBodyStyle = document.body.getAttribute('style');
        // Fixed body also prevents background scrolling in mobile Safari.
        Object.assign(document.body.style, { position: 'fixed', top: `-${scrollPosition}px`, width: '100%', overflow: 'hidden' });
        viewer.showModal();
        document.dispatchEvent(new CustomEvent('bj-photo-viewer-change', { detail: { open: true } }));
        image.src = link.href;
        if (image.complete && image.naturalWidth) loaded();
    });

    viewer.querySelector('[data-photo-close]').addEventListener('click', () => viewer.close());
    let backdropPress = false;
    const isBackdrop = target => target === viewer || target === stage;
    viewer.addEventListener('pointerdown', event => { backdropPress = isBackdrop(event.target); });
    viewer.addEventListener('click', event => { if (backdropPress && isBackdrop(event.target)) viewer.close(); });
    viewer.addEventListener('close', () => {
        image.removeAttribute('src');
        image.hidden = true;
        if (previousBodyStyle === null) document.body.removeAttribute('style');
        else document.body.setAttribute('style', previousBodyStyle);
        window.scrollTo({ top: scrollPosition, behavior: 'instant' });
        opener?.focus({ preventScroll: true });
        document.dispatchEvent(new CustomEvent('bj-photo-viewer-change', { detail: { open: false } }));
    });
    window.addEventListener('pagehide', () => { if (viewer.open) viewer.close(); });
});
