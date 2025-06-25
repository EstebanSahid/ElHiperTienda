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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id('id_pedido');
            $table->bigInteger('numero_pedido');
            $table->date('fecha_pedido');
            $table->string('estado');
            $table->boolean('bloqueado')->default(false);
            $table->foreignId('id_tienda')->constrained('tienda', 'id_tienda')->onUpdate('cascade')->onDelete('restrict');
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
        Schema::dropIfExists('pedidos');
    }
};
