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
       Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('idUtilisateur')->nullable(); // acheteur
            $table->foreign('idUtilisateur')->references('id')->on('users')->onDelete('set null');

            $table->unsignedBigInteger('idVoyage');
            $table->foreign('idVoyage')->references('id')->on('voyages')->onDelete('cascade');

            $table->integer('nombrePlaces');
            $table->decimal('montantTotal', 10, 2);

            $table->json('passagers'); // tous les passagers (ou tu peux normaliser en table)

            $table->enum('statut', ['en_attente_paiement', 'payée', 'expirée', 'annulée'])->default('en_attente_paiement');

            $table->unsignedBigInteger('idPaiement')->nullable();
            $table->foreign('idPaiement')->references('id')->on('paiements')->onDelete('set null');

            $table->timestamps();
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
