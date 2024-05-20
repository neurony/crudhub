<?php

namespace Database\Seeders\Crudhub;

use Illuminate\Database\Seeder;
use Spatie\Permission\Contracts\Role as RoleModelContract;
use Spatie\Permission\Contracts\Permission as PermissionModelContract;

class RoleSeeder extends Seeder
{
    /**
     * @var array|string[][]
     */
    protected array $roles = [
        [
            'name' => 'Root',
            'guard_name' => 'admin',
        ],
        [
            'name' => 'Administrator',
            'guard_name' => 'admin',
        ],
        [
            'name' => 'Guest',
            'guard_name' => 'admin',
        ],
    ];

    /**
     * @param RoleModelContract $roleModel
     * @param PermissionModelContract $permissionModel
     * @return void
     */
    public function run(RoleModelContract $roleModel, PermissionModelContract $permissionModel)
    {
        foreach ($this->roles as $data) {
            $role = $roleModel->where($data)->first();

            if (!($role instanceof RoleModelContract && $role->exists)) {
                $role = $roleModel->create($data);
            }

            if ($role->name == 'Administrator' && $role->guard_name == 'admin') {
                $role->syncPermissions($permissionModel->whereGuardName('admin')->get());
            }
        }
    }
}
