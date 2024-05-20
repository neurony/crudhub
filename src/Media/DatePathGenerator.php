<?php

namespace Zbiller\Crudhub\Media;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class DatePathGenerator extends DefaultPathGenerator
{
    /**
     * @param Media $media
     * @return string
     */
    public function getPath(Media $media): string
    {
        return $this->getDatePath($media);
    }

    /**
     * @param Media $media
     * @return string
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getDatePath($media);
    }

    /**
     * @param Media $media
     * @return string
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getDatePath($media);
    }

    /**
     * @param Media $media
     * @return string
     */
    protected function getDatePath(Media $media): string
    {
        $date = $media->created_at;

        if (!($date instanceof Carbon)) {
            try {
                $date = Carbon::parse($media->created_at);
            } catch (InvalidFormatException $e) {
                $date = Carbon::now();
            }
        }

        $path = "{$date->format('Y')}/{$date->format('m')}/{$date->format('d')}/";
        $prefix = config('media-library.prefix', '');

        if (!empty($prefix)) {
            return $prefix . '/' . $path;
        }

        return $path;
    }
}
