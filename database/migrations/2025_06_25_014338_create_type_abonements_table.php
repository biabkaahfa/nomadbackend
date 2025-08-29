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
            $table->string('nom')->unique();
            $table->float('taux');
            $table->float('prix'); // Correction ici
            $table->integer('maxTicket');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('type_abonements');
    }
};
