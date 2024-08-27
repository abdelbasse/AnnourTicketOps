<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Set the starting point of the auto-incrementing ID to 2200000
        DB::statement('ALTER TABLE tickets AUTO_INCREMENT = 2200000;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Optional: You can reset the auto-increment value to a specific point if needed
        DB::statement('ALTER TABLE tickets AUTO_INCREMENT = 1;');
    }
};
