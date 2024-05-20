<?php

namespace Database\Seeders\Crudhub;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Zbiller\Crudhub\Contracts\AdminModelContract;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param AdminModelContract $adminModel
     * @return void
     */
    public function run(AdminModelContract $adminModel)
    {
        $admin = $adminModel->whereEmail('admin@mail.com')->first();

        if (!($admin instanceof AdminModelContract && $admin->exists)) {
            $admin = $adminModel->create([
                'name' => 'Admin User',
                'email' => 'admin@mail.com',
                'email_verified_at' => Carbon::now(),
                'password' => bcrypt('admin'),
                'active' => true,
            ]);
        }

        if (!$admin->hasRole('Root', 'admin')) {
            $admin->assignRole('Root');
        }
    }
}
