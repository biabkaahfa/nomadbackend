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
        Schema::create('abonements', function (Blueprint $table) {
            $table->id();

            // Clé étrangère vers typeAbonement (camelCase)
            $table->unsignedBigInteger('idTypeAbonement');
            $table->foreign('idTypeAbonement')
                  ->references('id')
                  ->on('type_abonements') ;// Notez le pluriel camelCase
                //  ->onDelete('SET NULL');

            // Clé étrangère vers compagnie
            $table->unsignedBigInteger('idCompagnie');
            $table->foreign('idCompagnie')
                  ->references('id')
                  ->on('compagnies')
                  ->onDelete('CASCADE');

            // Durée en jours (integer plutôt que time)
            $table->integer('duree')->default(30);

            // Dates de début et fin
            $table->date('dateDebut');
            $table->date('dateFin');

            // Statut de l'abonnement
            $table->enum('statut', ['actif', 'inactif', 'expire'])->default('actif');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('abonements', function (Blueprint $table) {
            // Suppression des contraintes de clé étrangère
            $table->dropForeign(['idTypeAbonement']);
            $table->dropForeign(['idCompagnie']);
        });

        Schema::dropIfExists('abonements');
    }
};
