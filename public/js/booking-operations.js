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
})();
