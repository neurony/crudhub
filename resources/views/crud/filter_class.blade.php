@php echo "<?php" @endphp

/** Generated with Crudhub */

namespace {{ $classNamespace }};

use Zbiller\Crudhub\Filters\Filter;

class {{ $className }} extends Filter
{
    /**
     * @return string
     */
    public function morph(): string
    {
        return 'and';
    }

    /**
     * @return array
     */
    public function filters(): array
    {
@if(empty($filteringRules))
        return [];
@else
        return [
@foreach($filteringRules as $field => $rules)
            '{{ $field }}' => [
                'operator' => {!! $rules['operator'] !!},
                'condition' => {!! $rules['condition'] !!},
                'columns' => '{{ implode(',', $rules['columns']) }}',
            ],
@endforeach
        ];
@endif
    }
}
