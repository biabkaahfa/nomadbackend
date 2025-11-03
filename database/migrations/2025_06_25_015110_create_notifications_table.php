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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('contenu');
            $table->date('DateEnvoie');
            $table->string('type');
            $table->unsignedBigInteger('idUtilisateur')->nullable();
            $table->foreign('idUtilisateur')->references('id')->on('users')->onDelete('CASCADE');
            $table->foreignId('idVoyage')->constrained('voyages')->onDelete('cascade');
            $table->boolean('isRead')->default(false);

            $table->timestamps();
        });
        //  'titre',
        // 'contenu',
        // 'DateEnvoie',
        // 'idUtilisateur'
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
