<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anualidades', function (Blueprint $table) {
            $table->unsignedInteger('id_socios_membresia')->nullable()->after('id_socio');
            $table->foreign('id_socios_membresia')
                ->references('id')->on('socios_membresias')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('anualidades', function (Blueprint $table) {
            $table->dropForeign(['id_socios_membresia']);
            $table->dropColumn('id_socios_membresia');
        });
    }
};
