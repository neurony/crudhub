<?php

namespace Zbiller\Crudhub\Traits;

trait AutoSavesMediaFiles
{
    use SavesMediaFiles;

    /**
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded
     */
    public static function bootAutoSavesMediaFiles(): void
    {
        static::saved(static function (self $model) {
            $collections = $model->getRegisteredMediaCollections()->keyBy('name')->map->name->toArray();
            $data = request()->only($collections);

            $model->saveMedia($data);
        });
    }
}
