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
        Schema::create('scans', function (Blueprint $table) {
            $table->id();

            // Référence au ticket scanné
            $table->unsignedBigInteger('idTicket');

            // Référence au contrôleur qui a effectué le scan
            $table->unsignedBigInteger('idControleur');

            // Date et heure du scan
            $table->timestamp('dateScan')->useCurrent();

            // Statut de validation du scan
            $table->boolean('estValide')->default(true);

            // Notes optionnelles (erreur, incident, etc.)
            $table->text('notes')->nullable();

            // Type de scan (checkin, checkout)
            $table->enum('typeScan', ['checkin', 'checkout'])->default('checkin');

            // Données supplémentaires du ticket au moment du scan
            $table->json('snapshotTicket')->nullable();

            $table->timestamps();

            // Clés étrangères
            $table->foreign('idTicket')
                  ->references('id')
                  ->on('tickets')
                  ->onDelete('cascade');

            $table->foreign('idControleur')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Index pour optimiser les requêtes
            $table->index('dateScan');
            $table->index('estValide');
            $table->index('typeScan');
            $table->index(['idControleur', 'dateScan']);
            $table->index(['idTicket', 'dateScan']);

            // Index composite pour les performances
            $table->index(['idControleur', 'estValide', 'dateScan']);
        });
    }


    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scans');
    }
};
