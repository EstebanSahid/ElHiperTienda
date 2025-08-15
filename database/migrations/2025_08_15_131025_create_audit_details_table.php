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
        Schema::create('audit_details', function (Blueprint $table) {
            $table->id('id_auditoria_detalle');
            $table->unsignedBigInteger('id_auditoria');
            $table->string('campo')->nullable();
            $table->text('valor_anterior')->nullable();
            $table->text('valor_nuevo')->nullable();
            $table->json('cambios_dataJson')->nullable(); // Esto solo es para el crear y eliminar
            $table->timestamps();

            $table->foreign('id_auditoria')
                ->references('id_auditoria')
                ->on('audits')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_details');
    }
};
