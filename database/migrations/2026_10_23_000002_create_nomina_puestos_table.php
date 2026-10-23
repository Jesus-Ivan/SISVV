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
        Schema::create('nomina_puestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_area')->nullable()->constrained('nomina_areas')->nullOnDelete();
            $table->string('nombre', 100);
            $table->decimal('salario_sugerido', 10, 2)->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nomina_puestos');
    }
};

