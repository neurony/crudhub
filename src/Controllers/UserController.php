<?php

namespace Zbiller\Crudhub\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Zbiller\Crudhub\Contracts\UserFilterContract;
use Zbiller\Crudhub\Contracts\UserModelContract;
use Zbiller\Crudhub\Contracts\UserSortContract;
use Zbiller\Crudhub\Facades\Flash;
use Zbiller\Crudhub\Requests\UserRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Zbiller\Crudhub\Resources\Resource;
use Zbiller\Crudhub\Resources\UserResource;
use Zbiller\Crudhub\Traits\BulkDestroysRecords;
use Zbiller\Crudhub\Traits\PartiallyUpdatesRecords;

class UserController
{
    use AuthorizesRequests;
    use ValidatesRequests;
    use BulkDestroysRecords;
    use PartiallyUpdatesRecords;

    /**
     * @param Request $request
     * @param UserFilterContract $filter
     * @param UserSortContract $sort
     * @return \Inertia\Response
     */
    public function index(Request $request, UserFilterContract $filter, UserSortContract $sort)
    {
        $items = App::make(UserModelContract::class)
            ->filtered($request->all(), $filter)
            ->sorted($request->all(), $sort)
            ->paginate($request->get('per_page', 10))
            ->withQueryString();

        return inertia('Users/Index', [
            'items' => Resource::collection('user_resource', $items),
        ]);
    }

    /**
     * @return \Inertia\Response
     */
    public function create()
    {
        return inertia('Users/Create');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function store()
    {
        /** @var UserRequest $request */
        $request = $this->initRequest();

        try {
            DB::beginTransaction();

            $user = App::make(UserModelContract::class)->create($request->all());

            DB::commit();

            Flash::success('Record created successfully!');

            return Redirect::saved($request, route('admin.users.index'), route('admin.users.edit', $user->getKey()));
        } catch (Throwable $e) {
            DB::rollBack();

            Flash::error(exception: $e);
        }
    }

    /**
     * @param UserModelContract $user
     * @return \Inertia\Response
     */
    public function edit(UserModelContract $user)
    {
        return inertia('Users/Edit', [
            'item' => Resource::make('user_resource', $user),
        ]);
    }

    /**
     * @param UserModelContract $user
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function update(UserModelContract $user)
    {
        /** @var UserRequest $request */
        $request = $this->initRequest();

        try {
            DB::beginTransaction();

            $user->update($request->all());

            DB::commit();

            Flash::success('Record updated successfully!');

            return Redirect::saved($request, route('admin.users.index'), route('admin.users.edit', $user->getKey()));
        } catch (Throwable $e) {
            DB::rollBack();

            Flash::error(exception: $e);
        }
    }

    /**
     * @param Request $request
     * @param UserModelContract $user
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function destroy(Request $request, UserModelContract $user)
    {
        try {
            DB::beginTransaction();

            $user->delete();

            DB::commit();

            Flash::success('Record deleted successfully!');

            return Redirect::deleted(route('admin.users.index'));
        } catch (Throwable $e) {
            DB::rollBack();

            Flash::error(exception: $e);
        }
    }

    /**
     * @return string
     */
    public function bulkDestroyModel(): string
    {
        return UserModelContract::class;
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
        return UserModelContract::class;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function partialUpdateData(Request $request): array
    {
        return [
            'active' => $request->get('active', false),
        ];
    }

    /**
     * @return mixed
     */
    protected function initRequest(): mixed
    {
        $request = config('crudhub.bindings.form_requests.user_form_request', UserRequest::class);

        return App::make($request)->merged();
    }
}
