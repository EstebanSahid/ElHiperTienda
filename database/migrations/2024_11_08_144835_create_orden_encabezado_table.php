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
        Schema::create('orden_encabezado', function (Blueprint $table) {
            $table->id('id_encabezado');
            $table->foreignId('id_tienda')->constrained('tienda', 'id_tienda')->onUpdate('cascade')->onDelete('restrict');
            $table->bigInteger('posicion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_encabezado');
    }
};
