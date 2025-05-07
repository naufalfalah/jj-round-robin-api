<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PermissionType;

class PermissionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PermissionType::truncate();
        PermissionType::insert([
            [
                'permission_type' => 'Clients',
                'description' => 'Permission Type For Clients View',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type' => 'Lead Management',
                'description' => 'Permission Type For Client Lead Management',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type' => 'File manager',
                'description' => 'Permission Type For Client File manager',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type' => 'Lead Frequency',
                'description' => 'Permission Type For Client Lead Frequency',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type' => 'Task Management',
                'description' => 'Permission Type For Client Task Management',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type' => 'Calendar Management',
                'description' => 'Permission Type For Client Calendar Management',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type' => 'User Management',
                'description' => 'Permission Type For User Management',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type' => 'Send Notifications',
                'description' => 'Permission Type For Send Notifications TO Client',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type' => 'Agencies  ',
                'description' => 'Permission Type For Client Agencies',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type' => 'Criteria',
                'description' => 'Permission Type For Set Client Criteria',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type' => 'Settings',
                'description' => 'Permission Type For Settings App',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
