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
        Schema::create('stock_aliments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aliment_id')->constrained('aliments')->onDelete('cascade');
            $table->string('code_stock', 10)->unique();
            $table->foreignId('formule_id')->constrained('formules')->onDelete('cascade');
            $table->decimal('quantite_fabriquer', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_aliments');
    }
};
