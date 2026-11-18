<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        try {
            Password::sendResetLink(['email' => Str::lower(trim($validated['email']))]);
        } catch (\Throwable $e) {
            // Do not log the address, reset link, or mail transport credentials.
            Log::error('Pengiriman email reset password gagal.', ['exception' => get_class($e)]);

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => __('flash.email_send_failed')]);
        }

        // Use the same response for unknown addresses and throttled requests.
        return back()->with('status', __('flash.reset_link_sent'));
    }
}
