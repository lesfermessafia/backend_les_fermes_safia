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
        Schema::create('historique_oeufs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_oeuf_id')->constrained('stock_oeufs')->onDelete('cascade');
            $table->foreignId('gerant_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['entree', 'sortie', 'casse', 'vente']);
            $table->integer('quantite');
            $table->date('date_mouvement');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_oeufs');
    }
};
