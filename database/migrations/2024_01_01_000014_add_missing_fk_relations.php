<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * GARIS RELASI FISIK (Foreign Key) sesuai ERD canvas.
 * Kolom-kolom sudah ada, tinggal pasang garisnya agar tabel yang
 * belum berelasi jadi berelasi di Designer phpMyAdmin.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->addFk('penjualans', 'id_kurir', 'fk_penjualan_kurir_staf', 'users', 'id_user', 'SET NULL', 'CASCADE');
        $this->addFk('penjualans', 'id_kurir_anggota', 'fk_penjualan_kurir_anggota', 'anggotas', 'id_anggota', 'SET NULL', 'CASCADE');
        $this->addFk('chat_pesans', 'id_penjualan', 'fk_chat_penjualan', 'penjualans', 'id_penjualan', 'CASCADE', 'CASCADE');
        $this->addFk('chat_pesans', 'id_anggota', 'fk_chat_anggota', 'anggotas', 'id_anggota', 'CASCADE', 'CASCADE');
        $this->addFk('chat_pesans', 'id_kurir', 'fk_chat_kurir_staf', 'users', 'id_user', 'SET NULL', 'CASCADE');
        $this->addFk('chat_pesans', 'id_kurir_anggota', 'fk_chat_kurir_anggota', 'anggotas', 'id_anggota', 'SET NULL', 'CASCADE');
        $this->addFk('kopma_point_logs', 'id_anggota', 'fk_point_anggota', 'anggotas', 'id_anggota', 'CASCADE', 'CASCADE');
        $this->addFk('topup_requests', 'id_anggota', 'fk_topup_anggota', 'anggotas', 'id_anggota', 'CASCADE', 'CASCADE');
        $this->addFk('topup_requests', 'id_petugas', 'fk_topup_petugas', 'users', 'id_user', 'SET NULL', 'CASCADE');
    }

    public function down(): void
    {
        foreach (['fk_topup_petugas', 'fk_topup_anggota', 'fk_point_anggota', 'fk_chat_kurir_anggota', 'fk_chat_kurir_staf', 'fk_chat_anggota', 'fk_chat_penjualan', 'fk_penjualan_kurir_anggota', 'fk_penjualan_kurir_staf'] as $fk) {
            try {
                DB::statement("ALTER TABLE `" . $this->tableOf($fk) . "` DROP FOREIGN KEY `$fk`");
            } catch (\Throwable $e) {
            }
        }
    }

    private function tableOf(string $fk): string
    {
        return match (true) {
            str_starts_with($fk, 'fk_penjualan') => 'penjualans',
            str_starts_with($fk, 'fk_chat') => 'chat_pesans',
            str_starts_with($fk, 'fk_point') => 'kopma_point_logs',
            default => 'topup_requests',
        };
    }

    private function addFk(string $table, string $col, string $name, string $refTable, string $refCol, string $onDelete, string $onUpdate): void
    {
        if (!Schema::hasTable($table) || !Schema::hasTable($refTable)) return;
        if (!Schema::hasColumn($table, $col)) return;
        $ada = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?", [$table, $name]);
        if (!empty($ada)) return;
        // Bersihkan yatim agar FK tidak gagal.
        try {
            DB::statement("DELETE t FROM `$table` t LEFT JOIN `$refTable` r ON r.`$refCol` = t.`$col` WHERE t.`$col` IS NOT NULL AND r.`$refCol` IS NULL");
        } catch (\Throwable $e) {
        }
        Schema::table($table, function (Blueprint $t) use ($col, $name, $refTable, $refCol, $onDelete, $onUpdate) {
            $t->foreign($col, $name)->references($refCol)->on($refTable)->onDelete($onDelete)->onUpdate($onUpdate);
        });
    }
};
