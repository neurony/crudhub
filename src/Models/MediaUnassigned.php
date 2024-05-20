<?php

namespace Zbiller\Crudhub\Models;

use Zbiller\Crudhub\Traits\HasGlobalMediaConversions;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaUnassigned extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasGlobalMediaConversions;

    /**
     * @var string
     */
    public $table = 'media_unassigned';

    /**
     * @const
     */
    const MEDIA_COLLECTION = 'unassigned';

    /**
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection(self::MEDIA_COLLECTION)
            ->useDisk('media_unassigned')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->registerGlobalMediaConversions($media);
            });
    }
}
