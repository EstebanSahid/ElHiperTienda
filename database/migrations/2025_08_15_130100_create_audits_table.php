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
        Schema::create('audits', function (Blueprint $table) {
            $table->id('id_auditoria');
            $table->string('tipo_auditable'); // Sera el modelo
            $table->unsignedBigInteger('id_auditoria_registro'); // Id del registro
            $table->enum('evento', ['creado', 'actualizado', 'eliminado', 'restaurado']);
            $table->unsignedBigInteger('usuario_crea')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('direccion_ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['tipo_auditable', 'id_auditoria_registro']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
