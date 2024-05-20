@php echo "<?php" @endphp

/** Generated with Crudhub */

namespace {{ $classNamespace }};

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
@if($withTranslatable)
use Zbiller\CrudhubLang\Singletons\LocaleSingleton;
@endif

class {{ $className }} extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
@if(count($validationRules))

    /**
     * @return array
     */
    public function rules()
    {
@if($withTranslatable)
        return array_merge($this->translatedRules(), [
@else
        return [
@endif
@foreach($validationRules as $field => $data)
            '{{ $field }}' => [
@foreach($data['rules'] ?? [] as $rule)
@if($rule == 'unique')
                Rule::unique('{{ $data['unique_table'] }}', '{{ $data['unique_column'] }}')
@foreach($data['unique_related_columns'] ?? [] as $column)
                    ->where(function ($query) {
                        return $query->where('{{ $column }}', $this->get('{{ $column }}'));
                    })
@endforeach
                    ->ignore($this->route('{{ $routeBinding }}')?->getKey() ?: null),
@elseif($rule == 'exists')
                Rule::exists('{{ $data['exists_table'] }}', '{{ $data['exists_column'] }}'),
@else
                '{{ $rule }}',
@endif
@endforeach
            ],
@endforeach
@if($withTranslatable)
        ]);
@else
        ];
@endif
    }

    /**
     * @return array
     */
    public function attributes()
    {
@if($withTranslatable)
        return array_merge([

        ], $this->translatedAttributes());
@else
        return [];
@endif
    }
@endif
@if(count($requestMerges))
@php($mergeChain = '')
@foreach($requestMerges as $merge)
@php($mergeChain .= "->merge{$merge['name']}()")
@endforeach

    /**
     * @return $this
     */
    public function merged(): self
    {
        return $this{!! $mergeChain !!};
    }
@foreach($requestMerges as $merge)

    /**
     * @return $this
     */
    protected function merge{{ $merge['name'] }}(): self
    {
        if (!$this->filled('{{ $merge['field'] }}')) {
            $this->merge([
                '{{ $merge['field'] }}' => false,
            ]);
        }

        return $this;
    }
@endforeach
@endif
@if($withTranslatable)

    /**
     * @return array
     */
    protected function translatedRules(): array
    {
        $rules = [];

        foreach (LocaleSingleton::getActiveLocales() as $locale) {
@foreach($translatableValidationRules as $field => $data)
            $rules["{{ $field }}.$locale"] = [
@foreach($data['rules'] ?? [] as $rule)
                '{{ $rule }}',
@endforeach
            ];

@endforeach
        }

        return $rules;
    }

    /**
     * @return array
     */
    protected function translatedAttributes(): array
    {
        $attributes = [];

        foreach (LocaleSingleton::getActiveLocales() as $locale) {
@foreach($translatableValidationRules as $field => $data)
            $attributes["{{ $field }}.$locale"] = '{{ str_replace(['_', '-', '.'], ' ', $field) }}';
@endforeach
        }

        return $attributes;
    }
@endif
}
