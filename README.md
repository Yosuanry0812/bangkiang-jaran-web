# E-Tourism Bangkiang Jaran Waterfall

Panduan lengkap penggunaan dan pengembangan website promosi wisata serta pemesanan tiket Bangkiang Jaran, Desa Bakbakan, Gianyar, Bali. Terdapat dua peran akun: **wisatawan** dan **pengelola**. Pengunjung tanpa akun dapat membaca halaman publik sebagai **tamu**.

**Verifikasi: 4 November 2026.** Dokumentasi mengikuti source lokal, pengujian HTTP Laravel dengan SQLite in-memory, dan pemeriksaan browser aplikasi lokal. Hasil lokal tidak membuktikan kondisi deployment produksi. Fitur yang tersedia, yang membutuhkan konfigurasi, dan yang belum diuji pada layanan/perangkat nyata dibedakan di bawah.

**Gambar dan video dokumentasi seluruh halaman serta fitur akan ditambahkan nanti.** Daftar pada [rencana media](#14-rencana-gambar-dan-video) bukan tautan ke rekaman atau tangkapan layar baru yang sudah tersedia.

## Daftar isi

1. [Gambaran sistem dan istilah](#1-gambaran-sistem-dan-istilah)
2. [Mulai sebagai tamu dan halaman publik](#2-mulai-sebagai-tamu-dan-halaman-publik)
3. [Buat akun melalui email dan OTP](#3-buat-akun-melalui-email-dan-otp)
4. [Login email atau username](#4-login-email-atau-username)
5. [Daftar dan login dengan Google](#5-daftar-dan-login-dengan-google)
6. [Lupa password, profil, dan logout](#6-lupa-password-profil-dan-logout)
7. [Pemesanan tiket online dari awal](#7-pemesanan-tiket-online-dari-awal)
8. [Pembayaran, penolakan, dan unggah ulang](#8-pembayaran-penolakan-dan-unggah-ulang)
9. [Riwayat, e-ticket, dan status transaksi](#9-riwayat-e-ticket-dan-status-transaksi)
10. [Verifikasi publik dan pintu masuk](#10-verifikasi-publik-dan-pintu-masuk)
11. [Pemesanan offline oleh pengelola](#11-pemesanan-offline-oleh-pengelola)
12. [Seluruh menu pengelola](#12-seluruh-menu-pengelola)
13. [Bahasa, email, dan pembaruan status](#13-bahasa-email-dan-pembaruan-status)
14. [Rencana gambar dan video](#14-rencana-gambar-dan-video)
15. [Teknologi dan cara kerja sistem](#15-teknologi-dan-cara-kerja-sistem)
16. [Database dan penyimpanan berkas](#16-database-dan-penyimpanan-berkas)
17. [Peta halaman dan endpoint](#17-peta-halaman-dan-endpoint)
18. [Instalasi dan menjalankan secara lokal](#18-instalasi-dan-menjalankan-secara-lokal)
19. [Konfigurasi layanan dan lingkungan](#19-konfigurasi-layanan-dan-lingkungan)
20. [Pengujian otomatis dan bukti verifikasi](#20-pengujian-otomatis-dan-bukti-verifikasi)
21. [Pemeliharaan dan operasional](#21-pemeliharaan-dan-operasional)
22. [Pemecahan masalah](#22-pemecahan-masalah)
23. [Batas fitur dan pekerjaan lanjutan](#23-batas-fitur-dan-pekerjaan-lanjutan)
24. [Peta berkas untuk pengembang](#24-peta-berkas-untuk-pengembang)

## 1. Gambaran sistem dan istilah

Website menghubungkan promosi destinasi, transaksi tiket, dan pemeriksaan kunjungan. Alur online utama:

```text
Buka website sebagai tamu -> lihat informasi/tiket -> login atau daftar
  -> bila daftar email: verifikasi OTP
  -> pilih tanggal dan jumlah tiap jenis tiket
  -> isi data pengunjung/kendaraan -> pesanan tersimpan
  -> bayar di luar website -> unggah bukti
  -> pengelola menerima atau menolak bukti
  -> bila diterima: pesanan selesai dan tiket dapat digunakan
  -> check-in pada tanggal kunjungan -> check-out setelah kunjungan
```

| Istilah | Arti dalam aplikasi |
| --- | --- |
| Tamu | Pengunjung tanpa sesi login; dapat membaca halaman publik |
| Wisatawan | Akun untuk memesan dan melihat pesanan miliknya |
| Pengelola | Akun operasional untuk verifikasi, tiket, galeri, loket, laporan, dan scan |
| OTP | Kode 6 digit melalui email untuk menyelesaikan pendaftaran email |
| Kode booking | Identitas satu pesanan yang dapat berisi beberapa tiket |
| Kode tiket | Identitas satu detail tiket; berbeda dari kode booking |
| E-ticket | Tampilan tiket digital beserta kode dan QR |
| Pembayaran `pending` | Bukti diajukan dan menunggu pengelola |
| Pembayaran `valid` | Pembayaran disetujui atau dicatat lunas melalui loket |
| Pembayaran `ditolak` | Bukti ditolak; dapat diajukan ulang |
| Check-in / check-out | Pencatatan waktu masuk / keluar |

**Pembayaran online diperiksa manual.** Website tidak memeriksa mutasi bank secara otomatis. Berhasil mengunggah bukti belum berarti lunas.

## 2. Mulai sebagai tamu dan halaman publik

### 2.1 Pertama kali membuka website

1. Buka alamat aplikasi; alamat pengembangan umumnya `http://127.0.0.1:8000` jika Artisan dijalankan di port 8000.
2. Beranda dapat dibuka tanpa login.
3. Gunakan navigasi Beranda, Restoran, dan Tiket; pada ponsel buka menu navigasinya.
4. Gunakan pengalih bahasa Indonesia/Inggris bila diperlukan.
5. Login atau daftar ketika ingin memesan tiket.

Halaman login menyediakan pilihan melanjutkan sebagai tamu. Mode ini tidak memberi akses transaksi atau data pribadi. Sesi lama tanpa penanda login eksplisit dan cookie remember lama tidak otomatis memberi akses akun; perilaku ini tercakup pengujian sesi.

### 2.2 Beranda `/`

Beranda memuat pembuka visual, pengantar destinasi, cerita air terjun/Hidden Canyon, foto, informasi fasilitas, panduan kunjungan, dan bagian rute. Tombol tiket mengarahkan pengguna ke pemilihan/pemesanan tiket.

- Foto wisata berasal dari galeri pengelola; foto contoh dengan pola nama tertentu disaring dari tampilan pemasaran.
- Jika foto tambahan belum tersedia, halaman dapat memakai foto destinasi bawaan. Pengulangan foto tidak berarti ada dokumentasi berbeda untuk setiap objek.
- Informasi Hidden Canyon meminta pengunjung menanyakan akses dan kondisi terkini kepada pengelola. Panduan ini tidak menjamin kondisi jalur atau kegiatan di lapangan.
- Peta menampilkan tujuan Bangkiang Jaran. Pilihan titik keberangkatan dan tautan Google Maps membantu membuka rute di layanan peta.
- Lokasi perangkat memerlukan kemampuan browser dan izin pengguna. Akurasi GPS belum diuji pada perangkat fisik dalam pekerjaan ini.

`/informasi` diarahkan kembali ke beranda. Berkas Blade lokasi/galeri lama tidak otomatis menjadi halaman publik tersendiri bila tidak dirutekan.

### 2.3 Restoran `/restoran`

Halaman ini menyajikan pengantar tempat makan, suasana, foto restoran, serta arahan menanyakan menu dan ketersediaan tempat kepada pengelola.

- Foto bertipe `restoran` berasal dari menu Galeri pengelola.
- Jika foto restoran belum ditambahkan, bagian dokumentasi menampilkan keadaan kosong. Foto air terjun pada pembuka adalah lanskap destinasi, bukan bukti foto restoran.
- Informasi menu/harga mengarahkan pengguna untuk menanyakan menu terbaru.
- Belum ada keranjang makanan, checkout makanan, pengelolaan stok makanan, atau reservasi meja dengan konfirmasi otomatis.

### 2.4 Tiket `/tiket` dan detail tiket

Daftar menampilkan tiket aktif, harga, profil tiket, dan estimator biaya. Detail jenis tiket tersedia di `/tiket/{id}`.

- Tarif mengikuti database yang dikelola pengelola. Angka contoh pengujian bukan harga resmi.
- Estimator merupakan perkiraan; server menghitung kembali pesanan.
- Tiket nonaktif tidak dapat dibuka melalui detail publik atau dipakai memulai pesanan online.
- Tamu yang membuka pemesanan diminta login. URL tujuan, termasuk pilihan tiket yang ada pada URL, dapat diteruskan setelah autentikasi.
- Daftar tiket bukan jaminan kuota per tanggal; backend aktif tidak mengurangi kuota harian.

**Media:** gambar beranda, restoran, tiket/detail, navigasi ponsel, dan video rute akan ditambahkan nanti.

## 3. Buat akun melalui email dan OTP

### 3.1 Mengisi formulir pendaftaran

1. Buka `/register` dari tautan daftar pada autentikasi.
2. Isi nama lengkap dan pilih username yang belum dipakai.
3. Masukkan email aktif milik sendiri dengan huruf kecil.
4. Isi password dan konfirmasinya dengan nilai sama.
5. Kirim formulir untuk meminta OTP.
6. Buka email tujuan, lalu masukkan kode pada `/register/otp`.

| Kolom | Aturan server |
| --- | --- |
| `name` | Wajib, teks, maksimal 255 karakter |
| `username` | Wajib, unik, maksimal 50 karakter; huruf, angka, titik, garis bawah, tanda hubung |
| `email` | Wajib, format email, huruf kecil, unik, maksimal 255 karakter |
| `password` | Wajib, memenuhi aturan password Laravel yang dipakai aplikasi; bawaan minimal 8 karakter |
| `password_confirmation` | Harus sama dengan password |

Pendaftaran publik menghasilkan role **wisatawan**. Menambahkan input role pengelola tidak mengubah hasil pendaftaran.

### 3.2 Setelah formulir dikirim

Formulir belum langsung membuat akun di `users` dan belum membuat pengguna login. Server memvalidasi input, membuat hash password, mengikat tantangan ke session, dan menyimpan data sementara terenkripsi pada cache. Server kemudian mengirim OTP melalui mailer yang dikonfigurasi.

OTP disimpan sebagai hash. Layanan OTP tidak menuliskan kodenya ke log dan tidak mengembalikan kode sebagai old input ketika validasi gagal.

### 3.3 Verifikasi OTP

1. Tetap gunakan browser/sesi tempat pendaftaran dimulai.
2. Periksa inbox dan spam email tujuan.
3. Masukkan kode 6 digit, lalu kirim verifikasi.
4. Bila benar, server memeriksa ulang keunikan email/username dan membuat akun.
5. Asal akun dicatat `email`; verifikasi email dicatat `otp` beserta waktunya.
6. Data tantangan dihapus dan pengguna otomatis login.
7. Pengguna diteruskan ke tujuan pemesanan tersimpan atau mengikuti pengalihan wisatawan menuju beranda.

Kode yang sudah berhasil digunakan tidak membuat akun kedua. Jika email/username dipakai pendaftaran lain selama proses berlangsung, pemeriksaan akhir dapat menolak penyelesaian.

### 3.4 Batas waktu dan percobaan

| Aturan | Implementasi |
| --- | --- |
| Bentuk kode | 6 digit angka |
| Masa berlaku OTP | 600 detik / 10 menit |
| Masa data sementara | 1800 detik / 30 menit; diperpanjang setelah resend berhasil |
| Jeda kirim ulang | 60 detik |
| Kode salah | Maksimal 5 sebelum blokir sementara |
| Batas pengiriman email | 5 percobaan per email per jam |
| Batas pengiriman IP | 20 percobaan per IP per jam |
| Route POST daftar | `throttle:5,1` |
| Route POST verifikasi OTP | `throttle:10,1` |
| Route POST resend | `throttle:5,1` |

Batas pengiriman juga menghitung upaya menghubungi penyedia yang gagal setelah kuota dicadangkan. Memulai ulang pendaftaran tidak menghapus limit email/IP. Kode salah format ditolak. Penghitung kode salah tidak dihapus hanya dengan resend atau memulai pendaftaran kembali.

### 3.5 Kirim ulang, batal, dan kegagalan

- **Kode kedaluwarsa:** minta kode baru setelah jeda memungkinkan.
- **Resend berhasil:** gunakan kode terbaru; kode sebelumnya tidak berlaku.
- **Resend gagal:** kode sebelumnya tetap berlaku sampai waktu kedaluwarsa semula jika belum habis.
- **Salah berulang:** tunggu masa blokir yang ditampilkan; resend tidak melewati blokir.
- **Sesi berakhir:** daftar kembali. Kode pada sesi lain tidak menyelesaikan tantangan sesi ini.
- **Salah email:** batalkan pendaftaran sementara dan isi ulang dengan email benar. Tujuan pemesanan dipertahankan pada alur yang diuji.
- **Mailer belum aktif:** pendaftaran belum selesai. Transport simulasi seperti `log` ditolak pada penggunaan biasa agar aplikasi tidak mengaku mengirim kode ke inbox.

**Bukti uji:** mail fake memeriksa pengiriman dan seluruh kondisi OTP terkait, bukan penerimaan inbox nyata.

**Media:** gambar formulir, email OTP yang disamarkan, countdown, salah/kedaluwarsa, resend, dan video pendaftaran akan ditambahkan nanti.

## 4. Login email atau username

### 4.1 Langkah masuk

1. Buka `/login`.
2. Isi email atau username pada kolom login.
3. Isi password akun dan kirim.
4. Jika benar, session diperbarui dan metadata login dicatat.

Input berformat email dicari melalui email; input lainnya melalui username. Password diperiksa autentikasi Laravel.

| Kondisi | Hasil |
| --- | --- |
| Wisatawan tanpa tujuan tersimpan | Melalui `/redirect-after-login` menuju beranda |
| Wisatawan dari halaman pemesanan | Dapat kembali ke URL tujuan tersimpan |
| Pengelola | Menuju `/pengelola/dashboard` |
| Pengelola membuka pemasaran/guest tertentu | Diarahkan ke dashboard oleh middleware |
| Akun/password salah | Pesan `Email/Username atau password salah.` |
| Percobaan berlebihan | Dibatasi rate limiter dan route |

Rate limiter manual membatasi 5 percobaan pada kunci input login dan IP. POST login juga memakai `throttle:5,1`. Waktu tunggu mengikuti limit yang terkena.

### 4.2 Hubungan dengan OTP dan session

OTP digunakan menyelesaikan **pendaftaran email**. Login biasa memakai email/username dan password; tidak meminta OTP baru pada setiap login. OTP bukan kode reset password atau pembayaran.

Login eksplisit diberi penanda session. Input `remember` tidak membuat remember-me persisten. Session aktif tetap dipakai pada permintaan berikutnya; pergantian halaman tidak otomatis mengakhiri login. Masa session/cookie mengikuti konfigurasi. Penutupan/pemulihan browser fisik belum dibuktikan oleh pengujian HTTP.

## 5. Daftar dan login dengan Google

### 5.1 Alur pengguna

1. Pilih masuk dengan Google.
2. Pilih akun dan selesaikan persetujuan pada Google.
3. Google mengembalikan pengguna ke `/auth/google/callback`.
4. Server memeriksa identitas serta verifikasi email Google.
5. Akun baru dibuat sebagai wisatawan dan diarahkan ke Profil Saya.
6. Pilih username sendiri, simpan, kemudian tekan Lanjutkan.

Akun baru mendapat username sementara `pengunjung_` dengan nilai acak. Username pilihan pengguna dipertahankan pada login berikutnya dan tidak mengubah nama Google.

### 5.2 Aturan identitas dan konflik akun

- Email harus valid dan `email_verified` dari Google bernilai benar.
- Asal akun dan metode verifikasi dicatat `google`; tidak meminta OTP aplikasi tambahan.
- Pencocokan akun menggunakan `google_id`.
- Akun manual yang emailnya sama tidak digabung otomatis. Konflik identitas/email mengembalikan pengguna untuk memakai akun lama atau menghubungi admin.
- State OAuth yang tidak valid ditolak melalui Socialite.
- Akun asal Google tetap berlabel Google setelah login password. `last_login_method` hanya menunjukkan login terakhir.

Fitur memerlukan client ID, client secret, dan redirect URI sesuai environment serta konfigurasi Google. Jika belum lengkap, aplikasi kembali ke login dengan pesan konfigurasi. Penolakan izin/kegagalan callback juga dapat mengembalikan pengguna ke login.

**Bukti uji:** provider dimock; persetujuan Google nyata dan callback domain produksi belum diuji pada pekerjaan ini.

**Media:** gambar tombol, profil akun baru, username tersimpan, dan video OAuth nyata akan ditambahkan nanti dengan data pribadi disamarkan.

## 6. Lupa password, profil, dan logout

### 6.1 Lupa password

1. Pilih lupa password atau buka `/forgot-password`.
2. Isi email akun dan minta tautan reset.
3. Jika terdaftar dan terkirim, buka tautan dari email.
4. Isi password baru serta konfirmasinya.
5. Setelah berhasil, login dengan password baru.

Reset memakai **tautan bertoken**, bukan OTP pendaftaran. Broker menetapkan token 60 menit dan throttle 60 detik; route permintaan/reset memakai `throttle:5,1`.

Respons email dikenal/tidak dikenal dibuat sama: `Jika email terdaftar, link reset password akan dikirim. Periksa juga folder spam.` Respons ini bukan jaminan inbox menerima email. Jika pengiriman gagal dengan exception, aplikasi memberi pesan email belum dapat dikirim.

Token salah/kedaluwarsa dan password confirmation berbeda ditolak. Kondisi ini tercakup test suite.

### 6.2 Profil Saya `/profile`

| Data | Perilaku |
| --- | --- |
| Nama | Dapat diubah, maksimal 255 karakter |
| Username | Unik, maksimal 50 karakter, mengikuti format pendaftaran |
| Telepon | Opsional, maksimal 20 karakter, validasi karakter nomor telepon |
| Email OTP/Google | Dikunci terhadap penggantian melalui profil |
| Email akun lama di luar kondisi penguncian | Perubahan dapat divalidasi; bukti verifikasi sebelumnya dihapus bila berubah |
| Asal dan verifikasi akun | Menampilkan asal pendaftaran dan status/metode verifikasi yang tercatat |

Ubah kolom yang diizinkan, tekan Simpan Perubahan, lalu Lanjutkan bila ingin kembali. Pengelola memakai navigasi pengelola saat membuka profil.

### 6.3 Kata sandi dan penghapusan akun

Profil aktif menampilkan tab **Atur Kata Sandi** bagi akun dengan `google_id`. Form mengirim ke `/profile/set-password`, meminta password/konfirmasi, dan tidak meminta password lama pada jalur ini. Akun Google kemudian dapat memakai login manual. Endpoint hanya untuk akun Google yang sudah login.

`PUT /password` tersedia dan teruji untuk perubahan dengan password lama. `DELETE /profile` tersedia dan teruji dengan konfirmasi password benar. Namun, **view profil aktif tidak menampilkan formulir ubah password manual atau hapus akun tersebut**. Endpoint yang ada tidak otomatis berarti tombolnya tersedia. Akun manual dapat memakai lupa password dari login.

### 6.4 Logout

Gunakan logout pada navigasi akun. Logout mengakhiri autentikasi, menginvalidasi session, meregenerasi token CSRF, dan kembali ke beranda. Halaman pribadi meminta login lagi. Selain POST logout, aplikasi memiliki fallback `GET /logout`.

**Media:** gambar login/gagal, lupa/reset password, profil tiap peran, password Google, dan video logout akan ditambahkan nanti.

## 7. Pemesanan tiket online dari awal

### 7.1 Tahap pertama: tanggal dan jumlah

1. Login sebagai wisatawan.
2. Buka pemesanan dari tiket atau `/wisatawan/pemesanan`.
3. Pilih tanggal hari ini atau setelah hari ini.
4. Isi jumlah tiap jenis tiket. Satu pesanan dapat berisi beberapa jenis.
5. Periksa ringkasan dan lanjutkan ke data pengunjung.

Server memeriksa tanggal, array pilihan, bilangan bulat tidak negatif, dan sedikitnya satu jumlah lebih dari nol. Setiap tiket terpilih harus aktif. Harga/subtotal dihitung dari database, bukan mempercayai total kiriman browser.

Pada tahap ini pilihan tersimpan di session `pemesanan_flow`. **Belum ada pesanan permanen** sampai data pengunjung berhasil dikirim. POST awal pemesanan dibatasi `throttle:10,1`.

Validator jumlah saat ini tidak menetapkan batas maksimum bisnis. Test menguji jumlah di atas batas lama; hal itu bukan uji kapasitas server untuk jumlah tak terbatas.

### 7.2 Tahap kedua: identitas tiap tiket

| Kategori | Data wajib |
| --- | --- |
| `perorangan` | Nama maksimal 100 karakter dan jenis kelamin `L` atau `P` untuk setiap tiket |
| `kendaraan` | Plat kendaraan maksimal 20 karakter untuk setiap tiket kendaraan |

Jumlah formulir mengikuti jumlah unit. Dua tiket orang dan satu kendaraan memerlukan dua nama/gender serta satu plat. Nama akun pemesan tidak menggantikan semua identitas pengunjung.

1. Lengkapi setiap kolom dan periksa tanggal, jumlah, serta total.
2. Kirim data; perbaiki pesan validasi bila ada.
3. Server menyimpan pesanan dan detail dalam transaksi database.
4. Flow sementara dihapus dan aplikasi menuju pembayaran.

Jika flow session tidak ada, halaman data diri mengarahkan pengguna untuk memulai lagi.

### 7.3 Data dan status awal

Satu pesanan menyimpan pemilik akun, tanggal, jumlah agregat, total, ringkasan pilihan, dan kode booking. Setiap unit menghasilkan satu detail dengan kode tiket sendiri, termasuk kendaraan.

Status pesanan awal `pending`. Detail sudah dibuat dengan `status_tiket=aktif`, tetapi belum dapat digunakan sebelum pesanan selesai. Aktif pada detail bukan bukti sudah lunas.

Setelah tersimpan, aplikasi mencoba mengirim `PemesananBerhasil`. Email ini berarti pesanan tercatat, bukan pembayaran disetujui. Kegagalan email tidak otomatis membatalkan pesanan yang sudah tersimpan.

### 7.4 Contoh yang dibuktikan pengujian

Data uji memakai tiket orang Rp15.000 dan kendaraan Rp5.000. Ini **bukan penetapan tarif resmi**.

```text
2 x Rp15.000 = Rp30.000
1 x Rp 5.000 = Rp 5.000
Total        = Rp35.000
```

Server tetap menyimpan Rp35.000 meskipun request uji sengaja mengirim `total_harga=1`. Tarif aktual mengikuti database tiket.

**Media:** gambar pilihan, ringkasan, identitas orang/kendaraan, validasi, dan video pemesanan akan ditambahkan nanti.

## 8. Pembayaran, penolakan, dan unggah ulang

### 8.1 Metode online

`/wisatawan/pembayaran/{id_pemesanan}` hanya untuk pemilik pesanan. Katalog `config/payment.php` memuat **BCA, BRI, Mandiri, DANA, OVO, dan GoPay**. Ketersediaan aktual mengikuti environment.

- Di luar percobaan, metode memerlukan nomor tujuan dan nama penerima lengkap.
- Jika belum ada metode resmi lengkap, pengajuan ditolak dan pengguna diminta menghubungi pengelola.
- **QRIS tidak ada pada katalog pembayaran online aktif.** Label QRIS pada loket offline berbeda konteks.
- Tidak ada gateway yang mengonfirmasi dana otomatis dari penyedia.

### 8.2 Langkah wisatawan

1. Periksa pesanan dan total.
2. Pilih metode yang tersedia.
3. Periksa tujuan/nama penerima; gunakan kontrol salin bila tersedia.
4. Lakukan pembayaran di aplikasi bank/dompet digital di luar website.
5. Kembali, pilih bukti, dan periksa pratinjau gambar/informasi PDF.
6. Gunakan ganti/hapus jika berkas salah, kemudian pilih ulang.
7. Kirim bukti dan tunggu keputusan pada detail/riwayat.

README tidak memuat rekening/kredensial. Simpan bukti asli untuk klarifikasi dengan pengelola.

### 8.3 Validasi bukti

| Aturan | Implementasi |
| --- | --- |
| Format | JPG, JPEG, PNG, PDF |
| Ukuran | Maksimal 5120 KB / 5 MB menurut validator `max:5120` |
| Berkas | Wajib pada pengajuan |
| Metode | Harus termasuk metode yang tersedia pada server |
| MIME server | `image/jpeg`, `image/png`, `application/pdf` |
| Penyimpanan | Disk `public`, direktori `bukti/`, nama acak |

Unggah berhasil membuat pembayaran `pending`; pesanan tetap `pending`. Pratinjau dan keberhasilan upload tidak menetapkan lunas.

### 8.4 Pemeriksaan pengelola

1. Buka Verifikasi Pembayaran dan pilih bukti menunggu.
2. Cocokkan pemesan, total, metode, serta bukti dengan penerimaan dana di luar aplikasi.
3. Pilih `valid` atau `ditolak`.
4. Server menyimpan keputusan menggunakan transaksi dan penguncian baris.

| Keputusan | Pembayaran | Pesanan | Berikutnya |
| --- | --- | --- | --- |
| Diterima | `valid` | `selesai` | Tiket digunakan sesuai tanggal dan syarat masuk |
| Ditolak | `ditolak` | `pending` | Wisatawan memperbaiki bukti |

Keputusan hanya diproses untuk kondisi menunggu yang sesuai. Pembayaran valid tidak dapat diputuskan ulang melalui endpoint biasa. Sistem mencoba mengirim email hasil setelah perubahan tersimpan.

### 8.5 Pengajuan ulang

1. Setelah ditolak, buka pembayaran pesanan yang sama.
2. Periksa kembali tujuan/total, pilih metode dan bukti pengganti.
3. Kirim; record pembayaran yang sama diperbarui menjadi `pending`.
4. Berkas lama dihapus setelah penggantian berhasil.
5. Tunggu pemeriksaan baru.

Pengajuan ulang tidak membuat pembayaran kedua. Pembayaran masih pending atau sudah valid tidak menerima upload ulang lewat alur biasa; membuka halaman pembayarannya diarahkan ke detail.

### 8.6 Mode percobaan

`PAYMENT_TRIAL_MODE` hanya berlaku pada `APP_ENV=local` atau `testing`. Metode tanpa nomor/nama lengkap dapat dipakai menguji bukti contoh dengan label percobaan. Label ini bukan tujuan transfer sungguhan.

Test membuktikan mode percobaan tidak mengaktifkan metode tanpa rekening pada environment `production`. Jangan menganggap metode percobaan sebagai kanal pembayaran resmi.

**Media:** gambar metode, kondisi belum tersedia, pratinjau JPG/PDF, ganti/hapus, menunggu, ditolak, unggah ulang, dan video keputusan akan ditambahkan nanti.

## 9. Riwayat, e-ticket, dan status transaksi

### 9.1 Riwayat dan kepemilikan

`/wisatawan/pemesanan/riwayat` menampilkan pesanan akun login dari yang terbaru. Detail memuat booking, tanggal, total, status, dan tiket sesuai kondisi transaksi.

Mengganti ID tidak memberi akses pesanan orang lain. Detail, halaman sukses, pembayaran, endpoint status, dan upload milik akun lain ditolak dengan 404 pada pengujian tambahan.

### 9.2 Membaca status

| Data | Arti |
| --- | --- |
| Pesanan `pending`, belum ada pembayaran | Belum mengajukan bukti |
| Pesanan `pending`, pembayaran `pending` | Menunggu pengelola |
| Pesanan `pending`, pembayaran `ditolak` | Perlu pengajuan ulang |
| Pesanan `selesai`, pembayaran `valid` | Transaksi disetujui; tetap ikuti syarat masuk |
| Status tidak konsisten | Tidak dianggap berhasil hanya dari satu kolom |

`isApproved()` memerlukan pesanan `selesai` **dan** pembayaran `valid`. Endpoint mengirim token gabungan untuk mendeteksi perubahan.

Nilai `diproses`/`dibatalkan` terdapat pada skema, tetapi tidak membuktikan ada tombol pembatalan/refund wisatawan. Route tersebut tidak tersedia pada alur aktif.

### 9.3 Menggunakan e-ticket

1. Setelah diterima, buka detail pesanan.
2. Periksa tanggal/identitas setiap unit.
3. Siapkan QR atau kode tiket ketika datang.
4. Gunakan cetak/unduh untuk membuka dialog cetak browser.
5. Pilih simpan PDF di browser bila fasilitas tersebut tersedia.

E-ticket memakai `window.print()` dan gaya cetak, berbeda dari endpoint PDF laporan. Browser tidak selalu langsung mengunduh PDF secara otomatis.

Gambar QR menggunakan layanan eksternal `api.qrserver.com` dan memuat URL verifikasi. Pemuatan bergantung internet dan layanan tersebut; ketersediaannya pada semua perangkat tidak dibuktikan suite aplikasi.

### 9.4 Status tiap detail

| Data | Arti |
| --- | --- |
| `aktif` | Belum digunakan; tetap perlu pesanan selesai dan tanggal hari ini |
| `digunakan` | Telah check-in |
| `kadaluarsa` | Dikenali pemeriksaan; tidak membuktikan job otomatis mengisinya |
| `check_out_at` terisi | Sudah keluar; status detail tetap `digunakan` |

**Media:** gambar riwayat, seluruh kondisi status, e-ticket, dialog cetak, dan video penggunaan akan ditambahkan nanti.

## 10. Verifikasi publik dan pintu masuk

### 10.1 Verifikasi publik

`/tiket/verifikasi/{kode}` dapat dibuka tanpa login. Kode tak dikenal memberi hasil tidak ditemukan; kode dikenal menampilkan hasil/data tiket yang disiapkan controller.

Penanda `valid` publik memeriksa pesanan `selesai` dan detail `aktif`. Perhitungannya **tidak memeriksa tanggal kunjungan**. Membuka URL atau QR tidak melakukan check-in dan tidak mengubah penggunaan tiket. Pemeriksaan tanggal ada pada check-in petugas.

Karena publik, pertimbangkan data yang tampil sebelum membagikan kode/tautan. Hasil publik bukan otorisasi untuk mengubah tiket.

### 10.2 Pencarian petugas

1. Login pengelola dan buka `/pengelola/scan`.
2. Ketik kode tiket, gunakan scanner pengisi kode, atau aktifkan kamera.
3. Izinkan kamera bila diminta; tampilan menggunakan `html5-qrcode` eksternal.
4. Periksa identitas, tanggal, status pesanan, dan status penggunaan.
5. Konfirmasikan masuk bila memenuhi syarat.

Pencarian/kamera memasukkan kode; server memutuskan penggunaannya. Endpoint teruji tidak membuktikan izin kamera dan QR fisik pada semua perangkat.

### 10.3 Check-in

Server menolak bila kode tidak ditemukan, detail bukan `aktif`, pesanan bukan `selesai`, atau tanggal kunjungan bukan hari ini pada timezone aplikasi.

Jika diterima, detail menjadi `digunakan` dan `check_in_at` diisi. Check-in kedua ditolak. Perubahan menggunakan transaksi dan penguncian tiket.

Controller scan memeriksa status **pesanan** sebagai syarat lunas; tidak memeriksa ulang record pembayaran seperti `isApproved()` wisatawan. Alur persetujuan normal mengubah pesanan dan pembayaran bersama. Jangan mengklaim scan memeriksa mutasi bank atau pembayaran secara terpisah.

### 10.4 Check-out

Cari kode yang sudah masuk, lalu pilih check-out. Server mensyaratkan detail `digunakan`, waktu keluar belum ada, dan tanggal kunjungan hari ini. Hasilnya `check_out_at` terisi. Check-out kedua ditolak dan tiket tidak kembali aktif untuk masuk ulang.

### 10.5 Auto-checkout

`php artisan tiket:auto-checkout` mengisi waktu keluar untuk detail dengan check-in **lebih dari 24 jam** yang belum check-out. Perintah ini teruji pada fixture.

Pada verifikasi, `php artisan schedule:list` menghasilkan `No scheduled tasks have been defined.` Definisi di Kernel lama belum terlihat pada scheduler aktif. **Belum dapat dinyatakan auto-checkout berjalan otomatis setiap jam.** Aktivasi jadwal perlu pekerjaan terpisah.

**Media:** gambar hasil publik, kode salah, scanner/kamera, check-in/out, penolakan, dan video petugas pada perangkat nyata akan ditambahkan nanti.

## 11. Pemesanan offline oleh pengelola

Offline berarti **penjualan loket yang dicatat pengelola pada aplikasi web**. Bukan kemampuan tanpa internet, PWA, atau sinkronisasi otomatis setelah jaringan pulih.

### 11.1 Alur loket

1. Login pengelola dan buka Pemesanan Offline.
2. Pilih tanggal/jumlah tiket, lalu lanjutkan.
3. Isi nama dan jenis kelamin tiap detail.
4. Pilih metode penerimaan pembayaran.
5. Pastikan dana telah diperiksa petugas, lalu simpan.
6. Sistem membuat pesanan `selesai` dan pembayaran `valid` tanpa antrean online.
7. Tinjau tiket pada halaman sukses atau riwayat loket.

Booking berawalan `BJ-OFF-`. Pemilik record adalah pengelola pencatat. Riwayat loket dibatasi pada akun pencatat yang login dan pesanan selesai.

### 11.2 Perbedaan alur

| Aspek | Online | Loket offline |
| --- | --- | --- |
| Pelaksana/pemilik | Wisatawan | Pengelola pencatat |
| Tanggal | Hari ini atau setelahnya | Validator meminta tanggal valid tanpa `after_or_equal:today` |
| Orang | Nama dan gender | Nama dan gender |
| Kendaraan | Plat | Nama/gender untuk semua detail; plat `null` |
| Metode | Katalog online terkonfigurasi | `Tunai,BCA,BRI,Mandiri,Dana,OVO,GoPay,QRIS` |
| Bukti | Wajib unggah | `null`, tidak diwajibkan |
| Setelah simpan | Pesanan `pending` | Pesanan `selesai`, pembayaran `valid` |
| Verifikasi | Perlu pengelola | Tidak lewat antrean online |

QRIS loket adalah label pencatatan, bukan QR dinamis atau konfirmasi penyedia. Tanggal lampau yang bisa dicatat loket tetap tidak membuat tiket boleh check-in pada tanggal berbeda.

**Media:** gambar pilihan, identitas, metode, sukses/riwayat, dan video penjualan loket akan ditambahkan nanti.

## 12. Seluruh menu pengelola

### 12.1 Dashboard

| Ringkasan | Dasar controller |
| --- | --- |
| Pengunjung hari ini | Agregat `jumlah` pesanan selesai dengan kunjungan hari ini |
| Pendapatan bulan ini | Total pembayaran valid berdasarkan bulan/tahun `tgl_bayar` |
| Menunggu verifikasi | Pesanan dengan pembayaran pending |
| Total wisatawan | Akun role wisatawan |
| Scan hari ini | Detail digunakan dengan tanggal `updated_at` hari ini |
| Grafik komposisi | Jumlah pesanan selesai dikelompokkan menurut tiket utama |
| Pesanan terbaru | Lima pesanan terbaru |

Pengunjung merupakan unit pesanan dan dapat mencakup kendaraan, bukan jumlah orang yang fisik masuk. Grafik memakai tiket utama, bukan seluruh detail pesanan campuran. Scan menurut `updated_at` juga bukan pengukuran terpisah arus masuk/keluar.

### 12.2 Kelola Tiket

1. Buka daftar dan pilih tambah.
2. Isi nama Indonesia, nama Inggris opsional, harga, kategori, dan status.
3. Simpan; yang aktif dapat muncul di publik.
4. Gunakan edit untuk mengubah harga/data atau menonaktifkan.
5. Gunakan hapus untuk soft delete bila tak diperlukan.

Nama maksimal 100 karakter; harga angka minimal 0; kategori `perorangan,kendaraan`; status `aktif,nonaktif`. Detail transaksi menyimpan snapshot nama/harga. Form/controller tidak menyediakan pengaturan kuota harian.

### 12.3 Kelola Galeri

1. Tambah foto JPG/JPEG/PNG maksimal 5120 KB / 5 MB.
2. Pilih tipe `wisata` atau `restoran`.
3. Isi keterangan Indonesia/Inggris opsional, masing-masing maksimal 255 karakter.
4. Simpan dan tinjau tampilan tujuan.
5. Gunakan filter tipe dan tindakan hapus bila diperlukan.

Server memvalidasi gambar/MIME. Hapus melakukan soft delete record dan menghapus berkas. Route aktif mendukung daftar, tambah, simpan, hapus; **tidak ada edit/update galeri**. Form ini untuk gambar, bukan video.

Gunakan foto asli sesuai lokasi/keterangan. Pola nama foto contoh tertentu disaring dari sejumlah bagian pemasaran.

### 12.4 Verifikasi Pembayaran

Daftar hanya mengambil pesanan yang sudah memiliki pembayaran, memprioritaskan pending lalu terbaru, dengan pagination 20. Detail menyediakan informasi pemesan, pesanan, tiket, dan bukti. Ikuti [langkah keputusan](#84-pemeriksaan-pengelola).

Pesanan belum memiliki pembayaran dapat muncul pada notifikasi pesanan baru, tetapi belum masuk daftar bukti yang diajukan.

### 12.5 Scan Tiket

Menu menyediakan pencarian kode, kamera, hasil pemeriksaan, tindakan masuk/keluar, dan tiket hari ini dengan pesanan selesai. Aturannya dijelaskan pada [pintu masuk](#10-verifikasi-publik-dan-pintu-masuk).

### 12.6 Pemesanan Offline dan riwayat

Menu menyediakan flow loket dan daftar transaksi selesai milik pencatat yang login. Riwayat menggunakan pagination 20. Gunakan laporan untuk meninjau data offline sesuai filter.

### 12.7 Laporan kunjungan

1. Pilih laporan kunjungan.
2. Isi tanggal awal/akhir; akhir tidak boleh lebih awal.
3. Pilih online/offline bila perlu; tanpa pilihan berarti keduanya.
4. Tampilkan, lalu ekspor PDF jika dibutuhkan.

Periode berdasarkan `tgl_kunjungan`. Offline dikenali dari kode `BJ-OFF-`; online adalah yang tidak berawalan itu.

Controller menjumlahkan `jumlah` hasil filter tanpa syarat tambahan selesai/check-in. Rekap ini memuat unit pesanan, termasuk kemungkinan kendaraan dan pesanan belum selesai; bukan bukti semuanya datang.

### 12.8 Laporan transaksi

Langkah periode/filter/ekspor serupa. Laporan hanya mengambil pembayaran `valid`, memakai `tgl_bayar`, dan menjumlahkan total pembayaran hasil filter. Kedua laporan memiliki tampilan web serta PDF server DomPDF dengan A4 landscape.

Test lama memeriksa PDF kosong; tambahan memeriksa isi/filter online-offline pada transaksi loket. Seluruh variasi laporan berisi data nyata belum diaudit hasil cetaknya.

### 12.9 Data Wisatawan

Daftar hanya mengambil role wisatawan. Tersedia pencarian nama/username/email, jumlah pesanan, asal pendaftaran, status/metode verifikasi, dan pagination 20.

Menu ini **daftar/pencarian, bukan CRUD pengguna**. Tidak ada endpoint tambah, ubah peran, atau hapus wisatawan dari menu ini.

### 12.10 Notifikasi, profil, dan logout

Notifikasi mengelompokkan bukti pending dan pesanan baru tanpa pembayaran. Profil dan logout mengikuti [panduan akun](#6-lupa-password-profil-dan-logout).

**Media:** gambar semua daftar/form/detail pengelola, filter, pagination, dashboard, notifikasi, laporan/PDF, dan video tiap pekerjaan akan ditambahkan nanti.

## 13. Bahasa, email, dan pembaruan status

### 13.1 Bahasa

`/lang/id` dan `/lang/en` menyimpan locale ke session dan kembali ke halaman sebelumnya. Locale tak dikenal tidak mengganti pilihan.

Teks memakai `lang/id/messages.php`, `lang/en/messages.php`, serta beberapa pilihan teks pada view. Nama tiket/keterangan foto memiliki kolom Inggris dengan fallback Indonesia. Pengalih bahasa tidak membuktikan seluruh pesan validasi dan halaman pengelola telah diterjemahkan lengkap.

### 13.2 Email

| Pemicu | Mekanisme | Makna |
| --- | --- | --- |
| Daftar email | `RegistrationOtpMail` | Bukti kepemilikan sebelum akun dibuat |
| Lupa password | Notifikasi reset Laravel | Tautan token password baru |
| Pesanan tersimpan | `PemesananBerhasil` | Pencatatan pesanan, belum persetujuan bayar |
| Persetujuan | `PembayaranValid` | Keputusan valid |
| Penolakan | `PembayaranDitolak` | Pengajuan perlu diperbaiki |

Adanya template lain seperti `WelcomeMail` tidak membuktikan email itu selalu dikirim saat daftar. SMTP/pengirim harus benar untuk pengiriman nyata. Test fake tidak membuktikan inbox, spam, reputasi domain, atau latensi penyedia.

### 13.3 Polling status

Halaman transaksi menggunakan endpoint status milik pemesan untuk mendeteksi perubahan token/indikator persetujuan. Notifikasi pengelola juga memakai polling; interval layout pengelola adalah 20000 milidetik.

Endpoint notifikasi mengambil sampai 10 pembayaran pending dan sampai 10 pesanan baru tanpa pembayaran. `count` adalah jumlah hasil dua kelompok yang dibatasi itu, bukan total seluruh pekerjaan tanpa batas.

Pembaruan otomatis berarti permintaan berkala browser. Dependency/variabel broadcast tidak membuktikan WebSocket atau push notification aktif.

## 14. Rencana gambar dan video

### 14.1 Dokumentasi README

**Seluruh tangkapan layar dan video tutorial berikut akan ditambahkan nanti.** Penjelasan teks tersedia; tabel ini tidak menyatakan media baru sudah dibuat.

| Halaman/fitur | Gambar yang akan ditambahkan | Video yang akan ditambahkan |
| --- | --- | --- |
| Beranda/navigasi | Desktop, ponsel, menu, konten utama | Tur halaman publik |
| Peta/rute | Titik awal, tujuan, tautan Maps | Memilih keberangkatan |
| Restoran | Pembuka, foto, keadaan kosong | Menelusuri informasi |
| Tiket/detail | Jenis, harga, estimator | Memilih dan memahami biaya |
| Login/tamu | Form, gagal, pilihan guest | Login tiap peran dan alur tamu |
| Daftar | Form dan validasi | Daftar sampai meminta OTP |
| OTP/email | Form, countdown, resend, salah/kedaluwarsa; data disamarkan | Verifikasi, kirim ulang, batal |
| Google | Tombol, profil baru, username | OAuth dan pemilihan username |
| Lupa/reset | Form, notifikasi, reset | Tautan sampai login kembali |
| Profil | Tiap peran, email terkunci, password Google | Ubah profil/password Google |
| Pemesanan | Tanggal, jumlah, ringkasan | Memesan beberapa jenis |
| Identitas | Nama/gender dan plat per unit | Melengkapi sampai tersimpan |
| Pembayaran | Aktif, belum tersedia, percobaan | Instruksi dan unggah |
| Pratinjau bukti | Gambar/PDF, ganti/hapus, berkas ditolak | Memperbaiki berkas |
| Riwayat/status | Belum bayar, pending, ditolak, berhasil | Melacak dan unggah ulang |
| E-ticket | Kode/QR tiap tiket, dialog cetak | Menyiapkan dan simpan PDF browser |
| Verifikasi publik | Ditemukan, tidak ada, sudah digunakan | Membuka QR |
| Dashboard | Metrik, grafik, pesanan terbaru | Membaca angka dan batasnya |
| Kelola Tiket | Daftar, tambah/edit, nonaktif/hapus | Siklus kelola tiket |
| Galeri | Upload, dua bahasa, filter/hapus | Menambah foto asli |
| Verifikasi bayar | Daftar, bukti, keputusan | Terima dan tolak |
| Notifikasi | Dua kelompok dan badge | Membuka pekerjaan |
| Scan | Manual, kamera, sukses/gagal | Check-in/out perangkat nyata |
| Loket offline | Pilihan, identitas, metode, sukses | Penjualan awal sampai akhir |
| Riwayat loket | Daftar/detail | Meninjau transaksi pencatat |
| Laporan kunjungan | Periode, tipe, hasil/PDF | Filter dan ekspor |
| Laporan transaksi | Periode, tipe, total/PDF | Rekap pembayaran valid |
| Data Wisatawan | Pencarian, asal/verifikasi, pagination | Menemukan dan membaca akun |
| Bahasa/logout | ID/EN dan kembali guest | Ganti bahasa dan akhiri sesi |

Saat media ditambahkan, gunakan keadaan aplikasi yang benar dan samarkan email, kode aktif, bukti transfer, serta data pribadi. Jangan tampilkan password/token/kredensial. Rekaman layanan nyata baru menjadi bukti setelah alurnya benar-benar dijalankan.

### 14.2 Media di dalam website

Foto asli destinasi, canyon, fasilitas, dan restoran dapat dilengkapi nanti melalui Galeri sesuai tipe. Foto lanskap tidak dinyatakan sebagai foto restoran.

Video latar beranda dapat ditambahkan nanti di `public/videos/bangkiang-jaran.mp4` atau path relatif `TOURISM_HERO_VIDEO`. Berkas default itu tidak ditemukan saat sesi ini; ini tidak memeriksa seluruh path kustom pada deployment lain.

Template menampilkan video hanya bila path aman, ekstensi MP4/WebM, dan berkas lokal ada di bawah `public/`. Upload video melalui Galeri belum tersedia. Menambah media tidak otomatis memverifikasi fakta lapangan atau harga resmi.

## 15. Teknologi dan cara kerja sistem

### 15.1 Stack dari manifest

| Bagian | Teknologi/constraint |
| --- | --- |
| Backend / PHP | Laravel `^13.0` / PHP `^8.3` |
| Tampilan | Blade, Tailwind `^3.1.0`, Alpine.js `^3.4.2` |
| Auth dasar | Breeze `^2.4`, disesuaikan aplikasi |
| OAuth | Socialite `^5.28` |
| Token API | Sanctum `^4.3`; bukan bukti UI kelola token tersedia |
| Build | Vite `^4.0.0`, `laravel-vite-plugin ^0.7.2` |
| PDF | `barryvdh/laravel-dompdf ^3.1` |
| HTTP client | Guzzle `^7.2` |
| Database lokal | MySQL/MariaDB sesuai environment proyek |
| Database test | SQLite in-memory dari `phpunit.xml` |
| Pengujian | PHPUnit `^11.5` |

Constraint bukan versi terpasang yang persis. `composer.lock` dan `package-lock.json` mengunci instalasi.

### 15.2 Alur request

```text
Browser -> route web/auth
  -> middleware session, CSRF, login eksplisit, locale, auth, role
  -> controller -> validasi/otorisasi
  -> Eloquent/transaksi database
  -> Blade HTML atau JSON
  -> JavaScript untuk interaksi dan polling
```

Controller/service juga memanggil filesystem untuk upload, mailer untuk email, dan DomPDF untuk laporan. Halaman dirender server; aplikasi ini bukan Next.js atau SPA React. Alpine/JavaScript menambahkan interaksi pada halaman.

Layout publik, autentikasi, dan pengelola memuat CSS Tailwind serta Alpine dari build Vite (`resources/css/app.css` dan `resources/js/ui.js`). Token tema berada di `tailwind.config.js`; jalankan `npm run build` setelah perubahan frontend dan sebelum menjalankan versi tanpa server Vite. Entry UI memuat Alpine tanpa bootstrap Axios/Lodash.

### 15.3 Hak akses

| Pelaku | Akses utama |
| --- | --- |
| Tamu | Publik, autentikasi, verifikasi tiket publik |
| Wisatawan | Publik, profil, pemesanan, pembayaran/riwayat/detail sendiri |
| Pengelola | Route pengelola dan profil |

Route wisatawan memakai `auth` + `role:wisatawan`; pengelola memakai `auth` + `role:pengelola`. Kepemilikan diperiksa lagi pada query pesanan wisatawan. Menyembunyikan tombol saja bukan otorisasi.

### 15.4 Mekanisme pengamanan yang terlihat

- Hash password dan OTP; cache tantangan terenkripsi serta terikat session.
- Pembatasan login, daftar, OTP, reset, dan awal pemesanan.
- CSRF pada route web dan regenerasi session setelah autentikasi.
- Username/email unik, validasi ukuran/MIME upload, harga dari server.
- Transaksi/penguncian perubahan pembayaran, keputusan, dan scan.
- Constraint identitas tertentu dan satu pembayaran per pesanan.
- Tidak menggabungkan akun Google hanya berdasarkan email sama.

Ini bukan sertifikasi keamanan atau pentest menyeluruh. Bukti pembayaran saat ini berada pada disk publik; dokumentasi ini tidak mengubahnya menjadi penyimpanan privat. Pengujian SQLite juga bukan pembuktian seluruh race condition pada MySQL produksi.

## 16. Database dan penyimpanan berkas

### 16.1 Tabel utama

| Tabel | Fungsi |
| --- | --- |
| `users` | Identitas, role, kredensial/Google ID, metadata login dan metode akun |
| `tiket` | Nama dua bahasa, harga, kategori, status, soft delete |
| `pemesanan` | Pemilik, tanggal, jumlah, total, booking, ringkasan JSON, status |
| `detail_pemesanan` | Kode per unit, nama/gender/plat, snapshot tiket, status dan waktu masuk/keluar |
| `pembayaran` | Pesanan, total, metode, bukti, status, tanggal bayar |
| `galeri` | Path foto, keterangan dua bahasa, tipe, soft delete |
| `konten` | Tabel/model yang ada; bukan bukti CRUD konten aktif |
| `activity_logs` | Aktivitas yang dicatat helper |
| `log_login_sukses`, `log_login_gagal` | Log terpisah menurut model/migrasi lokal |
| `password_resets` | Token broker reset password |

Tabel log tidak otomatis menyediakan menu manajemen log.

### 16.2 Relasi dan kode

```text
User -> banyak Pemesanan
Pemesanan -> satu pemilik, satu tiket utama
          -> banyak DetailPemesanan
          -> satu Pembayaran
DetailPemesanan -> satu Pemesanan dan jenis Tiket terkait
Galeri -> foto wisata atau restoran
```

Tiket utama pesanan tidak menggantikan seluruh daftar detail. Pesanan campuran mempunyai rincian pada ringkasan/detail. `kode_booking` menandai keseluruhan pesanan; `kode_tiket` dipakai pemeriksaan tiap unit. Jangan memakai booking sebagai pengganti seluruh kode tiket di pintu masuk.

### 16.3 Asal akun dan verifikasi

| Kolom | Arti |
| --- | --- |
| `registration_method` | Asal pendaftaran email/Google |
| `email_verification_method` | Bukti OTP/Google atau kondisi akun lama |
| `email_verified_at` | Waktu verifikasi |
| `last_login_method` | Login terakhir manual/Google |
| `last_login_at`, `login_count` | Waktu/jumlah login |

Migrasi akun lama tidak memberi bukti OTP palsu. `AccountRegistrationMethodTest` memeriksa klasifikasi tersebut.

### 16.4 Penyimpanan runtime

| Lokasi | Isi |
| --- | --- |
| `storage/app/public/galeri/` | Foto unggahan |
| `storage/app/public/bukti/` | Bukti pembayaran |
| `public/storage` | Tautan publik ke unggahan |
| `public/images/`, `public/videos/` | Aset statis foto/video bila tersedia |
| `public/build/` | Hasil Vite |
| `storage/framework/` | Session/cache/view runtime |
| `storage/logs/` | Log aplikasi |

Backup SQL tidak otomatis mencakup unggahan. Pemulihan lengkap memerlukan database dan berkas yang cocok.

## 17. Peta halaman dan endpoint

Parameter `{id}` adalah identitas record. Tabel mengelompokkan endpoint, bukan menganggap semua respons JSON sebagai menu. Untuk daftar runtime beserta middleware, gunakan `php artisan route:list`.

### 17.1 Publik dan autentikasi

| Metode | URL | Fungsi |
| --- | --- | --- |
| GET | `/` | Beranda |
| GET | `/restoran` | Restoran |
| GET | `/informasi` | Redirect beranda |
| GET | `/tiket`, `/tiket/{id}` | Daftar/detail tiket aktif |
| GET | `/tiket/verifikasi/{kode}` | Verifikasi publik |
| GET, POST | `/register` | Form/permintaan OTP |
| GET, POST | `/register/otp` | Form/verifikasi OTP |
| POST | `/register/otp/resend` | Kirim ulang |
| POST | `/register/otp/cancel` | Batal |
| GET, POST | `/login` | Form/proses login |
| GET | `/auth/google`, `/auth/google/callback` | Mulai/callback OAuth |
| GET, POST | `/forgot-password` | Form/minta tautan reset |
| GET | `/reset-password/{token}` | Form reset |
| POST | `/reset-password` | Simpan melalui token |
| GET | `/lang/{locale}` | Pilih ID/EN |
| GET | `/up` | Health framework, bukan menu wisatawan |

### 17.2 Akun dan kompatibilitas

| Metode | URL | Fungsi |
| --- | --- | --- |
| GET | `/redirect-after-login` | Pengalihan peran/tujuan |
| GET, PATCH | `/profile` | Tampil/simpan profil |
| POST | `/profile/set-password` | Password akun Google |
| DELETE | `/profile` | Hapus akun dengan password; form tidak tampil di profil aktif |
| PUT | `/password` | Ubah dengan password lama; form manual tidak tampil di profil aktif |
| GET, POST | `/confirm-password` | Konfirmasi Breeze |
| GET | `/verify-email` | Prompt kompatibilitas Breeze |
| GET | `/verify-email/{id}/{hash}` | Verifikasi tautan signed |
| POST | `/email/verification-notification` | Minta tautan verifikasi Breeze |
| POST, GET | `/logout` | Logout utama/fallback |
| GET | `/api/user` | Pengguna terautentikasi Sanctum, bukan API CRUD lengkap |

Route verifikasi tautan lama tetap ada; daftar email utama memakai OTP sebelum akun dibuat. Keduanya bukan dua tahap wajib pada alur daftar yang sama.

### 17.3 Wisatawan

Semua memakai prefix `/wisatawan` dan role wisatawan.

| Metode | Sisa URL | Fungsi |
| --- | --- | --- |
| GET, POST | `/pemesanan` | Pilihan/simpan flow |
| GET, POST | `/pemesanan/data-diri` | Data/simpan pesanan |
| GET | `/pemesanan/sukses/{id}` | Hasil pesanan sendiri |
| GET | `/pemesanan/riwayat` | Riwayat sendiri |
| GET | `/pemesanan/status` | JSON status daftar sendiri |
| GET | `/pemesanan/{id}/status` | JSON satu pesanan sendiri |
| GET | `/pemesanan/{id}` | Detail/e-ticket sendiri |
| GET, POST | `/pembayaran/{id_pemesanan}` | Form/unggah bukti sendiri |

### 17.4 Pengelola

Semua memakai prefix `/pengelola` dan role pengelola.

| Metode | Sisa URL | Fungsi |
| --- | --- | --- |
| GET | `/dashboard` | Ringkasan |
| GET | `/tiket`, `/tiket/create` | Daftar/tambah |
| POST | `/tiket` | Simpan tiket |
| GET | `/tiket/{id}/edit` | Form edit |
| PUT, PATCH | `/tiket/{id}` | Ubah |
| DELETE | `/tiket/{id}` | Soft delete |
| GET | `/galeri`, `/galeri/create` | Daftar/form foto |
| POST | `/galeri` | Simpan foto |
| DELETE | `/galeri/{id}` | Hapus berkas/soft delete |
| GET | `/verifikasi`, `/verifikasi/{id}` | Daftar/detail pembayaran |
| POST | `/verifikasi/{id}/validasi` | Keputusan |
| GET | `/notifikasi` | JSON polling |
| GET | `/scan` | Halaman scan |
| POST | `/scan/cari` | Cari kode |
| POST | `/scan/gunakan`, `/scan/checkout` | Masuk/keluar |
| GET, POST | `/pemesanan-offline` | Pilihan loket |
| GET, POST | `/pemesanan-offline/data-diri` | Data/simpan transaksi lunas |
| GET | `/pemesanan-offline/sukses/{id}` | Hasil milik pencatat |
| GET | `/pemesanan-offline/riwayat` | Transaksi selesai milik pencatat |
| GET | `/laporan` | Pilihan laporan |
| GET | `/laporan/kunjungan`, `/laporan/transaksi` | Rekap; PDF bila parameter `export` disertakan |
| GET | `/user` | Daftar/pencarian wisatawan |

## 18. Instalasi dan menjalankan secara lokal

Prosedur ini berasal dari konfigurasi repository. Instalasi mesin kosong, migrasi MySQL pengguna, dan Docker tidak dijalankan ulang saat penulisan panduan.

### 18.1 Prasyarat

- PHP `^8.3`, Composer, dan ekstensi yang dibutuhkan dependency.
- Node/npm kompatibel lockfile; tahap aset Docker memakai Node 20.
- MySQL/MariaDB; lingkungan lokal proyek yang tercatat memakai XAMPP.
- Ekstensi SQLite PHP untuk pengujian in-memory.
- Pengiriman email nyata untuk menyelesaikan daftar email di luar pengujian.

### 18.2 Checkout baru

Jalankan dari folder aplikasi, bukan root `Sistem/`:

```powershell
composer install
npm ci
```

Jika `.env` belum ada, salin contoh sekali:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Jangan menimpa `.env` berisi konfigurasi pengguna. Isi database, URL, dan layanan melalui environment tanpa menyalin rahasia ke README/commit.

Untuk database baru yang sengaja disiapkan, buat database dan tinjau migrasi terlebih dahulu, kemudian:

```powershell
php artisan migrate
php artisan storage:link
npm run build
php artisan serve --host=127.0.0.1 --port=8000
```

Migrasi mengubah schema; perintah itu panduan setup, bukan tindakan yang dilakukan pada MySQL pengguna saat dokumentasi. Seeder opsional untuk pengembangan; tinjau `DatabaseSeeder`/`DemoDataSeeder` beserta environment sebelum menjalankannya. Pendaftaran publik tidak membuat pengelola. Tidak ada akun/password demo dalam README.

### 18.3 Pengembangan frontend

Jalankan `npm run dev` pada terminal terpisah untuk Vite pengembangan, atau `npm run build` untuk hasil kompilasi. Script npm tersedia: `dev`, `build`, `tunnel`. Jangan mengasumsikan `npm start` atau `composer run dev`.

Jika MySQL XAMPP belum aktif, halaman pembaca data bisa gagal walaupun server PHP aktif. Gunakan stack proyek yang sesuai.

## 19. Konfigurasi layanan dan lingkungan

### 19.1 Nama variabel

Tabel menyebut nama, bukan nilai rahasia.

| Kelompok | Variabel |
| --- | --- |
| Aplikasi | `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL` |
| Database | `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` |
| CA database | `MYSQL_ATTR_SSL_CA` |
| Session/cache | `SESSION_DRIVER`, `SESSION_LIFETIME`, `CACHE_DRIVER` |
| Filesystem | `FILESYSTEM_DISK`; beberapa controller menyebut disk `public` eksplisit |
| Email | `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` |
| Google | `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI` |
| Penerima | `PAYMENT_ACCOUNT_NAME` |
| Bank | `PAYMENT_BCA_NUMBER`, `PAYMENT_BRI_NUMBER`, `PAYMENT_MANDIRI_NUMBER` |
| Dompet digital | `PAYMENT_DANA_NUMBER`, `PAYMENT_OVO_NUMBER`, `PAYMENT_GOPAY_NUMBER` |
| Percobaan | `PAYMENT_TRIAL_MODE` |
| Bantuan | `CONTACT_WHATSAPP` |
| Video | `TOURISM_HERO_VIDEO`, relatif terhadap `public/` |

Beberapa opsi dibaca konfigurasi walaupun tidak tercantum pada setiap revisi `.env.example`.

### 19.2 Mengaktifkan pengiriman OTP nyata

1. Pilih transport pengiriman, misalnya SMTP.
2. Isi pengirim/kredensial melalui environment dan penuhi persyaratan penyedia.
3. Pastikan koneksi server ke penyedia tersedia.
4. Segarkan cache konfigurasi sesuai lingkungan setelah perubahan.
5. Uji memakai alamat sendiri dan periksa inbox/spam.

`MAIL_MAILER=log` ditolak layanan OTP pada penggunaan biasa; bukan cara memperoleh OTP pengguna. `array` hanya diizinkan untuk simulasi unit test. Jangan mematikan perlindungan agar pendaftaran terlihat berhasil.

Pada beberapa instance, session/cache tantangan dan rate limit harus konsisten antar-instance. File pada satu mesin tidak otomatis dibagikan ke mesin lain.

### 19.3 Ketergantungan eksternal

| Layanan | Yang dipakai | Batas verifikasi sesi ini |
| --- | --- | --- |
| Google OAuth | Socialite/callback | Provider mock, bukan persetujuan nyata |
| Email | OTP/reset/transaksi | Fake/notifikasi test, bukan inbox |
| Google Maps | Iframe/tautan | Bukan akurasi GPS lapangan |
| QR tiket | Gambar eksternal | Bukan pembacaan QR fisik |
| Kamera | `html5-qrcode` | Endpoint scan, bukan izin kamera nyata |
| WhatsApp | Tautan bantuan jika dikonfigurasi | Bukan chatbot/konfirmasi otomatis |

## 20. Pengujian otomatis dan bukti verifikasi

### 20.1 Isolasi

`phpunit.xml` memakai `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:` dengan `force=true`, session/cache array, dan mail array. Flow test memakai fake filesystem/mail bila dibutuhkan.

Pekerjaan dokumentasi ini tidak menjalankan migrasi/seed pada MySQL pengguna, transfer bank, pembuatan akun nyata, atau pengiriman OTP ke pihak lain. Browser membuka publik secara read-only; session dapat membuat berkas runtime normal.

### 20.2 Hasil sesi

| Pemeriksaan | Hasil |
| --- | --- |
| Suite awal | `95 passed (606 assertions)` |
| Tambahan `DocumentedFeatureFlowTest` | `9 passed (107 assertions)` |
| Suite final | `104 passed (713 assertions)` |
| `php artisan view:cache` | Lulus; cache dibersihkan setelah pemeriksaan |
| `npm run build` | Lulus |
| Sintaks PHP test tambahan | Lulus |
| Diff berkas yang dikerjakan | Lulus |
| Browser enam halaman x lima preset | 30 kombinasi tanpa overflow horizontal/gambar selesai dimuat yang rusak |
| Scheduler | Tidak ada jadwal aktif terdaftar |

PHPUnit masih memberi peringatan schema XML deprecated yang sudah ada. Hasil lulus tidak berarti semua lingkungan bebas peringatan.

Preset browser: 320, 375, 768, 1024, 1440 piksel. `window.innerWidth` Agentty berturut-turut **321, 376, 769, 1025, 1442** pada sesi ini; ini pemeriksaan preset, bukan klaim angka ukur persis sama. Halaman: `/`, `/restoran`, `/tiket`, `/login`, `/register`, `/forgot-password`.

Pemeriksaan DOM mencocokkan URL, judul, overflow, dan gambar yang sudah selesai dimuat. Ini bukan audit piksel, semua gambar lazy-load setelah scroll, seluruh interaksi keyboard, atau perangkat fisik. Halaman privat diuji melalui feature test; tidak diklaim semuanya diuji di browser sesi ini.

### 20.3 Tes tambahan untuk memeriksa panduan

[DocumentedFeatureFlowTest.php](tests/Feature/DocumentedFeatureFlowTest.php) menambahkan:

1. Flow online dua orang/satu kendaraan, total dari server, identitas, upload, persetujuan, e-ticket, check-in, penolakan check-in ganda, check-out.
2. Penolakan tanggal lampau online, jumlah nol, dan data pengunjung kosong.
3. Loket tunai menghasilkan transaksi lunas dan filter laporan online/offline sesuai.
4. Tambah/ubah/nonaktif/soft delete tiket; detail nonaktif ditolak bagi wisatawan.
5. Unggah/filter/hapus berkas/soft delete galeri.
6. Pembatasan detail, sukses, pembayaran, status, dan upload pesanan orang lain.
7. Pengalih bahasa serta kode publik tak ditemukan tanpa mengubah penggunaan tiket.
8. Pencarian daftar wisatawan tidak mengembalikan akun pengelola.
9. Auto-checkout hanya detail check-in lebih dari 24 jam dan belum keluar.

Dua asumsi draft assertion dikoreksi setelah uji awal: pengelola diarahkan dari detail publik ke dashboard, dan penghapusan galeri memakai soft delete. Tes akhir mengikuti implementasi; source bisnis tidak diubah agar cocok dengan asumsi panduan.

### 20.4 Cakupan suite yang sudah ada

| Kelompok test | Cakupan |
| --- | --- |
| `Auth/RegistrationTest.php` | OTP benar/salah/format/kedaluwarsa, resend, limit, sesi, cancel, keunikan, kegagalan mail, tujuan booking |
| `Auth/AuthenticationTest.php`, `Auth/GuestSessionTest.php` | Login/gagal, guest, sesi lama/remember, role, tujuan booking, logout |
| `Auth/GoogleLoginTest.php` | Email terverifikasi, konflik identitas, state, username, redirect |
| `Auth/PasswordResetTest.php` | Reset/token/kedaluwarsa, konfirmasi, mail gagal, limit |
| `Auth/PasswordUpdateTest.php`, `Auth/PasswordConfirmationTest.php`, `Auth/EmailVerificationTest.php` | Endpoint Breeze terkait |
| `ProfileTest.php`, `AccountRegistrationMethodTest.php` | Profil, email terkunci, username, asal/bukti verifikasi |
| `ExampleTest.php` | Render publik, placeholder/foto, restoran, tiket |
| `VisitorRevisionTest.php`, `TransactionIntegrityTest.php` | Jumlah, percobaan, persetujuan, integritas pembayaran/identitas |
| `Wisatawan/PembayaranUlangTest.php` | Resubmit ditolak/metode belum tersedia |
| `Pengelola/ScanTiketTest.php` | Masuk/keluar, tanggal salah, belum lunas, kode tidak ada |
| `Pengelola/NotifikasiTest.php` | Notifikasi/prioritas pending |
| `Pengelola/HalamanPengelolaTest.php` | Render menu utama tanpa seed |
| `Pengelola/LaporanPdfTest.php`, `EmailTemplateTest.php` | PDF kosong/template email |

### 20.5 Menjalankan ulang

Jalankan dari folder aplikasi:

```powershell
php artisan test --compact
php artisan test --compact --filter=DocumentedFeatureFlowTest
php artisan test --compact --filter=RegistrationTest
php artisan test --compact --filter=GoogleLoginTest
php artisan test --compact --filter=ScanTiketTest
php artisan view:cache
php artisan view:clear
npm run build
php -l tests/Feature/DocumentedFeatureFlowTest.php
```

Filter merupakan alternatif pemeriksaan terarah; tidak perlu semuanya diulang setelah suite lengkap lulus tanpa perubahan.

### 20.6 Belum dibuktikan sesi ini

- Inbox OTP/reset/keputusan nyata, spam, dan latensi penyedia.
- OAuth nyata dan callback produksi.
- Transfer, mutasi, nomor tujuan resmi, rekonsiliasi keuangan.
- Kamera/QR fisik, izin GPS, akurasi lokasi.
- Printer fisik, simpan PDF semua browser, seluruh variasi PDF data nyata.
- Semua halaman privat di semua perangkat, screen reader/keyboard fisik.
- Instalasi bersih, Docker build/run, deploy, cron berjalan terus.
- Locking pada beban konkuren MySQL produksi.
- Kondisi destinasi/fasilitas, akses canyon, menu/harga resmi terbaru.

## 21. Pemeliharaan dan operasional

### 21.1 Cache dan build

Gunakan `view:cache` untuk kompilasi Blade dan `view:clear` untuk membersihkan cache hasil pemeriksaan. Aset memakai `npm run build`. Setelah environment berubah, segarkan cache konfigurasi sesuai lingkungan.

### 21.2 Backup

`scripts/backup-db.bat` mengasumsikan path MySQL XAMPP dan database lokal tertentu. Tidak dijalankan pada sesi ini dan tidak otomatis cocok semua konfigurasi.

Simpan backup SQL serta unggahan di lokasi aman di luar publik. Kredensial berasal dari environment/konfigurasi klien aman, bukan password command line/README. Uji restore pada lingkungan terpisah.

### 21.3 Log

`ActivityLogger` mencatat aktivitas dan meneruskan login sukses/gagal ke model terpisah bila penyimpanan berhasil. Metadata dapat memuat input login/identitas, IP, user-agent, dan metode; perlakukan sebagai data pribadi.

Ini bukan audit trail semua aksi. Menu log khusus tidak terdaftar. Saat diagnosis, jangan menyalin data sensitif/kredensial dari log ke dokumen.

### 21.4 Scheduler

Perintah auto-checkout tersedia, tetapi jadwal belum terdaftar saat verifikasi. Sebelum mengandalkan otomatisasi, pasang jadwal melalui mekanisme bootstrap aktif, periksa `schedule:list`, lalu siapkan pemanggilan scheduler host. Aktivasi tidak dilakukan saat penulisan README.

### 21.5 Docker

`docker/Dockerfile` memakai build aset Node 20 dan runtime PHP 8.3 Apache dengan Composer/ekstensi terkait. Entrypoint membuat storage link, menjalankan migrasi `--force`, lalu cache konfigurasi/route/view.

Karena startup menjalankan migrasi, tinjau efeknya dan arahkan hanya ke database yang disiapkan. Build/run Docker belum diuji dalam sesi ini; keberadaan Dockerfile bukan bukti image lolos pada mesin pengguna.

### 21.6 Demo dan produksi

`start-demo.bat` dan `npm run tunnel` tersedia untuk demo/ngrok. Tunnel dapat membuka server lokal ke jaringan publik; tidak dijalankan pada pekerjaan ini.

Produksi memerlukan URL/HTTPS, debug sesuai, database, SMTP, storage persisten, session/cache, akun pengelola, dan tujuan pembayaran resmi. Panduan ini bukan catatan bahwa konfigurasi produksi tersebut sudah diterapkan.

## 22. Pemecahan masalah

| Gejala | Pemeriksaan/tindakan |
| --- | --- |
| Tamu diminta login untuk pesan | Batas normal; login/daftar lalu lanjutkan tujuan |
| Akun belum muncul setelah daftar | Belum dibuat sampai OTP benar |
| OTP tak masuk | Periksa email/spam, SMTP/pengirim, limit; jangan mencari kode di log |
| OTP salah/habis waktu | Gunakan kode terbaru, sesi sama, masih berlaku |
| Resend belum tersedia | Tunggu 60 detik; limit per jam tetap dapat berlaku |
| Kode lama masih berlaku setelah resend gagal | Disengaja sampai expiry semula |
| Google belum aktif | Lengkapi environment OAuth/callback |
| Email manual sama dengan Google ditolak | Tidak digabung otomatis; gunakan metode akun lama |
| Email profil terkunci | Sesuai aturan email OTP/Google |
| Form hapus akun/password manual tidak ada | Endpoint tersimpan, tetapi form tidak tampil pada profil aktif |
| Sesi data diri hilang | Mulai flow pesan kembali |
| Tiket nonaktif tak tersedia | Tinjau status tiket melalui pengelola |
| Metode bayar kosong | Lengkapi penerima/nomor resmi; percobaan hanya local/testing |
| Tidak ada QRIS online | Katalog online belum memuat QRIS |
| Bukti ditolak format/ukuran | Pakai JPG/JPEG/PNG/PDF valid dalam batas |
| Upload sukses belum lunas | Tunggu keputusan pengelola |
| Pembayaran ditolak | Buka pembayaran dan unggah pengganti |
| QR tak muncul | Periksa layanan/internet; siapkan kode teks |
| Publik valid tetapi belum boleh masuk | Check-in tetap meminta tanggal hari ini |
| Check-in ditolak | Periksa kode, tanggal, pesanan selesai, detail aktif |
| Check-out ditolak | Harus sudah masuk, belum keluar, tanggal hari ini |
| Auto-checkout tak berjalan periodik | Scheduler belum memiliki jadwal aktif |
| Notifikasi tidak seketika | Polling; periksa koneksi/session |
| Angka pengunjung berbeda dari lapangan | Agregat unit pesanan bukan hitung orang fisik |
| Foto tidak tampil | Periksa berkas/path/storage link/tipe/filter placeholder |
| Video tidak tampil | Periksa berkas MP4/WebM dan path `TOURISM_HERO_VIDEO` |
| Error database | Pastikan MySQL proyek aktif dan environment cocok |
| Sesi berakhir | Login lagi; CSRF expired dapat kembali ke beranda |

## 23. Batas fitur dan pekerjaan lanjutan

Belum boleh dinyatakan tersedia hanya berdasarkan teks/berkas lama:

- QRIS online, gateway, konfirmasi bank atau refund otomatis;
- pembatalan mandiri melalui menu wisatawan;
- reservasi meja, keranjang makanan, stok/checkout makanan;
- kuota harian dan pengurangan stok tiket;
- upload video Galeri, edit/update galeri, CRUD wisatawan;
- loket tanpa internet/PWA dan sinkronisasi;
- aplikasi native, push notification, WebSocket aktif;
- galeri/lokasi publik tersendiri dari view tidak dirutekan;
- email sambutan setiap daftar hanya karena template ada;
- job auto-checkout/kedaluwarsa yang sudah aktif periodik;
- menu aktif untuk setiap endpoint Breeze tersimpan.

Tindak lanjut dapat meliputi foto/video asli, media panduan, uji layanan/perangkat nyata, scheduler, serta peninjauan perbedaan metrik/alur online-offline. Ini batas dan peluang lanjutan, bukan klaim implementasi.

## 24. Peta berkas untuk pengembang

| Kebutuhan | Berkas utama |
| --- | --- |
| Route | [routes/web.php](routes/web.php), [routes/auth.php](routes/auth.php) |
| Bootstrap | [bootstrap/app.php](bootstrap/app.php), `app/Http/Middleware/` |
| Daftar/OTP | [RegisteredUserController.php](app/Http/Controllers/Auth/RegisteredUserController.php), [RegistrationOtpController.php](app/Http/Controllers/Auth/RegistrationOtpController.php), [RegistrationOtp.php](app/Services/RegistrationOtp.php) |
| Email/form OTP | `app/Mail/RegistrationOtpMail.php`, `resources/views/auth/registration-otp.blade.php`, `public/js/registration-otp.js` |
| Login | `app/Http/Controllers/Auth/AuthenticatedSessionController.php`, `app/Http/Requests/Auth/LoginRequest.php` |
| Google | [AuthController.php](app/Http/Controllers/AuthController.php), `config/services.php` |
| Profil | `app/Http/Controllers/ProfileController.php`, `app/Http/Requests/ProfileUpdateRequest.php`, `resources/views/profile/edit.blade.php` |
| Publik | `app/Http/Controllers/Wisatawan/LandingController.php`, `resources/views/wisatawan/` |
| Tiket publik | `app/Http/Controllers/Wisatawan/TiketController.php` |
| Pemesanan | [PemesananController.php](app/Http/Controllers/Wisatawan/PemesananController.php) |
| Pembayaran | [PembayaranController.php](app/Http/Controllers/Wisatawan/PembayaranController.php), [config/payment.php](config/payment.php) |
| Pengelola | `app/Http/Controllers/Pengelola/`, `resources/views/pengelola/` |
| Persetujuan | [Pemesanan.php](app/Models/Pemesanan.php), `resources/views/partials/booking-status.blade.php` |
| Rute/video | [config/tourism.php](config/tourism.php), `public/js/section-refinement.js`, `public/js/site.js` |
| Bahasa | `lang/id/messages.php`, `lang/en/messages.php` |
| Model/migrasi | `app/Models/`, `database/migrations/` |
| Aset | `resources/css/app.css`, `resources/js/app.js`, `vite.config.js`, `public/css/` |
| Pengujian panduan | [DocumentedFeatureFlowTest.php](tests/Feature/DocumentedFeatureFlowTest.php), [phpunit.xml](phpunit.xml) |
| Operasional | `docker/`, `scripts/backup-db.bat` |

Setelah pengembangan berikutnya, periksa route/controller/view aktif, jalankan uji terkait, dan perbarui tanggal/bukti bagian yang berubah. Nama berkas, tampilan lama, dan rencana fitur tidak cukup untuk menyatakan suatu alur sudah berhasil.
