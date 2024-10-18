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
        Schema::create('pedidos_detalle', function (Blueprint $table) {
            $table->id('id_pdetalle');
            $table->string('nombre_producto');
            $table->string('plus_producto');
            $table->bigInteger('cantidad');
            $table->string('estado');
            $table->foreignId('id_producto')->constrained('productos','id_producto')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('id_pedido')->constrained('pedidos','id_pedido')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('id_unidad_pedido')->constrained('unidad_pedido','id_unidad_pedido')->onUpdate('cascade')->onDelete('restrict');
            $table->unsignedBigInteger('ucrea');
            $table->unsignedBigInteger('umodifica')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos_detalle');
    }
};
