<?php

namespace Zbiller\Crudhub\Resources;

use Countable;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource
{
    /**
     * @param string $key
     * @param mixed $resource
     * @return JsonResource
     */
    public static function make(string $key, mixed $resource, string $config = 'crudhub'): JsonResource
    {
        /** @var JsonResource $class */
        $class = (string)config("$config.bindings.resources.$key");

        return $class::make($resource);
    }

    /**
     * @param string $key
     * @param mixed $resource
     * @return AnonymousResourceCollection
     */
    public static function collection(string $key, mixed $resource, string $config = 'crudhub'): AnonymousResourceCollection
    {
        /** @var JsonResource $class */
        $class = (string)config("$config.bindings.resources.$key");

        return $class::collection($resource);
    }
}
