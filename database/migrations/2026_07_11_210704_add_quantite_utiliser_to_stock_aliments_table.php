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
        Schema::table('stock_aliments', function (Blueprint $table) {
            $table->decimal('quantite_utiliser', 10, 2)->default(0)->after('quantite_fabriquer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_aliments', function (Blueprint $table) {
            $table->dropColumn('quantite_utiliser');
        });
    }
};
