<?php

namespace Zbiller\Crudhub\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Throwable;
use Zbiller\Crudhub\Contracts\AuthenticatableTwoFactor;

class TwoFactorCodeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var AuthenticatableTwoFactor
     */
    public AuthenticatableTwoFactor $user;

    /**
     * @var string
     */
    public string $code;

    /**
     * @var string
     */
    public string $expiration;

    /**
     * @param AuthenticatableTwoFactor $user
     */
    public function __construct(AuthenticatableTwoFactor $user)
    {
        $this->user = $user;
        $this->code = $user->{$user->getTwoFactorCodeField()};

        try {
            $this->expiration = $user->{$user->getTwoFactorCodeExpiresAtField()}
                ->diffForHumans(Carbon::now(), [
                    'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
                    'parts' => 1,
                ]);
        } catch (Throwable $e) {
            $this->expiration = '';
        }
    }

    /**
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name') . ' - Two Factor Code',
        );
    }

    /**
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'crudhub::emails.two_factor_code',
        );
    }
}
