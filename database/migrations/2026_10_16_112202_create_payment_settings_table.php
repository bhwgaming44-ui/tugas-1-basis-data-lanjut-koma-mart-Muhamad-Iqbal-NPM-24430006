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
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('gateway')->default('midtrans');
            $table->string('server_key', 500)->nullable();
            $table->string('client_key', 500)->nullable();
            $table->string('merchant_id', 100)->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->enum('environment', ['sandbox', 'production'])->default('sandbox');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
