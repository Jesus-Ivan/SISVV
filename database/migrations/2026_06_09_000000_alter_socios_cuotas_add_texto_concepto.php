<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('socios_cuotas', function (Blueprint $table) {
            $table->string('texto_concepto', 100)->nullable()->after('monto_personalizado');
            $table->enum('posicion_texto', ['izquierda', 'derecha'])->nullable()->after('texto_concepto');
        });
    }

    public function down(): void
    {
        Schema::table('socios_cuotas', function (Blueprint $table) {
            $table->dropColumn(['texto_concepto', 'posicion_texto']);
        });
    }
};
