<?php

namespace Zbiller\Crudhub\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Zbiller\Crudhub\Contracts\UserModelContract;
use Zbiller\Crudhub\Traits\AutoSavesMediaFiles;
use Zbiller\Crudhub\Traits\FiltersRecords;
use Zbiller\Crudhub\Traits\HasGlobalMediaConversions;
use Zbiller\Crudhub\Traits\SortsRecords;

class User extends Authenticatable implements UserModelContract, HasMedia
{
    use FiltersRecords;
    use SortsRecords;
    use InteractsWithMedia;
    use AutoSavesMediaFiles;
    use HasGlobalMediaConversions;

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
