(() => {
    'use strict';
    document.querySelectorAll('form[data-booking-confirm]').forEach(form => {
        form.addEventListener('submit', event => {
            if (!window.confirm(form.dataset.bookingConfirm)) event.preventDefault();
        });
    });
    const timers = [...document.querySelectorAll('[data-booking-countdown]')];
    const started = performance.now();
    const tick = () => {
        let pending = false;
        timers.forEach(node => {
            const remaining = Math.max(0, Number(node.dataset.bookingCountdown) - Number(node.dataset.serverNow) - Math.floor((performance.now() - started) / 1000));
            node.textContent = remaining > 0 ? `${Math.floor(remaining / 3600)}:${String(Math.floor(remaining % 3600 / 60)).padStart(2, '0')}:${String(remaining % 60).padStart(2, '0')}` : node.dataset.expiredLabel;
            pending ||= remaining > 0;
        });
        return pending;
    };
    let clock;
    const resume = () => {
        clearInterval(clock);
        if (!document.hidden && tick()) clock = setInterval(() => { if (!tick()) clearInterval(clock); }, 1000);
    };
    if (timers.length) { resume(); document.addEventListener('visibilitychange', resume); }
    const status = document.querySelector('[data-quota-status]');
    const date = document.querySelector('input[name="tgl_kunjungan"]');
    if (status && date) {
        let request;
        const load = async () => {
            request?.abort();
            if (!date.value) { status.textContent = ''; return; }
            const current = request = new AbortController();
            status.textContent = status.dataset.loading;
            try {
                const url = new URL(status.dataset.quotaStatus, location.origin);
                url.searchParams.set('date', date.value);
                const response = await fetch(url, { headers: { Accept: 'application/json' }, signal: current.signal, cache: 'no-store' });
                if (!response.ok) throw new Error('Capacity request failed');
                const data = await response.json();
                if (request !== current) return;
                status.textContent = data.remaining === null ? status.dataset.unlimited : `${status.dataset.remaining}: ${data.remaining}`;
            } catch (error) { if (error.name !== 'AbortError' && request === current) status.textContent = status.dataset.error; }
        };
        date.addEventListener('change', load);
        load();
    }
})();
