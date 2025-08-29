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
        //
        Schema::create('personalisation_cards', function (Blueprint $table) {
    $table->id(); // clé primaire
    $table->string('pays');
    $table->string('devise');
    $table->string('numero');
    $table->string('couleur_principale');
    $table->unsignedBigInteger('idCompagnie');
    $table->foreign('idCompagnie')
          ->references('id')
          ->on('compagnies')
          ->onDelete('CASCADE');
    $table->timestamps();
});

}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
