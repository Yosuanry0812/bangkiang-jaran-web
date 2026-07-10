# Black Box Testing Checklist — E-Tourism Bangkiang Jaran Waterfall

## Modul Autentikasi & Registrasi

| No | Skenario | Langkah Uji | Hasil Diharapkan | Status |
|----|----------|-------------|------------------|--------|
| 1 | Registrasi sukses | Isi form registrasi dengan data valid (name, username, email, phone, password, confirm password) | Redirect ke landing, user tersimpan di DB dengan role 'wisatawan' | |
| 2 | Registrasi email duplikat | Registrasi dengan email yang sudah terdaftar (wisatawan@demo.com) | Muncul error validasi "Email sudah terdaftar", data tidak tersimpan | |
| 3 | Registrasi username duplikat | Registrasi dengan username "wisatawan" | Muncul error "Username sudah digunakan" | |
| 4 | Registrasi password terlalu pendek | Password < 8 karakter | Error validasi "Password minimal 8 karakter" | |
| 5 | Registrasi tanpa angka di password | Password huruf saja | Error "Password harus mengandung angka" | |
| 6 | Login sukses dengan email | Masukkan email + password benar | Redirect ke landing, session aktif | |
| 7 | Login sukses dengan username | Masukkan username + password benar | Redirect ke landing, session aktif | |
| 8 | Login password salah | Email benar + password salah | Error "Email/Username atau password salah" | |
| 9 | Rate limit login | Login gagal 5x berturut-turut | Muncul pesan "Terlalu banyak percobaan login. Silakan coba lagi dalam X menit." | |
| 10 | Logout | Klik tombol logout | Redirect ke landing, session terhapus | |

## Modul Role-Based Access

| No | Skenario | Langkah Uji | Hasil Diharapkan | Status |
|----|----------|-------------|------------------|--------|
| 11 | Wisatawan akses dashboard admin | Login sebagai wisatawan, buka /pengelola/dashboard | Muncul 403 Access Denied | |
| 12 | Wisatawan akses verifikasi | Buka /pengelola/verifikasi | 403 Access Denied | |
| 13 | Wisatawan akses route admin lain | Coba akses /pengelola/konten, /pengelola/tiket, dll | 403 Access Denied | |
| 14 | Pengelola akses route wisatawan | Login sebagai pengelola, akses route wisatawan | Bisa akses (karena middleware role:wisatawan) | |

## Modul Pemesanan Tiket

| No | Skenario | Langkah Uji | Hasil Diharapkan | Status |
|----|----------|-------------|------------------|--------|
| 15 | Pemesanan sukses | Pilih tiket, tanggal kunjungan (>= hari ini), jumlah (<= kuota) | Kode booking tergenerate, redirect ke halaman sukses, email terkirim (cek storage/logs/laravel.log) | |
| 16 | Pemesanan melebihi kuota | Pilih tiket, jumlah > sisa kuota harian | Error "Kuota tidak mencukupi. Sisa kuota: X tiket." | |
| 17 | Pemesanan tanpa login | Akses /wisatawan/pemesanan | Redirect ke halaman login | |
| 18 | Pemesanan tanggal lampau | Pilih tanggal kemarin | Error "Tanggal kunjungan tidak boleh sebelum hari ini" | |
| 19 | Pemesanan jumlah 0 | Isi jumlah 0 | Error "Minimal 1 tiket" | |
| 20 | Pemesanan jumlah > 100 | Isi jumlah 101 | Error "Maksimal 100 tiket" | |
| 21 | Rate limit pemesanan | Lakukan pemesanan > 10 kali dalam 1 menit | Muncul error rate limit "Too Many Attempts" | |
| 22 | Overbooking race condition | Kirim 2 request pemesanan simultan dengan jumlah > sisa kuota | Hanya 1 yang berhasil, satunya dapat error kuota habis (gunakan DB transaction + lock) | |

## Modul IDOR Prevention

| No | Skenario | Langkah Uji | Hasil Diharapkan | Status |
|----|----------|-------------|------------------|--------|
| 23 | Wisatawan A lihat pemesanan B | Login sebagai wisatawan A, ubah URL /wisatawan/pemesanan/{id_pemesanan milik user B} | Muncul 404, data tidak bocor (karena filter where id_user = Auth::id()) | |
| 24 | Wisatawan A upload pembayaran pesanan B | Akses /wisatawan/pembayaran/{id_pemesanan milik user B} | 404 (filter id_user) | |
| 25 | Wisatawan akses detail pemesanan user lain via URL | Manipulasi URL parameter | 404 Not Found | |

## Modul Pembayaran

| No | Skenario | Langkah Uji | Hasil Diharapkan | Status |
|----|----------|-------------|------------------|--------|
| 26 | Upload bukti bayar valid | Pilih file .jpg/.png/.pdf < 2MB | Upload sukses, status pemesanan jadi "diproses" | |
| 27 | Upload file .exe | Pilih file .exe | Error "Format file harus: jpg, jpeg, png, atau pdf" | |
| 28 | Upload file > 2MB | Pilih file > 2MB | Error "Ukuran file maksimal 2MB" | |
| 29 | Upload file dengan MIME palsu | Rename .exe jadi .jpg lalu upload | Error "Tipe file tidak valid" (validasi MIME asli) | |
| 30 | Upload bukti ganda | Upload bukti, lalu upload lagi | Error "Pembayaran sudah pernah diajukan" | |
| 31 | Upload tanpa pilih metode | Tidak pilih metode pembayaran | Error "Pilih metode pembayaran" | |
| 32 | Upload tanpa file | Klik submit tanpa pilih file | Error "Upload bukti transfer" | |

