<?php

namespace Zbiller\Crudhub\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Zbiller\Crudhub\Contracts\AuthenticatableTwoFactor;

class TwoFactorCodeGenerated
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
     */
    public function __construct(AuthenticatableTwoFactor $user)
    {
        $this->user = $user;
        $this->code = $user->{$user->getTwoFactorCodeField()};
    }
}
