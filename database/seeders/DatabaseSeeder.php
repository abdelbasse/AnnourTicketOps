<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // Call all the seeders you want to run
        $this->call([
            UsersTableSeeder::class,
            AerportsTableSeeder::class,
            NatureIncidentsTableSeeder::class,
            NatureSolutionsTableSeeder::class,
            FileFolderSeeder::class,
        ]);
    }
}
