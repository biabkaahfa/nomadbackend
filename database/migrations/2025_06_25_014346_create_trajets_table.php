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
        Schema::create('trajets', function (Blueprint $table) {
            $table->id();
            $table->string('pointDepart');
             $table->string('pointArrive');
             $table->float('prix');
             $table->enum('status',['actif', 'inactif']);
             $table->unsignedBigInteger('idCompagnie');
              $table->float('distance');
              $table->foreign('idCompagnie')->references('id')->on('compagnies')->onDelete('CASCADE');

        //     $'pointDepart',
        // 'pointArrive',
        // "prix",
        // "status",
        // "idCompagnie"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trajets');
    }
};
