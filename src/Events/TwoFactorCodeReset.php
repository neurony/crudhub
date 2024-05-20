<?php

namespace Zbiller\Crudhub\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Zbiller\Crudhub\Contracts\AuthenticatableTwoFactor;

class TwoFactorCodeReset
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var AuthenticatableTwoFactor
     */
    public AuthenticatableTwoFactor $user;

    /**
     * @var string
     */
    public string $code;

    /**
     * @param AuthenticatableTwoFactor $user
     * @param string $code
     */
    public function __construct(AuthenticatableTwoFactor $user, string $code)
    {
        $this->user = $user;
        $this->code = $code;
    }
}
