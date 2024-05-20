<?php

namespace Zbiller\Crudhub\Traits;

use Spatie\Image\Enums\Fit;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasGlobalMediaConversions
{
    /**
     * @param Media|null $media
     * @return void
     * @throws InvalidManipulation
     */
    public function registerGlobalMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->width(150)
            ->height(150)
            ->fit(Fit::Crop, 150, 150);
    }
}
