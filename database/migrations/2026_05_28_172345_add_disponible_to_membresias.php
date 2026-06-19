<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('membresias')) return;

        Schema::table('membresias', function (Blueprint $table) {
            if (!Schema::hasColumn('membresias', 'disponible')) {
                $table->boolean('disponible')->default(true)->after('descripcion');
            }
        });

        // Membresías que no aparecen en formularios de registro/edición
        DB::table('membresias')->whereIn('clave', [
            'CC-V-C', 'CC-V-S',
            'CG-V-C', 'CG-V-S',
            'COR', 'CUR', 'EST', 'EVE', 'INT',
        ])->update(['disponible' => false]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('membresias')) return;

        Schema::table('membresias', function (Blueprint $table) {
            $table->dropColumn('disponible');
        });
    }
};
