<?php

namespace Zbiller\Crudhub\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Zbiller\Crudhub\Events\TwoFactorCodeGenerated;
use Zbiller\Crudhub\Mail\TwoFactorCodeMail;

class SendTwoFactorCodeToUser implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @param TwoFactorCodeGenerated $event
     */
    public function handle(TwoFactorCodeGenerated $event): void
    {
        $user = $event->user;

        Mail::to($user->{$user->getTwoFactorEmailField()})
            ->send(new TwoFactorCodeMail($user));
    }
}
