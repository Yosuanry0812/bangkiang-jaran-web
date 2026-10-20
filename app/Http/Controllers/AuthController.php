<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
        } catch (\Exception $e) {
            Log::warning('Google login gagal: ' . $e->getMessage());

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

        if ($user) {
            $user->update([
                'last_login_at' => now(),
                'last_login_method' => 'google',
            ]);
            $user->increment('login_count');
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?: $email,
                'username' => $this->uniqueUsername($email),
                'email' => $email,
                'google_id' => $googleId,
                'role' => 'wisatawan',
                'last_login_at' => now(),
                'last_login_method' => 'google',
                'login_count' => 1,
            ]);
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('redirect.after.login');
    }

    private function uniqueUsername(string $email): string
    {
        $base = substr(preg_replace('/[^a-zA-Z0-9_.-]/', '', Str::before($email, '@')), 0, 40) ?: 'pengunjung';
        $username = $base;
        $suffix = 1;
        while (User::where('username', $username)->exists()) {
            $username = substr($base, 0, 50 - strlen((string) $suffix)) . $suffix;
            $suffix++;
        }

        return $username;
    }
}
