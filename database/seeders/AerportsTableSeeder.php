<?php
// database/seeders/AerportsTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AerportsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('aerports')->insert([
            ['code' => 'CAS', 'location' => 'CASABLANCA-MEDV', 'address' => '001'],
            ['code' => 'MAR', 'location' => 'MARRAKECH', 'address' => '039'],
            ['code' => 'AGA', 'location' => 'AGADIR', 'address' => '049'],
            ['code' => 'RAB', 'location' => 'RABAT', 'address' => '059'],
            ['code' => 'TNG', 'location' => 'TANGER', 'address' => '069'],
            ['code' => 'FES', 'location' => 'FES', 'address' => '079'],
            ['code' => 'OJD', 'location' => 'OUJDA', 'address' => '089'],
            ['code' => 'NDR', 'location' => 'NADOR', 'address' => '099'],
            ['code' => 'HCM', 'location' => 'ELHOCEIMA', 'address' => '109'],
            ['code' => 'DKH', 'location' => 'DAKHLA', 'address' => '119'],
            ['code' => 'ORZ', 'location' => 'OUARZAZATE', 'address' => '129'],
            ['code' => 'LYN', 'location' => 'LAAYOUNE', 'address' => '139'],
            ['code' => 'ESR', 'location' => 'ESSAOUIRA', 'address' => '149'],
            ['code' => 'BNS', 'location' => 'BENSLIMANE', 'address' => '159'],
            ['code' => 'BML', 'location' => 'BENIMELLAL', 'address' => '169'],
            ['code' => 'TTN', 'location' => 'TETOUAN', 'address' => '179'],
            ['code' => 'ERC', 'location' => 'ERRACHIDIA', 'address' => '189'],
            ['code' => 'ZGR', 'location' => 'ZAGORA', 'address' => '199'],
            ['code' => 'BRF', 'location' => 'BOUAARFA', 'address' => '218'],
            ['code' => 'TTM', 'location' => 'TITMELLIL', 'address' => '219'],
            ['code' => 'GLM', 'location' => 'GUELMIM', 'address' => '229'],
            ['code' => 'IFR', 'location' => 'IFRANE', 'address' => '239'],
            ['code' => 'TAN', 'location' => 'TANTAN', 'address' => '249'],
        ]);
    }
}
