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
        Schema::create('nomina_periodo_empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_periodo')->constrained('nomina_periodos')->cascadeOnDelete();
            $table->foreignId('id_empleado')->constrained('nomina_empleados')->cascadeOnDelete();
            $table->decimal('monto_acordado', 10, 2)->default(0);
            $table->string('notas', 255)->nullable();
            $table->timestamps();

            $table->unique(['id_periodo', 'id_empleado'], 'uniq_periodo_empleado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nomina_periodo_empleados');
    }
};
