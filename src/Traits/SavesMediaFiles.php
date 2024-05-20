<?php

namespace Zbiller\Crudhub\Traits;

use Zbiller\Crudhub\Exceptions\TooManyFiles;
use Zbiller\Crudhub\Models\Media;
use Zbiller\Crudhub\Models\MediaUnassigned;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\Exceptions\MimeTypeNotAllowed;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;

trait SavesMediaFiles
{
    /**
     * @param array<mixed> $data
     * @return void
     * @throws FileCannotBeAdded
     */
    public function saveMedia(array $data): void
    {
        set_time_limit(0);

        foreach ($this->getRegisteredMediaCollections()->keyBy('name') as $mediaCollection) {
            if (empty($data[$mediaCollection->name])) {
                continue;
            }

            $this->validateMediaCollection($mediaCollection, $data[$mediaCollection->name]);
        }

        foreach ($this->getRegisteredMediaCollections()->keyBy('name') as $mediaCollection) {
            if (empty($data[$mediaCollection->name])) {
                continue;
            }

            foreach ($data[$mediaCollection->name] as $collectionData) {
                $this->saveSingleMedia($mediaCollection, $collectionData);
            }
        }
    }

    /**
     * @param MediaCollection $mediaCollection
     * @param array $collectionData
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function saveSingleMedia(MediaCollection $mediaCollection, array $collectionData): void
    {
        switch (strtolower($collectionData['action'] ?? '')) {
            case 'create':
                $this->createSingleMedia($mediaCollection, $collectionData);
                break;
            case 'update':
                $this->updateSingleMedia($collectionData);
                break;
            case 'destroy':
                $this->destroySingleMedia($collectionData);
                break;
        }
    }

    /**
     * @param MediaCollection $mediaCollection
     * @param array $collectionData
     * @throws MimeTypeNotAllowed
     * @throws TooManyFiles
     */
    public function validateMediaCollection(MediaCollection $mediaCollection, array $collectionData): void
    {
        $this->validateMediaCollectionCount($mediaCollection, $collectionData);

        foreach ($collectionData as $collectionDatum) {
            if (strtolower($collectionDatum['action'] ?? '') !== 'create') {
                continue;
            }

            $this->validateAcceptedMimeTypes($mediaCollection, $collectionDatum);
        }
    }

    /**
     * @param MediaCollection $mediaCollection
     * @param array $collectionData
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     */
    protected function createSingleMedia(MediaCollection $mediaCollection, array $collectionData): void
    {
        if (($collectionData['model_type'] ?? null) != MediaUnassigned::class) {
            return;
        }

        if (empty($collectionData['path']) || !Storage::disk('media_unassigned')->exists($collectionData['path'])) {
            return;
        }

        $this->addMedia(Storage::disk('media_unassigned')->path($collectionData['path']))
            ->withCustomProperties($collectionData['custom_properties'] ?? [])
            ->setOrder(($collectionData['order_column'] ?? 1) == 1 ? $this->getMedia($mediaCollection->name)->count() + 1 : $collectionData['order_column'])
            ->toMediaCollection($mediaCollection->name, $mediaCollection->diskName);

        $this->deleteUnassignedMedia($collectionData);
    }

    /**
     * @param array $collectionData
     * @return void
     */
    protected function updateSingleMedia(array $collectionData): void
    {
        if (($collectionData['model_type'] ?? null) != $this->getMorphClass()) {
            return;
        }

        $media = $this->media()->find($collectionData['id']);

        if (!($media instanceof Media && $media->exists)) {
            return;
        }

        $media->custom_properties = $collectionData['custom_properties'] ?? [];
        $media->{$media->getOrderColumnName()} = $collectionData['order_column'];

        $media->update([
            'order_column' => $collectionData['order_column'] ?? [],
            'custom_properties' => $collectionData['custom_properties'] ?? [],
        ]);
    }

    /**
     * @param array $collectionData
     * @return void
     */
    protected function destroySingleMedia(array $collectionData): void
    {
        $media = Media::find($collectionData['id']);

        if ($media instanceof Media && $media->exists) {
            $media->delete();
        }

        $this->deleteUnassignedMedia($collectionData);
    }

    /**
     * @param array $collectionData
     * @return void
     */
    protected function deleteUnassignedMedia(array $collectionData)
    {
        if (($collectionData['model_type'] ?? null) != MediaUnassigned::class) {
            return;
        }

        $mediaUnassigned = MediaUnassigned::find($collectionData['model_id']);

        if ($mediaUnassigned instanceof MediaUnassigned && $mediaUnassigned->exists) {
            $mediaUnassigned->delete();
        }
    }

    /**
     * @param MediaCollection $mediaCollection
     * @param array $collectionData
     * @return void
     * @throws TooManyFiles
     */
    protected function validateMediaCollectionCount(MediaCollection $mediaCollection, array $collectionData): void
    {
        if (!$mediaCollection->collectionSizeLimit) {
            return;
        }

        $alreadyUploadedCount = $this->getMedia($mediaCollection->name)->count();

        $forUploadCount = collect($collectionData)->filter(static function ($data) {
            return isset($data['action']) && $data['action'] === 'create';
        })->count();

        $forDeleteCount = collect($collectionData)->filter(static function ($data) {
            return isset($data['action']) && $data['action'] === 'destroy' ? 1 : 0;
        })->count();

        if ($forUploadCount + $alreadyUploadedCount - $forDeleteCount > $mediaCollection->collectionSizeLimit) {
            throw TooManyFiles::create($mediaCollection->name, $mediaCollection->collectionSizeLimit);
        }
    }

    /**
     * @param MediaCollection $mediaCollection
     * @param array<mixed> $mediaCollectionDatum
     * @return void
     * @throws MimeTypeNotAllowed
     */
    protected function validateAcceptedMimeTypes(MediaCollection $mediaCollection, array $mediaCollectionDatum): void
    {
        if (!$mediaCollection->acceptsMimeTypes && empty($mediaCollectionDatum['path'])) {
            return;
        }

        $path = Storage::disk('media_unassigned')->path($mediaCollectionDatum['path']);

        $this->guardAgainstInvalidMimeType($path, $mediaCollection->acceptsMimeTypes);
    }
}
