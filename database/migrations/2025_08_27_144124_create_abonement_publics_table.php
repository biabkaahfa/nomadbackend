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
            Schema::create('abonement_publics', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('idUser')->nullable();
                $table->foreign('idUser')
                      ->references('id')
                      ->on('users')
                      ->onDelete('CASCADE');
                $table->unsignedBigInteger('idCompagnie');
                $table->foreign('idCompagnie')
                      ->references('id')
                      ->on('compagnies')
                      ->onDelete('CASCADE');
                $table->string('nom');
                $table->string('prenom');
                $table->string('profession');
                 $table->unsignedBigInteger('idPaiement');
            $table->foreign('idPaiement')->references('id')->on('paiements')->onDelete('CASCADE');
                $table->string('etablissement')->nullable();
                $table->string('photo')->nullable();
                $table->enum('statut',['actif','inactif']);
                $table->integer('duree'); // <-- Correction ici
                $table->date('dateNaiss');
                $table->dateTime('dateDebut'); // <-- Il vaut mieux utiliser DATETIME
                $table->dateTime('dateFin'); // <-- Il vaut mieux utiliser DATETIME

                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abonement_publics');
    }
};
