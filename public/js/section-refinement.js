document.addEventListener('DOMContentLoaded', () => {
    const english = document.documentElement.lang.startsWith('en');
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');

    document.querySelectorAll('[data-bj-route-planner]').forEach(planner => {
        const destination = planner.dataset.destination;
        const placeId = planner.dataset.placeId;
        const map = planner.querySelector('[data-bj-route-map]');
        const open = planner.querySelector('[data-route-open]');
        const selection = planner.querySelector('[data-route-selection]');
        const status = planner.querySelector('[data-route-status]');
        const buttons = [...planner.querySelectorAll('[data-route-origin]')];
        let requestId = 0;
        const message = (text, error = false) => {
            status.textContent = text;
            status.classList.toggle('is-error', error);
        };
        const release = () => buttons.forEach(button => { button.removeAttribute('aria-busy'); button.disabled = false; });
        const select = (button, origin) => {
            buttons.forEach(item => {
                const active = item === button;
                item.classList.toggle('is-active', active);
                item.setAttribute('aria-pressed', String(active));
                const icon = item.querySelector('i');
                if (icon) icon.textContent = active ? 'check' : 'radio_button_unchecked';
            });
            selection.textContent = button.dataset.routeLabel;
            const route = new URL('https://www.google.com/maps/dir/');
            route.search = new URLSearchParams({ api: '1', origin, destination, destination_place_id: placeId, travelmode: 'driving' });
            open.href = route.href;
            // Named POI prevents reverse geocoding from selecting a nearby temple.
            const embed = new URL('https://www.google.com/maps');
            embed.search = new URLSearchParams({ saddr: origin, daddr: destination, output: 'embed' });
            map.src = embed.href;
        };
        buttons.forEach(button => button.addEventListener('click', async () => {
            const currentRequest = ++requestId;
            release();
            if (!button.hasAttribute('data-route-current')) {
                select(button, button.dataset.routeOrigin);
                message(english ? 'Open Google Maps for turn-by-turn directions.' : 'Buka Google Maps untuk petunjuk perjalanan lengkap.');
                return;
            }
            if (!window.isSecureContext || !navigator.geolocation) {
                message(english ? 'Device location is unavailable here. Use HTTPS or choose a starting city.' : 'Lokasi perangkat tidak tersedia. Gunakan HTTPS atau pilih kota keberangkatan.', true);
                return;
            }
            button.disabled = true;
            button.setAttribute('aria-busy', 'true');
            message(english ? 'Allow location in your browser. Finding a fresh position…' : 'Izinkan lokasi di browser. Sedang mencari posisi terbaru…');
            try {
                const permission = await navigator.permissions?.query({ name: 'geolocation' });
                if (currentRequest !== requestId) return;
                if (permission?.state === 'denied') {
                    release();
                    message(english ? 'Location is blocked. Allow it in browser site settings, then try again.' : 'Izin lokasi diblokir. Izinkan melalui pengaturan situs di browser, lalu coba lagi.', true);
                    return;
                }
            } catch { /* Browsers without Permissions API still request geolocation normally. */ }
            if (currentRequest !== requestId) return;
            navigator.geolocation.getCurrentPosition(position => {
                if (currentRequest !== requestId) return;
                release();
                const { latitude, longitude, accuracy } = position.coords;
                if (!Number.isFinite(latitude) || !Number.isFinite(longitude) || Math.abs(latitude) > 90 || Math.abs(longitude) > 180) {
                    message(english ? 'The device returned an invalid position. Try again.' : 'Posisi dari perangkat tidak valid. Silakan coba lagi.', true);
                    return;
                }
                select(button, `${latitude},${longitude}`);
                if (Number.isFinite(accuracy) && accuracy >= 0) {
                    const metres = Math.ceil(accuracy);
                    message(english ? `Location found. Approximate accuracy: ${metres} m.${metres > 500 ? ' Check your starting point in Google Maps.' : ''}` : `Lokasi ditemukan. Perkiraan akurasi: ${metres} m.${metres > 500 ? ' Periksa titik awal di Google Maps.' : ''}`);
                } else {
                    message(english ? 'Location found. Check the starting point in Google Maps.' : 'Lokasi ditemukan. Periksa titik awal di Google Maps.');
                }
            }, error => {
                if (currentRequest !== requestId) return;
                release();
                const errors = english
                    ? { 1: 'Location permission was declined. Allow it in site settings or choose a city.', 2: 'Your device cannot determine its location. Try again or choose a city.', 3: 'Finding your location timed out. Try again outdoors or choose a city.' }
                    : { 1: 'Izin lokasi ditolak. Izinkan lewat pengaturan situs atau pilih kota.', 2: 'Perangkat belum dapat menentukan lokasi. Coba lagi atau pilih kota.', 3: 'Pencarian lokasi melewati batas waktu. Coba lagi di area terbuka atau pilih kota.' };
                message(errors[error.code] || errors[2], true);
            }, { enableHighAccuracy: true, maximumAge: 0, timeout: 15000 });
        }));
    });

    const title = document.querySelector('[data-hero-title-motion]');
    const titleButton = document.querySelector('[data-hero-title-toggle]');
    if (title && titleButton && Element.prototype.animate) {
        let paused = false;
        let visible = false;
        let animation;
        const sync = () => {
            titleButton.hidden = reduced.matches || navigator.connection?.saveData === true;
            if (!animation || titleButton.hidden || paused || !visible || document.hidden) animation?.pause();
            else animation.play();
            titleButton.setAttribute('aria-pressed', String(paused));
            titleButton.setAttribute('aria-label', paused ? titleButton.dataset.playLabel : titleButton.dataset.pauseLabel);
            titleButton.querySelector('span').textContent = paused ? 'play_arrow' : 'pause';
        };
        const setup = () => {
            animation?.cancel();
            animation = null;
            if (!reduced.matches && !navigator.connection?.saveData) {
                animation = title.animate([{ transform: 'translate3d(0,0,0)' }, { transform: `translate3d(${innerWidth < 768 ? 4 : 9}px,0,0)` }], { duration: 7000, direction: 'alternate', iterations: Infinity, easing: 'ease-in-out' });
                animation.pause();
            }
            sync();
        };
        titleButton.addEventListener('click', () => { paused = !paused; sync(); });
        new IntersectionObserver(entries => { visible = entries[0].isIntersecting; sync(); }).observe(title.closest('section'));
        document.addEventListener('visibilitychange', sync);
        reduced.addEventListener('change', setup);
        setup();
    }

    const watcher = document.querySelector('[data-booking-watch], [data-bookings-watch]');
    if (watcher) {
        const url = watcher.dataset.bookingWatch || watcher.dataset.bookingsWatch;
        const baseline = watcher.dataset.statusToken || JSON.parse(watcher.dataset.statusTokens || '{}');
        let timer;
        let busy = false;
        let stopped = false;
        let delay = 6000;
        const poll = async () => {
            clearTimeout(timer);
            if (busy || stopped || document.hidden) return;
            busy = true;
            try {
                const response = await fetch(url, { credentials: 'same-origin', cache: 'no-store', headers: { Accept: 'application/json' } });
                if ([401, 403, 404].includes(response.status)) { stopped = true; return; }
                if (!response.ok) throw new Error('Status request failed');
                const result = await response.json();
                const changed = typeof baseline === 'string' ? result.token !== baseline : JSON.stringify(result) !== JSON.stringify(baseline);
                if (changed) {
                    stopped = true;
                    if (watcher.dataset.bookingDestination) location.assign(watcher.dataset.bookingDestination);
                    else location.reload();
                    return;
                }
                delay = 6000;
            } catch { delay = Math.min(delay * 2, 60000); }
            finally {
                busy = false;
                if (!stopped && !document.hidden) timer = setTimeout(poll, delay);
            }
        };
        document.addEventListener('visibilitychange', () => { clearTimeout(timer); if (!document.hidden) poll(); });
        addEventListener('pagehide', () => { stopped = true; clearTimeout(timer); });
        addEventListener('pageshow', () => { stopped = false; clearTimeout(timer); timer = setTimeout(poll, delay); });
        timer = setTimeout(poll, delay);
    }
});
