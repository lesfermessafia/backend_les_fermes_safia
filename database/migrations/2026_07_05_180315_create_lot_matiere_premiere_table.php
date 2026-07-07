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
        Schema::create('lot_matiere_premiere', function (Blueprint $table) {
            $table->foreignId('lot_id')->constrained('lots')->onDelete('cascade');
            $table->foreignId('matiere_premiere_id')->constrained('matiere_premieres')->onDelete('cascade');
            $table->decimal('quantite', 10, 2);
            $table->primary(['lot_id', 'matiere_premiere_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lot_matiere_premiere');
    }
};
