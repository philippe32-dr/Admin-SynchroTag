<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puces', function (Blueprint $table) {
            $table->id();
            $table->string('cle_unique')->unique();
            $table->float('latitude')->default(0);
            $table->float('longitude')->default(0);
            $table->string('status')->default('Libre');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puces');
    }
};
