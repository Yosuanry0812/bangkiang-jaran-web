#!/bin/sh
set -e
cd /var/www/html

# Storage symlink (galeri, bukti bayar) — idempotent
[ -L public/storage ] || php artisan storage:link

# Migrate — idempotent, aman dijalankan tiap boot
php artisan migrate --force

# Cache produksi
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
