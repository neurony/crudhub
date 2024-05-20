<?php

namespace Zbiller\Crudhub\Models;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Zbiller\Crudhub\Contracts\AdminModelContract;
use Zbiller\Crudhub\Contracts\AuthenticatableTwoFactor;
use Zbiller\Crudhub\Traits\AutoSavesMediaFiles;
use Zbiller\Crudhub\Traits\FiltersRecords;
use Zbiller\Crudhub\Traits\HasGlobalMediaConversions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Zbiller\Crudhub\Traits\SortsRecords;
use Zbiller\Crudhub\Traits\TwoFactorAuthentication;

class Admin extends Authenticatable implements AdminModelContract, AuthenticatableTwoFactor, HasMedia
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use InteractsWithMedia;
    use AutoSavesMediaFiles;
    use HasGlobalMediaConversions;
    use FiltersRecords;
    use SortsRecords;
    use TwoFactorAuthentication;

    /**
     * @var string
     */
    protected $table = 'admins';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code',
        'two_factor_code_expires_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'active' => 'boolean',
        'two_factor_code_expires_at' => 'datetime',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'initials',
    ];

    /**
     * @return string
     */
    public function getInitialsAttribute(): string
    {
        $words = array_filter(explode(' ', $this->name));
        $initials = '';

        foreach($words as $index => $word) {
            if ($index > 1) {
                break;
            }

            $initials .= strtoupper($word[0]);
        }

        return $initials;
    }

    /**
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $notification = new ResetPasswordNotification($token);

        $notification->createUrlUsing(function ($notifiable, $token) {
            return route('admin.password_reset.create', [
                'email' => $notifiable->getEmailForPasswordReset(),
                'token' => $token,
            ]);
        });

        $this->notify($notification);
    }

    /**
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('avatars')
            ->useDisk(config('crudhub.media.disk_name'))
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {

            });
    }

    /**
     * @param Media|null $media
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->registerGlobalMediaConversions($media);
    }
}
