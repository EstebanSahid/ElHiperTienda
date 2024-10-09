<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Agregar la columna Rol
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id_rol')->default(1);
        });

        // Lo hacemos ForeingKey
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('id_rol')
                ->references('id_rol')->on('roles')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_rol']);
            $table->dropColumn('id_rol');
        });
    }
};
