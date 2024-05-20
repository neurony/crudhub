<?php

namespace Zbiller\Crudhub\Exceptions;

use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;

class FileIsTooBig extends FileCannotBeAdded
{
    /**
     * @param string $file
     * @param float $maxSize
     * @param string $collectionName
     * @return FileIsTooBig
     */
    public static function create(string $file, float $maxSize, string $collectionName): FileIsTooBig
    {
        $actualFileSize = filesize($file);

        return new static(
            sprintf('File size is %s, while max size of file in %s is %s', $actualFileSize, $collectionName, $maxSize)
        );
    }
}
