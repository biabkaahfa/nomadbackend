<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('idAbonement');
    $table->enum('type', ['rappelTicket', 'rappelFin', 'notification']);
    $table->text('contenu');
    $table->timestamp('dateEnvoi')->useCurrent();
    $table->boolean('estVu')->default(false);
    $table->timestamp('dateLecture')->nullable(); // Cette colonne doit exister
    $table->timestamps();

    $table->foreign('idAbonement')
          ->references('id')
          ->on('abonements')
          ->onDelete('cascade');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
