@php echo "<?php" @endphp

/** Generated with Crudhub */

namespace {{ $classNamespace }};

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
@if(count($mediaCollections))
use Zbiller\Crudhub\Resources\MediaResource;
@endif

class {{ $className }} extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request)
    {
@if(count($mediaCollections))
@foreach($mediaCollections as $collection)
        ${{ $collection['name'] }} = $this->resource->getMedia('{{ $collection['name'] }}');
@endforeach

@endif
        return array_merge((array)parent::toArray($request), [
@if(!count($definedRelations) && !count($mediaCollections))
{!! '            ' !!}
@endif
@foreach($definedRelations as $relation)
            '{{ $relation['name'] }}' => $this->whenLoaded('{{ $relation['name'] }}'),
@endforeach
@foreach($mediaCollections as $collection)
            '{{ $collection['name'] }}' => MediaResource::collection(${{ $collection['name'] }}),
            '{{ \Illuminate\Support\Str::singular($collection['name']) }}' => MediaResource::make(${{ $collection['name'] }}->first()),
@endforeach
        ]);
    }
}
