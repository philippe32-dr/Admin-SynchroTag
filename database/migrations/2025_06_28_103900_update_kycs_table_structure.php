<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Exécute les migrations.
     */
    public function up(): void
    {
        // Supprimer d'abord les contraintes de clé étrangère existantes
        Schema::table('kycs', function (Blueprint $table) {
            if (Schema::hasColumn('kycs', 'client_id')) {
                $table->dropForeign(['client_id']);
            }
            if (Schema::hasColumn('kycs', 'user_id')) {
                $table->dropForeign(['user_id']);
            }
        });

        // Modifier la structure de la table
        Schema::table('kycs', function (Blueprint $table) {
            // Remplacer pdf_cip par numero_npi
            if (Schema::hasColumn('kycs', 'pdf_cip')) {
                $table->renameColumn('pdf_cip', 'numero_npi');
            } elseif (!Schema::hasColumn('kycs', 'numero_npi')) {
                $table->string('numero_npi', 10)->after('adresse_postale');
            }
            
            // Modifier le type de status en enum
            if (Schema::hasColumn('kycs', 'status')) {
                // Convertir les données existantes si nécessaire
                DB::statement("UPDATE kycs SET status = 'EnCours' WHERE status IS NULL OR status = ''");
                
                // Modifier la colonne en enum
                $table->enum('status', ['EnCours', 'Valide', 'Rejete'])
                    ->default('EnCours')
                    ->change();
            } else {
                $table->enum('status', ['EnCours', 'Valide', 'Rejete'])
                    ->default('EnCours');
            }
            
            // Ajouter l'index sur numero_npi pour les performances de recherche
            $table->string('numero_npi', 10)->change();
            $table->unique('numero_npi');
            
            // Recréer les clés étrangères avec les bonnes options
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
                
            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->onDelete('set null');
        });
    }

    /**
     * Annule les migrations.
     */
    public function down(): void
    {
        // Ne pas annuler les modifications de structure pour éviter la perte de données
        // Une nouvelle migration serait nécessaire pour revenir en arrière
    }
};
