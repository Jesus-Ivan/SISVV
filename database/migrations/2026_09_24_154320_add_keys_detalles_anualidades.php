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
        Schema::table('detalles_anualidades', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->unsigned()->first();
            $table->bigInteger('id_locker')->unsigned()->nullable()->after('id_cuota');
            $table->foreign('id_locker')
                ->references('id_locker')
                ->on('locker')
                ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalles_anualidades', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('id_locker');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
};
