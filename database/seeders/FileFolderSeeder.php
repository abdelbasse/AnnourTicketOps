<?php

namespace Database\Seeders;

use App\Models\FileFolder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FileFolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create the root folder
        FileFolder::create([
            'userId' => 1, // Set this to an existing user ID
            'name' => 'Root',
            'isFile' => false, // This is a folder
            'path' => null,    // No path
            'extension' => null, // No extension
            'parentId' => null // No parent
        ]);
    }
}
