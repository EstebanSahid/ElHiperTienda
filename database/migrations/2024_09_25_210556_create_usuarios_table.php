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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('nombres');
            $table->string('primer_apellido');
            $table->string('segundo_apellido');
            $table->string('telefono');
            $table->string('usuario')->unique();
            $table->string('password');
            $table->foreignId('id_rol')->constrained('roles', 'id_rol')->onUpdate('cascade')->onUpdate('restrict');
            $table->string('estado', 25);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
