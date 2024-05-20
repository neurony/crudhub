<?php

namespace Zbiller\Crudhub\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<mixed>
     */
    public function toArray(Request $request): array
    {
        $avatars = $this->resource->getMedia('avatars');

        return array_merge(parent::toArray($request), [
            'avatars' => MediaResource::collection($avatars),
            'avatar' => MediaResource::make($avatars->first()),
        ]);
    }
}
