<?php

namespace App\Filament\Auth;

use App\Models\User;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    private const MAX_ATTEMPTS  = 5;
    private const LOCKOUT_MINS  = 5;

    public function authenticate(): ?LoginResponse
    {
        $data  = $this->form->getState();
        $email = strtolower(trim($data['email'] ?? ''));
        $ip    = request()->ip();

        // ── 1. Camp Over check ────────────────────────────────────────────
        if (setting('camp_over', '0') === '1') {
            $user = User::where('email', $email)->first();
            if (! $user || ! $user->hasRole('super_admin')) {
                throw ValidationException::withMessages([
                    'data.email' => 'Camp has ended. Administrative access is disabled.',
                ]);
            }
        }

        // ── 2. Rate limit check ───────────────────────────────────────────
        $lockKey    = "login_lock:{$ip}:{$email}";
        $attemptKey = "login_attempts:{$ip}:{$email}";

        $lockedUntil = Cache::get($lockKey);
        if ($lockedUntil) {
            $remaining = now()->diffInSeconds($lockedUntil, false);
            if ($remaining > 0) {
                $mins = (int) ceil($remaining / 60);
                Log::warning('auth.blocked', ['email' => $email, 'ip' => $ip]);
                throw ValidationException::withMessages([
                    'data.email' => "Too many failed attempts. Please wait {$mins} minute(s) before trying again.",
                ]);
            }
            Cache::forget($lockKey);
        }


        // -- 3b. Manual block check (admin-imposed via UserResource) ----------
        $targetUser = User::where('email', $email)->first();
        if ($targetUser && $targetUser->isLockedOut()) {
            $until   = $targetUser->locked_until;
            $minutes = now()->diffInMinutes($until, false);
            $msg = $minutes > 1440
                ? 'Your account has been locked by an administrator. Please contact IT Support.'
                : 'Your account is locked until ' . $until->format('d M Y H:i') . '. Contact the IT Support if this is an error.';
            Log::warning('auth.admin_blocked', ['email' => $email, 'ip' => $ip]);
            throw ValidationException::withMessages(['data.email' => $msg]);
        }

        // ── 3. Attempt authentication ─────────────────────────────────────
        try {
            $response = parent::authenticate();
        } catch (ValidationException $e) {
            // Cache::increment returns false if key is new — treat that as 1
            $current  = Cache::get($attemptKey, 0);
            $attempts = $current + 1;
            Cache::put($attemptKey, $attempts, now()->addMinutes(self::LOCKOUT_MINS));

            if ($attempts >= self::MAX_ATTEMPTS) {
                $lockUntil = now()->addMinutes(self::LOCKOUT_MINS);
                Cache::put($lockKey, $lockUntil, $lockUntil);
                Cache::forget($attemptKey);

                Log::warning('auth.locked_out', ['email' => $email, 'ip' => $ip, 'attempts' => $attempts]);

                throw ValidationException::withMessages([
                    'data.email' => 'Too many failed attempts. Please wait ' . self::LOCKOUT_MINS . ' minutes before trying again.',
                ]);
            }

            $remaining = self::MAX_ATTEMPTS - $attempts;
            Log::info('auth.failed', ['email' => $email, 'ip' => $ip, 'attempts' => $attempts, 'remaining' => $remaining]);

            throw ValidationException::withMessages([
                'data.email' => ($e->errors()['data.email'][0] ?? 'These credentials do not match our records.')
                    . " ({$remaining} attempt(s) remaining before lockout.)",
            ]);
        }

        // ── 5. Successful login — clear rate limit, record metadata ───────
        Cache::forget($attemptKey);
        Cache::forget($lockKey);

        // Re-fetch user from DB — auth()->user() is reliably set after parent::authenticate()
        $user = auth()->user();

        if ($user) {
            $user->update([
                'last_login_at'    => now(),
                'last_login_ip'    => $ip,
                'last_login_agent' => substr(request()->userAgent() ?? '', 0, 255),
            ]);

            Log::info('auth.success', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'ip'      => $ip,
            ]);
        }

        return $response;
    }
}
