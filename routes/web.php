<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ETicketController;
use App\Http\Controllers\PaymentProofController;
use App\Http\Controllers\Pengelola\DashboardController;
use App\Http\Controllers\Pengelola\GaleriController;
use App\Http\Controllers\Pengelola\LaporanController;
use App\Http\Controllers\Pengelola\OperationsController;
use App\Http\Controllers\Pengelola\PemesananOfflineController;
use App\Http\Controllers\Pengelola\ScanController;
use App\Http\Controllers\Pengelola\TiketController as PengelolaTiketController;
use App\Http\Controllers\Pengelola\UserController;
use App\Http\Controllers\Pengelola\VerifikasiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Wisatawan\LandingController;
use App\Http\Controllers\Wisatawan\PembayaranController;
use App\Http\Controllers\Wisatawan\PemesananController;
use App\Http\Controllers\Wisatawan\TiketController as WisatawanTiketController;
use App\Http\Middleware\RedirectPengelolaFromPublicPages;
use App\Services\VisitQuota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - E-Tourism Bangkiang Jaran Waterfall
|--------------------------------------------------------------------------
*/

// ========== PUBLIC ROUTES ==========
Route::middleware(RedirectPengelolaFromPublicPages::class)->group(function () {
    Route::get('/', [LandingController::class, 'index'])->name('landing');
    Route::get('/restoran', [LandingController::class, 'restoran'])->name('restoran');
    Route::get('/informasi', function () {
        return redirect()->route('landing');
    })->name('informasi');
    Route::get('/tiket', [WisatawanTiketController::class, 'index'])->name('tiket.index');
    Route::get('/tiket/{id}', [WisatawanTiketController::class, 'detail'])->name('tiket.detail');
});
Route::get('/tiket/verifikasi/{kode}', [WisatawanTiketController::class, 'verifikasi'])->middleware('throttle:30,1')->name('tiket.verifikasi');
Route::get('/kunjungan/kuota', function (Request $request, VisitQuota $quota) {
    $values = $request->validate(['date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today']]);

    return response()->json($quota->availability($values['date']))->header('Cache-Control', 'no-store');
})->middleware('throttle:60,1')->name('kunjungan.kuota');

