<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; background: #f0fdf4; margin: 0; padding: 0;">
<div style="max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
<div style="background: linear-gradient(135deg, #0d9488, #065f46); padding: 40px 30px; text-align: center;">
<h1 style="color: #ffffff; margin: 0; font-size: 28px;">🌿 Bangkiang Jaran Waterfall</h1>
<p style="color: #ccfbf1; margin: 8px 0 0; font-size: 16px;">Selamat Datang di Petualangan Alam!</p>
</div>
<div style="padding: 30px;">
<h2 style="color: #065f46; margin-top: 0;">Halo, {{ $user->name }}!</h2>
<p style="color: #4b5563; line-height: 1.7;">Terima kasih telah mendaftar di sistem pemesanan tiket <strong>Bangkiang Jaran Waterfall</strong>. Nikmati keindahan air terjun tropis di Desa Bakbakan, Gianyar, Bali.</p>
<table style="width: 100%; margin: 20px 0; background: #f0fdf4; border-radius: 12px; padding: 20px;">
<tr>
<td style="padding: 8px 0; color: #374151;"><strong>Username:</strong></td>
<td style="padding: 8px 0; color: #374151;">{{ $user->username }}</td>
</tr>
<tr>
<td style="padding: 8px 0; color: #374151;"><strong>Email:</strong></td>
<td style="padding: 8px 0; color: #374151;">{{ $user->email }}</td>
</tr>
</table>
<p style="color: #4b5563;">Anda sekarang bisa memesan tiket secara online dan menikmati pengalaman wisata yang tak terlupakan.</p>
<div style="text-align: center; margin: 30px 0;">
<a href="{{ url('/tiket') }}" style="display: inline-block; background: #0d9488; color: #ffffff; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 16px;">Pesan Tiket Sekarang</a>
</div>
</div>
<div style="background: #f9fafb; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
<p style="color: #9ca3af; font-size: 12px; margin: 0;">© {{ date('Y') }} Bangkiang Jaran Waterfall. Desa Bakbakan, Gianyar, Bali.</p>
</div>
</div>
</body>
</html>
