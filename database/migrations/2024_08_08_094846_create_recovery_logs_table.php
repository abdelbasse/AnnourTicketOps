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
        Schema::create('recovery_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('TicketID');
            $table->integer('UserID');
            $table->integer('naruteSolutionID')->nullable();
            $table->dateTime('dateRecovery')->nullable();
            $table->longText('repportBody')->nullable();
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
        Schema::dropIfExists('recovery_logs');
    }
};
