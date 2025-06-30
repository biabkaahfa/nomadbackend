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
            $table->unsignedBigInteger('idUtilisateur')->nullable();
            $table->foreign('idUtilisateur')->references('id')->on('users')->onDelete('CASCADE');
            $table->string('name',)->nullable();
            $table->string('telephone',)->nullable();
            $table->string('email',)->nullable();
            $table->enum('typeAchat',['En_ligne','sur_place']);
            $table->enum('modeReception',['email','papier','application']);
            $table->unsignedBigInteger('idVoyage');
            $table->foreign('idVoyage')->references('id')->on('voyages')->onDelete('CASCADE');
            $table->unsignedBigInteger('idGarre')->nullable();
            $table->foreign('idGarre')->references('id')->on('garres')->onDelete('CASCADE');
            $table->date('dateScan')->nullable();
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
