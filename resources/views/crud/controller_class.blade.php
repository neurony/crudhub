@php echo "<?php" @endphp

/** Generated with Crudhub */

namespace {{ $classNamespace }};

use {{ $baseControllerFqn }};
use {{ $filterFqn }};
use {{ $resourceFqn }};
use {{ $formRequestFqn }};
use {{ $sortFqn }};
use {{ $modelFqn }};
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Throwable;
use Zbiller\Crudhub\Facades\Flash;
use Zbiller\Crudhub\Traits\BulkDestroysRecords;
use Zbiller\Crudhub\Traits\PartiallyUpdatesRecords;
@if($withReorderable)
use Zbiller\Crudhub\Traits\SavesRecordsOrder;
@endif
@if($withSoftDelete)
use Zbiller\Crudhub\Traits\SoftDeletesRecords;
@endif

class {{ $className }} extends {{ $baseControllerName }}
{
    use BulkDestroysRecords;
    use PartiallyUpdatesRecords;
@if($withReorderable)
    use SavesRecordsOrder;
@endif
@if($withSoftDelete)
    use SoftDeletesRecords;
@endif

    /**
     * @param Request $request
     * @param {{ $filterName }} $filter
     * @param {{ $sortName }} $sort
     * @return \Inertia\Response
     */
    public function index(Request $request, {{ $filterName }} $filter, {{ $sortName }} $sort)
    {
        $query = {{ $modelName }}::query()
@if(count($modelRelations))
            ->with([
@foreach($modelRelations as $relation)
                '{{ $relation['name'] }}',
@endforeach
            ])
@endif
@if($withSoftDelete)
            ->when((int)$request->get('with_trashed') == 1, function ($q) {
                $q->withTrashed();
            })
@endif
            ->filtered($request->all(), $filter)
            ->sorted($request->all(), $sort);

@if($withReorderable)
        if (has_query_filled($request, ['page'])) {
            $items = $query
                ->paginate($request->get('per_page', 10))
                ->withQueryString();
        } else {
            $items = $query
                ->ordered()
                ->get();
        }
@else
        $items = $query
            ->paginate($request->get('per_page', 10))
            ->withQueryString();
@endif

        return Inertia::render('{{ $indexView }}', [
            'items' => {{ $resourceName }}::collection($items),
            'options' => [
@foreach($optionVariables['relations'] as $optionVariable)
                '{{ $optionVariable['field_name'] }}' => $this->{{ $optionVariable['method_name'] }}(),
@endforeach
            ],
        ]);
    }

    /**
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('{{ $createView }}', [
            'options' => [
@foreach($optionVariables['relations'] as $optionVariable)
                '{{ $optionVariable['field_name'] }}' => $this->{{ $optionVariable['method_name'] }}(),
@endforeach
            ],
        ]);
    }

    /**
     * @param {{ $formRequestName }} $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function store({{ $formRequestName }} $request)
    {
        try {
            DB::beginTransaction();

            {{ $modelVariable }} = {{ $modelName }}::create($request->all());
@foreach($modelRelations as $relation)
@if($relation['type'] == 'belongsToMany')
            {{ $modelVariable }}->{{ $relation['name'] }}()->attach((array)$request->get('{{ $relation['name'] }}', []));
@endif
@endforeach

            DB::commit();

            Flash::success('Record created successfully!');

            return Redirect::saved($request, route('{{ $routeNames['index'] }}'), route('{{ $routeNames['edit'] }}', {{ $modelVariable }}->getKey()));
        } catch (Throwable $e) {
            DB::rollBack();

            Flash::error(exception: $e);
        }
    }

    /**
     * @param {{ $modelName }} {{ $modelVariable }}
     * @return \Inertia\Response
     */
    public function edit({{ $modelName }} {{ $modelVariable }})
    {
@if(count($modelRelations))
        {{ $modelVariable }}->load([
@foreach($modelRelations as $relation)
            '{{ $relation['name'] }}',
@endforeach
        ]);
@endif

        return Inertia::render('{{ $editView }}', [
            'item' => {{ $resourceName }}::make({{ $modelVariable }}),
            'options' => [
@foreach($optionVariables['relations'] as $optionVariable)
                '{{ $optionVariable['field_name'] }}' => $this->{{ $optionVariable['method_name'] }}(),
@endforeach
            ],
        ]);
    }

    /**
     * @param {{ $formRequestName }} $request
     * @param {{ $modelName }} {{ $modelVariable }}
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function update({{ $formRequestName }} $request, {{ $modelName }} {{ $modelVariable }})
    {
        try {
            DB::beginTransaction();

            {{ $modelVariable }}->update($request->all());
@foreach($modelRelations as $relation)
@if($relation['type'] == 'belongsToMany')
            {{ $modelVariable }}->{{ $relation['name'] }}()->sync((array)$request->get('{{ $relation['name'] }}', []));
@endif
@endforeach

            DB::commit();

            Flash::success('Record updated successfully!');

            return Redirect::saved($request, route('{{ $routeNames['index'] }}'), route('{{ $routeNames['edit'] }}', {{ $modelVariable }}->getKey()));
        } catch (Throwable $e) {
            DB::rollBack();

            Flash::error(exception: $e);
        }
    }

    /**
     * @param Request $request
     * @param {{ $modelName }} {{ $modelVariable }}
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function destroy(Request $request, {{ $modelName }} {{ $modelVariable }})
    {
        try {
            DB::beginTransaction();

            {{ $modelVariable }}->delete();

            DB::commit();

            Flash::success('Record deleted successfully!');

            return Redirect::deleted(route('{{ $routeNames['index'] }}'));
        } catch (Throwable $e) {
            DB::rollBack();

            Flash::error(exception: $e);
        }
    }
@foreach($optionVariables['relations'] as $optionVariable)

    /**
     * @return Collection
     */
    protected function {{ $optionVariable['method_name'] }}(): Collection
    {
        return DB::table('{{ $optionVariable['table_related'] }}')
            ->get(['{{ $optionVariable['primary_key'] }}', '{{ $optionVariable['related_attribute'] }}'])
            ->map(function ($item) {
                return [
                    'value' => $item->{{ $optionVariable['primary_key'] }},
                    'label' => $item->{{ $optionVariable['related_attribute'] }},
                ];
            })
            ->values();
    }
@endforeach

    /**
     * @return string
     */
    public function bulkDestroyModel(): string
    {
        return {{ $modelName }}::class;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function bulkDestroyIds(Request $request): array
    {
        return (array)$request->get('ids') ?? [];
    }

    /**
     * @return string
     */
    public function partialUpdateModel(): string
    {
        return {{ $modelName }}::class;
    }

    /**
     * @param Request $request
     * @return array
    */
    public function partialUpdateData(Request $request): array
    {
        return [
@foreach($formFields as $formField)
            '{{ $formField['name'] }}' => $request->get('{{ $formField['name'] }}'),
@endforeach
        ];
    }
@if($withSoftDelete)

    /**
    * @return string
    */
    public function softDeleteModel(): string
    {
        return {{ $modelName }}::class;
    }
@endif
@if($withReorderable)

    /**
     * @return string
     */
    public function reorderModel(): string
    {
        return {{ $modelName }}::class;
    }
@endif
}
