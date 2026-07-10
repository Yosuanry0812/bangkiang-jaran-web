<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Wisatawan\LandingController;
use App\Http\Controllers\Wisatawan\TiketController as WisatawanTiketController;
use App\Http\Controllers\Wisatawan\PemesananController;
use App\Http\Controllers\Wisatawan\PembayaranController;
use App\Http\Controllers\Pengelola\DashboardController;
use App\Http\Controllers\Pengelola\KontenController;
use App\Http\Controllers\Pengelola\GaleriController;
use App\Http\Controllers\Pengelola\TiketController as PengelolaTiketController;
use App\Http\Controllers\Pengelola\VerifikasiController;
use App\Http\Controllers\Pengelola\LaporanController;
use App\Http\Controllers\Pengelola\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes - E-Tourism Bangkiang Jaran Waterfall
|--------------------------------------------------------------------------
*/

// ========== PUBLIC ROUTES ==========
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/informasi', function () {
    return redirect()->route('landing');
})->name('informasi');
Route::get('/tiket', [WisatawanTiketController::class, 'index'])->name('tiket.index');
Route::get('/tiket/{id}', [WisatawanTiketController::class, 'detail'])->name('tiket.detail');

// ========== AUTHENTICATED WISATAWAN ROUTES ==========
Route::middleware(['auth', 'verified', 'role:wisatawan'])->prefix('wisatawan')->name('wisatawan.')->group(function () {
    // Pemesanan
    Route::get('/pemesanan', [PemesananController::class, 'create'])->name('pemesanan.create');
    Route::post('/pemesanan', [PemesananController::class, 'store'])->middleware('throttle:10,1')->name('pemesanan.store');
    Route::get('/pemesanan/sukses/{id}', [PemesananController::class, 'sukses'])->name('pemesanan.sukses');
    Route::get('/pemesanan/riwayat', [PemesananController::class, 'riwayat'])->name('pemesanan.riwayat');
    Route::get('/pemesanan/{id}', [PemesananController::class, 'detailPemesanan'])->name('pemesanan.detail');

    // Pembayaran
    Route::get('/pembayaran/{id_pemesanan}', [PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('/pembayaran/{id_pemesanan}', [PembayaranController::class, 'store'])->name('pembayaran.store');
});

// ========== PENGGELOLA (ADMIN) ROUTES ==========
Route::middleware(['auth', 'verified', 'role:pengelola'])->prefix('pengelola')->name('pengelola.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Kelola Konten
    Route::resource('/konten', KontenController::class)->parameters([
        'konten' => 'id'
    ])->except(['show']);

    // Kelola Galeri
    Route::resource('/galeri', GaleriController::class)->parameters([
        'galeri' => 'id'
    ])->except(['edit', 'update', 'show']);

    // Kelola Tiket
    Route::resource('/tiket', PengelolaTiketController::class)->parameters([
        'tiket' => 'id'
    ])->except(['show']);

    // Verifikasi Pembayaran
    Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
    Route::get('/verifikasi/{id}', [VerifikasiController::class, 'show'])->name('verifikasi.show');
    Route::post('/verifikasi/{id}/validasi', [VerifikasiController::class, 'validasi'])->name('verifikasi.validasi');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/kunjungan', [LaporanController::class, 'kunjungan'])->name('laporan.kunjungan');
    Route::get('/laporan/transaksi', [LaporanController::class, 'transaksi'])->name('laporan.transaksi');

    // Manajemen User
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
});

// ========== PROFILE ROUTES ==========
Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ========== BREEZE AUTH ROUTES ==========
require __DIR__ . '/auth.php';

// ========== LANGUAGE SWITCH ==========
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['id', 'en'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

// ========== LOGOUT (GET fallback buat CSRF expired) ==========
Route::get('/logout', function () {
    if (auth()->check()) {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
    return redirect('/');
})->name('logout.get');

// Redirect after login berdasarkan role
Route::get('/redirect-after-login', function () {
    if (auth()->user()->role === 'pengelola') {
        return redirect()->route('pengelola.dashboard');
    }
    return redirect()->route('landing');
})->name('redirect.after.login');
