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
        Schema::table('lot_matiere_premiere', function (Blueprint $table) {
            // Supprimer les clés étrangères d'abord
            $table->dropForeign(['lot_id']);
            $table->dropForeign(['matiere_premiere_id']);
        });

        Schema::table('lot_matiere_premiere', function (Blueprint $table) {
            // Supprimer la clé primaire composite
            $table->dropPrimary(['lot_id', 'matiere_premiere_id']);
            
            // Ajouter une colonne id auto-incrémentée comme nouvelle clé primaire
            $table->id()->first();
            
            // Ajouter une colonne magasin_id
            $table->foreignId('magasin_id')->nullable()->after('matiere_premiere_id');
            
            // Recréer les clés étrangères
            $table->foreign('lot_id')->references('id')->on('lots')->onDelete('cascade');
            $table->foreign('matiere_premiere_id')->references('id')->on('matiere_premieres')->onDelete('cascade');
            $table->foreign('magasin_id')->references('id')->on('magasins')->onDelete('cascade');
            
            // Recréer les index pour les performances
            $table->index(['lot_id', 'matiere_premiere_id']);
            $table->index(['lot_id', 'magasin_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lot_matiere_premiere', function (Blueprint $table) {
            // Supprimer les clés étrangères et index
            $table->dropForeign(['magasin_id']);
            $table->dropForeign(['lot_id']);
            $table->dropForeign(['matiere_premiere_id']);
            $table->dropIndex(['lot_id', 'magasin_id']);
            $table->dropIndex(['lot_id', 'matiere_premiere_id']);
            
            // Supprimer la colonne magasin_id et id
            $table->dropColumn('magasin_id');
            $table->dropColumn('id');
            
            // Recréer la clé primaire composite
            $table->primary(['lot_id', 'matiere_premiere_id']);
            
            // Recréer les clés étrangères
            $table->foreign('lot_id')->references('id')->on('lots')->onDelete('cascade');
            $table->foreign('matiere_premiere_id')->references('id')->on('matiere_premieres')->onDelete('cascade');
        });
    }
};
