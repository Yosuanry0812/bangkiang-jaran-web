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

        $username = explode('@', $googleUser->getEmail())[0];

        // handle username conflict — tambah angka kalo udah dipake
        $base = $username;
        $suffix = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $suffix;
            $suffix++;
        }

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'password' => bcrypt(uniqid()),
                'username' => $username,
                'role' => 'wisatawan',
                'email_verified_at' => now(),
            ]
        );

        Auth::login($user);

        return redirect()->route('redirect.after.login');
    }
}
