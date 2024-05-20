<?php

namespace Zbiller\Crudhub\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Spatie\Permission\Contracts\Role as RoleModelContract;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Zbiller\Crudhub\Contracts\AdminFilterContract;
use Zbiller\Crudhub\Contracts\AdminModelContract;
use Zbiller\Crudhub\Contracts\AdminSortContract;
use Zbiller\Crudhub\Facades\Flash;
use Zbiller\Crudhub\Requests\AdminRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Zbiller\Crudhub\Resources\Resource;
use Zbiller\Crudhub\Traits\BulkDestroysRecords;
use Zbiller\Crudhub\Traits\PartiallyUpdatesRecords;

class AdminController
{
    use AuthorizesRequests;
    use ValidatesRequests;
    use BulkDestroysRecords;
    use PartiallyUpdatesRecords;

    /**
     * @param Request $request
     * @param Authenticatable $user
     * @param AdminFilterContract $filter
     * @param AdminSortContract $sort
     * @return \Inertia\Response
     */
    public function index(Request $request, Authenticatable $user, AdminFilterContract $filter, AdminSortContract $sort)
    {
        $items = App::make(AdminModelContract::class)
            ->where('id', '!=', $user->getAuthIdentifier())
            ->filtered($request->all(), $filter)
            ->sorted($request->all(), $sort)
            ->paginate($request->get('per_page', 10))
            ->withQueryString();

        return Inertia::render('Admins/Index', [
            'items' => Resource::collection('admin_resource', $items),
            'options' => [
                'roles' => $this->getRoles(),
            ],
        ]);
    }

    /**
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Admins/Create', [
            'options' => [
                'roles' => $this->getRoles(),
            ],
        ]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function store()
    {
        /** @var AdminRequest $request */
        $request = $this->initRequest();

        try {
            DB::beginTransaction();

            $admin = App::make(AdminModelContract::class)->create($request->all());
            $admin->assignRole($request->get('role'));

            DB::commit();

            Flash::success('Record created successfully!');

            return Redirect::saved($request, route('admin.admins.index'), route('admin.admins.edit', $admin->getKey()));
        } catch (Throwable $e) {
            DB::rollBack();

            Flash::error(exception: $e);
        }
    }

    /**
     * @param AdminModelContract $admin
     * @return \Inertia\Response
     */
    public function edit(AdminModelContract $admin)
    {
        $this->checkForSameUserWithoutPermission($admin, 'admins-edit');

        return Inertia::render('Admins/Edit', [
            'item' => Resource::make('admin_resource', $admin),
            'options' => [
                'roles' => $this->getRoles(),
            ],
        ]);
    }

    /**
     * @param AdminModelContract $admin
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function update(AdminModelContract $admin)
    {
        $this->checkForSameUserWithoutPermission($admin, 'admins-edit');

        /** @var AdminRequest $request */
        $request = $this->initRequest();

        try {
            DB::beginTransaction();

            $admin->update($request->all());
            $admin->syncRoles($request->get('role'));

            DB::commit();

            Flash::success('Record updated successfully!');

            return Redirect::saved($request, route('admin.admins.index'), route('admin.admins.edit', $admin->getKey()));
        } catch (Throwable $e) {
            DB::rollBack();

            Flash::error(exception: $e);
        }
    }

    /**
     * @param Request $request
     * @param AdminModelContract $admin
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function destroy(Request $request, AdminModelContract $admin)
    {
        if ($admin->getKey() === $request->user()->getAuthIdentifier()) {
            Flash::error('You cannot delete yourself!');

            return Redirect::back();
        }

        try {
            DB::beginTransaction();

            $admin->delete();

            DB::commit();

            Flash::success('Record deleted successfully!');

            return Redirect::deleted(route('admin.admins.index'));
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
        return AdminModelContract::class;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function bulkDestroyIds(Request $request): array
    {
        return array_values(array_filter((array)$request->get('ids') ?? [], function ($id) use ($request) {
            return $id !== $request->user()->getAuthIdentifier();
        }));
    }

    /**
     * @return string
     */
    public function partialUpdateModel(): string
    {
        return AdminModelContract::class;
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
     * @return \Illuminate\Support\Collection
     */
    protected function getRoles(): Collection
    {
        return App::make(RoleModelContract::class)
            ->whereGuardName('admin')
            ->get()
            ->map(function ($role) {
                return [
                    'value' => $role->id,
                    'label' => $role->name,
                ];
            })
            ->values();
    }

    /**
     * @return mixed
     */
    protected function initRequest(): mixed
    {
        $request = config('crudhub.bindings.form_requests.admin_form_request', AdminRequest::class);

        return App::make($request)->merged();
    }

    /**
     * @param AdminModelContract $admin
     * @param string $permission
     * @return void
     */
    protected function checkForSameUserWithoutPermission(AdminModelContract $admin, string $permission): void
    {
        $user = Auth::guard('admin')->user();

        if (!($user->hasRole('Root') || $user->hasPermissionTo($permission, 'admin') || $admin->getKey() == $user->getKey())) {
            throw UnauthorizedException::forPermissions([$permission]);
        }
    }
}
