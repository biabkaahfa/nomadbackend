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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->float('montant');
             $table->dateTime('datePaiement');
            $table->enum('moyenPaiement',['OM', 'MOOV', 'CARTE', 'ESPECE', 'GRATUIT']);
            $table->enum('statut',['EN_ATTENTE', 'SUCCES', 'ECHEC', 'MANUEL_VALIDE']);
            $table->enum('typeSource',['MOBILE', 'GUICHET']);
            $table->unsignedBigInteger('idUtilisateur');
             $table->foreign('idUtilisateur')->references('id')->on('users')->onDelete('CASCADE');
            $table->string('referenceTransaction')->nullable();
           
            $table->timestamps();
        });
    }
    // 'montant',
    //     'datePaiement',
    //     'moyenPaiement',
    //     'statut',
    //     'typeSource',
    //    'idUtilisateur',
    //    'referenceTransaction'

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
