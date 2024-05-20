<?php

namespace Zbiller\Crudhub\Traits;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait ThrottlesRequests
{
    /**
     * @return string
     */
    public function throttleField(): string
    {
        return 'email';
    }

    /**
     * @return string
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input($this->throttleField())).'|'.$this->ip());
    }

    /**
     * @return void
     */
    public function increaseRateLimit(): void
    {
        RateLimiter::hit($this->throttleKey(), config('crudhub.login.lockout_time', 60));
    }

    /**
     * @return void
     */
    public function clearRateLimit(): void
    {
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * @return void
     * @throws ValidationException
     */
    public function guardAgainstRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), config('crudhub.login.allowed_attempts', 3))) {
            return;
        }

        Event::dispatch(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            $this->throttleField() => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }
}
