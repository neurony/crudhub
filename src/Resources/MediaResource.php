<?php

namespace Zbiller\Crudhub\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'path' => $this->resource->getPathRelativeToRoot(),
            'original_url' => $this->resource->getFullUrl(),
            'preview_url' => $this->resource->getFullUrl('preview'),
            'human_readable_size' => $this->resource->human_readable_size,
        ]);
    }
}
