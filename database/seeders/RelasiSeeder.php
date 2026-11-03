<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * RelasiSeeder
 * -----------------------------------------------------------------------------
 * Mengisi data RELASI contoh agar relasi antar tabel terlihat nyata saat
 * dipresentasikan/dijelaskan (JOIN anggotas<->users, penjualans<->barangs, dll).
 *
 * Seeder ini mengisi tabel: penjualans, penjualan_details.
 * Data pokok (users, anggotas, kategori_barangs, barangs)
 * diasumsikan SUDAH dibuat oleh KopmaSeeder.
 *
 * Cara pakai:
 *   php artisan db:seed --class=RelasiSeeder
 * atau otomatis jalan saat:
 *   php artisan migrate:fresh --seed
 * -----------------------------------------------------------------------------
 */
class RelasiSeeder extends Seeder
{
    public function run(): void
    {
        // Cegah data relasi dobel kalau seeder dijalankan berulang.
        DB::table('penjualan_details')->delete();
        DB::table('penjualans')->delete();

        // A. PENJUALAN / HEADER KASIR (relasi: anggotas -> users[kasir])
        //    id_anggota NULL = pembeli umum (non-member). Diskon 10% member.
        DB::table('penjualans')->insert([
            ['id_penjualan' => 1, 'no_struk' => 'STR-20261010-001', 'tanggal_transaksi' => '2026-10-10 12:30:00', 'id_anggota' => 1,    'id_kasir' => 8,  'total_bruto' => 43500.00,  'total_diskon' => 4350.00,  'total_netto' => 39150.00,  'nominal_bayar' => 50000.00,  'kembalian' => 10850.00, 'metode_bayar' => 'tunai',          'created_at' => now(), 'updated_at' => now()],
            ['id_penjualan' => 2, 'no_struk' => 'STR-20261010-002', 'tanggal_transaksi' => '2026-10-10 13:10:00', 'id_anggota' => 2,    'id_kasir' => 8,  'total_bruto' => 48500.00,  'total_diskon' => 4850.00,  'total_netto' => 43650.00,  'nominal_bayar' => 50000.00,  'kembalian' => 6350.00,  'metode_bayar' => 'qris',           'created_at' => now(), 'updated_at' => now()],
            ['id_penjualan' => 3, 'no_struk' => 'STR-20261010-003', 'tanggal_transaksi' => '2026-10-10 14:45:00', 'id_anggota' => null, 'id_kasir' => 9,  'total_bruto' => 18000.00,  'total_diskon' => 0.00,     'total_netto' => 18000.00,  'nominal_bayar' => 20000.00,  'kembalian' => 2000.00,  'metode_bayar' => 'tunai',          'created_at' => now(), 'updated_at' => now()],
            ['id_penjualan' => 4, 'no_struk' => 'STR-20261010-004', 'tanggal_transaksi' => '2026-10-10 15:20:00', 'id_anggota' => 3,    'id_kasir' => 10, 'total_bruto' => 143000.00, 'total_diskon' => 14300.00, 'total_netto' => 128700.00, 'nominal_bayar' => 150000.00, 'kembalian' => 21300.00, 'metode_bayar' => 'transfer', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // C. DETAIL PENJUALAN (relasi: penjualans -> barangs)
        //    subtotal = jumlah * harga_satuan (harga member / harga umum).
        DB::table('penjualan_details')->insert([
            // Struk 1 (anggota 1): Indomie Goreng (16) x5 @3100 + Telur (6) x1 @28000 = 43500
            ['id_penjualan' => 1, 'id_barang' => 16, 'jumlah' => 5, 'harga_satuan' => 3100.00,  'subtotal' => 15500.00, 'created_at' => now(), 'updated_at' => now()],
            ['id_penjualan' => 1, 'id_barang' => 6,  'jumlah' => 1, 'harga_satuan' => 28000.00, 'subtotal' => 28000.00, 'created_at' => now(), 'updated_at' => now()],
            // Struk 2 (anggota 2): Minyak Goreng Kita (2) x2 @15500 + Gula (4) x1 @17500 = 48500
            ['id_penjualan' => 2, 'id_barang' => 2,  'jumlah' => 2, 'harga_satuan' => 15500.00, 'subtotal' => 31000.00, 'created_at' => now(), 'updated_at' => now()],
            ['id_penjualan' => 2, 'id_barang' => 4,  'jumlah' => 1, 'harga_satuan' => 17500.00, 'subtotal' => 17500.00, 'created_at' => now(), 'updated_at' => now()],
            // Struk 3 (pembeli umum): Garam (7) x2 @5000 + Santan (10) x2 @4000 = 18000
            ['id_penjualan' => 3, 'id_barang' => 7,  'jumlah' => 2, 'harga_satuan' => 5000.00,  'subtotal' => 10000.00, 'created_at' => now(), 'updated_at' => now()],
            ['id_penjualan' => 3, 'id_barang' => 10, 'jumlah' => 2, 'harga_satuan' => 4000.00,  'subtotal' => 8000.00,  'created_at' => now(), 'updated_at' => now()],
            // Struk 4 (anggota 3): Beras Merah Putih (1) x1 @68000 + Kaos Polo KOPMA (95) x1 @75000 = 143000
            ['id_penjualan' => 4, 'id_barang' => 1,  'jumlah' => 1, 'harga_satuan' => 68000.00, 'subtotal' => 68000.00, 'created_at' => now(), 'updated_at' => now()],
            ['id_penjualan' => 4, 'id_barang' => 95, 'jumlah' => 1, 'harga_satuan' => 75000.00, 'subtotal' => 75000.00, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
