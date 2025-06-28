<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Ajouter profile_photo à la table users
        if (!Schema::hasColumn('users', 'profile_photo')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('profile_photo')->nullable()->after('statut_kyc');
            });
        }

        // Mettre à jour la table kycs
        if (Schema::hasColumn('kycs', 'pdf_cip')) {
            Schema::table('kycs', function (Blueprint $table) {
                $table->dropColumn('pdf_cip');
            });
        }
        
        if (!Schema::hasColumn('kycs', 'numero_npi')) {
            Schema::table('kycs', function (Blueprint $table) {
                $table->string('numero_npi', 10)->after('id');
            });
        }

        // Mettre à jour la table puces
        if (!Schema::hasColumn('puces', 'object_name')) {
            Schema::table('puces', function (Blueprint $table) {
                $table->string('object_name')->nullable()->after('status');
                $table->string('object_photo')->nullable()->after('object_name');
                $table->integer('object_range')->nullable()->after('object_photo');
            });
        }
    }

    public function down()
    {
        // Ne rien faire en cas de rollback pour éviter la perte de données
    }
};
