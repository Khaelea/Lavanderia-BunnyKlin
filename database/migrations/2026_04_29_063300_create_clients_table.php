<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // --- Datos Generales ---
            $table->string('name', 100);
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();

            // --- Dirección General (Operativa / De Envío) ---
            $table->string('codigo_postal', 5)->nullable();
            $table->string('calle')->nullable();
            $table->string('numero_exterior', 20)->nullable();
            $table->string('numero_interior', 20)->nullable();
            $table->string('colonia')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('estado')->nullable();

            // --- Suscripción ---
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->nullOnDelete();
            $table->date('end_subscription')->nullable();

            // --- Datos Fiscales ---
            $table->string('rfc', 14)->nullable();
            $table->string('razon_social')->nullable();
            $table->string('regimen_fiscal', 10)->nullable();
            $table->boolean('same_billing_address')->default(false);

            // --- Dirección Fiscal (CFDI) ---
            $table->string('fiscal_codigo_postal', 5)->nullable();
            $table->string('fiscal_calle')->nullable();
            $table->string('fiscal_numero_exterior', 20)->nullable();
            $table->string('fiscal_numero_interior', 20)->nullable();
            $table->string('fiscal_colonia')->nullable();
            $table->string('fiscal_ciudad')->nullable();
            $table->string('fiscal_estado')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
