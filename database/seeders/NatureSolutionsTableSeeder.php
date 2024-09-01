<?php
// database/seeders/NatureSolutionsTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NatureSolutionsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('nature_solutions')->insert([
            ['id' => 4, 'val' => 'Rétablissement par site local', 'desc' => '.'],
            ['id' => 5, 'val' => 'Rétablissement par l\'opérateur', 'desc' => '.'],
            ['id' => 6, 'val' => 'Rétablissement par l\'équipe de sécurité', 'desc' => '.'],
            ['id' => 7, 'val' => 'Rétablissement par l\'équipe de maintenance', 'desc' => '.'],
        ]);
    }
}
