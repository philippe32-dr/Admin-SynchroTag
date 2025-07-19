<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kycs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('nom');
            $table->string('prenom');
            $table->string('nationalite');
            $table->string('telephone');
            $table->string('adresse_postale');
            $table->string('numero_npi', 10);
            $table->enum('status', ['EnCours', 'Valide', 'Rejete'])->default('EnCours');
            $table->string('photo_recto')->nullable();
            $table->string('photo_verso')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('raison_rejet')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            $table->unique('numero_npi');
        });

        // Conversion des données existantes si nécessaire
        DB::statement("UPDATE kycs SET status = 'EnCours' WHERE status IS NULL OR status = ''");
    }

    public function down(): void
    {
        Schema::dropIfExists('kycs');
    }
};
