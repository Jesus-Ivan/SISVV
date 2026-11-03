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
        Schema::create('nomina_periodos', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 50)->unique();
            $table->string('nombre', 150);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->date('fecha_pago')->nullable();
            $table->enum('tipo_periodo', ['DIARIO', 'SEMANAL', 'QUINCENAL', 'MENSUAL', 'AGUINALDO', 'FINIQUITO', 'EXTRAORDINARIO'])->default('QUINCENAL');
            $table->enum('estado', ['BORRADOR', 'APROBADO', 'PAGADO', 'CERRADO'])->default('BORRADOR');
            $table->decimal('total_fiscal', 12, 2)->default(0);
            $table->decimal('total_efectivo', 12, 2)->default(0);
            $table->decimal('total_percepciones', 12, 2)->default(0);
            $table->decimal('total_deducciones', 12, 2)->default(0);
            $table->decimal('total_neto', 12, 2)->default(0);
            $table->foreignId('id_usuario')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nomina_periodos');
    }
};

