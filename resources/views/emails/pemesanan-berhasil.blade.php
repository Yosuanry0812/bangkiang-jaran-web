<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; background: #f0fdf4; margin: 0; padding: 0;">
<div style="max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
<div style="background: linear-gradient(135deg, #0d9488, #065f46); padding: 40px 30px; text-align: center;">
<h1 style="color: #ffffff; margin: 0; font-size: 24px;">✅ Pemesanan Berhasil</h1>
<p style="color: #ccfbf1; margin: 8px 0 0;">Bangkiang Jaran Waterfall</p>
</div>
<div style="padding: 30px;">
<h2 style="color: #065f46; margin-top: 0;">Halo, {{ $pemesanan->user->name }}!</h2>
<p style="color: #4b5563;">Pemesanan tiket Anda berhasil dibuat. Berikut detail pemesanan:</p>

<table style="width: 100%; margin: 20px 0; background: #f0fdf4; border-radius: 12px; padding: 20px;">
<tr><td style="padding: 8px 0; color: #374151;"><strong>Kode Booking:</strong></td><td style="padding: 8px 0; color: #0d9488; font-size: 18px; font-weight: bold;">{{ $pemesanan->kode_booking }}</td></tr>
<tr><td style="padding: 8px 0; color: #374151;"><strong>Tiket:</strong></td><td style="padding: 8px 0; color: #374151;">{{ $pemesanan->tiket->nama_tiket }}</td></tr>
<tr><td style="padding: 8px 0; color: #374151;"><strong>Tanggal Kunjungan:</strong></td><td style="padding: 8px 0; color: #374151;">{{ \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->format('d F Y') }}</td></tr>
<tr><td style="padding: 8px 0; color: #374151;"><strong>Jumlah Tiket:</strong></td><td style="padding: 8px 0; color: #374151;">{{ $pemesanan->jumlah }} tiket</td></tr>
<tr><td style="padding: 8px 0; color: #374151;"><strong>Total Harga:</strong></td><td style="padding: 8px 0; color: #374151;">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</td></tr>
<tr><td style="padding: 8px 0; color: #374151;"><strong>Status:</strong></td><td style="padding: 8px 0;"><span style="background: #f59e0b; color: #ffffff; padding: 4px 12px; border-radius: 20px; font-size: 12px;">Menunggu Pembayaran</span></td></tr>
</table>

<p style="color: #4b5563;">Silakan lakukan pembayaran ke rekening yang tertera di halaman pemesanan dan upload bukti transfer untuk verifikasi.</p>

<div style="text-align: center; margin: 30px 0;">
<a href="{{ route('wisatawan.pemesanan.detail', $pemesanan->id_pemesanan) }}" style="display: inline-block; background: #0d9488; color: #ffffff; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 16px;">Lihat Detail Pemesanan</a>
</div>
</div>
<div style="background: #f9fafb; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
<p style="color: #9ca3af; font-size: 12px; margin: 0;">© {{ date('Y') }} Bangkiang Jaran Waterfall. Desa Bakbakan, Gianyar, Bali.</p>
</div>
</div>
</body>
</html>
