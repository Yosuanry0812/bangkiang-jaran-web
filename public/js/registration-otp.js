(() => {
    const expiry = document.querySelector('[data-expires-at]');
    const resend = document.querySelector('[data-resend-at]');
    const status = document.getElementById('otp-resend-countdown');
    const en = (document.documentElement.lang || 'id').toLowerCase().startsWith('en');
    const t = en ? {
        valid: (m, s) => `Code valid for ${m}:${s} more.`,
        expired: 'The code has expired or was not sent. Please request a new code.',
        wait: (w) => `Resend (${w} s)`,
        resend: 'Resend code',
        ready: 'You can request a new code.',
    } : {
        valid: (m, s) => `Kode berlaku ${m}:${s} lagi.`,
        expired: 'Kode kedaluwarsa atau belum terkirim. Silakan kirim ulang kode.',
        wait: (w) => `Kirim ulang (${w} detik)`,
        resend: 'Kirim ulang kode',
        ready: 'Anda dapat meminta kode baru.',
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
