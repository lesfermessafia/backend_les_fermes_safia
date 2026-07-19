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
        Schema::table('stock_poulets', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_poulets', 'fournisseur')) {
                $table->string('fournisseur')->nullable()->after('code_stock');
            }
        });

        Schema::dropIfExists('mouvement_poulets');
        Schema::dropIfExists('arrivage_poulets');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_poulets', function (Blueprint $table) {
            if (Schema::hasColumn('stock_poulets', 'fournisseur')) {
                $table->dropColumn('fournisseur');
            }
        });

        if (!Schema::hasTable('arrivage_poulets')) {
            Schema::create('arrivage_poulets', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->foreignId('poulet_id')->constrained('poulets')->onDelete('cascade');
                $table->integer('quantite');
                $table->string('nom_fournisseur')->nullable();
                $table->foreignId('ferme_id')->nullable()->constrained('fermes')->onDelete('set null');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('mouvement_poulets')) {
            Schema::create('mouvement_poulets', function (Blueprint $table) {
                $table->id();
                $table->string('code_arrivage_poulet');
                $table->enum('type', ['decedee', 'malade', 'vente', 'aprovisionnement']);
                $table->integer('quantite');
                $table->date('date_mouvement');
                $table->timestamps();

                $table->foreign('code_arrivage_poulet')->references('code')->on('arrivage_poulets')->onDelete('cascade');
            });
        }
    }
};
