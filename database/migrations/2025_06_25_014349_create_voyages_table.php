<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('voyages', function (Blueprint $table) {
            $table->id();
            $table->time('heuresDepart');
            $table->unsignedBigInteger('idTrajet');
             $table->foreign('idTrajet')->references('id')->on('trajets')->onDelete('CASCADE');
            $table->unsignedBigInteger('idBus')->nullable();
             $table->foreign('idBus')->references('id')->on('buses')->onDelete('CASCADE');
            $table->Date('dateDepart');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voyages');
    }
};
