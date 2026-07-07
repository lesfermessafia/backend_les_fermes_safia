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
        Schema::create('mouvement_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magasin_id')->constrained('magasins')->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained('matiere_premieres')->onDelete('cascade');
            $table->foreignId('lot_id')->constrained('lots')->onDelete('cascade');
            $table->enum('type', ['sortie', 'entree']);
            $table->decimal('quantite', 10, 2);
            $table->date('date_mouvement');
            $table->foreignId('gerant_id')->constrained('users')->onDelete('cascade');
            $table->text('observation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvement_stocks');
    }
};
