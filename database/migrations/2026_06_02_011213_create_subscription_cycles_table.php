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
        Schema::create('subscription_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_subscription_id')->constrained('client_subscription')->cascadeOnDelete();

            $table->date('cycle_start');
            $table->date('cycle_end');

            // Control de Kilos
            $table->decimal('kilos_allowed', 8, 2);
            $table->decimal('kilos_consumed', 8, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_cycles');
    }
};
