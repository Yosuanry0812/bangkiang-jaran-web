<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        if (!config('services.google.client_id') || !config('services.google.client_secret')) {
            return redirect()->route('login')
                ->with('error', 'Login Google belum dikonfigurasi. Hubungi admin.');
        }

        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            Log::warning('Google login gagal: ' . $e->getMessage());

            return redirect()->route('login')
                ->with('error', 'Login dengan Google gagal. Silakan coba lagi.');
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // User sudah ada (manual atau Google) — jangan sentuh password, catat login
            $user->update([
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'last_login_at' => now(),
                'last_login_method' => 'google',
            ]);
            $user->increment('login_count');
        } else {
            $username = $this->uniqueUsername($googleUser->getEmail());

            $user = User::create([
                'name' => $googleUser->getName(),
                'username' => $username,
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'role' => 'wisatawan',
                'email_verified_at' => now(),
                'last_login_at' => now(),
                'last_login_method' => 'google',
                'login_count' => 1,
                // password null = wajib set password untuk akun website ini
            ]);
        }

        Auth::login($user);

        return redirect()->route('redirect.after.login');
    }

    private function uniqueUsername(string $email): string
    {
        $base = explode('@', $email)[0];
        $username = $base;
        $suffix = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $suffix;
            $suffix++;
        }

        return $username;
    }
}
