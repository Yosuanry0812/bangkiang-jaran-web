<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Services\RegistrationOtp;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegistrationOtpController extends Controller
{
    public function show(Request $request, RegistrationOtp $otp): View|RedirectResponse
    {
        $pending = $otp->pending($request);
        if (!$pending) {
            return redirect()->route('register')->with('error', 'Sesi pendaftaran berakhir. Silakan daftar kembali.');
        }

        return view('auth.registration-otp', [
            'email' => $pending['email'],
            'expiresAt' => $pending['expires_at'],
            'resendAt' => $pending['sent_at'] ? $pending['sent_at'] + RegistrationOtp::RESEND_DELAY : 0,
        ]);
    }

    public function verify(Request $request, RegistrationOtp $otp): RedirectResponse
    {
        $validated = $request->validate(['otp' => ['required', 'string', 'regex:/\A[0-9]{6}\z/']], [
            'otp.required' => 'Masukkan kode OTP dari email Anda.',
            'otp.regex' => 'Kode OTP harus berisi 6 digit angka.',
        ]);
        $user = $otp->verify($request, $validated['otp']);
        event(new Verified($user));
        ActivityLogger::log('Registrasi', 'Pendaftaran email terverifikasi OTP.', $user->id);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('redirect.after.login')->with('status', 'email-verified');
    }

    public function resend(Request $request, RegistrationOtp $otp): RedirectResponse
    {
        $otp->resend($request);

        return redirect()->route('registration.otp')->with('status', 'otp-sent');
    }

    public function cancel(Request $request, RegistrationOtp $otp): RedirectResponse
    {
        $otp->cancel($request);

        return redirect()->route('register');
    }
}
