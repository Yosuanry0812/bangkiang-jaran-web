# E-Tourism Bangkiang Jaran Waterfall

> Sistem pemesanan tiket online + promosi wisata untuk **Bangkiang Jaran Waterfall**, Desa Bakbakan, Gianyar, Bali. Dua role: **Wisatawan** (publik) & **Pengelola** (admin). Build dengan Laravel + Blade + Tailwind + Alpine, Vite, MySQL.

![Laravel](https://img.shields.io/badge/Laravel-13-red) ![PHP](https://img.shields.io/badge/PHP-8.3-777BB4) ![Tailwind](https://img.shields.io/badge/Tailwind-3.1-38BDF8) ![MySQL](https://img.shields.io/badge/MySQL-8-4479A1) ![Docker](https://img.shields.io/badge/Docker-ready-2496ED)

---

## Daftar Isi

1. [Ringkasan & Tujuan](#1-ringkasan--tujuan)
2. [Demo & Akun](#2-demo--akun)
3. [Tech Stack Lengkap](#3-tech-stack-lengkap)
4. [Arsitektur & Alur Sistem](#4-arsitektur--alur-sistem)
5. [Skema Database](#5-skema-database)
6. [Fitur Lengkap per Role](#6-fitur-lengkap-per-role)
7. [Routing Lengkap](#7-routing-lengkap)
8. [Desain System — Tropical Sanctuary](#8-desain-system--tropical-sanctuary)
9. [Struktur Direktori](#9-struktur-direktori)
10. [Konfigurasi Environment](#10-konfigurasi-environment)
11. [Instalasi Lokal](#11-instalasi-lokal)
12. [Docker](#12-docker)
13. [Menjalankan Demo & Ngrok HTTPS](#13-menjalankan-demo--ngrok-https)
14. [Internasionalisasi (i18n)](#14-internasionalisasi-i18n)
15. [Email & Notifikasi](#15-email--notifikasi)
16. [Pemesanan → Pembayaran → Verifikasi → E-Ticket](#16-pemesanan--pembayaran--verifikasi--e-ticket)
17. [Scan Tiket (Check-in / Check-out)](#17-scan-tiket-check-in--check-out)
18. [Keamanan](#18-keamanan)
19. [Backup, Log & Maintenance](#19-backup-log--maintenance)
20. [Testing & QA](#20-testing--qa)
21. [Checklist Deploy Production](#21-checklist-deploy-production)
22. [Troubleshooting](#22-troubleshooting)
23. [Panduan Penerus — Ubah Apa di Mana](#23-panduan-penerus--ubah-apa-di-mana)
24. [Lisensi](#24-lisensi)

---

## 1. Ringkasan & Tujuan

Website melakukan dua fungsi utama:

- **Promosi**: landing editorial premium, galeri foto, informasi sejarah & fasilitas, lokasi (Google Maps embed), halaman Restoran "Kepulauan Rasa", daftar tiket.
- **Transaksi**: wisatawan pilih tiket → isi data pengunjung per orang → bayar (transfer/QRIS) → upload bukti → pengelola verifikasi → e-ticket per orang dengan `kode_tiket` unik + QR → scan di pintu masuk (check-in / check-out). Pengelola kelola tiket, galeri, laporan, user, dan entri offline.

Prinsip: **mobile-first, SEO-friendly Blade SSR, no SPA overhead, transaksi aman dengan lock & idempotency**.

---

## 2. Demo & Akun

### Akun Seeder (`database/seeders/DatabaseSeeder.php` + `DemoDataSeeder.php`)

| Role | Email | Username | Password | Catatan |
|------|-------|----------|----------|---------|
| Pengelola | `pengelola@bangkiangjaran.com` | `pengelola` | `Password123` | `role=pengelola` |
| Wisatawan | `wisatawan@demo.com` | `wisatawan` | `Password123` | `role=wisatawan` |
| Wisatawan (Google) | `yosuanry66@gmail.com` | `yosuanry66` | `null` (login Google) | `google_id` terisi, `login_count`, `last_login_method=google` |

> 12 data pemesanan + pembayaran demo dibuat oleh `DemoDataSeeder`.

### Akses Cepat

- Lokal: `http://localhost:8000`
- Setelah login otomatis redirect via `/redirect-after-login` → pengelola ke `pengelola.dashboard`, wisatawan ke `landing`.

---

## 3. Tech Stack Lengkap

| Layer | Teknologi | Versi / Catatan |
|-------|-----------|-----------------|
| Backend | **Laravel Framework** | `^13.0` (PHP `^8.3`) |
| Auth | **Laravel Breeze** (Blade) | `^2.4` — dikustom 2 role + login email/username |
| API Token | **Laravel Sanctum** | `^4.3` |
| OAuth | **Laravel Socialite** | `^5.28` — Google Login |
| Frontend | **Blade + Tailwind CSS + Alpine.js** | Tailwind `^3.1.0`, Alpine `^3.4.2`, `@tailwindcss/forms ^0.5.2` |
| Build | **Vite** | `^4.0.0` + `laravel-vite-plugin ^0.7.2`, `postcss ^8.4.6`, `autoprefixer ^10.4.2` |
| PDF | **barryvdh/laravel-dompdf** | `^3.1` — laporan kunjungan & transaksi |
| HTTP Client | **guzzlehttp/guzzle** | `^7.2` |
| DB | **MySQL / MariaDB** | `bangkiang_jaran`, `utf8mb4_unicode_ci`, timezone `Asia/Makassar` |
| Dev | **ngrok** | `^5.0.0-beta.2` — `npm run tunnel` / `start-demo.bat` |
| Container | **Docker** | `node:20-alpine` (build) + `php:8.3-apache` (runtime), `composer:2` |
| Testing | **PHPUnit** | `^11.5`, `fakerphp/faker ^1.23`, `mockery ^1.6`, `pint ^1.18`, `collision ^8.0`, `sail ^1.63` |

> `config/app.php`: `locale=id`, `fallback_locale=en`, `faker_locale=id_ID`, `timezone=Asia/Makassar`.

---

## 4. Arsitektur & Alur Sistem

```
Browser (Blade SSR + Alpine)
  → Routes (web.php + auth.php) → Middleware (auth, CheckRole, Localization, Throttle)
    → Controller (Wisatawan/*, Pengelola/*, Auth/*)
      → Model (Eloquent) → MySQL
      → Mail (Mailable) → log/smtp
      → Storage (public/galeri, bukti_bayar)
      → DomPDF (laporan)
```

**Alur pemesanan (happy path):**

```
[Landing] → [Tiket list/detail] → [Pemesanan: pilih tiket & tgl & qty] (throttle 10/menit)
  → [Data Diri: nama, gender, plat per tiket] → DB::transaction → Pemesanan (kode_booking unik) + N DetailPemesanan (kode_tiket unik)
    → [Pembayaran: pilih metode transfer/QRIS] → upload bukti (JPG/PNG/PDF 5MB)
      → Pembayaran status=pending → Pengelola verifikasi (valid/ditolak)
        → Pemesanan status=selesai → DetailPemesanan status=aktif → email PemesananBerhasil / PembayaranValid
          → [Riwayat/Detail] QR per tiket → [Scan pengelola] check-in (aktif→digunakan) / check-out
```

**Validasi ketat:** hanya tiket `tgl_kunjungan = hari ini` + `status=selesai` + `status_tiket=aktif` yang bisa di-scan.

---

## 5. Skema Database

29 migration di `database/migrations/`. Inti:

### `users`
| Kolom | Tipe | Ket |
|-------|------|-----|
| `id` | bigIncrements | PK |
| `name`, `username`, `email`, `phone` | string | `username`/`email` unique |
| `password` | string nullable | `null` untuk akun Google-only |
| `role` | `enum(pengelola,wisatawan)` | |
| `google_id` | string nullable | OAuth |
| `email_verified_at` | datetime nullable | |
| `last_login_at`, `last_login_method`, `login_count` | tracking | `manual`/`google` |
| `remember_token` | | |

### `tiket`
| Kolom | Tipe | Ket |
|-------|------|-----|
| `id_tiket` | PK | |
| `nama_tiket`, `nama_tiket_en` | string 100 | i18n via accessor |
| `harga` | decimal 10,2 | |
| `kategori` | string | `perorangan` (paket keluarga dihapus via `2026_07_25_210000`) |
| `status` | enum aktif/nonaktif | scope `aktif()` |
| `kuota` | **dihapus** | `2024_01_01_000009_drop_kuota_from_tiket_table` — sekarang unlimited/harian di logic |
| `deleted_at` | softDeletes | |

### `pemesanan`
| Kolom | Tipe |
|-------|------|
| `id_pemesanan` PK | |
| `id_user` FK→users.id | |
| `id_tiket` FK→tiket.id_tiket | tiket utama (agregat) |
| `tgl_kunjungan` date, indexed | |
| `jumlah` int | |
| `total_harga` decimal 10,2 | |
| `detail` json nullable (text/long) | |
| `status` enum `pending,diproses,selesai,dibatalkan` indexed | |
| `kode_booking` string 20 unique indexed | `BJ-XXXX` style |
| `deleted_at` | |

### `detail_pemesanan` (tiket per orang)
| Kolom | Tipe |
|-------|------|
| `id_detail` PK | |
| `id_pemesanan` FK | |
| `id_tiket` FK | |
| `kode_tiket` string 20 | **unik per orang**, di-QR |
| `nama_tiket` string | snapshot |
| `nama_pengunjung` string | |
| `jenis_kelamin` enum L/P | |
| `plat_kendaraan` string nullable | |
| `harga` decimal | snapshot |
| `status_tiket` enum `aktif,digunakan,kadaluarsa` | |
| `check_in_at`, `check_out_at` datetime nullable | |

### `pembayaran`
| Kolom | Tipe |
|-------|------|
| `id_bayar` PK | |
| `id_pemesanan` FK unique | 1:1 |
| `total` decimal | |
| `metode` string | `transfer`, `qris`, `e_wallet` |
| `bukti_bayar` string | path `storage/app/public/bukti` |
| `status` enum `pending,valid,ditolak` | |
| `tgl_bayar` datetime | |

### `galeri`
| Kolom | Tipe |
|-------|------|
| `id_galeri` PK | |
| `file` string | `galeri/galeri-01.jpg` |
| `keterangan`, `keterangan_en` | i18n via accessor |
| `tipe` enum `wisata,restoran` | scope `wisata()`/`restoran()` |
| `deleted_at` | |

### `konten`
| `id`, `judul`, `isi` (longText), `jenis` enum `sejarah,info,umum` |

### `activity_logs`
| `id`, `id_user`, `aksi`, `deskripsi`, `ip`, `user_agent`, `created_at` |

### Lain
- `password_resets`, `failed_jobs`, `personal_access_tokens` (Sanctum)
- Seeder: tiket dewasa 20k, anak 15k, WNA 30k; konten sejarah/info; 8 galeri placeholder.

**Relasi Eloquent:**
- `User hasMany Pemesanan`
- `Pemesanan belongsTo User/Tiket, hasOne Pembayaran, hasMany DetailPemesanan`
- `DetailPemesanan belongsTo Pemesanan/Tiket`
- `Tiket hasMany Pemesanan`

---

## 6. Fitur Lengkap per Role

### A. Publik (tanpa login)
- **Landing `/`** (`LandingController@index`): hero editorial, narasi warisan, 4 pilar pengalaman (tirta 15M, kolam alami, jalur rimba, kuliner), daftar tiket aktif, galeri `wisata`, travel/akses (Ubud 15km/25m, Sanur 23km/40m, Kuta 32km/50m, Bandara 38km/60m), etika suaka, FAQ 5 item, CTA finale, footer.
- **Restoran `/restoran`** (`LandingController@restoran`): filosofi Kepulauan Rasa, menu (utama/kudapan/minuman), 3 ambiance (gazebo tepi sungai, bale utama, lesehan bambu), reservasi WA, galeri `restoran`.
- **Tiket `/tiket` + `/tiket/{id}`**: list & detail tiket, filter tanggal, sisa kuota harian, harga.
- **Verifikasi QR `/tiket/verifikasi/{kode}`**: cek keabsahan `kode_tiket`.
- **Auth**: login (email **atau** username) + register + forgot password + Google OAuth (`/auth/google` → `/auth/google/callback`).
- **Ganti bahasa** `GET /lang/{id|en}` → `session.locale`.

### B. Wisatawan (`middleware auth + role:wisatawan`, prefix `wisatawan.`)

| Route | Fungsi |
|-------|--------|
| `GET /wisatawan/pemesanan` | Form pilih tiket, tgl, qty (throttle 10/m) |
| `POST /wisatawan/pemesanan` | `store()` — transaksi, buat `kode_booking` + N `detail_pemesanan` |
| `GET /wisatawan/pemesanan/data-diri` | Form nama/gender/plat per tiket |
| `POST /wisatawan/pemesanan/data-diri` | Simpan data pengunjung |
| `GET /wisatawan/pemesanan/sukses/{id}` | Konfirmasi + kode booking |
| `GET /wisatawan/pemesanan/riwayat` | Riwayat + ringkasan status (selesai/pending/dibatalkan/diproses) |
| `GET /wisatawan/pemesanan/{id}` | Detail pemesanan + QR per `kode_tiket` + tombol Bayar/Cetak PDF |
| `GET /wisatawan/pembayaran/{id}` | Pilih metode (BCA 1234 5678 9012 a.n. Pengelola, QRIS) |
| `POST /wisatawan/pembayaran/{id}` | Upload bukti (JPG/PNG/PDF max 5MB) → `pembayaran.pending` |

> Pembayaran menunggu verifikasi 1×24 jam; notifikasi email setelah valid/ditolak. Tombol "Masuk & Pesan" → modal login jika guest.

### C. Pengelola (`middleware auth + role:pengelola`, prefix `pengelola.`)

| Route | Fungsi |
|-------|--------|
| `GET /pengelola/dashboard` | KPI: pengunjung hari ini (`pemesanan.selesai` sum jumlah), pendapatan bulan ini (`pembayaran.valid`), pemesanan pending, total wisatawan, scan hari ini, grafik pie komposisi per tiket, 5 pemesanan terbaru |
| `resource /pengelola/galeri` | CRUD galeri (file, keterangan, tipe) — `except edit,update,show` |
| `resource /pengelola/tiket` | CRUD tiket (nama, nama_en, harga, kategori, status) — `except show` |
| `GET /pengelola/verifikasi` | List pemesanan butuh verifikasi |
| `GET /pengelola/verifikasi/{id}` | Detail + bukti bayar |
| `POST /pengelola/verifikasi/{id}/validasi` | valid/ditolak → update status, kirim `PembayaranValid`/`PembayaranDitolak`, log |
| `GET /pengelola/notifikasi` | Polling verifikasi baru (realtime sederhana) |
| `GET /pengelola/laporan` | Filter periode |
| `GET /pengelola/laporan/kunjungan` | Laporan kunjungan + export PDF `pdf-kunjungan.blade.php` |
| `GET /pengelola/laporan/transaksi` | Laporan transaksi + export PDF `pdf-transaksi.blade.php` |
| `GET /pengelola/user` | List user + search + total pemesanan |
| `GET /pengelola/scan` | Daftar tiket hari ini |
| `POST /pengelola/scan/cari` | Cari `kode_tiket` (uppercase trim, max 20) |
| `POST /pengelola/scan/gunakan` | Check-in: `aktif→digunakan`, set `check_in_at`, validasi lunas + tgl hari ini, `DB::transaction` |
| `POST /pengelola/scan/checkout` | Check-out: `digunakan` + `check_out_at` |
| `GET /pengelola/pemesanan-offline/*` | Flow offline mirror wisatawan (create/data-diri/sukses/riwayat) — untuk loket langsung |

### D. Profile & Utilitas (auth)
- `GET/PATCH /profile` — edit nama/username/email/phone, ganti password, `POST /profile/set-password` (akun Google), `DELETE /profile` (soft/hard sesuai policy)
- `GET /logout` — fallback GET saat CSRF expired (invalidate + regenerateToken)
- `GET /redirect-after-login` — router role

---

## 7. Routing Lengkap

File: `routes/web.php` (136 baris), `routes/auth.php` (Breeze).

```
PUBLIC
  GET /                          → landing
  GET /restoran                  → resto
  GET /informasi                 → redirect landing (legacy)
  GET /tiket                     → tiket.index
  GET /tiket/{id}                → tiket.detail
  GET /tiket/verifikasi/{kode}   → tiket.verifikasi
  GET /auth/google
  GET /auth/google/callback

WISATAWAN (auth, role:wisatawan)
  GET  /wisatawan/pemesanan
  POST /wisatawan/pemesanan              (throttle:10,1)
  GET  /wisatawan/pemesanan/data-diri
  POST /wisatawan/pemesanan/data-diri
  GET  /wisatawan/pemesanan/sukses/{id}
  GET  /wisatawan/pemesanan/riwayat
  GET  /wisatawan/pemesanan/{id}
  GET  /wisatawan/pembayaran/{id_pemesanan}
  POST /wisatawan/pembayaran/{id_pemesanan}

PENGELOLA (auth, role:pengelola)
  GET  /pengelola/dashboard
  RESOURCE /pengelola/galeri   (index,create,store,destroy)
  RESOURCE /pengelola/tiket    (index,create,store,edit,update,destroy)
  GET  /pengelola/verifikasi
  GET  /pengelola/verifikasi/{id}
  POST /pengelola/verifikasi/{id}/validasi
  GET  /pengelola/notifikasi
  GET  /pengelola/laporan
  GET  /pengelola/laporan/kunjungan
  GET  /pengelola/laporan/transaksi
  GET  /pengelola/user
  GET  /pengelola/scan
  POST /pengelola/scan/cari
  POST /pengelola/scan/gunakan
  POST /pengelola/scan/checkout
  GET  /pengelola/pemesanan-offline
  POST /pengelola/pemesanan-offline
  ...

AUTH (guest/profile)
  GET  /profile, PATCH /profile, POST /profile/set-password, DELETE /profile
  GET  /lang/{locale}
  GET  /logout (fallback)
  GET  /redirect-after-login
  + Breeze routes (login, register, password.*)
```

Middleware terdaftar: `CheckRole`, `Localization` (set locale dari `session`), `TrustProxies`, `TrimStrings`, `VerifyCsrfToken`, dll (`app/Http/Middleware/` total 11 file).

---

## 8. Desain System — Tropical Sanctuary

Sumber: `docs/DESIGN.md` + `tailwind.config.js` + `resources/views/**/*.blade.php`. Tema: **Tropical Elegance** — mewah tropis, minimal + tactile, foto alam jadi hero.

### Palet Warna (Material 3 inspired)

| Token | Hex | Pakai |
|-------|-----|-------|
| `primary` | `#005344` | Header, CTA filled |
| `primary-container` | `#006d5b` | Hover, Emerald depth |
| `on-primary` | `#ffffff` | Teks di primary |
| `secondary` | `#3b6934` | Depth, Leaf Green |
| `secondary-container` | `#b9eeab` | Pill/tag bg |
| `tertiary` | `#5b442a` | Wood Brown functional |
| `background` | `#fbf9f4` | Page bg (cream warm) |
| `surface` | `#fbf9f4` | Card base |
| `surface-container` | `#f0eee9` | Section alt |
| `on-surface` | `#1b1c19` | Body text (charcoal soft) |
| `outline-variant` | `#bec9c4` | Input stroke |
| `error` | `#ba1a1a` | Validasi |

> Cream `#fbf9f4` bukan putih murni → kurangi eye strain, lebih organik. Gradient hero: emerald → leaf → wood.

### Tipografi

- **Heading**: `Playfair Display` — editorial, eksklusif (display-lg 64px/700, headline-md 32px/600)
- **Body/UI**: `Plus Jakarta Sans` — apertur terbuka, legibilitas (body-lg 18px/400, label-md 14px/600 uppercase tracked)
- Negatif tracking di display desktop, label uppercase + `letterSpacing 0.05em`.

### Layout & Spacing

- Desktop: 12 kolom, gutter 24px, container max `1280px`, antar-section `80px (xl)`.
- Tablet: 6 kolom, gutter 24px, margin 32px.
- Mobile: 2 kolom, gutter 16px, margin 20px.
- Unit `8px` base (4,12,24,48,80). Hero text offset agar foto air terjun tetap focal.

### Elevation

- L0 base: `#fbf9f4`
- L1 cards/inputs: `#ffffff` + `0 4px 20px rgba(0,50,40,.04)`
- L2 modal: `#ffffff` + `0 12px 40px rgba(0,50,40,.08)`
- Nav sticky: `backdrop-blur 10px`, `bg-white/80`, transparan di hero → glass on scroll.

### Radius & Bentuk

- Button/input `12px` (`lg`), card/container `24px` (`xl`), chip pill `full`. Foto grid ikut `24px`.

### Komponen

- **Button**: primary filled emerald/white, secondary outlined 1.5px, tertiary wood underline. Hover lift subtle.
- **Card tiket**: radius 24, image full-bleed top, padding 24, shadow L1, badge kategori.
- **Input**: stroke `#bec9c4`, focus emerald + glow 4px.
- **Chip**: `secondary-container` bg, `on-secondary-container` text.
- **Nav**: desktop transparent → glass; link `Plus Jakarta Sans Bold 14px uppercase`. Mobile drawer Alpine.

### Halaman Kunci untuk Referensi Desain

- `wisatawan/landing.blade.php` (~1100 baris) — hero, narrative, 4 pilar, tiket grid, galeri zoom, resto teaser, travel, etika, FAQ accordion, finale CTA
- `wisatawan/restoran.blade.php` — philosophy, menu tabs (Alpine), ambiance 3 card, booking WA
- `wisatawan/tiket.blade.php` — filter tanggal, qty stepper (Alpine), ringkasan pajak 10%
- `wisatawan/pemesanan.blade.php`, `pemesanan-data-diri.blade.php`, `pemesanan-sukses.blade.php`, `pemesanan-detail.blade.php` (e-ticket QR)
- `wisatawan/pembayaran.blade.php` — metode, drag&drop upload, preview, validasi 5MB
- `pengelola/dashboard.blade.php` — KPI grid, Chart.js pie, tabel terbaru
- `pengelola/scan/index.blade.php` — input kode, kamera (butuh HTTPS), status badge

### Aset Frontend

- `resources/css/app.css` — Tailwind directives, custom scrollbar, print style
- `resources/js/app.js` — bootstrap `Alpine.start()`, `axios`, Vite HMR
- `vite.config.js` — input `css/app.css` + `js/app.js`
- `tailwind.config.js` — content `resources/views/**/*.blade.php`, plugin `@tailwindcss/forms`, font `Figtree` fallback

---

## 9. Struktur Direktori

```
bangkiang-jaran-web/
├── app/
│   ├── Console/
│   │   ├── Kernel.php                         # scheduler
│   │   └── Commands/AutoCheckout.php          # checkout otomatis tiket terlewat
│   ├── Helpers/ActivityLogger.php             # helper log aktivitas
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/ (9 file)                # Breeze: login/register/password/reset
│   │   │   ├── AuthController.php            # Google OAuth redirect/callback
│   │   │   ├── ProfileController.php         # edit/update/set-password/destroy
│   │   │   ├── Wisatawan/
│   │   │   │   ├── LandingController.php     # index, restoran
│   │   │   │   ├── TiketController.php       # index, detail, verifikasi
│   │   │   │   ├── PemesananController.php   # create/store/dataDiri/riwayat/detail
│   │   │   │   └── PembayaranController.php  # create/store bukti
│   │   │   └── Pengelola/
│   │   │       ├── DashboardController.php   # KPI + grafik
│   │   │       ├── TiketController.php       # CRUD tiket
│   │   │       ├── GaleriController.php      # CRUD galeri
│   │   │       ├── VerifikasiController.php  # index/show/validasi/notifikasi
│   │   │       ├── LaporanController.php     # index/kunjungan/transaksi + PDF
│   │   │       ├── ScanController.php        # index/cari/gunakan/checkout
│   │   │       ├── PemesananOfflineController.php # loket manual
│   │   │       └── UserController.php        # list + search
│   │   ├── Middleware/ (11)                  # CheckRole, Localization, TrustProxies, etc.
│   │   └── Requests/
│   │       ├── ProfileUpdateRequest.php
│   │       └── Auth/LoginRequest.php         # email/username + rate limit 5x
│   ├── Mail/ (5)                             # WelcomeMail, PemesananBerhasil, PembayaranValid/Ditolak, TestMail
│   ├── Models/ (8)                           # User, Tiket, Pemesanan, DetailPemesanan, Pembayaran, Galeri, Konten, ActivityLog
│   ├── Providers/ (5)                        # AppServiceProvider, Auth, Event, Route, Broadcast
│   └── View/Components/ (2)                  # AppLayout, GuestLayout
├── bootstrap/                                # app.php, cache
├── config/                                   # app.php (locale id, tz Makassar), auth, database, mail, etc.
├── database/
│   ├── migrations/ (29)                      # users, tiket, pemesanan, pembayaran, galeri, konten, activity_logs, detail_pemesanan, etc.
│   ├── seeders/                              # DatabaseSeeder, DemoDataSeeder
│   └── factories/
├── docs/
│   ├── DESIGN.md                             # palet, tipografi, layout token
│   ├── CLASS_DIAGRAM.md                      # PlantUML
│   ├── BLACKBOX_TESTING.md                   # 61 skenario uji
│   └── diagrams/ (PNG)                       # arsitektur
├── docker/
│   ├── Dockerfile                            # multi-stage node+php
│   └── entrypoint.sh                         # migrate + cache + storage:link
├── lang/
│   ├── id/messages.php (660 baris)           # semua string UI ID
│   └── en/messages.php                       # mirroring EN
├── public/
│   ├── index.php
│   ├── build/ (Vite manifest)                # hasil npm run build
│   ├── images/, videos/                      # aset statis
│   └── storage → storage/app/public          # symlink
├── resources/
│   ├── css/app.css
│   ├── js/app.js (Alpine + axios)
│   └── views/
│       ├── auth/ (6)                         # login, register, forgot, reset, verify, confirm
│       ├── components/ (13)                  # button, input, modal, dropdown, nav-link, etc.
│       ├── emails/ (5)                       # welcome, pemesanan-berhasil, pembayaran-valid/ditolak, test
│       ├── layouts/ (3)                      # app.blade.php, guest.blade.php, navigation.blade.php
│       ├── pengelola/
│       │   ├── dashboard.blade.php
│       │   ├── galeri/ (index, form)
│       │   ├── tiket/ (index, form)
│       │   ├── verifikasi/ (index, show)
│       │   ├── laporan/ (index, kunjungan, transaksi, pdf-*.blade)
│       │   ├── scan/index.blade.php
│       │   ├── pemesanan-offline/ (4)
│       │   ├── user/index.blade.php
│       │   └── layouts/admin.blade.php
│       ├── wisatawan/ (13)                   # landing, restoran, tiket, pemesanan, data-diri, sukses, detail, pembayaran, riwayat, galeri, lokasi
│       ├── profile/ (edit + partials)
│       └── vendor/pagination (11)
├── routes/
│   ├── web.php                               # semua route (lihat §7)
│   └── auth.php                              # Breeze
├── scripts/
│   └── backup-db.bat                         # mysqldump helper (Windows)
├── storage/
│   ├── app/public/galeri/                    # upload galeri
│   ├── app/public/bukti/                     # bukti bayar
│   └── logs/laravel.log
├── tests/                                    # Feature/Unit (PHPUnit)
├── .env.example
├── .env
├── artisan
├── composer.json / composer.lock
├── package.json / package-lock.json
├── vite.config.js
├── tailwind.config.js
├── postcss.config.js
├── phpunit.xml
├── start-demo.bat                            # one-click demo + ngrok
└── README.md (file ini)
```

> Total Blade: 76 file (18 folder). Total PHP app: 60 file (14 folder).

---

## 10. Konfigurasi Environment

`.env.example` (74 baris) — salin ke `.env`:

```ini
APP_NAME="E-Tourism Bangkiang Jaran Waterfall"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bangkiang_jaran
DB_USERNAME=root
DB_PASSWORD=

# Aiven prod: path CA cert, mis /var/www/html/database/aiven-ca.pem
MYSQL_ATTR_SSL_CA=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=5256000

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=587
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="info@bangkiangjaran.com"
MAIL_FROM_NAME="${APP_NAME}"

GOOGLE_MAPS_API_KEY=
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=           # default APP_URL + /auth/google/callback

AWS_*, PUSHER_*                # optional
VITE_PUSHER_*                  # optional
```

**Wajib isi untuk fitur penuh:**
- `GOOGLE_CLIENT_ID/SECRET/REDIRECT_URI` → https://console.cloud.google.com/apis/credentials (Authorized redirect URI harus cocok)
- `MAIL_MAILER=smtp` + kredensial jika ingin email beneran terkirim (default `log` hanya ke `storage/logs`)
- `GOOGLE_MAPS_API_KEY` jika embed Maps pakai JS API (saat ini pakai iframe gratis tanpa key juga jalan)
- `MYSQL_ATTR_SSL_CA` jika DB Aiven/SSL

---

## 11. Instalasi Lokal

### Prasyarat
- PHP 8.3+, Composer 2.x, Node 20+, MySQL 5.7+/MariaDB 10.3+ (atau XAMPP), Git

### Langkah (Windows / XAMPP)

```bash
# 1. Clone
git clone https://github.com/anomali0812/bangkiang-jaran-web.git
cd bangkiang-jaran-web

# 2. Dependencies
composer install
npm install

# 3. Env
copy .env.example .env
php artisan key:generate

# 4. DB — buat via phpMyAdmin atau CLI
mysql -u root -e "CREATE DATABASE IF NOT EXISTS bangkiang_jaran CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 5. Migrasi & seeder (demo akun + 12 pemesanan)
php artisan migrate --seed

# 6. Storage symlink (wajib agar galeri/bukti bisa diakses)
php artisan storage:link

# 7. Build frontend (prod) atau dev
npm run build        # prod
# atau
npm run dev          # dev HMR

# 8. Serve
php artisan serve --port=8000
# buka http://localhost:8000
```

> Pint (`composer pint` / `vendor/bin/pint`) untuk format. `php artisan migrate:fresh --seed` untuk reset total.

---

## 12. Docker

### Build & Run

```bash
docker build -f docker/Dockerfile -t bangkiang-jaran .
docker run -p 8000:80 --env-file .env bangkiang-jaran
# atau compose jika ada
```

### Dockerfile (2 stage)

- **Stage 1 `assets`**: `node:20-alpine` → `npm ci` → `npm run build` → `public/build`
- **Stage 2 `php`**: `php:8.3-apache` → install `libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libxml2-dev libicu-dev libonig-dev zlib1g-dev` → `docker-php-ext-install pdo_mysql mbstring zip gd exif intl opcache` → `a2enmod rewrite` → copy `composer:2` → `composer install --no-dev` → `chown storage bootstrap/cache` → set `APACHE_DOCUMENT_ROOT=/var/www/html/public`.

### Entrypoint (`docker/entrypoint.sh`)

```sh
[ -L public/storage ] || php artisan storage:link
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
exec "$@"
```

Idempotent — aman tiap boot container.

---

## 13. Menjalankan Demo & Ngrok HTTPS

Fitur **scan kamera** butuh **HTTPS** agar browser HP izinkan kamera.

### Opsi A — One-click Windows

```bash
# double-click
start-demo.bat
```

Skrip: cek `public/build/manifest.json` → `npm run build` jika belum ada → cek `ngrok config` → minta token jika belum → `php artisan serve --port=8000` (window 1) → `ngrok http 8000` (window 2) → buka `http://localhost:8000`.

> 1 token ngrok bisa untuk 2 website **gantian**, bukan bareng. Tutup window ngrok website lain dulu.

### Opsi B — Manual

```bash
npm run build
php artisan serve --port=8000
# terminal baru
ngrok config add-authtoken <TOKEN>  # sekali saja — dari https://dashboard.ngrok.com/get-started/your-authtoken
ngrok http 8000
# salin https://xxxx.ngrok-free.app → buka di HP
```

**Test scan di HP:**
- Pengelola: login `pengelola / Password123` → **Scan QR / Validasi Tiket** → izinkan kamera → scan QR wisatawan.
- Wisatawan: login `wisatawan / Password123` → **Tiket Saya / Riwayat** → tampilkan QR.

### Script npm

```json
"scripts": {
  "dev": "vite",
  "build": "vite build",
  "tunnel": "ngrok http 8000"
}
```

---

## 14. Internasionalisasi (i18n)

- File: `lang/id/messages.php` (660 baris) & `lang/en/messages.php` — 1:1 mirror.
- Middleware `Localization` set `app()->setLocale(session('locale','id'))`.
- Switch: `GET /lang/{id|en}` → `session.put('locale')` → redirect back.
- Model accessor: `Tiket::getNamaTiketAttribute()` & `Galeri::getKeteranganAttribute()` fallback ke ID jika EN kosong.
- Blade pakai `__('messages.key')` atau `__()`; contoh `__('messages.book_now')`, `__('messages.hero_title_lead')`.
- Tambah bahasa: duplikat `lang/id/messages.php` → `lang/fr/...`, tambah di `in_array` check `routes/web.php` & `Localization`.

---

## 15. Email & Notifikasi

Mailable di `app/Mail/` (5):

| Mailable | Trigger | Template `resources/views/emails/` |
|----------|---------|------------------------------------|
| `WelcomeMail` | register berhasil | `welcome.blade.php` |
| `PemesananBerhasil` | pemesanan dibuat | `pemesanan-berhasil.blade.php` |
| `PembayaranValid` | verifikasi valid | `pembayaran-valid.blade.php` |
| `PembayaranDitolak` | verifikasi ditolak | `pembayaran-ditolak.blade.php` |
| `TestMail` | `php artisan mail:test` (jika ada) | `test.blade.php` |

Default `MAIL_MAILER=log` → email masuk `storage/logs/laravel.log`. Ganti ke `smtp` + isi `MAIL_HOST/PORT/USERNAME/PASSWORD/ENCRYPTION` untuk kirim beneran. `MAIL_FROM_ADDRESS=info@bangkiangjaran.com`.

Pengelola juga punya polling `GET /pengelola/notifikasi` (JSON count pending) — bisa dipoles ke WebSocket (Pusher) jika perlu realtime push.

---

## 16. Pemesanan → Pembayaran → Verifikasi → E-Ticket

### Urutan Kode

1. `PemesananController@create` — tampilkan tiket aktif + datepicker.
2. `PemesananController@store` — validasi `tgl_kunjungan >= today`, `jumlah 1..10`, `throttle:10,1`, hit `total_harga`, `DB::transaction` + `lockForUpdate` (cegah overbooking jika kuota diaktifkan lagi), generate `kode_booking` unik (cek collision), insert `pemesanan` + loop `detail_pemesanan` (satu row per orang, `kode_tiket` unik incremental/random), kirim `PemesananBerhasil`.
3. `PemesananController@dataDiri` / `storeDataDiri` — isi `nama_pengunjung`, `jenis_kelamin`, `plat_kendaraan` per `kode_tiket`.
4. `PembayaranController@create` — tampilkan total, bank BCA, QRIS.
5. `PembayaranController@store` — validasi `metode`, `bukti_bayar` (`mimetypes:jpeg,png,jpg,pdf`, `max 5120`), simpan `storage/app/public/bukti`, `pembayaran.status=pending`.
6. `VerifikasiController@validasi` — pengelola pilih `valid`/`ditolak` (+ alasan), `DB::transaction`, update `pembayaran` + `pemesanan.status` (`selesai`/`dibatalkan`) + `detail_pemesanan.status_tiket` (`aktif`/`kadaluarsa`), kirim `PembayaranValid`/`PembayaranDitolak`, `ActivityLogger::log()`.
7. Wisatawan lihat `pemesanan-detail.blade.php` — tiap `kode_tiket` render QR (pakai library JS QR atau `qrcode` package), tombol **Cetak/Download PDF** (DomPDF).

### File Upload

- Validasi MIME + ekstensi + ukuran di controller (jangan hanya di frontend).
- Path: `storage/app/public/bukti/{kode_booking}.{ext}` & `galeri/{file}`.
- Wajib `php artisan storage:link`.

---

## 17. Scan Tiket (Check-in / Check-out)

Controller: `ScanController.php` (177 baris). View: `pengelola/scan/index.blade.php` (kamera via `getUserMedia`).

| Aksi | Endpoint | Validasi |
|------|----------|----------|
| Lihat hari ini | `GET /pengelola/scan` | `DetailPemesanan` where `pemesanan.tgl_kunjungan = today` & `status=selesai` |
| Cari | `POST /pengelola/scan/cari` | `kode` required string max 20, uppercase trim |
| Check-in | `POST /pengelola/scan/gunakan` | `status_tiket=aktif` + `pemesanan.status=selesai` + `tgl_kunjungan=today` → `status_tiket=digunakan`, `check_in_at=now()` |
| Check-out | `POST /pengelola/scan/checkout` | `status_tiket=digunakan` + `check_out_at null` + `tgl=today` → `check_out_at=now()` |

Semua pakai `DB::beginTransaction/commit/rollBack`. Error message spesifik (sudah digunakan, kadaluarsa, dibatalkan, belum lunas, bukan hari ini).

**Butuh HTTPS** — jalankan via `start-demo.bat` / `ngrok http 8000`, buka `https://xxxx.ngrok-free.app/pengelola/scan` di HP.

Auto-checkout sisa `digunakan` tanpa `check_out_at` bisa via `php artisan` command `AutoCheckout` + scheduler `app/Console/Kernel.php` (jalankan `php artisan schedule:run` atau cron).

---

## 18. Keamanan

- **Hash**: `bcrypt` via `Hash::make` (seeder & register).
- **Role**: `CheckRole` middleware — `abort(403)` jika role tak cocok; route group `role:wisatawan` / `role:pengelola`.
- **CSRF**: `VerifyCsrfToken` di semua POST; form pakai `@csrf`; fallback `GET /logout` untuk sesi expired.
- **XSS**: Blade `{{ }}` escaped; hindari `{!! !!}` kecuali sanitized.
- **SQL Injection**: Eloquent ORM + binding; jangan `DB::raw` dari input user.
- **IDOR**: tiap query cek ownership (`where id_user = auth()->id()` untuk wisatawan; pengelola boleh semua tapi tetap via policy).
- **Rate Limit**: `LoginRequest` 5x (throttle login), `throttle:10,1` di pemesanan.
- **Upload**: validasi `mimes`, `max`, ekstensi; simpan di `storage` bukan `public` langsung.
- **Soft Delete**: `tiket`, `pemesanan`, `galeri` pakai `SoftDeletes`.
- **Session**: `regenerate()` setelah login; `SESSION_LIFETIME=5256000`.
- **Transaction + Row Lock**: `DB::transaction` + `lockForUpdate` saat buat pemesanan.

---

## 19. Backup, Log & Maintenance

### Backup DB

```bash
# phpMyAdmin → Export Quick

# CLI
mysqldump -u root bangkiang_jaran > backup_bangkiang_jaran_20240101.sql
mysql -u root bangkiang_jaran < backup_bangkiang_jaran_20240101.sql

# Windows helper
scripts\backup-db.bat
# atau
php artisan backup:run  # jika spatie/laravel-backup terpasang
```

Cron produksi (tiap jam 2 pagi):
```
0 2 * * * /usr/bin/mysqldump -u root bangkiang_jaran > /backups/bangkiang_jaran_$(date +\%Y\%m\%d).sql
```

### Log

- `storage/logs/laravel.log` (level `debug` di local, `error` di prod)
- `ActivityLog` model → `activity_logs` table (aksi verifikasi, CRUD tiket/galeri)

### Scheduler

```bash
php artisan schedule:run   # di cron tiap menit
# Kernel.php jadwalkan AutoCheckout harian
```

---

## 20. Testing & QA

- **PHPUnit**: `phpunit.xml` — `php artisan test` atau `vendor/bin/phpunit`
- **Black Box**: `docs/BLACKBOX_TESTING.md` — 61 skenario (auth, pemesanan, pembayaran, verifikasi, scan, laporan, i18n)
- **Class Diagram**: `docs/CLASS_DIAGRAM.md` (PlantUML) + `docs/diagrams/*.png`
- Manual checklist penting:
  - [ ] Register → email log muncul
  - [ ] Login email vs username
  - [ ] Google OAuth (butuh `GOOGLE_*` terisi)
  - [ ] Pemesanan + data diri + pembayaran upload
  - [ ] Verifikasi valid/ditolak → status berubah + email
  - [ ] Scan hari H (aktif→digunakan→checkout)
  - [ ] Scan H-1/H+1 ditolak
  - [ ] Ganti bahasa ID↔EN
  - [ ] Laporan filter & export PDF
  - [ ] Galeri upload & tampil di landing/restoran

---

## 21. Checklist Deploy Production

1. `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://domain.com`
2. `php artisan key:generate` (jika fresh), `php artisan migrate --force`, `php artisan storage:link`
3. `composer install --no-dev --optimize-autoloader`, `npm run build`
4. `php artisan config:cache && route:cache && view:cache`
5. DB: isi `MYSQL_ATTR_SSL_CA` jika Aiven, buat user DB non-root
6. Mail: `MAIL_MAILER=smtp` + kredensial valid, test kirim
7. Google OAuth: tambah `https://domain.com/auth/google/callback` di Google Console
8. HTTPS wajib (Let's Encrypt / Cloudflare) — untuk scan kamera
9. Cron: `* * * * * php /var/www/html/artisan schedule:run >> /dev/null 2>&1`
10. Backup harian (mysqldump + simpan offsite)
11. Permission: `chown -R www-data:www-data storage bootstrap/cache`, `chmod 775`
12. Apache/Nginx: `DocumentRoot` → `public`, `AllowOverride All`, `mod_rewrite` on

---

## 22. Troubleshooting

| Gejala | Sebab | Fix |
|--------|-------|-----|
| Galeri/bukti 404 | symlink belum | `php artisan storage:link` |
| `Vite manifest not found` | belum build | `npm run build` (prod) atau `npm run dev` |
| Kamera tidak muncul di HP | buka via HTTP | pakai `ngrok http 8000` → buka `https://...` |
| Google login error `redirect_uri_mismatch` | `GOOGLE_REDIRECT_URI` beda | samakan dengan yang di Google Console (`APP_URL + /auth/google/callback`) |
| Email tidak terkirim | `MAIL_MAILER=log` | ganti `smtp` + cek `storage/logs` |
| 403 saat buka `/pengelola/*` | role bukan pengelola | login sebagai `pengelola` |
| CSRF token mismatch | sesi expired | refresh, atau pakai `GET /logout` lalu login lagi |
| `SQLSTATE HY000 SSL` | Aiven butuh CA | isi `MYSQL_ATTR_SSL_CA=/path/to/aiven-ca.pem` |
| Docker build gagal `libonig-dev` | base image trixie purge | sudah di-fix di `docker/Dockerfile` (install ulang `libonig-dev`) |

---

## 23. Panduan Penerus — Ubah Apa di Mana

| Kebutuhan | File |
|-----------|------|
| **Ganti warna/tema** | `docs/DESIGN.md` (token) + `tailwind.config.js` + `resources/css/app.css` |
| **Ganti font** | `tailwind.config.js` (`fontFamily`) + `resources/views/layouts/*.blade.php` (Google Fonts link) |
| **Tambah tiket baru default** | `database/seeders/DatabaseSeeder.php` (`$tikets`) |
| **Ubah harga/logic pajak** | `PemesananController` + `wisatawan/tiket.blade.php` (Alpine calc) — pajak 10% di ringkasan |
| **Tambah metode bayar** | `PembayaranController` + `lang/*/messages.php` + `wisatawan/pembayaran.blade.php` + `pengelola/verifikasi/show.blade.php` |
| **Ganti nomor rekening/QRIS** | `lang/id/messages.php` (`bank_*`, `scan_qris*`) + `wisatawan/pembayaran.blade.php` |
| **Tambah bahasa** | duplikat `lang/id/messages.php` → `lang/xx/`, update `Localization` & `routes/web.php` `in_array` |
| **Edit landing/restoran copy** | `lang/id/messages.php` (semua `hero_*`, `narrative_*`, `resto_*`, `faq_*`) + `wisatawan/landing.blade.php` |
| **Ganti foto hero/galeri** | upload ke `storage/app/public/galeri/` via **Pengelola → Galeri**, atau ganti `public/images/hero.jpg` |
| **Ubah alamat/jam buka** | `lang/*/messages.php` (`address`, `operating_hours`) + `wisatawan/landing.blade.php` section travel |
| **Laporan kolom baru** | `LaporanController` + `pengelola/laporan/*.blade.php` + `pdf-*.blade.php` |
| **Scan rule (mis allow H+1)** | `ScanController@cari/gunakan/checkout` — ubah `tgl_kunjungan !== today` |
| **Email template** | `resources/views/emails/*.blade.php` + `app/Mail/*.php` |
| **Menambah field user** | migration baru + `User.php $fillable` + `ProfileController` + `profile/edit.blade.php` |

> **Jangan** edit `vendor/` manual. Jalankan `composer update` / `npm update` untuk dependency. Pakai `php artisan make:migration` untuk skema baru; jangan ubah migration lama yang sudah di-push (buat migration baru).

---

## 24. Lisensi

Hak cipta © 2024 Pengelola Bangkiang Jaran Waterfall — untuk demonstrasi produk jasa. Dibuat oleh Yosuanry Simbolon (`2301020122`). Bebas dikembangkan lanjut untuk Desa Bakbakan.

---

### Perintah Penting Ringkas

```bash
composer install && npm install
copy .env.example .env && php artisan key:generate
php artisan migrate --seed && php artisan storage:link
npm run build && php artisan serve --port=8000
# demo HTTPS
start-demo.bat        # Windows one-click
npm run tunnel        # atau npx ngrok http 8000
php artisan test      # QA
php artisan schedule:run  # scheduler
```

**Butuh bantuan?** Cek `storage/logs/laravel.log`, `docs/BLACKBOX_TESTING.md`, atau hubungi pengelola via WA di halaman **Restoran → Reservasi**.

