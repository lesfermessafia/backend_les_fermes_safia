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
        Schema::create('historique_aliments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_aliment_id')->constrained('stock_aliments')->onDelete('cascade');
            $table->foreignId('gerant_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['entree', 'sortie']);
            $table->decimal('quantite', 10, 2);
            $table->date('date_mouvement');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_aliments');
    }
};
