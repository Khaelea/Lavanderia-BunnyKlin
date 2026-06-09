<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_caja', function (Blueprint $table) {
            $table->id();
            $table->decimal('fondo_inicial', 10, 2)->default(500.00);
            $table->string('nombre_negocio')->default('Lavandería BunnyKlin');
            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('telefono')->nullable();
            $table->timestamps();
        });

        // Fila única con valores iniciales
        DB::table('configuracion_caja')->insert([
            'fondo_inicial'  => 0.00,
            'nombre_negocio' => 'Lavandería BunnyKlin',
            'direccion'      => 'Calle 5 de Mayo, Col. Centro',
            'ciudad'         => 'San Juan del Río, Qro.',
            'telefono'       => '427 123 4567',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_caja');
    }
};