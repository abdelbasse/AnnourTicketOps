<?php
// database/seeders/NatureIncidentsTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NatureIncidentsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('nature_incidents')->insert([
            ['id' => 11, 'val' => 'surcharge sur liaison IAM', 'desc' => '.'],
            ['id' => 12, 'val' => 'surcharge sur liaison orange', 'desc' => '.'],
            ['id' => 13, 'val' => 'maintenance préventif', 'desc' => '.'],
            ['id' => 14, 'val' => 'Coupure de liaison Orange', 'desc' => '.'],
            ['id' => 15, 'val' => 'Coupure de liaison IAM', 'desc' => '.'],
            ['id' => 16, 'val' => 'coupure électrique au niveau d\'armoire M1', 'desc' => '.'],
            ['id' => 17, 'val' => 'coupure électrique au niveau d\'armoire M2', 'desc' => '.'],
            ['id' => 18, 'val' => 'coupure électrique au niveau Routeur IAM', 'desc' => '.'],
            ['id' => 19, 'val' => 'coupure électrique au niveau Routeur Orange', 'desc' => '.'],
            ['id' => 20, 'val' => 'coupure d\'électricité', 'desc' => '.'],
            ['id' => 21, 'val' => 'Perturbation liaison Orange', 'desc' => '.'],
            ['id' => 22, 'val' => 'Perturbation liaison IAM', 'desc' => '.'],
            ['id' => 23, 'val' => 'Lenteur de liaison Orange', 'desc' => '.'],
            ['id' => 24, 'val' => 'Lenteur de liaison IAM', 'desc' => '.'],
            ['id' => 25, 'val' => 'Problème Watchguard (down)', 'desc' => '.'],
            ['id' => 26, 'val' => 'Problème VPN', 'desc' => '.'],
            ['id' => 27, 'val' => 'Coupure liaison IAM (tout les site)', 'desc' => '.'],
            ['id' => 28, 'val' => 'Coupure liaison Orange (tout les site)', 'desc' => '.'],
            ['id' => 29, 'val' => 'Problème Câble', 'desc' => '.'],
            ['id' => 30, 'val' => 'Migration du Watchguard', 'desc' => '.'],
            ['id' => 31, 'val' => 'Switch Figée', 'desc' => '.'],
        ]);
    }
}
