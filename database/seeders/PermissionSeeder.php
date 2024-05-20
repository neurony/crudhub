<?php

namespace Database\Seeders\Crudhub;

use Illuminate\Database\Seeder;
use Spatie\Permission\Contracts\Permission as PermissionModelContract;

class PermissionSeeder extends Seeder
{
    /**
     * @var array|string[][]
     */
    protected array $permissions = [
        // users
        [
            'name' => 'users-list',
            'guard_name' => 'admin'
        ],
        [
            'name' => 'users-add',
            'guard_name' => 'admin'
        ],
        [
            'name' => 'users-edit',
            'guard_name' => 'admin'
        ],
        [
            'name' => 'users-delete',
            'guard_name' => 'admin'
        ],
        [
            'name' => 'users-impersonate',
            'guard_name' => 'admin'
        ],

        // admins
        [
            'name' => 'admins-list',
            'guard_name' => 'admin'
        ],
        [
            'name' => 'admins-add',
            'guard_name' => 'admin'
        ],
        [
            'name' => 'admins-edit',
            'guard_name' => 'admin'
        ],
        [
            'name' => 'admins-delete',
            'guard_name' => 'admin'
        ],

        // permissions
        [
            'name' => 'permissions-list',
            'guard_name' => 'admin'
        ],
        [
            'name' => 'permissions-edit',
            'guard_name' => 'admin'
        ],
    ];

    /**
     * @param PermissionModelContract $permissionModel
     * @return void
     */
    public function run(PermissionModelContract $permissionModel)
    {
        foreach ($this->permissions as $data) {
            $permission = $permissionModel->where($data)->first();

            if (!($permission instanceof PermissionModelContract && $permission->exists)) {
                $permissionModel->create($data);
            }
        }
    }
}
