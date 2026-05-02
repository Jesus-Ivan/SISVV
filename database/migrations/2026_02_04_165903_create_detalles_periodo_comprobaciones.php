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
        Schema::create('detalles_periodo_comprobaciones', function (Blueprint $table) {
            $table->id();
            $table->integer('folio_periodo');
            $table->date('fecha_nota');
            $table->string('tipo_documento', 20);
            $table->string('proveedor', 100);
            $table->string('area', 50);
            $table->string('concepto', 255);
            $table->decimal('importe', 10, 2);
            $table->string('forma_pago', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_periodo_comprobaciones');
    }
};
