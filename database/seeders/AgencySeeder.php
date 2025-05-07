<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Industry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Agency::insert([
            [
                'name' => 'PropNex Realty Pte Ltd',
                'address' => '480 Lor 6 Toa Payoh, #18-01 East Wing, HDB Hub, Singapore 310480',
                'added_by_id' => 1,
            ],
            [
                'name' => 'ERA Realty Network Pte Ltd',
                'address' => 'ERA APAC Centre 450 Lorong 6 Toa Payoh Singapore 319394',
                'added_by_id' => 1,
            ],
            [
                'name' => 'Huttons Asia Pte Ltd',
                'address' => '3 Bishan Place #05-01. CPF Bishan Building Singapore 579838',
                'added_by_id' => 1,
            ],
            [
                'name' => 'OrangeTee & Tie Pte Ltd',
                'address' => '430 Lor 6 Toa Payoh, #01-01 OrangeTee, Building 319402',
                'added_by_id' => 1,
            ],
            [
                'name' => 'SRI Pte Ltd',
                'address' => '1 Kim Seng Promenade #17-10/12 Great World City, West Lobby, 237994',
                'added_by_id' => 1,
            ]
        ]);

        Industry::insert([
            [
                'industries' => 'Real Estate',
            ],
            [
                'industries' => 'Automotive and Transportation',
            ],
            [
                'industries' => 'Insurance and Financial Services',
            ],
            [
                'industries' => 'Technology and Software',
            ],
            [
                'industries' => 'Legal and Consulting',
            ],
            [
                'industries' => 'Retail and E-Commerce',
            ]
        ]);
    }
}
