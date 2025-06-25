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
        Schema::create('compagnies', function (Blueprint $table) {
            $table->id();
            $table->string('name',255);
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->string('telephone',8)->nullable();
            $table->string('email')->unique();
           // $table->string('password');
            //$table->
            $table->timestamps();
        });
         //   'name',
        // 'logo',
        // 'description',
        // 'telephone',
        // 'email'
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compagnies');
    }
};
