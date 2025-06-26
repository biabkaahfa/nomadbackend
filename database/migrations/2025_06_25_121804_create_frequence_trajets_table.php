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
        Schema::create('frequence_trajets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idTrajet');
             $table->foreign('idTrajet')->references('id')->on('trajets')->onDelete('CASCADE');
             $table->integer('nombrePlaceMinimum')->default(25);
            $table->Time('heureDepart');
            $table->enum('jourSemaine',['LUNDI', 'MARDI', 'MERCREDI', 'JEUDI', 'VENDREDI', 'SAMEDI', 'DIMANCHE', 'TOUS_LES_JOURS']);
            $table->timestamps();
        });
        // 'idTrajet',
        // 'nombrePlaceMinimum',
        // 'heureDepart',
        // 'jourSemaine'
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frequence_trajets');
    }
};
