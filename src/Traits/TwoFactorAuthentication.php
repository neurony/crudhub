<?php

namespace Zbiller\Crudhub\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Zbiller\Crudhub\Events\TwoFactorCodeGenerated;
use Zbiller\Crudhub\Events\TwoFactorCodeReset;
use Zbiller\Crudhub\Exceptions\TwoFactorException;

trait TwoFactorAuthentication
{
    /**
     * @return string
     */
    public function getTwoFactorEmailField(): string
    {
        return 'email';
    }

    /**
     * @return string
     */
    public function getTwoFactorCodeField(): string
    {
        return 'two_factor_code';
    }

    /**
     * @return string
     */
    public function getTwoFactorCodeExpiresAtField(): string
    {
        return 'two_factor_code_expires_at';
    }

    /**
     * @param string $code
     * @return void
     * @throws TwoFactorException
     */
    public function validateTwoFactorCode(string $code): void
    {
        $currentCode = $this->{$this->getTwoFactorCodeField()};
        $currentExpiration = $this->{$this->getTwoFactorCodeExpiresAtField()};

        if ($currentCode !== $code) {
            throw new TwoFactorException('Invalid two-factor code');
        }

        if ($currentExpiration instanceof Carbon && $currentExpiration < Carbon::now()) {
            throw new TwoFactorException('Two-factor code expired');
        }

        $this->{$this->getTwoFactorCodeField()} = null;
        $this->{$this->getTwoFactorCodeExpiresAtField()} = null;
        $this->saveQuietly();

        Event::dispatch(new TwoFactorCodeReset($this, $code));
    }

    /**
     * @return void
     */
    public function requestTwoFactorCode(): void
    {
        $code = (string)rand(100000, 999999);
        $expiration = Carbon::now()->addMinutes(config('crudhub.login.two_factor_code_expiration', 60));

        $this->{$this->getTwoFactorCodeField()} = $code;
        $this->{$this->getTwoFactorCodeExpiresAtField()} = $expiration;
        $this->saveQuietly();

        Event::dispatch(new TwoFactorCodeGenerated($this));
    }
}
