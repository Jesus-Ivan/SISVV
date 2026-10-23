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
        Schema::create('nomina_recibos', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 50)->unique();
            $table->foreignId('id_periodo')->nullable()->constrained('nomina_periodos')->nullOnDelete();
            $table->foreignId('id_empleado')->constrained('nomina_empleados')->cascadeOnDelete();
            $table->enum('tipo_recibo', ['NOMINA', 'AGUINALDO', 'FINIQUITO', 'EXTRAORDINARIO'])->default('NOMINA')->index();
            
            // Snapshots históricos
            $table->unsignedBigInteger('id_empleado_snapshot');
            $table->string('nombre_empleado_snapshot', 255);
            $table->string('area_snapshot', 100)->nullable();
            $table->string('puesto_snapshot', 100)->nullable();
            $table->string('tipo_empleado_snapshot', 50)->nullable();

            // Fechas del periodo
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->date('fecha_pago')->nullable();

            // Percepciones
            $table->decimal('nomina_fiscal', 10, 2)->default(0);
            $table->decimal('diferencia_efectivo', 10, 2)->default(0);
            $table->decimal('horas_extras', 10, 2)->default(0);
            $table->decimal('bonos_otros', 10, 2)->default(0);
            $table->decimal('monto_aguinaldo', 10, 2)->default(0);
            $table->decimal('monto_vacaciones', 10, 2)->default(0);
            $table->decimal('monto_prima_vacacional', 10, 2)->default(0);
            $table->decimal('total_percepciones', 10, 2)->default(0);

            // Deducciones
            $table->decimal('descuento_adelanto', 10, 2)->default(0);
            $table->decimal('descuento_infonavit', 10, 2)->default(0);
            $table->decimal('otros_descuentos', 10, 2)->default(0);
            $table->decimal('total_deducciones', 10, 2)->default(0);

            // Totales y pago
            $table->decimal('neto_pagar', 10, 2)->default(0);
            $table->enum('metodo_pago', ['EFECTIVO', 'TRANSFERENCIA', 'MIXTO'])->default('EFECTIVO');
            $table->enum('estado_pago', ['PAGADO', 'PENDIENTE', 'PARCIAL'])->default('PENDIENTE')->index();
            $table->date('fecha_pago_real')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('id_usuario')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nomina_recibos');
    }
};

