<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE stock_poulets MODIFY statut VARCHAR(255) NOT NULL DEFAULT 'demarrage'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE stock_poulets MODIFY statut ENUM('en_stock', 'vendu', 'mort', 'en_production') NOT NULL DEFAULT 'en_stock'");
    }
};
