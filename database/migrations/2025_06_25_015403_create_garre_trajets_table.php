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
        Schema::create('garre_trajets', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('idGarre');
            $table->foreign('idGarre')->references('id')->on('garres')->onDelete('CASCADE');
             $table->unsignedBigInteger('idTrajet');
            $table->foreign('idTrajet')->references('id')->on('trajets')->onDelete('CASCADE');
            $table->timestamps();
        });
        // 'idGarre',
        // 'idTrajet',
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garre_trajets');
    }
};
