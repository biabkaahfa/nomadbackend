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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idTicket');
            $table->foreign('idTicket')->references('id')->on('tickets')->onDelete('CASCADE');
            $table->integer('note');
            $table->text('commentaire');
            $table->dateTime('dateNote');
            $table->timestamps();
        });
        //    'idTicket',
        // 'note',
        // 'commentaire',
        // 'dateNote'
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
