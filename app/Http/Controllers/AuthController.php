<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\User;
use App\Services\RegistrationOtp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        if (!config('services.google.client_id') || !config('services.google.client_secret')) {
            return redirect()->route('login')
                ->with('error', 'Login Google belum dikonfigurasi. Hubungi admin.');
        }

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            try {
                $googleUser = Socialite::driver('google')->stateless()->user();
            } catch (\Exception $inner) {
                Log::warning('Google login gagal.', ['exception_type' => get_class($inner)]);

                return redirect()->route('login')
                    ->with('error', 'Login dengan Google gagal. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            Log::warning('Google login gagal.', ['exception_type' => get_class($e)]);

            return redirect()->route('login')
                ->with('error', 'Login dengan Google gagal. Silakan coba lagi.');
        }

        $email = strtolower(trim((string) $googleUser->getEmail()));
        $googleId = (string) $googleUser->getId();

        if ($googleId === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || ($googleUser->user['email_verified'] ?? false) !== true) {
            return redirect()->route('login')
                ->with('error', 'Akun Google harus memiliki email yang telah diverifikasi.');
        }

        $user = User::where('google_id', $googleId)->first();
        $emailOwner = User::where('email', $email)->first();

        // Never link an existing account solely on the basis of a matching email.
        if (($emailOwner && (!$user || $emailOwner->id !== $user->id)) || ($user && $user->email !== $email)) {
            return redirect()->route('login')
                ->with('error', 'Email ini sudah terdaftar atau berubah. Silakan masuk dengan akun yang sudah ada atau hubungi admin.');
        }

        $fullName = $googleUser->getName() ?: $email;

        // ── AKUN LAMA LOGIN ──
        // Jika akun Google sudah terdaftar sebelumnya, langsung login dan arahkan ke Beranda (atau intended URL),
        // tanpa masuk ke halaman setting akun / profil lagi.
        if ($user) {
            $user->update([
                'last_login_at' => now(),
                'last_login_method' => 'google',
            ]);
            $user->increment('login_count');

            Auth::login($user);
            $request->session()->regenerate();

            ActivityLogger::log('Login sukses', 'Login via Google sebagai ' . $user->email, $user->id);

            return redirect()->intended(route('landing'));
        }

        // ── AKUN BARU DAFTAR (PERTAMA KALI) ──
        // Wisatawan yang mendaftar pertama kali via Google diarahkan ke halaman pembuatan username sendiri.
        // Tidak membuatkan username otomatis dari email.
        $request->session()->put('google_register', [
            'google_id' => $googleId,
            'name'      => $fullName,
            'email'     => $email,
        ]);

        return redirect()->route('auth.google.username');
    }

    /**
     * Halaman form pembuatan username mandiri untuk akun Google baru.
     */
    public function createGoogleUsername(Request $request)
    {
        $googleData = $request->session()->get('google_register');

        if (!$googleData) {
            return redirect()->route('login');
        }

        return view('auth.google-username', [
            'name'  => $googleData['name'],
            'email' => $googleData['email'],
        ]);
    }

    /**
     * Menyimpan username pilihan wisatawan dan mengaktifkan akun Google baru.
     */
    public function storeGoogleUsername(Request $request)
    {
        $googleData = $request->session()->get('google_register');

        if (!$googleData) {
            return redirect()->route('login')
                ->with('error', 'Sesi pendaftaran Google telah berakhir. Silakan coba lagi.');
        }

        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-Z0-9_.-]+$/',
                'unique:users,username',
            ],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.min'      => 'Username minimal 3 karakter.',
            'username.max'      => 'Username maksimal 50 karakter.',
            'username.regex'    => 'Username hanya boleh huruf, angka, titik, garis bawah (_), dan strip (-).',
            'username.unique'   => 'Username ini sudah digunakan, silakan pilih username lain.',
        ]);

        // Re-check keunikan email
        if (User::where('email', $googleData['email'])->exists()) {
            $request->session()->forget('google_register');
            return redirect()->route('login')
                ->with('error', 'Email ini sudah terdaftar. Silakan masuk menggunakan akun Anda.');
        }

        // CATATAN KEAMANAN:
        // Pengguna baru yang mendaftar melalui Google SELALU otomatis berstatus role 'wisatawan'.
        // Role 'pengelola' tidak pernah diberikan kepada pendaftar publik atau Google.
        $user = User::create([
            'name'              => $googleData['name'],
            'username'          => strtolower(trim($validated['username'])),
            'email'             => $googleData['email'],
            'google_id'         => $googleData['google_id'],
            'role'              => 'wisatawan',
            'last_login_at'     => now(),
            'last_login_method' => 'google',
            'login_count'       => 1,
        ]);

        $user->forceFill([
            'registration_method'      => 'google',
            'email_verified_at'        => now(),
            'email_verification_method' => 'google',
        ])->save();

        $request->session()->forget('google_register');
        app(RegistrationOtp::class)->cancel($request);

        Auth::login($user);
        $request->session()->regenerate();

        ActivityLogger::log('Registrasi', 'Registrasi akun Google dengan username @' . $user->username, $user->id);
        ActivityLogger::log('Login sukses', 'Login via Google sebagai ' . $user->email, $user->id);

        return redirect()->intended(route('landing'))->with('status', 'Selamat datang! Akun Anda berhasil dibuat.');
    }

    /**
     * Membatalkan sesi pendaftaran Google baru dan kembali ke login.
     */
    public function cancelGoogleRegistration(Request $request)
    {
        $request->session()->forget('google_register');
        return redirect()->route('login');
    }
}
