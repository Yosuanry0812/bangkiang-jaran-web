<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Helpers\ActivityLogger;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Mail\WelcomeMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:' . User::class],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'phone'    => ['required', 'string', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'password' => ['required', 'confirmed', Rules\Password::min(8)->letters()->mixedCase()->numbers()],
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email sudah terdaftar.',
            'phone.required'    => 'Nomor telepon wajib diisi.',
            'phone.regex'       => 'Format nomor telepon tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
            'password.letters'  => 'Password harus mengandung huruf.',
            'password.mixedCase' => 'Password harus kombinasi huruf besar dan kecil.',
            'password.numbers'  => 'Password harus mengandung angka.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'wisatawan',
        ]);

        event(new Registered($user));

        ActivityLogger::log('Registrasi akun baru', 'Email: ' . $user->email);

        Auth::login($user);

        // Kirim email selamat datang
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Exception $e) {
            \Log::warning('Gagal kirim email welcome: ' . $e->getMessage());
        }

        return redirect(RouteServiceProvider::HOME);
    }
}
