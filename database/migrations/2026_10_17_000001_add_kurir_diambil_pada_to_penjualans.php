<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * AKUN UJI COBA + PERBAIKAN KURIR (idempotent — aman jalan berulang).
 * 1) Pastikan kolom penjualans.kurir_diambil_pada ada (perbaiki error SQLSTATE 42S22).
 * 2) Pastikan akun uji sesuai tombol di /masuk:
 *    - users: admin/admin123, kasir1/kasir123, manajer_toko/admin123,
 *      keuangan/keuangan123, mitra/mitra123
 *    - anggotas: Muhamad Iqbal/password, Nabila Putri/password, Reza Pratama/password
 * Tanpa ini, tombol "klik untuk masuk otomatis" gagal (password tidak cocok / akun hilang).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('penjualans') && !Schema::hasColumn('penjualans', 'kurir_diambil_pada')) {
            Schema::table('penjualans', function (Blueprint $table) {
                $table->timestamp('kurir_diambil_pada')->nullable()->after('kurir_tipe');
            });
        }

        // --- Akun staf uji (tabel users) ---
        if (Schema::hasTable('users')) {
            $staf = [
                ['username' => 'admin', 'pass' => 'admin123', 'nama' => 'Drs. H. Suryanto, M.M.', 'jabatan' => 'Direktur Utama KOPMA MART', 'role' => 'admin'],
                ['username' => 'kasir1', 'pass' => 'kasir123', 'nama' => 'Siti Rahma', 'jabatan' => 'Kasir Utama Shift Pagi', 'role' => 'kasir'],
                ['username' => 'manajer_toko', 'pass' => 'admin123', 'nama' => 'Wahyu Hidayat, S.M.', 'jabatan' => 'Manajer Operasional Toko KOPMA MART', 'role' => 'admin'],
                ['username' => 'keuangan', 'pass' => 'keuangan123', 'nama' => 'Rina Kusuma, S.E., Ak.', 'jabatan' => 'Manajer Keuangan & Pembukuan', 'role' => 'manajer'],
                ['username' => 'mitra', 'pass' => 'mitra123', 'nama' => 'Rizky Pratama', 'jabatan' => 'Mitra Kurir / Pengantar Pesanan', 'role' => 'mitra'],
            ];
            foreach ($staf as $s) {
                $ada = \DB::table('users')->where('username', $s['username'])->first();
                $hash = Hash::make($s['pass']);
                $passLama = (string) ($ada->password ?? '');
                $perluReset = !$ada || strlen($passLama) < 20;
                if (!$perluReset) {
                    try { $perluReset = !Hash::check($s['pass'], $passLama); }
                    catch (\Throwable $e) { $perluReset = true; }
                }
                if (!$ada) {
                    \DB::table('users')->insert([
                        'username' => $s['username'], 'password' => $hash,
                        'nama_lengkap' => $s['nama'], 'jabatan' => $s['jabatan'],
                        'role' => $s['role'], 'created_at' => now(), 'updated_at' => now(),
                    ]);
                } elseif ($perluReset) {
                    \DB::table('users')->where('username', $s['username'])->update([
                        'password' => $hash, 'nama_lengkap' => $s['nama'],
                        'jabatan' => $s['jabatan'], 'role' => $s['role'], 'updated_at' => now(),
                    ]);
                }
            }
        }

        // --- Akun member uji (tabel anggotas) ---
        if (Schema::hasTable('anggotas')) {
            $member = [
                ['nama' => 'Muhamad Iqbal', 'kode' => 'MBR-001', 'hp' => '081234567890', 'email' => 'iqbal@email.com'],
                ['nama' => 'Nabila Putri', 'kode' => 'MBR-002', 'hp' => '081298765432', 'email' => 'nabila@email.com'],
                ['nama' => 'Reza Pratama', 'kode' => 'MBR-003', 'hp' => '081377889900', 'email' => 'reza@email.com'],
            ];
            foreach ($member as $m) {
                $ada = \DB::table('anggotas')->where('nama', $m['nama'])->first();
                $hash = Hash::make('password');
                $passLama = (string) ($ada->password ?? '');
                $perluReset = !$ada || strlen($passLama) < 20;
                if (!$perluReset) {
                    try { $perluReset = !Hash::check('password', $passLama); }
                    catch (\Throwable $e) { $perluReset = true; }
                }
                if (!$ada) {
                    \DB::table('anggotas')->insert([
                        'kode_member' => $m['kode'], 'nama' => $m['nama'],
                        'no_hp' => $m['hp'], 'email' => $m['email'], 'password' => $hash,
                        'poin' => 0, 'total_belanja' => 0, 'status_aktif' => 'aktif',
                        'tanggal_daftar' => now()->toDateString(),
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                } elseif ($perluReset) {
                    \DB::table('anggotas')->where('nama', $m['nama'])->update([
                        'password' => $hash, 'status_aktif' => 'aktif', 'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Sengaja kosong: akun uji & kolom kurir dipertahankan agar login tidak rusak.
    }
};
