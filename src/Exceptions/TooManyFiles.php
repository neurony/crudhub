<?php

namespace Zbiller\Crudhub\Exceptions;

use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;

class TooManyFiles extends FileCannotBeAdded
{
    /**
     * @param int $maxFileCount
     * @param string|null $collectionName
     * @return TooManyFiles
     */
    public static function create(string $collectionName = null, int $maxFileCount = 0): TooManyFiles
    {
        return new static(
            sprintf('Max file count in %s is %s', $collectionName, $maxFileCount)
        );
    }
}
