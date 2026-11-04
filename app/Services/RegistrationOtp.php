<?php

namespace App\Services;

use App\Mail\RegistrationOtpMail;
use App\Models\User;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegistrationOtp
{
    public const SESSION_KEY = 'registration.challenge';
    public const CODE_LIFETIME = 600;
    public const PENDING_LIFETIME = 1800;
    public const RESEND_DELAY = 60;
    public const MAX_ATTEMPTS = 5;

    public function start(Request $request, array $validated): void
    {
        $this->cancel($request);
        $request->session()->regenerate();
        $request->session()->put(self::SESSION_KEY, Str::random(64));
        $this->save($request, [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'pending_until' => now()->timestamp + self::PENDING_LIFETIME,
            'code_hash' => null,
            'expires_at' => 0,
            'sent_at' => 0,
        ]);
        $this->resend($request);
    }

    public function pending(Request $request): ?array
    {
        $id = $request->session()->get(self::SESSION_KEY);
        if (!is_string($id) || !preg_match('/\A[a-zA-Z0-9]{64}\z/', $id)) {
            return null;
        }

        $encrypted = Cache::get('registration:'.$id);
        if (!$encrypted) {
            return null;
        }

        return json_decode(Crypt::decryptString($encrypted), true, flags: JSON_THROW_ON_ERROR);
    }

    public function resend(Request $request): void
    {
        $this->locked($request, function (array $pending) use ($request): void {
            $key = $this->emailKey($pending['email']);
            $cooldown = 'registration:cooldown:'.$key;
            $hourly = 'registration:send:'.$key;
            $ip = 'registration:ip:'.hash('sha256', (string) $request->ip());

            if (RateLimiter::tooManyAttempts($cooldown, 1)) {
                $this->fail('Tunggu '.RateLimiter::availableIn($cooldown).' detik sebelum meminta kode baru.');
            }
            if (RateLimiter::tooManyAttempts($hourly, 5) || RateLimiter::tooManyAttempts($ip, 20)) {
                $this->fail('Batas pengiriman kode tercapai. Coba lagi dalam satu jam.');
            }

            $this->ensureDeliveryMailer();
            // Reserve the quota before contacting SMTP, including failed attempts.
            RateLimiter::hit($cooldown, self::RESEND_DELAY);
            RateLimiter::hit($hourly, 3600);
            RateLimiter::hit($ip, 3600);
            do {
                $code = (string) random_int(100000, 999999);
            } while ($pending['code_hash'] && Hash::check($code, $pending['code_hash']));
            try {
                Mail::to($pending['email'])->send(new RegistrationOtpMail($pending['name'], $code));
            } catch (\Throwable $e) {
                // SMTP errors may contain credentials or message content. Log only the type.
                Log::warning('Pengiriman OTP pendaftaran gagal.', ['exception_type' => get_class($e)]);
                $this->fail('Email verifikasi belum dapat dikirim. Tunggu 60 detik lalu coba kirim ulang.');
            }

            // A failed resend leaves the previous code valid until its original expiry.
            $pending['code_hash'] = Hash::make($code);
            $pending['sent_at'] = now()->timestamp;
            $pending['expires_at'] = now()->timestamp + self::CODE_LIFETIME;
            $pending['pending_until'] = now()->timestamp + self::PENDING_LIFETIME;
            $this->save($request, $pending);
        });
    }

    public function verify(Request $request, string $code): User
    {
        return $this->locked($request, function (array $pending) use ($request, $code): User {
            $attempts = 'registration:attempts:'.$this->emailKey($pending['email']);
            if (RateLimiter::tooManyAttempts($attempts, self::MAX_ATTEMPTS)) {
                $this->fail('Terlalu banyak kode salah. Coba lagi dalam '.RateLimiter::availableIn($attempts).' detik.');
            }
            if (!$pending['code_hash'] || $pending['expires_at'] <= now()->timestamp) {
                $this->fail('Kode sudah kedaluwarsa atau belum terkirim. Silakan kirim ulang kode.');
            }
            if (!Hash::check($code, $pending['code_hash'])) {
                RateLimiter::hit($attempts, self::CODE_LIFETIME);
                $this->fail('Kode OTP tidak sesuai. Periksa kembali email Anda.');
            }

            // Recheck uniqueness: another session may have completed registration meanwhile.
            Validator::make($pending, [
                'email' => ['unique:users,email'],
                'username' => ['unique:users,username'],
            ])->validate();

            $user = DB::transaction(function () use ($pending): User {
                $user = new User([
                    'name' => $pending['name'],
                    'username' => $pending['username'],
                    'email' => $pending['email'],
                    'password' => $pending['password'],
                    'role' => 'wisatawan',
                    'last_login_at' => now(),
                    'last_login_method' => 'manual',
                    'login_count' => 1,
                ]);
                $user->forceFill([
                    'registration_method' => 'email',
                    'email_verification_method' => 'otp',
                    'email_verified_at' => now(),
                ])->save();

                return $user;
            });

            $this->cancel($request);
            RateLimiter::clear($attempts);

            return $user;
        });
    }

    public function cancel(Request $request): void
    {
        $id = $request->session()->pull(self::SESSION_KEY);
        if (is_string($id)) {
            Cache::forget('registration:'.$id);
        }
    }

    private function save(Request $request, array $pending): void
    {
        Cache::put('registration:'.$request->session()->get(self::SESSION_KEY),
            Crypt::encryptString(json_encode($pending, JSON_THROW_ON_ERROR)),
            max(1, $pending['pending_until'] - now()->timestamp));
    }

    private function locked(Request $request, callable $callback): mixed
    {
        $pending = $this->pending($request);
        if (!$pending) {
            $this->fail('Sesi pendaftaran berakhir. Silakan daftar kembali.');
        }

        try {
            return Cache::lock('registration:lock:'.$this->emailKey($pending['email']), 45)->block(2, function () use ($request, $callback) {
                $pending = $this->pending($request);
                if (!$pending || $pending['pending_until'] <= now()->timestamp) {
                    $this->fail('Sesi pendaftaran berakhir. Silakan daftar kembali.');
                }

                return $callback($pending);
            });
        } catch (LockTimeoutException $e) {
            $this->fail('Permintaan sebelumnya masih diproses. Silakan coba lagi.');
        }
    }

    private function emailKey(string $email): string
    {
        return hash_hmac('sha256', strtolower($email), config('app.key'));
    }

    private function ensureDeliveryMailer(): void
    {
        $transport = config('mail.mailers.'.config('mail.default').'.transport');
        if (app()->runningUnitTests() && $transport === 'array') {
            return;
        }
        // Never write OTPs to laravel.log or report a simulated delivery as success.
        if (!in_array($transport, ['smtp', 'sendmail', 'ses', 'mailgun', 'postmark', 'resend'], true)) {
            $this->fail('Pengiriman email belum tersedia. Hubungi pengelola untuk mengaktifkan SMTP.');
        }
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['otp' => $message]);
    }
}
