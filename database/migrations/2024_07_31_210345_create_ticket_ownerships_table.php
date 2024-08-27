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
        Schema::create('ticket_ownerships', function (Blueprint $table) {
            $table->id();
            $table->integer('ticketID');
            $table->integer('ownerID');
            $table->integer('reseverID');
            $table->integer('statu')->nullable();
            $table->dateTime('respond_at')->nullable();
            $table->boolean('forced')->default(false);
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
        Schema::dropIfExists('ticket_ownerships');
    }
};
