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
        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corte_id')->nullable()->constrained('cortes_caja')->nullOnDelete();
            $table->enum('tipo', ['gasto', 'retiro']);
            $table->decimal('monto', 10, 2);
            $table->string('concepto_o_responsable');
            $table->date('fecha_turno'); // para agrupar por día/turno
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_caja');
    }
};
