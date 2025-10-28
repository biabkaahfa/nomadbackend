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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idTicket');
            $table->foreign('idTicket')->references('id')->on('tickets')->onDelete('CASCADE');

            // Notes par critères (échelle 1-5 ou 1-10)
            $table->integer('securite')->nullable(); // Note pour la sécurité
            $table->integer('confort')->nullable();  // Note pour le confort
            $table->integer('ponctualite')->nullable(); // Note pour la ponctualité
            $table->integer('accueil')->nullable();  // Note pour l'accueil du personnel
            $table->integer('proprete')->nullable(); // Note pour la propreté du véhicule

            // Note globale (peut être calculée automatiquement)
            $table->integer('note_globale')->nullable();

            $table->text('commentaire')->nullable();
            $table->dateTime('dateNote');
            $table->timestamps();

            // Une seule note par ticket
            $table->unique('idTicket');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
