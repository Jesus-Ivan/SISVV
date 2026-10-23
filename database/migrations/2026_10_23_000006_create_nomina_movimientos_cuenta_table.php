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
        Schema::create('nomina_movimientos_cuenta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empleado')->constrained('nomina_empleados')->cascadeOnDelete();
            $table->foreignId('id_recibo')->nullable()->constrained('nomina_recibos')->nullOnDelete();
            $table->foreignId('id_periodo')->nullable()->constrained('nomina_periodos')->nullOnDelete();
            $table->enum('tipo_movimiento', ['ADELANTO', 'PRESTAMO', 'PAGO_NOMINA', 'DESCUENTO_NOMINA', 'AGUINALDO', 'FINIQUITO', 'AJUSTE'])->index();
            $table->enum('naturaleza', ['CARGO', 'ABONO'])->index();
            $table->decimal('monto', 10, 2);
            $table->decimal('saldo_anterior', 10, 2)->default(0);
            $table->decimal('saldo_nuevo', 10, 2)->default(0);
            $table->date('fecha')->index();
            $table->string('referencia', 100)->nullable();
            $table->foreignId('id_usuario')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nomina_movimientos_cuenta');
    }
};

