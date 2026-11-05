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
        Schema::table('nomina_recibos', function (Blueprint $table) {
            $table->decimal('retardo_inasistencias', 10, 2)->default(0)->after('descuento_infonavit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nomina_recibos', function (Blueprint $table) {
            $table->dropColumn('retardo_inasistencias');
        });
    }
};
