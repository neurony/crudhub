<?php

namespace Zbiller\Crudhub\Controllers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Zbiller\Crudhub\Resources\MediaResource;
use Zbiller\Crudhub\Models\MediaUnassigned;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Zbiller\Crudhub\Resources\Resource;

class UploadController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @param Request $request
     * @return JsonResource
     */
    public function store(Request $request): JsonResource
    {
        $media = MediaUnassigned::create()
            ->addMedia($request->file('file'))
            ->toMediaCollection(MediaUnassigned::MEDIA_COLLECTION);

        return Resource::make('media_resource', $media);
    }
}
