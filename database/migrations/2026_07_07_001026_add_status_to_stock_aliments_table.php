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
            $table->enum('status', ['en attente', 'en production', 'production terminer', 'annule', 'consommer'])->default('en attente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_aliments', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
