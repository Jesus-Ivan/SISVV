<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nomina_empleados_historial_laboral', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empleado')->constrained('nomina_empleados')->cascadeOnDelete();
            $table->date('fecha_ingreso');
            $table->date('fecha_baja')->nullable();
            $table->decimal('salario_base', 10, 2)->default(0);
            $table->foreignId('id_puesto')->nullable()->constrained('nomina_puestos')->nullOnDelete();
            $table->foreignId('id_area')->nullable()->constrained('nomina_areas')->nullOnDelete();
            $table->enum('tipo_empleado', ['PLANTA', 'TEMPORAL'])->default('PLANTA')->index();
            $table->string('motivo_baja', 255)->nullable();
            $table->string('motivo_reingreso', 255)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['id_empleado', 'fecha_ingreso', 'fecha_baja'], 'idx_historial_fechas');
        });

        // Migración retroactiva (backfill) de los empleados existentes
        $empleados = DB::table('nomina_empleados')->get();
        foreach ($empleados as $emp) {
            $fechaIngreso = $emp->fecha_ingreso ?: ($emp->created_at ? substr($emp->created_at, 0, 10) : now()->toDateString());
            DB::table('nomina_empleados_historial_laboral')->insert([
                'id_empleado' => $emp->id,
                'fecha_ingreso' => $fechaIngreso,
                'fecha_baja' => $emp->fecha_baja,
                'salario_base' => $emp->salario_base ?? 0,
                'id_puesto' => $emp->id_puesto,
                'id_area' => $emp->id_area,
                'tipo_empleado' => $emp->tipo_empleado ?? 'PLANTA',
                'motivo_reingreso' => 'Registro inicial del trabajador',
                'motivo_baja' => $emp->fecha_baja ? 'Baja registrada en migración inicial' : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nomina_empleados_historial_laboral');
    }
};

