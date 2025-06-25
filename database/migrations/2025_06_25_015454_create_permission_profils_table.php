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
        Schema::create('permission_profils', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('idPermission');
            $table->foreign('idPermission')->references('id')->on('permissions')->onDelete('CASCADE');
             $table->unsignedBigInteger('idProfil');
            $table->foreign('idProfil')->references('id')->on('profils')->onDelete('CASCADE');
            $table->timestamps();
        });
        // 'idPermission',
        // 'idProfil'
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_profils');
    }
};
