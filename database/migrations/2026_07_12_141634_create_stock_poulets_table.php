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
        Schema::create('stock_poulets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ferme_id')->nullable()->constrained('fermes')->onDelete('set null');
            $table->foreignId('poulet_id')->nullable()->constrained('poulets')->onDelete('set null');
            $table->integer('quantite')->default(0);
            $table->date('date_entree')->nullable();
            $table->date('date_sortie')->nullable();
            $table->enum('statut', ['en_stock', 'vendu', 'mort', 'en_production'])->default('en_stock');
            $table->decimal('poids_moyen', 8, 2)->nullable();
            $table->integer('age_jours')->nullable();
            $table->string('code_stock')->unique();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_poulets');
    }
};
