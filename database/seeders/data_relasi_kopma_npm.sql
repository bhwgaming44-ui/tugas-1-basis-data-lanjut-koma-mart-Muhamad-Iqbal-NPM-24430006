-- =====================================================================
-- DATA RELASI CONTOH — DATABASE kopma_npm
-- Tujuan: menampilkan relasi antar tabel agar mudah dipresentasikan.
-- Jalankan SETELAH: php artisan migrate:fresh --seed
-- (Tabel & data pokong sudah dibuat oleh migration + KopmaSeeder)
--
-- Cara pakai di phpMyAdmin / CLI:
--   mysql -u root kopma_npm < data_relasi_kopma_npm.sql
--
-- Catatan: kolom created_at/updated_at diisi NOW() agar konsisten.
-- =====================================================================

USE kopma_npm;

-- ---------------------------------------------------------------------
-- A. PENJUALAN / HEADER KASIR (relasi: anggotas -> users[kasir])
--    id_anggota NULL = pembeli umum (non-member).
--    Diskon 10% untuk member, 0% untuk pembeli umum.
-- ---------------------------------------------------------------------
INSERT INTO penjualans
    (id_penjualan, no_struk, tanggal_transaksi, id_anggota, id_kasir,
     total_bruto, total_diskon, total_netto, nominal_bayar, kembalian, metode_bayar, created_at, updated_at)
VALUES
    (1, 'STR-20261010-001', '2026-10-10 12:30:00', 1,    8, 43500.00,  4350.00, 39150.00, 50000.00, 10850.00, 'tunai',          NOW(), NOW()),
    (2, 'STR-20261010-002', '2026-10-10 13:10:00', 2,    8, 48500.00,  4850.00, 43650.00, 50000.00,  6350.00, 'qris',           NOW(), NOW()),
    (3, 'STR-20261010-003', '2026-10-10 14:45:00', NULL, 9, 18000.00,     0.00, 18000.00, 20000.00,  2000.00, 'tunai',          NOW(), NOW()),
    (4, 'STR-20261010-004', '2026-10-10 15:20:00', 3,   10, 143000.00, 14300.00,128700.00,150000.00, 21300.00, 'transfer',       NOW(), NOW());

-- ---------------------------------------------------------------------
-- C. DETAIL PENJUALAN (relasi: penjualans -> barangs)
--    subtotal = jumlah * harga_satuan.
--    Harga member dipakai utk pembeli member; harga umum utk umum.
-- ---------------------------------------------------------------------
INSERT INTO penjualan_details
    (id_penjualan, id_barang, jumlah, harga_satuan, subtotal, created_at, updated_at)
VALUES
    -- Struk 1 (anggota 1): Indomie Goreng (16) x5 @3100 + Telur (6) x1 @28000 = 43500
    (1, 16, 5,  3100.00, 15500.00, NOW(), NOW()),
    (1,  6, 1, 28000.00, 28000.00, NOW(), NOW()),
    -- Struk 2 (anggota 2): Minyak Goreng Kita (2) x2 @15500 + Gula (4) x1 @17500 = 48500
    (2,  2, 2, 15500.00, 31000.00, NOW(), NOW()),
    (2,  4, 1, 17500.00, 17500.00, NOW(), NOW()),
    -- Struk 3 (pembeli umum): Garam (7) x2 @5000 + Santan (10) x2 @4000 = 18000
    (3,  7, 2,  5000.00, 10000.00, NOW(), NOW()),
    (3, 10, 2,  4000.00,  8000.00, NOW(), NOW()),
    -- Struk 4 (anggota 3): Beras Merah Putih (1) x1 @68000 + Kaos Polo KOPMA (95) x1 @75000 = 143000
    (4,  1, 1, 68000.00, 68000.00, NOW(), NOW()),
    (4, 95, 1, 75000.00, 75000.00, NOW(), NOW());
