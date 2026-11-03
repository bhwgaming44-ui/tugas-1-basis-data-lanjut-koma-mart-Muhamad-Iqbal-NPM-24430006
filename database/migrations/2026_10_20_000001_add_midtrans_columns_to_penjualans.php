<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan 'midtrans' ke enum metode_bayar (aman jika sudah ada nilainya)
        try {
            DB::statement("ALTER TABLE penjualans MODIFY metode_bayar ENUM('tunai','qris','transfer','koppay','transfer_bank','virtual_account','midtrans') NOT NULL DEFAULT 'tunai'");
        } catch (\Throwable $e) {
            // Jika DB bukan MySQL atau enum sudah berubah, lanjutkan tanpa gagal
            \Illuminate\Support\Facades\Log::warning('Gagal alter enum metode_bayar: ' . $e->getMessage());
        }

        Schema::table('penjualans', function (Blueprint $table) {
            if (!Schema::hasColumn('penjualans', 'midtrans_order_id')) {
                $table->string('midtrans_order_id', 100)->nullable()->after('no_struk');
            }
            if (!Schema::hasColumn('penjualans', 'midtrans_snap_token')) {
                $table->text('midtrans_snap_token')->nullable();
            }
            if (!Schema::hasColumn('penjualans', 'midtrans_payment_type')) {
                $table->string('midtrans_payment_type', 50)->nullable();
            }
            if (!Schema::hasColumn('penjualans', 'midtrans_transaction_time')) {
                $table->timestamp('midtrans_transaction_time')->nullable();
            }
            if (!Schema::hasColumn('penjualans', 'midtrans_gross_amount')) {
                $table->decimal('midtrans_gross_amount', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('penjualans', 'midtrans_signature_key')) {
                $table->string('midtrans_signature_key', 255)->nullable();
            }
            if (!Schema::hasColumn('penjualans', 'midtrans_status_code')) {
                $table->string('midtrans_status_code', 20)->nullable();
            }
            if (!Schema::hasColumn('penjualans', 'midtrans_raw_response')) {
                $table->longText('midtrans_raw_response')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn([
                'midtrans_order_id',
                'midtrans_snap_token',
                'midtrans_payment_type',
                'midtrans_transaction_time',
                'midtrans_gross_amount',
                'midtrans_signature_key',
                'midtrans_status_code',
                'midtrans_raw_response',
            ]);
        });
    }
};