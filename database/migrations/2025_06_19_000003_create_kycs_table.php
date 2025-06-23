<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kycs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Lien direct vers users
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('nom');
            $table->string('prenom');
            $table->string('nationalite');
            $table->string('telephone');
            $table->string('adresse_postale');
            $table->string('pdf_cip');
            $table->string('status')->default('EnCours');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kycs');
    }
};
