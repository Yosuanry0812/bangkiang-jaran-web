@extends('emails.layout')
@section('title', 'Verifikasi email')
@section('content')
<h1 style="margin:0 0 18px;font-family:Georgia,serif;font-size:28px;color:#173d34;">Verifikasi email Anda</h1>
<p style="line-height:1.7;">Halo {{ $name }}, masukkan kode berikut di halaman pendaftaran Bangkiang Jaran untuk menyelesaikan pembuatan akun.</p>
<p style="margin:28px 0;padding:20px;text-align:center;font-size:32px;font-weight:bold;letter-spacing:8px;color:#173d34;background:#f1f5ef;">{{ $code }}</p>
<p style="line-height:1.7;">Kode berlaku selama <strong>10 menit</strong> dan hanya dapat digunakan sekali. Jangan bagikan kode ini kepada siapa pun.</p>
<p style="line-height:1.7;color:#66736c;">Jika Anda tidak mendaftar, abaikan email ini. Akun belum dibuat sebelum kode berhasil diverifikasi.</p>
@endsection
