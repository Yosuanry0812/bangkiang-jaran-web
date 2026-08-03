# Hero Video

Taruh file video hero di folder ini dengan nama:

```
hero-bangkiang.mp4
```

**Spesifikasi yang disarankan:**
- Format: MP4 (H.264/H.265)
- Resolusi: 1920×1080 atau 2560×1440
- Durasi: 15–60 detik (loop friendly)
- Ukuran: < 15 MB (compressed untuk web)
- Audio: Bisa ada audio, tapi default muted — user bisa toggle sound

**Tips encoding (ffmpeg):**
```bash
ffmpeg -i input.mp4 -vcodec libx264 -crf 28 -preset slow -an -movflags faststart hero-bangkiang.mp4
```

Jika file video tidak ada, hero akan otomatis fallback ke gambar `sejarah-bangkiang-waterfall.webp`.
