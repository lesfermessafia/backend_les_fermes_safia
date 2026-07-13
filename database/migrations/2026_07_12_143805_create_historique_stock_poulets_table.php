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
        Schema::create('historique_stock_poulets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_poulet_id')->constrained('stock_poulets')->onDelete('cascade');
            $table->enum('type_mouvement', ['entree', 'sortie']);
            $table->integer('quantite');
            $table->string('motif')->nullable();
            $table->date('date_mouvement');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_stock_poulets');
    }
};
