<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::truncate();
        Permission::insert([
            [
                'permission_type_id' => '1',
                'name' => 'client read',
                'slug' => 'client-read',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '1',
                'name' => 'Client write',
                'slug' => 'client-write',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '1',
                'name' => 'Client update',
                'slug' => 'client-update',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '1',
                'name' => 'Client delete',
                'slug' => 'client-delete',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '2',
                'name' => 'Lead management read',
                'slug' => 'lead-management-read',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '2',
                'name' => 'Lead management write',
                'slug' => 'lead-management-write',
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'permission_type_id' => '2',
                'name' => 'Lead management update',
                'slug' => 'lead-management-update',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '2',
                'name' => 'Lead management delete',
                'slug' => 'lead-management-delete',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '3',
                'name' => 'File manager read',
                'slug' => 'file-manager-read',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '3',
                'name' => 'File manager write',
                'slug' => 'file-manager-write',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '3',
                'name' => 'File manager update',
                'slug' => 'file-manager-update',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '3',
                'name' => 'File manager delete',
                'slug' => 'file-manager-delete',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '4',
                'name' => 'Lead frequency read',
                'slug' => 'lead-frequency-read',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '4',
                'name' => 'Lead frequency write',
                'slug' => 'lead-frequency-write',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '4',
                'name' => 'Lead frequency update',
                'slug' => 'lead-frequency-update',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '4',
                'name' => 'Lead frequency delete',
                'slug' => 'lead-frequency-delete',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '5',
                'name' => 'Task Management read',
                'slug' => 'task-management-read',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '5',
                'name' => 'Task Management write',
                'slug' => 'task-management-write',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '5',
                'name' => 'Task Management update',
                'slug' => 'task-management-update',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '5',
                'name' => 'Task Management delete',
                'slug' => 'task-management-delete',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '6',
                'name' => 'Calendar management read',
                'slug' => 'calendar-management-read',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '6',
                'name' => 'Calendar management write',
                'slug' => 'calendar-management-write',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '6',
                'name' => 'Calendar management update',
                'slug' => 'calendar-management-update',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '6',
                'name' => 'Calendar management delete',
                'slug' => 'calendar-management-delete',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '7',
                'name' => 'User management read',
                'slug' => 'user-management-read',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '7',
                'name' => 'User management write',
                'slug' => 'user-management-write',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '7',
                'name' => 'User management update',
                'slug' => 'user-management-update',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '7',
                'name' => 'User management delete',
                'slug' => 'user-management-delete',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '8',
                'name' => 'Send notification read',
                'slug' => 'send-notification-read',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '8',
                'name' => 'Send notification write',
                'slug' => 'send-notification-write',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '8',
                'name' => 'Send notification update',
                'slug' => 'send-notification-update',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '8',
                'name' => 'Send notification delete',
                'slug' => 'send-notification-delete',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '9',
                'name' => 'Agencies read',
                'slug' => 'agencies-read',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '9',
                'name' => 'Agencies write',
                'slug' => 'agencies-write',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '9',
                'name' => 'Agencies update',
                'slug' => 'agencies-update',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '9',
                'name' => 'Agencies delete',
                'slug' => 'agencies-delete',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '10',
                'name' => 'Criteria read',
                'slug' => 'criteria-read',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '10',
                'name' => 'Criteria write',
                'slug' => 'criteria-write',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '10',
                'name' => 'Criteria update',
                'slug' => 'criteria-update',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '10',
                'name' => 'Criteria delete',
                'slug' => 'criteria-delete',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '11',
                'name' => 'Settings read',
                'slug' => 'settings-read',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '11',
                'name' => 'Settings write',
                'slug' => 'settings-write',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '11',
                'name' => 'Settings update',
                'slug' => 'settings-update',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'permission_type_id' => '11',
                'name' => 'Settings delete',
                'slug' => 'settings-delete',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
