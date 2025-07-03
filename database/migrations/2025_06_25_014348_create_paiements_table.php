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
    $table->dateTime('datePaiement')->default(DB::raw('CURRENT_TIMESTAMP')); // date automatique
    $table->enum('moyenPaiement', ['OM', 'MOOV', 'CARTE', 'ESPECE']);
    $table->enum('statut', ['EN_ATTENTE', 'SUCCES', 'ECHEC', 'MANUEL_VALIDE']);
    $table->enum('typeSource', ['MOBILE', 'GUICHET'])->default('GUICHET'); // valeur par défaut
    $table->string('telephone')->nullable(); // contact guichet
   


            $table->unsignedBigInteger('idUtilisateur')->nullable();
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
