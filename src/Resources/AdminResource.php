<?php

namespace Zbiller\Crudhub\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $avatars = $this->resource->getMedia('avatars');

        return array_merge(parent::toArray($request), [
            'role' => $this->resource->roles()->whereGuardName('admin')->first(),
            'roles' => $this->resource->getRoleNames(),
            'permissions' => $this->resource->getAllPermissions()->pluck('name'),
            'avatar' => MediaResource::make($avatars->first()),
            'avatars' => MediaResource::collection($avatars),
        ]);
    }
}
