<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\RegistrationOtp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request, RegistrationOtp $otp): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9_.-]+$/', 'unique:' . User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (! config('features.otp_registration')) {
            // OTP disembunyikan: akun langsung dibuat (role selalu wisatawan), email belum terverifikasi.
            $user = new User([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'wisatawan',
                'last_login_at' => now(),
                'last_login_method' => 'manual',
                'login_count' => 1,
            ]);
            $user->forceFill(['registration_method' => 'email'])->save();

            ActivityLogger::log('Registrasi', 'Pendaftaran akun email tanpa OTP.', $user->id);
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('redirect.after.login');
        }

        try {
            $otp->start($request, $validated);
        } catch (ValidationException $e) {
            return redirect()->route('registration.otp')->withErrors($e->errors());
        }

        return redirect()->route('registration.otp')->with('status', 'otp-sent');
    }
}
