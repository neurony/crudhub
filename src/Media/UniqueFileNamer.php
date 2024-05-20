<?php

namespace Zbiller\Crudhub\Media;

use Illuminate\Support\Facades\App;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\Support\FileNamer\FileNamer;

class UniqueFileNamer extends FileNamer
{
    /**
     * @param string $fileName
     * @return string
     */
    public function originalFileName(string $fileName): string
    {
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $uniqueIdentifier = md5(time() . rand());

        if (App::make(config('crudhub.media.media_model'))->whereFileName($fileName)->exists()) {
            return $baseName;
        }

        return "{$baseName}-{$uniqueIdentifier}";
    }

    /**
     * @param string $fileName
     * @param Conversion $conversion
     * @return string
     */
    public function conversionFileName(string $fileName, Conversion $conversion): string
    {
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);

        return "{$baseName}-{$conversion->getName()}";
    }

    /**
     * @param string $fileName
     * @return string
     */
    public function responsiveFileName(string $fileName): string
    {
        return pathinfo($fileName, PATHINFO_FILENAME);
    }
}
