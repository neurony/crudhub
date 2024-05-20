<?php

namespace Zbiller\Crudhub\Contracts;

use Zbiller\Crudhub\Exceptions\TwoFactorException;

interface AuthenticatableTwoFactor
{
    /**
     * @return string
     */
    public function getTwoFactorEmailField(): string;

    /**
     * @return string
     */
    public function getTwoFactorCodeField(): string;

    /**
     * @return string
     */
    public function getTwoFactorCodeExpiresAtField(): string;

    /**
     * @param string $code
     * @return void
     * @throws TwoFactorException
     */
    public function validateTwoFactorCode(string $code): void;

    /**
     * @return void
     */
    public function requestTwoFactorCode(): void;
}
