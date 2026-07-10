# E-Tourism Bangkiang Jaran Waterfall

Sistem pemesanan tiket online dan promosi wisata untuk **Bangkiang Jaran Waterfall**, Desa Bakbakan, Gianyar, Bali.

## Fitur Utama

- **Promosi Wisata**: Landing page, informasi & sejarah, galeri foto, lokasi via Google Maps
- **Pemesanan Tiket Online**: Pilih tiket, tentukan tanggal, kalkulasi otomatis, kode booking unik
- **Pembayaran Manual**: Transfer bank/QRIS, upload bukti, verifikasi oleh pengelola
- **Panel Pengelola**: Dashboard, kelola konten/galeri/tiket, verifikasi pembayaran, cetak laporan PDF
- **Notifikasi Email**: Registrasi, konfirmasi pemesanan, verifikasi pembayaran (valid/ditolak)
- **Keamanan**: Role-based access, CSRF, XSS prevention, IDOR protection, rate limiting, SQL injection prevention, soft delete

## Tech Stack

- **Backend**: Laravel 9 (PHP 8.0+)
- **Database**: MySQL (via XAMPP)
- **Frontend**: Blade + Tailwind CSS + Alpine.js
- **Maps**: Google Maps Embed (iframe)
- **PDF**: DomPDF (barryvdh/laravel-dompdf)
- **Auth**: Laravel Breeze (Blade, dengan kustomisasi 2 role)

## Persyaratan Sistem

- PHP 8.0+
- Composer 2.x
- MySQL 5.7+ / MariaDB 10.3+
- XAMPP (atau web server Apache + MySQL)

## Instalasi

### 1. Clone / Copy Project

```bash
cd C:/xampp/htdocs/bangkiang-jaran-web
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Konfigurasi Environment

```bash
cp .env.example .env
```

Edit file `.env` dan sesuaikan konfigurasi database:

```
DB_DATABASE=bangkiang_jaran
DB_USERNAME=root
DB_PASSWORD=
```

Lalu generate key:

```bash
php artisan key:generate
```

### 4. Buat Database

Buka phpMyAdmin (http://localhost/phpmyadmin) atau jalankan:

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS bangkiang_jaran CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 5. Migrasi & Seeder

```bash
php artisan migrate --seed
```

### 6. Storage Link

```bash
php artisan storage:link
```

### 7. Jalankan Server

```bash
php artisan serve
```

Akses: http://localhost:8000

## Akun Demo

### Pengelola (Admin)
- **Email**: pengelola@bangkiangjaran.com
- **Username**: pengelola
- **Password**: Password123

### Wisatawan
- **Email**: wisatawan@demo.com
- **Username**: wisatawan
- **Password**: Password123

## Struktur Direktori

```
app/
  Http/
    Controllers/
      Auth/           # Auth controllers (Breeze customization)
      Wisatawan/      # Landing, Tiket, Pemesanan, Pembayaran
      Pengelola/      # Dashboard, Konten, Galeri, Tiket, Verifikasi, Laporan, User
    Middleware/
      CheckRole.php   # Role-based access middleware
    Requests/
      Auth/
        LoginRequest.php  # Custom login (email/username) + rate limiting
  Mail/               # Mailable classes (Welcome, Pemesanan, Pembayaran)
  Models/             # User, Tiket, Pemesanan, Pembayaran, Galeri, Konten, Laporan
database/
  migrations/         # 7 migration files
  seeders/
    DatabaseSeeder.php # Demo data seeder
resources/views/
  auth/               # Login, register, forgot password
  emails/             # Email templates (HTML)
  layouts/            # app.blade.php, guest.blade.php
  wisatawan/          # Landing, informasi, tiket, pemesanan, pembayaran, riwayat
  pengelola/          # Dashboard, konten, galeri, tiket, verifikasi, laporan, user
routes/
  web.php             # Semua route definitions
  auth.php            # Auth routes
```

## Yang Perlu Kamu Kerjakan Manual

1. **Buat database** `bangkiang_jaran` di phpMyAdmin atau MySQL CLI
2. **Upload foto asli** ke `storage/app/public/galeri/` untuk mengganti placeholder
3. **Isi Google Maps API key** di `.env` jika ingin embed map yang akurat (bisa juga pakai iframe gratis dari Google Maps)
4. **Konfigurasi email** di `.env` (ganti MAIL_MAILER=log menjadi MAIL_MAILER=smtp dan isi SMTP credentials) jika ingin email benar-benar terkirim
5. **Jalankan** `php artisan storage:link` agar foto bisa diakses dari web
6. **Upload logo/hero image** - hero section saat ini hanya gradient background, upload foto asli air terjun untuk tampilan lebih maksimal
7. Untuk **production**, set `APP_DEBUG=false` dan `APP_ENV=production` di `.env`

## Keamanan

- Password di-hash dengan bcrypt
- Role-based middleware (CheckRole)
- CSRF protection di semua form
- SQL Injection prevention via Eloquent ORM
- XSS prevention via Blade `{{ }}` escaping
- IDOR protection (cek ownership data di setiap query)
- Rate limiting (login 5x attempts, pemesanan 10x per menit)
- File upload validasi (MIME type, ekstensi, ukuran)
- Soft delete pada data penting
- Session regenerasi setelah login
- Database transaction + row lock untuk cegah overbooking

## Backup Database

Backup database secara berkala sangat penting untuk keamanan data. Berikut cara backup:

### Via phpMyAdmin
1. Buka http://localhost/phpmyadmin
2. Pilih database `bangkiang_jaran`
3. Klik tab "Export"
4. Pilih metode "Quick" atau "Custom"
5. Klik "Go" untuk mendownload file .sql

### Via Command Line (mysqldump)
```bash
# Backup
mysqldump -u root bangkiang_jaran > backup_bangkiang_jaran_$(date +%Y%m%d).sql

# Restore
mysql -u root bangkiang_jaran < backup_bangkiang_jaran_20240101.sql
```

### Via Artisan (recommended, jika sudah ada package backup)
```bash
# Install spatie/laravel-backup untuk backup otomatis terjadwal
composer require spatie/laravel-backup
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
# Konfigurasi di config/backup.php lalu jalankan:
php artisan backup:run
```

### Otomatisasi
Untuk production, buat cron job yang menjalankan backup setiap hari:
```
# Backup jam 2 pagi setiap hari
0 2 * * * /usr/bin/mysqldump -u root bangkiang_jaran > /backups/bangkiang_jaran_$(date +\%Y\%m\%d).sql
```

## Black Box Testing

Lihat file `BLACKBOX_TESTING.md` untuk checklist pengujian lengkap (61 skenario).

## Lisensi

Hak cipta (c) 2024 Pengelola Bangkiang Jaran Waterfall. Dibuat untuk keperluan demonstrasi produk jasa.
