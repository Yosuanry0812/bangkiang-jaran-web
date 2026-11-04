(() => {
    const expiry = document.querySelector('[data-expires-at]');
    const resend = document.querySelector('[data-resend-at]');
    const status = document.getElementById('otp-resend-countdown');
    if (!expiry || !resend || !status) return;

    const update = () => {
        const now = Math.floor(Date.now() / 1000);
        const remaining = Math.max(0, Number(expiry.dataset.expiresAt) - now);
        expiry.textContent = remaining > 0
            ? `Kode berlaku ${Math.floor(remaining / 60)}:${String(remaining % 60).padStart(2, '0')} lagi.`
            : 'Kode kedaluwarsa atau belum terkirim. Silakan kirim ulang kode.';
        const wait = Math.max(0, Number(resend.dataset.resendAt) - now);
        resend.disabled = wait > 0;
        // Keep the countdown visual; announce only when resending becomes available.
        resend.textContent = wait > 0 ? `Kirim ulang (${wait} detik)` : 'Kirim ulang kode';
        if (wait === 0 && status.textContent === '') status.textContent = 'Anda dapat meminta kode baru.';
    };
    update();
    window.setInterval(update, 1000);
})();
