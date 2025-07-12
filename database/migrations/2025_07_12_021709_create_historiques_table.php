<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('historiques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->decimal('longitude_moi', 10, 7);
            $table->decimal('latitude_moi', 10, 7);
            $table->decimal('longitude_cible', 10, 7);
            $table->decimal('latitude_cible', 10, 7);
            $table->decimal('distance', 10, 2);
            $table->date('date');
            $table->time('heure');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('historiques');
    }
};
