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
        Schema::create('mouvement_poulets', function (Blueprint $table) {
            $table->id();
            $table->string('code_arrivage_poulet', 10);
            $table->enum('type', ['decedee', 'malade', 'vente', 'aprovisionnement']);
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
        Schema::dropIfExists('mouvement_poulets');
    }
};
