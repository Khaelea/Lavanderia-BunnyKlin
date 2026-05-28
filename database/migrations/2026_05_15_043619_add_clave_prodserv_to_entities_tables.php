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
        foreach (['supplies', 'services', 'subscriptions'] as $tabla) {

            Schema::table($tabla, function (Blueprint $table) {

                $table->string('clave_prodserv', 8)
                    ->nullable()
                    ->after('name');

            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['supplies', 'services', 'subscriptions'] as $tabla) {

            Schema::table($tabla, function (Blueprint $table) {

                $table->dropColumn('clave_prodserv');

            });
        }
    }
};

