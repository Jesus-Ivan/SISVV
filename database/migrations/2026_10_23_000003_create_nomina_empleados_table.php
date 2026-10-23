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
        Schema::create('nomina_empleados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellido_paterno', 100);
            $table->string('apellido_materno', 100)->nullable();
            $table->enum('tipo_empleado', ['PLANTA', 'TEMPORAL'])->default('PLANTA')->index();
            $table->foreignId('id_area')->nullable()->constrained('nomina_areas')->nullOnDelete();
            $table->foreignId('id_puesto')->nullable()->constrained('nomina_puestos')->nullOnDelete();
            $table->decimal('salario_base', 10, 2)->default(0);
            $table->enum('tipo_pago_predeterminado', ['EFECTIVO', 'TRANSFERENCIA', 'MIXTO'])->default('EFECTIVO');
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_baja')->nullable();
            $table->boolean('activo')->default(true)->index();
            $table->string('foto_path', 255)->nullable();
            $table->string('telefono', 25)->nullable();
            $table->string('rfc', 20)->nullable();
            $table->string('curp', 25)->nullable();
            $table->string('nss', 25)->nullable();
            $table->decimal('saldo_adelantos', 10, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nomina_empleados');
    }
};

