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
        Schema::create('garres', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('localisation')->nullable()->unique();
            $table->string('ville');
            $table->unsignedBigInteger('idCompagnie');
            $table->foreign('idCompagnie')->references('id')->on('compagnies')->onDelete('CASCADE');
            $table->timestamps();
        });
        //  'name',
        // 'localisation',
        // 'ville',
        // 'idCompagnie'
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garres');
    }
};