// ========== AUTHENTICATED WISATAWAN ROUTES ==========
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::middleware(['auth', 'role:wisatawan'])->prefix('wisatawan')->name('wisatawan.')->group(function () {
    // Pemesanan
    Route::get('/pemesanan', [PemesananController::class, 'create'])->name('pemesanan.create');
    Route::post('/pemesanan', [PemesananController::class, 'store'])->middleware('throttle:10,1')->name('pemesanan.store');
    Route::get('/pemesanan/data-diri', [PemesananController::class, 'dataDiri'])->name('pemesanan.data-diri');
    Route::post('/pemesanan/data-diri', [PemesananController::class, 'storeDataDiri'])->block(30, 30)->name('pemesanan.store-data-diri');
    Route::post('/pemesanan/{id}/batal', [PemesananController::class, 'cancel'])->name('pemesanan.cancel');
    Route::get('/pemesanan/sukses/{id}', [PemesananController::class, 'sukses'])->name('pemesanan.sukses');
    Route::get('/pemesanan/riwayat', [PemesananController::class, 'riwayat'])->name('pemesanan.riwayat');
    Route::get('/pemesanan/status', [PemesananController::class, 'statuses'])->name('pemesanan.statuses');
    Route::get('/pemesanan/{id}/status', [PemesananController::class, 'status'])->name('pemesanan.status');
    Route::get('/pemesanan/{id}', [PemesananController::class, 'detailPemesanan'])->name('pemesanan.detail');

    // Pembayaran
    Route::get('/pembayaran/{id_pemesanan}', [PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('/pembayaran/{id_pemesanan}', [PembayaranController::class, 'store'])->name('pembayaran.store');
});

// ========== PENGGELOLA (ADMIN) ROUTES ==========
Route::middleware(['auth', 'role:pengelola'])->prefix('pengelola')->name('pengelola.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pesanan', [OperationsController::class, 'bookings'])->name('pesanan.index');
    Route::get('/di-lokasi', [OperationsController::class, 'onsite'])->name('onsite.index');
    Route::get('/aktivitas', [OperationsController::class, 'audit'])->name('audit.index');
    Route::get('/pengaturan', [OperationsController::class, 'settings'])->name('settings.edit');
    Route::put('/pengaturan', [OperationsController::class, 'updateSettings'])->name('settings.update');
    Route::get('/kuota', [OperationsController::class, 'quotas'])->name('quotas.index');
    Route::put('/kuota', [OperationsController::class, 'updateQuota'])->name('quotas.update');

    // Kelola Galeri
    Route::resource('/galeri', GaleriController::class)->parameters([
        'galeri' => 'id',
    ])->except(['edit', 'update', 'show']);

    // Kelola Tiket
    Route::resource('/tiket', PengelolaTiketController::class)->parameters([
        'tiket' => 'id',
    ])->except(['show']);

    // Verifikasi Pembayaran
    Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
    Route::get('/verifikasi/{id}', [VerifikasiController::class, 'show'])->name('verifikasi.show');
    Route::post('/verifikasi/{id}/validasi', [VerifikasiController::class, 'validasi'])->name('verifikasi.validasi');

    // Notifikasi realtime (polling)
    Route::get('/notifikasi', [VerifikasiController::class, 'notifikasi'])->name('notifikasi');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/kunjungan', [LaporanController::class, 'kunjungan'])->name('laporan.kunjungan');
    Route::get('/laporan/transaksi', [LaporanController::class, 'transaksi'])->name('laporan.transaksi');

    // Manajemen User
    Route::get('/user', [UserController::class, 'index'])->name('user.index');

    // Scan Tiket
    Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
    Route::post('/scan/cari', [ScanController::class, 'cari'])->name('scan.cari');
    Route::post('/scan/gunakan', [ScanController::class, 'gunakan'])->name('scan.gunakan');
    Route::post('/scan/checkout', [ScanController::class, 'checkout'])->name('scan.checkout');

    // Pemesanan Offline (manual ticket entry)
    Route::get('/pemesanan-offline', [PemesananOfflineController::class, 'create'])->name('pemesanan-offline.create');
    Route::post('/pemesanan-offline', [PemesananOfflineController::class, 'store'])->name('pemesanan-offline.store');
    Route::get('/pemesanan-offline/data-diri', [PemesananOfflineController::class, 'dataDiri'])->name('pemesanan-offline.data-diri');
    Route::post('/pemesanan-offline/data-diri', [PemesananOfflineController::class, 'storeDataDiri'])->block(30, 30)->name('pemesanan-offline.store-data-diri');
    Route::get('/pemesanan-offline/sukses/{id}', [PemesananOfflineController::class, 'sukses'])->name('pemesanan-offline.sukses');
    Route::get('/pemesanan-offline/riwayat', [PemesananOfflineController::class, 'riwayat'])->name('pemesanan-offline.riwayat');
});

// ========== PROFILE ROUTES ==========
Route::middleware('auth')->group(function () {
    Route::get('/pembayaran/{id}/bukti', [PaymentProofController::class, 'show'])->name('payment.proof');
    Route::get('/pemesanan/{id}/eticket.pdf', [ETicketController::class, 'download'])->name('eticket.download');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/set-password', [ProfileController::class, 'setPassword'])->name('profile.set-password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ========== BREEZE AUTH ROUTES ==========
require __DIR__.'/auth.php';

// ========== LANGUAGE SWITCH ==========
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['id', 'en'])) {
        session()->put('locale', $locale);
    }

    return redirect()->back();
})->name('lang.switch');

// ========== LOGOUT (GET fallback buat CSRF expired) ==========
Route::get('/logout', function () {
    // Backward-compatible navigation only; logout itself requires POST + CSRF.
    return redirect('/');
})->name('logout.get');

// Redirect after login berdasarkan role
Route::middleware('auth')->get('/redirect-after-login', function () {
    if (auth()->user()->role === 'pengelola') {
        return redirect()->route('pengelola.dashboard');
    }

    return redirect()->intended(route('landing'));
})->name('redirect.after.login');
