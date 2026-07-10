<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; background: #f0fdf4; margin: 0; padding: 0;">
<div style="max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
<div style="background: linear-gradient(135deg, #dc2626, #b91c1c); padding: 40px 30px; text-align: center;">
<h1 style="color: #ffffff; margin: 0; font-size: 24px;">❌ Pembayaran Ditolak</h1>
<p style="color: #fecaca; margin: 8px 0 0;">Bangkiang Jaran Waterfall</p>
</div>
<div style="padding: 30px;">
<h2 style="color: #b91c1c; margin-top: 0;">Yth. {{ $pemesanan->user->name }},</h2>

<p style="color: #4b5563;">Mohon maaf, pembayaran Anda untuk pemesanan dengan kode <strong>{{ $pemesanan->kode_booking }}</strong> <span style="color: #dc2626; font-weight: bold;">ditolak</span> oleh pengelola.</p>

<p style="color: #4b5563;">Hal ini bisa disebabkan oleh:</p>
<ul style="color: #4b5563; line-height: 1.8;">
<li>Bukti transfer tidak jelas atau terbaca</li>
<li>Jumlah transfer tidak sesuai</li>
<li>Rekening tujuan tidak sesuai</li>
</ul>

<p style="color: #4b5563;">Silakan upload ulang bukti pembayaran yang valid melalui halaman detail pemesanan Anda.</p>

<div style="text-align: center; margin: 30px 0;">
<a href="{{ route('wisatawan.pemesanan.detail', $pemesanan->id_pemesanan) }}" style="display: inline-block; background: #0d9488; color: #ffffff; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 16px;">Upload Ulang Bukti Bayar</a>
</div>

<p style="color: #9ca3af; font-size: 14px;">Jika ada pertanyaan, hubungi pengelola.</p>
</div>
<div style="background: #f9fafb; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
<p style="color: #9ca3af; font-size: 12px; margin: 0;">© {{ date('Y') }} Bangkiang Jaran Waterfall. Desa Bakbakan, Gianyar, Bali.</p>
</div>
</div>
</body>
</html>
