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
        Schema::table('arrivage_poulets', function (Blueprint $table) {
            $table->foreignId('ferme_id')->constrained('fermes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arrivage_poulets', function (Blueprint $table) {
            $table->dropForeign(['ferme_id']);
            $table->dropColumn('ferme_id');
        });
    }
};
