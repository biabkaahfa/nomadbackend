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

            // ✅ NOUVEAUX CHAMPS POUR LES MODES D'ENVOI
            $table->boolean('email_sent')->default(false);
            $table->boolean('sms_sent')->default(false);
            $table->boolean('push_sent')->default(false);

            // ✅ STATISTIQUES D'ENVOI
            $table->integer('email_count')->default(0);
            $table->integer('sms_count')->default(0);
            $table->integer('push_count')->default(0);

            $table->unsignedBigInteger('idUtilisateur')->nullable();
            $table->foreign('idUtilisateur')->references('id')->on('users')->onDelete('CASCADE');
            $table->foreignId('idVoyage')->constrained('voyages')->onDelete('cascade');
            $table->boolean('isRead')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