## Modul Verifikasi Pengelola

| No | Skenario | Langkah Uji | Hasil Diharapkan | Status |
|----|----------|-------------|------------------|--------|
| 33 | Verifikasi pembayaran valid | Klik "Validasi" pada pemesanan dengan bukti bayar | Status pembayaran jadi "valid", status pemesanan jadi "selesai", email terkirim ke wisatawan | |
| 34 | Tolak pembayaran | Klik "Tolak" | Status pembayaran jadi "ditolak", status pemesanan jadi "pending", email ditolak terkirim ke wisatawan | |
| 35 | Konsistensi status setelah validasi | Cek DB setelah validasi | Pembayaran.status = 'valid', Pemesanan.status = 'selesai' | |
| 36 | Verifikasi tanpa bukti bayar | Buka pemesanan yang belum upload bukti | Tombol validasi/tolak tidak muncul (karena status bayar bukan 'pending') | |

## Modul CRUD Konten, Galeri, Tiket (Pengelola)

| No | Skenario | Langkah Uji | Hasil Diharapkan | Status |
|----|----------|-------------|------------------|--------|
| 37 | Tambah konten baru | Isi judul, konten, pilih jenis | Data tersimpan, muncul di halaman informasi wisatawan | |
| 38 | Edit konten | Ubah judul/isi konten yang sudah ada | Data terupdate | |
| 39 | Hapus konten (soft delete) | Hapus konten | Data masuk soft delete (deleted_at terisi), tidak tampil di list | |
| 40 | Upload galeri | Upload foto .jpg/.png < 5MB | Foto tersimpan di storage/galeri, tampil di galeri | |
| 41 | Upload galeri file bukan gambar | Upload file .pdf | Error validasi "File harus berupa gambar" | |
| 42 | Hapus galeri | Hapus foto galeri | File terhapus dari storage, data soft delete | |
| 43 | Tambah tiket | Isi nama, harga, kuota, status | Tiket baru muncul di halaman daftar tiket wisatawan | |
| 44 | Nonaktifkan tiket | Ubah status tiket jadi "nonaktif" | Tiket tidak muncul di halaman wisatawan | |
| 45 | Edit tiket | Ubah harga tiket | Harga terupdate di sistem | |
| 46 | Hapus tiket (soft delete) | Hapus tiket | Data tiket masuk soft delete | |

## Modul Laporan

| No | Skenario | Langkah Uji | Hasil Diharapkan | Status |
|----|----------|-------------|------------------|--------|
| 47 | Laporan kunjungan per periode | Pilih tanggal awal-akhir, klik filter | Data sesuai periode yang dipilih | |
| 48 | Export PDF kunjungan | Klik export PDF | File PDF terdownload, data sesuai filter | |
| 49 | Laporan transaksi per periode | Pilih tanggal, filter | Data transaksi (pembayaran valid) sesuai periode | |
| 50 | Export PDF transaksi | Klik export PDF | PDF terdownload dengan total pendapatan | |
| 51 | Filter laporan tanpa tanggal | Klik filter tanpa mengisi tanggal | Error validasi "Periode awal/akhir wajib diisi" | |
| 52 | Filter periode terbalik | Isi periode_akhir < periode_awal | Error "Periode akhir harus setelah atau sama dengan periode awal" | |

## Modul Notifikasi Email

| No | Skenario | Langkah Uji | Hasil Diharapkan | Status |
|----|----------|-------------|------------------|--------|
| 53 | Email registrasi | Daftar akun baru | Cek storage/logs/laravel.log, ada email welcome dengan template HTML | |
| 54 | Email pemesanan | Buat pemesanan baru | Log email berisi detail pemesanan & kode booking | |
| 55 | Email pembayaran valid | Verifikasi pembayaran jadi valid | Log email berisi konfirmasi tiket siap digunakan | |
| 56 | Email pembayaran ditolak | Tolak pembayaran | Log email berisi pemberitahuan & ajakan upload ulang | |

## Modul Keamanan Tambahan

| No | Skenario | Langkah Uji | Hasil Diharapkan | Status |
|----|----------|-------------|------------------|--------|
| 57 | CSRF protection | Hapus @csrf dari form, submit | Error 419 Page Expired | |
| 58 | SQL Injection pada input | Masukkan SQL injection string di input pencarian | Data tidak bocor (Eloquent ORM parameterized query) | |
| 59 | XSS pada input komentar | Masukkan `<script>alert('xss')</script>` | Output diescape dengan {{ }} | |
| 60 | Session fixation setelah login | Capture session ID sebelum login, lihat session ID setelah login | Session ID berbeda (regenerasi session setelah login) | |
| 61 | Soft delete rollback | Hapus tiket, cek DB | deleted_at terisi, data masih ada di DB | |

## Catatan

- Pengujian email menggunakan driver `log` — cek file `storage/logs/laravel.log` untuk melihat konten email
- Pastikan MySQL server sedang berjalan (XAMPP)
- Untuk Google Maps, isi `GOOGLE_MAPS_API_KEY` di .env jika ingin menampilkan peta
- Jalankan `php artisan storage:link` untuk mengakses file upload
- Database migration sudah include foreign key constraints dan index
- Semua form sudah menggunakan CSRF token (`@csrf`)
