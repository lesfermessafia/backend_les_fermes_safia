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
        Schema::table('matiere_premieres', function (Blueprint $table) {
            $table->decimal('seuil_alerte', 10, 2)->default(10)->after('unite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matiere_premieres', function (Blueprint $table) {
            $table->dropColumn('seuil_alerte');
        });
    }
};
