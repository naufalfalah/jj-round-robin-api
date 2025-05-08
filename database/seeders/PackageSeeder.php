<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Package::create([
            'sub_account_id' => 1,
            'name' => 'Default Package',
            'description' => 'This is a basic package.',
            'price' => 100.00,
            'url' => 'https://example.com/basic-package',
        ]);
    }
}
