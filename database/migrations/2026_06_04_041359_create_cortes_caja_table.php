<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cortes_caja', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->dateTime('fecha_cierre');
            $table->decimal('fondo_inicial', 10, 2)->default(0);
            $table->decimal('total_ingresos', 10, 2)->default(0);
            $table->decimal('total_gastos', 10, 2)->default(0);
            $table->decimal('total_retiros', 10, 2)->default(0);
            $table->decimal('efectivo_esperado', 10, 2)->default(0);
            $table->decimal('efectivo_contado', 10, 2)->default(0);
            $table->decimal('diferencia', 10, 2)->default(0);
            $table->boolean('facturado')->default(false);
            $table->string('facturapi_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cortes_caja');
    }
};
