(() => {
    const expiry = document.querySelector('[data-expires-at]');
    const resend = document.querySelector('[data-resend-at]');
    const status = document.getElementById('otp-resend-countdown');
    const en = (document.documentElement.lang || 'id').toLowerCase().startsWith('en');
    const I18N = window.BJ_I18N || {};
    const tr = (key, fallback, vars = {}) => Object.entries(vars).reduce((text, [name, value]) => text.replace(':' + name, value), I18N[key] ?? fallback);
    const t = {
        valid: (m, s) => tr('otp_valid', en ? 'Code valid for :menit::detik more.' : 'Kode berlaku :menit::detik lagi.', { menit: m, detik: s }),
        expired: tr('otp_expired', en ? 'The code has expired or was not sent. Please request a new code.' : 'Kode kedaluwarsa atau belum terkirim. Silakan kirim ulang kode.'),
        wait: (w) => tr('otp_wait', en ? 'Resend (:detik s)' : 'Kirim ulang (:detik detik)', { detik: w }),
        resend: tr('otp_resend', en ? 'Resend code' : 'Kirim ulang kode'),
        ready: tr('otp_ready', en ? 'You can request a new code.' : 'Anda dapat meminta kode baru.'),
    };
    if (!expiry || !resend || !status) return;

    const update = () => {
        const now = Math.floor(Date.now() / 1000);
        const remaining = Math.max(0, Number(expiry.dataset.expiresAt) - now);
        expiry.textContent = remaining > 0
            ? t.valid(Math.floor(remaining / 60), String(remaining % 60).padStart(2, '0'))
            : t.expired;
        const wait = Math.max(0, Number(resend.dataset.resendAt) - now);
        resend.disabled = wait > 0;
        // Keep the countdown visual; announce only when resending becomes available.
        resend.textContent = wait > 0 ? t.wait(wait) : t.resend;
        if (wait === 0 && status.textContent === '') status.textContent = t.ready;
    };
    update();
    window.setInterval(update, 1000);
})();
