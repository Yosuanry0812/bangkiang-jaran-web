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

Framing responsif memakai `object-fit: contain` agar seluruh bidang video/poster terlihat. Zoom halus bergerak dari skala `.985` ke `1`, tanpa memperbesar melewati frame. Pada layar tegak hingga `1023px`, media mendapat area lanskap di atas teks hero; judul dan tombol berada di bawah video. Area sisa memakai warna latar hero. Berkas MP4 dan kualitas sumber dipertahankan.

Di beranda, navbar disembunyikan sampai hero sepenuhnya melewati bagian atas layar, lalu selalu terlihat pada bagian lain untuk kedua arah scroll. Kembali ke hero menyembunyikannya lagi, termasuk dari fokus keyboard.

Teks pembuka menunggu 5000 ms pemutaran video yang aktif; waktu buffering/tab tersembunyi/hero di luar layar tidak dihitung. Judul tampil sekali per kunjungan melalui huruf berurutan setiap 22 ms, dengan fade/translate 180 ms per huruf, disusul deskripsi dan tombol. Layout akhir sudah dicadangkan agar tidak bergeser. Reduced motion, Save-Data, error media atau autoplay ditolak langsung menampilkan teks dan tombol. Jika enhancement tidak termuat, teks dibuka kembali setelah 12000 ms.
