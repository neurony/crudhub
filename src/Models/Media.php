<?php

namespace Zbiller\Crudhub\Models;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Observers\MediaObserver;
use Zbiller\Crudhub\Contracts\MediaModelContract;

class Media extends SpatieMedia implements MediaModelContract
{
    /**
     * @const
     */
    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';
    const TYPE_AUDIO = 'audio';
    const TYPE_FILE = 'file';

    /**
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::observe(MediaObserver::class);
    }

    /**
     * @return string
     */
    public function getFileType(): string
    {
        if (Str::startsWith($this->mime_type, 'image/')) {
            return self::TYPE_IMAGE;
        }

        if (Str::startsWith($this->mime_type, 'video/')) {
            return self::TYPE_VIDEO;
        }

        if (Str::startsWith($this->mime_type, 'audio/')) {
            return self::TYPE_AUDIO;
        }

        return self::TYPE_FILE;
    }

    /**
     * @return string
     */
    public function getOrderColumnName(): string
    {
        return $this->determineOrderColumnName();
    }
}
