<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * NORMALISASI Length/Values kolom database kopma_npm.
 * Dibuat setelah ditemukan nilai janggal ala hasil AI:
 *  - anggotas.id_anggota BIGINT(2)     -> BIGINT UNSIGNED
 *  - anggotas.nama VARCHAR(3)          -> VARCHAR(100)
 * Nilai baru mengikuti FUNGSI tiap kolom (kode member 20, no HP 20,
 * email 100, nama 100-150, no struk 30, nominal decimal 12,2, dsb).
 * Aman diulang: setiap ALTER dibungkus hasColumn + try/catch.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->mod('anggotas', 'id_anggota', 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->mod('anggotas', 'kode_member', 'VARCHAR(20) NOT NULL');
        $this->mod('anggotas', 'nama', 'VARCHAR(100) NOT NULL');
        $this->mod('anggotas', 'alamat', 'VARCHAR(150) NULL');
        $this->mod('anggotas', 'no_hp', 'VARCHAR(20) NULL');
        $this->mod('anggotas', 'email', 'VARCHAR(100) NULL');
        $this->mod('anggotas', 'password', 'VARCHAR(255) NULL');
        $this->mod('anggotas', 'foto', 'VARCHAR(500) NULL');
        $this->mod('anggotas', 'poin', 'INT NOT NULL DEFAULT 0');
        $this->mod('anggotas', 'saldo_kopma', 'DECIMAL(12,2) NOT NULL DEFAULT 0.00');
        $this->mod('anggotas', 'total_belanja', 'DECIMAL(12,2) NOT NULL DEFAULT 0.00');

        $this->mod('users', 'id_user', 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->mod('users', 'username', 'VARCHAR(50) NOT NULL');
        $this->mod('users', 'nama_lengkap', 'VARCHAR(100) NOT NULL');
        $this->mod('users', 'jabatan', "VARCHAR(100) NOT NULL DEFAULT 'Staf Operasional'");

        $this->mod('kategori_barangs', 'id_kategori', 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->mod('kategori_barangs', 'nama_kategori', 'VARCHAR(50) NOT NULL');

        $this->mod('barangs', 'id_barang', 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->mod('barangs', 'kode_barcode', 'VARCHAR(50) NOT NULL');
        $this->mod('barangs', 'nama_barang', 'VARCHAR(150) NOT NULL');

        $this->mod('penjualans', 'id_penjualan', 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->mod('penjualans', 'no_struk', 'VARCHAR(30) NOT NULL');
        $this->mod('penjualans', 'alamat_antar', 'VARCHAR(255) NULL');
        $this->mod('penjualans', 'catatan', 'VARCHAR(255) NULL');

        $this->mod('penjualan_details', 'id_detail', 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->mod('penjualan_details', 'id_penjualan', 'BIGINT UNSIGNED NOT NULL');
        $this->mod('penjualan_details', 'id_barang', 'BIGINT UNSIGNED NOT NULL');
        $this->mod('penjualan_details', 'jumlah', 'INT NOT NULL');
        $this->mod('penjualan_details', 'harga_satuan', 'DECIMAL(12,2) NOT NULL');
        $this->mod('penjualan_details', 'subtotal', 'DECIMAL(12,2) NOT NULL');

        $this->mod('chat_pesans', 'id', 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->mod('chat_pesans', 'id_penjualan', 'BIGINT UNSIGNED NULL');
        $this->mod('chat_pesans', 'id_kurir', 'BIGINT UNSIGNED NULL');
        $this->mod('chat_pesans', 'id_kurir_anggota', 'BIGINT UNSIGNED NULL');
        $this->mod('chat_pesans', 'id_anggota', 'BIGINT UNSIGNED NOT NULL');
        $this->mod('chat_pesans', 'pengirim', "VARCHAR(10) NOT NULL DEFAULT 'user'");
        $this->mod('chat_pesans', 'nama_pengirim', 'VARCHAR(100) NULL');
        $this->mod('chat_pesans', 'pesan', 'TEXT NOT NULL');

        $this->mod('topup_requests', 'id', 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->mod('topup_requests', 'kode_bayar', 'VARCHAR(30) NOT NULL');
        $this->mod('topup_requests', 'id_anggota', 'BIGINT UNSIGNED NOT NULL');
        $this->mod('topup_requests', 'nominal', 'DECIMAL(12,2) NOT NULL');
        $this->mod('topup_requests', 'metode', "VARCHAR(20) NOT NULL DEFAULT 'qris'");
        $this->mod('topup_requests', 'status', "VARCHAR(30) NOT NULL DEFAULT 'menunggu_pembayaran'");
        $this->mod('topup_requests', 'id_petugas', 'BIGINT UNSIGNED NULL');
        $this->mod('topup_requests', 'catatan', 'VARCHAR(255) NULL');

        $this->mod('kopma_point_logs', 'id', 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->mod('kopma_point_logs', 'id_anggota', 'BIGINT UNSIGNED NOT NULL');
        $this->mod('kopma_point_logs', 'jenis', 'VARCHAR(20) NOT NULL');
        $this->mod('kopma_point_logs', 'metode', 'VARCHAR(20) NULL');
        $this->mod('kopma_point_logs', 'nominal', 'DECIMAL(12,2) NOT NULL');
        $this->mod('kopma_point_logs', 'keterangan', 'VARCHAR(255) NULL');

        $this->mod('transaksi_simpanan', 'id', 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->mod('transaksi_simpanan', 'jenis_mutasi', 'VARCHAR(255) NULL');
        $this->mod('transaksi_simpanan', 'nominal', 'DECIMAL(15,2) NOT NULL DEFAULT 0.00');
    }

    public function down(): void
    {
        // Sengaja kosong: normalisasi tipe kolom tidak perlu di-rollback.
    }

    private function mod(string $table, string $col, string $def): void
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $col)) {
            return;
        }
        try {
            DB::statement("ALTER TABLE `$table` MODIFY `$col` $def");
        } catch (\Throwable $e) {
        }
    }
};
