<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Admin::create(
            [
                'name' => 'super admin',
                'email' => 'super_admin@admin.com',
                'username' => 'super_admin',
                'password' => bcrypt(123456),
                'user_type' => 'admin',
                'role_name' => 'super_admin',
            ],
        );

        $this->call([
            AgencySeeder::class,
            PermissionSeeder::class,
            PermissionTypeSeeder::class,
        ]);
    }
}
