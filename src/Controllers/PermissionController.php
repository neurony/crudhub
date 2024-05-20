<?php

namespace Zbiller\Crudhub\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Spatie\Permission\Contracts\Permission as PermissionModelContract;
use Spatie\Permission\Contracts\Role as RoleModelContract;
use Zbiller\Crudhub\Facades\Flash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class PermissionController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        $roles = App::make(RoleModelContract::class)
            ->with('permissions')
            ->where('name', '!=', 'Root')
            ->get();

        $permissions = App::make(PermissionModelContract::class)
            ->get()
            ->groupBy(function (PermissionModelContract $permission) {
                return Str::title(array_filter(explode('-', $permission->name))[0]);
            });

        return inertia('Permissions/Index', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            foreach ((array)($request->get('roles_permissions') ?? []) as $roleId => $permissionIds) {
                $role = App::make(RoleModelContract::class)->find($roleId);

                if (!($role instanceof RoleModelContract && $role->exists)) {
                    continue;
                }

                $role->syncPermissions($permissionIds);
            }

            DB::commit();

            Flash::success('Record updated successfully!');

            return Redirect::route('admin.permissions.index');
        } catch (Throwable $e) {
            DB::rollBack();

            Flash::error(exception: $e);
        }
    }
}
