<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('type_abonements', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // freemium, standard, premium, public
            $table->string('libelle');
            $table->decimal('prix_mensuel', 10, 2)->default(0);
            $table->integer('limite_notifications')->nullable(); // null = illimité
            $table->boolean('acces_notes')->default(false);

            // Commission pour tickets sur place
            $table->decimal('commission_sur_place', 5, 2)->default(0);

            // Commission pour tickets en ligne
            $table->decimal('commission_en_ligne', 5, 2)->default(0);

            // Type de compagnie (privee, publique)
            $table->enum('type_compagnie', ['privee', 'publique'])->default('privee');

            $table->text('description')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('type_abonements');
    }
};
