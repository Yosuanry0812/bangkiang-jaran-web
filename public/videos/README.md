# Hero Video

Taruh file video hero di folder ini dengan nama:

```
hero-bangkiang.mp4
```

**Spesifikasi yang disarankan:**
- Format: MP4 (H.264 agar kompatibel luas)
- Orientasi lanskap; pertahankan resolusi sumber (versi saat ini 1280×720, 24 fps)
- Durasi: seluruh track video asli dipertahankan, 90.916667 detik, dengan `loop` (container asli 91.019365 detik mencakup ekor audio)
- Kompresi berkualitas visual tinggi; file asli `bangkiang-jaran.mp4` tetap dipertahankan
- Audio: Sebaiknya dihapus saat ekspor; video hero diputar tanpa suara

**Tips encoding (ffmpeg):**
```bash
ffmpeg -display_rotation 90 -i bangkiang-jaran.mp4 -map 0:v:0 -c:v copy -an -movflags +faststart hero-bangkiang.mp4
```

Jika file video tidak ada, hero akan otomatis fallback ke gambar `sejarah-bangkiang-waterfall.webp`.

Perintah memakai FFmpeg 9; metadata rotasi 90° hanya untuk sumber saat ini yang tersimpan menyamping. `-c:v copy` mempertahankan bitstream video persis tanpa encoding ulang. Audio dihapus; tidak mengurangi resolusi, frame rate, atau durasi track video. Ukuran turun dari 55.514.940 menjadi 54.498.506 byte. `faststart` memindahkan metadata MP4 ke awal berkas. Poster `hero-bangkiang.webp` diambil dari video lanskap.

Hero berjalan otomatis tanpa kontrol putar/jeda. Video dan drift berhenti sementara ketika hero keluar layar atau tab tersembunyi, lalu berlanjut ketika kembali terlihat. Preferensi reduced motion dan Save-Data menampilkan poster. Jika autoplay ditolak browser, poster tetap tampil dan pemutaran dicoba lagi setelah interaksi pengguna.

Framing responsif memakai `object-fit: cover`: video/poster memenuhi hero setinggi layar, dengan bidang yang terlihat menyesuaikan rasio layar. Zoom halus bergerak dari skala `1` ke `1.025`. Judul pendek dan tombol berada di bawah video sebagai overlay pada semua ukuran layar, termasuk ponsel; konten tetap dapat tumbuh saat teks diperbesar.

Untuk sumber `hero-bangkiang.mp4`, Blade memilih `hero-bangkiang-web.mp4` jika tersedia. Versi web H.264 berukuran 17.931.364 byte, dibanding sumber 54.498.506 byte. Durasi, tampilan lanskap 1280×720 dan 24 fps dipertahankan, dengan bitrate lebih rendah; sumber tidak ditimpa. Reproduksi dari video hero yang sudah dikoreksi orientasinya:

```bash
ffmpeg -i hero-bangkiang.mp4 -map 0:v:0 -c:v libx264 -preset slow -b:v 1600k -maxrate 2000k -bufsize 4000k -pix_fmt yuv420p -an -movflags +faststart hero-bangkiang-web.mp4
```

Tiga foto `public/images/bangkiang-film-{12,36,62}.webp` diambil pada detik 12, 36, dan 62 dari video sumber untuk pergantian foto pengantar/cerita ketika galeri asli belum tersedia.

Di beranda, navbar disembunyikan sampai hero sepenuhnya melewati bagian atas layar, lalu selalu terlihat pada bagian lain untuk kedua arah scroll. Kembali ke hero menyembunyikannya lagi, termasuk dari fokus keyboard.

Teks pembuka menunggu 5000 ms pemutaran video yang aktif; waktu buffering/tab tersembunyi/hero di luar layar tidak dihitung. Judul tampil sekali per kunjungan melalui huruf berurutan setiap 22 ms, dengan fade/translate 180 ms per huruf, disusul deskripsi dan tombol. Layout akhir sudah dicadangkan agar tidak bergeser. Reduced motion, Save-Data, error media atau autoplay ditolak langsung menampilkan teks dan tombol. Jika enhancement tidak termuat, teks dibuka kembali setelah 12000 ms.

## Tiga varian video hero (2026-11-18)

Sumber: `bangkiang-jaran.mp4` asli (720x1280 tersimpan menyamping, 4,8 Mbps, 24 fps). Versi lama `hero-bangkiang.mp4` (1,6 Mbps) kualitasnya sudah turun dari sumber, jadi varian baru dibuat langsung dari sumber asli, bukan dari berkas itu.

| Berkas | Dipakai untuk | Spesifikasi | Ukuran |
| --- | --- | --- | --- |
| `hero-bangkiang-hd.mp4` | Layar ≥ 1100 px, bukan Save-Data, koneksi tidak 2G/3G atau di bawah 1,5 Mbps | 1920x1080, H.264 High, ~3 Mbps | ~34 MB |
| `hero-bangkiang-web.mp4` | HP, tablet, koneksi lambat (varian standar) | 1280x720, H.264 Main level 3.1, ~1,3 Mbps | ~15 MB |
| `hero-bangkiang.mp4` | Cadangan bila dua berkas di atas tidak ada | 1280x720, 1,6 Mbps | ~18 MB |

Resolusi tidak bisa melebihi sumber (720p). Varian HD diubah ke 1080p dengan Lanczos + sedikit penajaman agar tidak melar buram di layar besar, dan derau halus dikurangi (hqdn3d) supaya bitrate terpakai untuk detail. Keyframe tiap 2 detik dan `faststart` untuk mulai cepat dan seek mulus; video diputar bertahap (`preload="none"`), tidak diunduh sekaligus. `data-video-src-hd` pada `<source>` dipilih oleh `waterfall-experience.js`. Poster `hero-bangkiang.webp` diambil dari frame pertama sumber pada 1920x1080 (~185 KB).

Reproduksi (FFmpeg 6.1, `transpose=2` memutar sumber ke lanskap):
```bash
# HD
ffmpeg -i bangkiang-jaran.mp4 -map 0:v:0 -an -vf "transpose=2,hqdn3d=1.5:1.5:4:4,scale=1920:1080:flags=lanczos,unsharp=5:5:0.4:5:5:0.0" -c:v libx264 -preset slow -crf 25 -maxrate 3000k -bufsize 6000k -profile:v high -level 4.1 -pix_fmt yuv420p -g 48 -keyint_min 24 -sc_threshold 0 -movflags +faststart hero-bangkiang-hd.mp4
# Ringan
ffmpeg -i bangkiang-jaran.mp4 -map 0:v:0 -an -vf "transpose=2,hqdn3d=1.8:1.8:5:5,scale=1280:720:flags=lanczos,unsharp=5:5:0.4:5:5:0.0" -c:v libx264 -preset slow -crf 26 -maxrate 1300k -bufsize 2600k -profile:v main -level 3.1 -pix_fmt yuv420p -g 48 -keyint_min 24 -sc_threshold 0 -movflags +faststart hero-bangkiang-web.mp4
# Poster
ffmpeg -ss 0 -i bangkiang-jaran.mp4 -vf "transpose=2,scale=1920:1080:flags=lanczos,unsharp=5:5:0.6:5:5:0.0" -frames:v 1 -c:v libwebp -quality 75 hero-bangkiang.webp
```
