<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_folders', function (Blueprint $table) {
            $table->id();
            $table->integer('userId');
            $table->boolean('isFile'); // true for files, false for folders
            $table->string('name');
            $table->string('path')->nullable();
            $table->string('extension')->nullable();
            $table->integer('parentId')->nullable()->onDelete('cascade'); // Self-referential
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_folders');
    }
};
