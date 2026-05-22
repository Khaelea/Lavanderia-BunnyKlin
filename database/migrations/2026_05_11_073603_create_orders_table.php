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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id')
                ->unique()
                ->constrained('sales')
                ->cascadeOnDelete();
            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->nullOnDelete();
            $table->foreignId('service_id')
                ->nullable()
                ->constrained('services')
                ->nullOnDelete();

            $table->decimal('quantity', 8, 2)->default(0.00);

            $table->string('details')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->decimal('advance_payment', 10, 2)->default(0.00);
            $table->string('status', 20)->default('pendiente');
            $table->dateTime('arrival_date')->useCurrent();
            $table->dateTime('delivery_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
