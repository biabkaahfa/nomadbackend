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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
           
            $table->dateTime('dateReservation');
           $table->enum('statut', ['CONFIRME', 'ANNULE', 'REPORTE', 'UTILISE']);
            $table->unsignedBigInteger('idUtilisateur');
            $table->foreign('idUtilisateur')->references('id')->on('users')->onDelete('CASCADE');
            $table->unsignedBigInteger('idVoyage');
             $table->foreign('idVoyage')->references('id')->on('voyages')->onDelete('CASCADE');
            $table->unsignedBigInteger('idGarre')->nullable();
             $table->foreign('idGarre')->references('id')->on('garres')->onDelete('CASCADE');
            $table->date('dateScan');
            $table->unsignedBigInteger('idPaiement');
            $table->foreign('idPaiement')->references('id')->on('paiements')->onDelete('CASCADE');


            $table->timestamps();
        });
        //  'dateReservation',
        // 'statut',
        // 'idUtilisateur',
        // 'idVoyage',
        // 'idGarre',
        // 'dateScan',
        // 'idPaiement'
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
