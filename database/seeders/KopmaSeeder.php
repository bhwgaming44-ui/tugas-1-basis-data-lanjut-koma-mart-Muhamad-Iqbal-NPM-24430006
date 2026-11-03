<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KopmaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed 20 Pegawai & Staf KOPMA MART
        $users = [
            [
                'id_user'      => 1,
                'username'     => 'admin',
                'password'     => Hash::make('admin123'),
                'nama_lengkap' => 'Drs. H. Suryanto, M.M.',
                'jabatan'      => 'Direktur Utama KOPMA MART',
                'role'         => 'admin',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 2,
                'username'     => 'wakil_ketua',
                'password'     => Hash::make('admin123'),
                'nama_lengkap' => 'Ir. Hendra Prasetyo',
                'jabatan'      => 'Wakil Direktur Bidang Operasional & Kemitraan',
                'role'         => 'admin',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 3,
                'username'     => 'sekretaris',
                'password'     => Hash::make('admin123'),
                'nama_lengkap' => 'Siti Aisyah, S.Kom.',
                'jabatan'      => 'Sekretaris Utama KOPMA MART',
                'role'         => 'admin',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 4,
                'username'     => 'keuangan',
                'password'     => Hash::make('keuangan123'),
                'nama_lengkap' => 'Rina Kusuma, S.E., Ak.',
                'jabatan'      => 'Manajer Keuangan & Pembukuan',
                'role'         => 'manajer',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 5,
                'username'     => 'keuangan2',
                'password'     => Hash::make('keuangan123'),
                'nama_lengkap' => 'Dewi Anggraini, S.E.',
                'jabatan'      => 'Staf Kas & Pembukuan Harian',
                'role'         => 'manajer',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 6,
                'username'     => 'manajer_toko',
                'password'     => Hash::make('admin123'),
                'nama_lengkap' => 'Wahyu Hidayat, S.M.',
                'jabatan'      => 'Manajer Operasional Toko KOPMA MART',
                'role'         => 'admin',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 7,
                'username'     => 'spv_kasir',
                'password'     => Hash::make('kasir123'),
                'nama_lengkap' => 'Mega Puspitasari',
                'jabatan'      => 'Supervisor Kasir & POS',
                'role'         => 'kasir',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 8,
                'username'     => 'kasir1',
                'password'     => Hash::make('kasir123'),
                'nama_lengkap' => 'Siti Rahma',
                'jabatan'      => 'Kasir Utama (Shift Pagi)',
                'role'         => 'kasir',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 9,
                'username'     => 'kasir2',
                'password'     => Hash::make('kasir123'),
                'nama_lengkap' => 'Rahmat Hidayat',
                'jabatan'      => 'Kasir Toko (Shift Siang)',
                'role'         => 'kasir',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 10,
                'username'     => 'kasir3',
                'password'     => Hash::make('kasir123'),
                'nama_lengkap' => 'Dinda Ayu Lestari',
                'jabatan'      => 'Kasir Toko (Shift Sore)',
                'role'         => 'kasir',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 11,
                'username'     => 'kasir4',
                'password'     => Hash::make('kasir123'),
                'nama_lengkap' => 'Dimas Prayogo',
                'jabatan'      => 'Kasir Toko (Shift Malam)',
                'role'         => 'kasir',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 12,
                'username'     => 'ka_gudang',
                'password'     => Hash::make('admin123'),
                'nama_lengkap' => 'Joko Susilo',
                'jabatan'      => 'Kepala Pergudangan & Logistik',
                'role'         => 'admin',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 13,
                'username'     => 'staff_gudang1',
                'password'     => Hash::make('admin123'),
                'nama_lengkap' => 'Fajar Nugroho',
                'jabatan'      => 'Staf Pengadaan Sembako & ATK',
                'role'         => 'admin',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 14,
                'username'     => 'staff_gudang2',
                'password'     => Hash::make('admin123'),
                'nama_lengkap' => 'Tri Wahyuni',
                'jabatan'      => 'Staf Quality Control & Stok Opname',
                'role'         => 'admin',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 15,
                'username'     => 'staff_it',
                'password'     => Hash::make('admin123'),
                'nama_lengkap' => 'Muhamad Iqbal, S.Kom.',
                'jabatan'      => 'Staf IT, Database & Sistem POS',
                'role'         => 'admin',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 16,
                'username'     => 'staff_keanggotaan',
                'password'     => Hash::make('admin123'),
                'nama_lengkap' => 'Anisa Nurul',
                'jabatan'      => 'Staf Administrasi & Pelayanan Anggota',
                'role'         => 'admin',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 17,
                'username'     => 'staff_member',
                'password'     => Hash::make('keuangan123'),
                'nama_lengkap' => 'Bagus Pratama',
                'jabatan'      => 'Staf Layanan Member & Program Loyalitas',
                'role'         => 'manajer',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 18,
                'username'     => 'pramuniaga1',
                'password'     => Hash::make('kasir123'),
                'nama_lengkap' => 'Eka Novitasari',
                'jabatan'      => 'Pramuniaga & Display Toko KOPMA MART',
                'role'         => 'kasir',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 19,
                'username'     => 'pramuniaga2',
                'password'     => Hash::make('kasir123'),
                'nama_lengkap' => 'Yusuf Ardiansyah',
                'jabatan'      => 'Pramuniaga & Layanan Pesanan Pelanggan',
                'role'         => 'kasir',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 20,
                'username'     => 'security',
                'password'     => Hash::make('admin123'),
                'nama_lengkap' => 'Bambang Irawan',
                'jabatan'      => 'Koordinator Keamanan & Ketertiban Toko',
                'role'         => 'admin',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_user'      => 21,
                'username'     => 'mitra',
                'password'     => Hash::make('mitra123'),
                'nama_lengkap' => 'Rizky Pratama',
                'jabatan'      => 'Mitra Kurir / Pengantar Pesanan',
                'role'         => 'mitra',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ];
        DB::table('users')->insert($users);

        // 2. Seed Member / Pelanggan KOPMA MART
        DB::table('anggotas')->insert([
            [
                'id_anggota'      => 1,
                'kode_member'     => 'MBR-001',
                'nama'            => 'Muhamad Iqbal',
                'alamat'          => 'Jl. Melati No. 12, RT 03/RW 05, Kota Bandung',
                'no_hp'           => '081234567890',
                'email'           => 'iqbal@email.com',
                'password'        => Hash::make('password'),
                'poin'            => 1250,
                'total_belanja'   => 750000.00,
                'status_aktif'    => 'aktif',
                'tanggal_daftar'  => '2026-10-09',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'id_anggota'      => 2,
                'kode_member'     => 'MBR-002',
                'nama'            => 'Nabila Putri',
                'alamat'          => 'Jl. Mawar No. 8, RT 01/RW 02, Kota Bandung',
                'no_hp'           => '081298765432',
                'email'           => 'nabila@email.com',
                'password'        => Hash::make('password'),
                'poin'            => 640,
                'total_belanja'   => 420000.00,
                'status_aktif'    => 'aktif',
                'tanggal_daftar'  => '2026-09-02',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'id_anggota'      => 3,
                'kode_member'     => 'MBR-003',
                'nama'            => 'Reza Pratama',
                'alamat'          => 'Jl. Kenanga No. 21, RT 05/RW 01, Kota Bandung',
                'no_hp'           => '081377889900',
                'email'           => 'reza@email.com',
                'password'        => Hash::make('password'),
                'poin'            => 890,
                'total_belanja'   => 530000.00,
                'status_aktif'    => 'aktif',
                'tanggal_daftar'  => '2026-09-05',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);

        // 3. Seed Kategori Barang (6 Kategori KOPMA MART)
        DB::table('kategori_barangs')->insert([
            ['id_kategori' => 1, 'nama_kategori' => 'Sembako & Kebutuhan Pokok', 'created_at' => now(), 'updated_at' => now()],
            ['id_kategori' => 2, 'nama_kategori' => 'Makanan & Minuman Ringan', 'created_at' => now(), 'updated_at' => now()],
            ['id_kategori' => 3, 'nama_kategori' => 'Alat Tulis Kantor (ATK) & Perkuliahan', 'created_at' => now(), 'updated_at' => now()],
            ['id_kategori' => 4, 'nama_kategori' => 'Kebersihan & Perawatan Diri', 'created_at' => now(), 'updated_at' => now()],
            ['id_kategori' => 5, 'nama_kategori' => 'Obat & Farmasi Ringan (Apotek Desa)', 'created_at' => now(), 'updated_at' => now()],
            ['id_kategori' => 6, 'nama_kategori' => 'Merchandise & Perlengkapan Kampus', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 4. Seed Tepat 100 Barang Berbeda Toko KOPMA MART
        $barangs = [
            // --- Sembako & Bahan Pokok (1 - 15) ---
            ['Beras Merah Putih Premium 5kg', 1, 65000, 72000, 68000, 50, 10],
            ['Minyak Goreng Kita 1 Liter', 1, 14500, 16500, 15500, 80, 15],
            ['Minyak Goreng Bimoli Pouch 2 Liter', 1, 33000, 37000, 35000, 45, 10],
            ['Gula Pasir Gulaku Premium 1kg', 1, 16000, 18500, 17500, 60, 15],
            ['Tepung Terigu Segitiga Biru 1kg', 1, 11500, 13500, 12500, 55, 10],
            ['Telur Ayam Ras Segar 1kg', 1, 26000, 29500, 28000, 35, 10],
            ['Garam Beryodium Merah Putih 500g', 1, 3500, 5000, 4500, 70, 20],
            ['Kecap Manis Bango Botol 520ml', 1, 21000, 24000, 22500, 40, 10],
            ['Saus Sambal ABC Botol 335ml', 1, 13000, 15500, 14000, 45, 10],
            ['Santan Kelapa Instan Kara 65ml', 1, 3000, 4000, 3500, 90, 20],
            ['Susu Kental Manis Frisian Flag 370g', 1, 11000, 13000, 12000, 65, 15],
            ['Margarin Blue Band Serbaguna 200g', 1, 9000, 11500, 10500, 50, 10],
            ['Bawang Merah Brebes Pilihan 500g', 1, 16000, 19000, 18000, 30, 8],
            ['Bawang Putih Kating Pilihan 500g', 1, 18000, 22000, 20000, 30, 8],
            ['Gas LPG Melon 3kg (Subsidized)', 1, 18000, 22000, 20000, 40, 10],

            // --- Makanan & Minuman Ringan (16 - 45) ---
            ['Indomie Goreng Spesial 85g', 2, 2800, 3500, 3100, 200, 30],
            ['Indomie Kuah Ayam Bawang 69g', 2, 2700, 3300, 3000, 180, 30],
            ['Indomie Kuah Soto Mie 70g', 2, 2700, 3300, 3000, 180, 30],
            ['Mie Sedaap Goreng Kriuk 90g', 2, 2700, 3300, 3000, 150, 25],
            ['Pop Mie Goreng Pedes Gledek 75g', 2, 5000, 6500, 5800, 80, 15],
            ['Pop Mie Kuah Rasa Baso Spesial 75g', 2, 5000, 6500, 5800, 80, 15],
            ['Air Mineral Le Minerale 600ml', 2, 2500, 3500, 3000, 150, 25],
            ['Air Mineral Aqua Botol 600ml', 2, 2800, 4000, 3300, 140, 25],
            ['Air Mineral Aqua Galon 19L Refill', 2, 17000, 21000, 19000, 30, 5],
            ['Teh Botol Sosro Kotak 250ml', 2, 3000, 4000, 3500, 90, 15],
            ['Teh Pucuk Harum Melati 350ml', 2, 3000, 4000, 3500, 120, 20],
            ['Floridina Orange Dingin 350ml', 2, 2800, 3800, 3200, 95, 15],
            ['Ultra Milk UHT Cokelat 250ml', 2, 5800, 7200, 6500, 85, 15],
            ['Ultra Milk UHT Full Cream 250ml', 2, 5800, 7200, 6500, 85, 15],
            ['Susu Steril Bear Brand 189ml', 2, 9500, 11500, 10500, 70, 15],
            ['Kopi Kapal Api Spesial Mix 1 Renteng (10 sachet)', 2, 12000, 15000, 13500, 50, 10],
            ['Good Day Cappuccino 1 Renteng (10 sachet)', 2, 17000, 21000, 19000, 45, 10],
            ['Kopi Nescafe Classic Jar 100g', 2, 38000, 44000, 41000, 25, 5],
            ['Teh Celup Sariwangi Kotak isi 25', 2, 6500, 8500, 7500, 60, 10],
            ['Biskuit Roma Kelapa Gurih 300g', 2, 9500, 12000, 10800, 55, 10],
            ['Biskuit Khong Guan Classic Kaleng Mini 650g', 2, 48000, 56000, 52000, 20, 5],
            ['Nabati Wafer Richeese Keju 130g', 2, 6000, 8000, 7000, 75, 15],
            ['Oreo Sandwich Biscuit Vanilla 123g', 2, 7500, 9800, 8700, 70, 15],
            ['Chitato Keripik Sapi Panggang 68g', 2, 9500, 12000, 11000, 55, 10],
            ['Qtela Keripik Singkong Balado 60g', 2, 5000, 6800, 6000, 65, 10],
            ['SilverQueen Milk Chocolate Almond 58g', 2, 13000, 16500, 15000, 50, 10],
            ['Beng-Beng Chocolate Wafer Box isi 17', 2, 28000, 34000, 31000, 35, 8],
            ['Yupi Gummy Candy Permen Kenyal 120g', 2, 7000, 9000, 8000, 45, 10],
            ['Sari Roti Tawar Spesial Kupas 350g', 2, 13000, 15500, 14500, 25, 5],
            ['Selai Cokelat Ceres Choco Spread 350g', 2, 27000, 32000, 29500, 20, 5],

            // --- ATK & Perkuliahan (46 - 75) ---
            ['Buku Tulis KIKY Bergaris 38 Lembar', 3, 3000, 4500, 4000, 120, 20],
            ['Buku Tulis KIKY Bergaris 58 Lembar', 3, 4200, 6000, 5200, 100, 15],
            ['Buku Tulis Campus Big Boss 36 Lembar', 3, 4500, 6500, 5500, 90, 15],
            ['Buku Catatan Spiral A5 Hardcover 100 Lembar', 3, 12000, 16000, 14000, 50, 10],
            ['Kertas HVS PaperOne A4 75gsm 1 Rim (500 Lembar)', 3, 45000, 53000, 49000, 70, 15],
            ['Kertas HVS Sinar Dunia F4 70gsm 1 Rim', 3, 47000, 55000, 51000, 60, 15],
            ['Kertas Folio Bergaris Sinar Dunia isi 100 Lembar', 3, 16000, 20000, 18000, 45, 10],
            ['Pulpen Standard AE7 0.5mm Tinta Hitam', 3, 2000, 3000, 2500, 180, 30],
            ['Pulpen Standard AE7 0.5mm Tinta Biru', 3, 2000, 3000, 2500, 120, 20],
            ['Pulpen Standard AE7 0.5mm Tinta Merah', 3, 2000, 3000, 2500, 80, 15],
            ['Pulpen Gel Joyko GP-180 Shokyo 0.5mm Hitam', 3, 3200, 4500, 3800, 100, 20],
            ['Pulpen Tanda Tangan Pilot Balliner Hitam', 3, 11000, 14000, 12500, 45, 10],
            ['Pensil Ujian 2B Faber-Castell Asli', 3, 3500, 5000, 4200, 90, 15],
            ['Penghapus Pensil Faber-Castell EB-20 Putih', 3, 2500, 4000, 3200, 90, 15],
            ['Rautan Pensil Joyko Putar Mini', 3, 4500, 6500, 5500, 50, 10],
            ['Tipe-X Kertas Joyko CT-522 Correction Tape', 3, 6000, 8500, 7200, 80, 15],
            ['Tipe-X Cair Pentel Correction Pen ZL31-W', 3, 9500, 12500, 11000, 50, 10],
            ['Stabilo Boss Original Highlighter Kuning Neon', 3, 9000, 12000, 10500, 65, 12],
            ['Stabilo Joyko Pastel Set 6 Warna', 3, 18000, 23000, 20500, 45, 10],
            ['Penggaris Besi Tebal 30cm Joyko', 3, 6500, 9000, 7800, 55, 10],
            ['Gunting Kertas Serbaguna Joyko SC-838', 3, 7500, 10500, 9000, 45, 8],
            ['Lem Kertas Glukol Tradisional Sedang', 3, 3000, 4500, 3800, 65, 10],
            ['Lem Kertas Stik UHU Glue Stick 8.2g', 3, 6500, 9000, 7800, 55, 10],
            ['Stapler Joyko No.10 HD-10 Kompak', 3, 11000, 14500, 12800, 45, 10],
            ['Isi Stapler Joyko No.10 Kotak Kecil', 3, 2000, 3000, 2500, 120, 20],
            ['Paper Clip Logam Trigonal No.3 Joyko', 3, 3000, 4500, 3800, 75, 15],
            ['Binder Clip Hitam Joyko No.107 isi 12', 3, 4000, 6000, 5000, 65, 12],
            ['Map Dokumen Clear Holder F4 isi 20 Lembar', 3, 14000, 18000, 16000, 40, 8],
            ['Map Kertas Snelhechter Merah Putih isi 10', 3, 2000, 3000, 2500, 130, 20],
            ['Sticky Notes Post-It 3x3 Kuning Cerah', 3, 6500, 9000, 7800, 55, 10],

            // --- Kebersihan & Perawatan Diri (76 - 85) ---
            ['Sabun Mandi Lifebuoy Total 10 Merah 85g', 4, 3800, 5000, 4400, 85, 15],
            ['Shampo Pantene Anti Dandruff Botol 160ml', 4, 19000, 23500, 21500, 40, 8],
            ['Pasta Gigi Pepsodent Pencegah Gigi Berlubang 190g', 4, 11500, 14500, 13000, 55, 10],
            ['Sikat Gigi Pepsodent Double Care Sensitive isi 3', 4, 14000, 18000, 16000, 45, 10],
            ['Sabun Cuci Piring Sunlight Jeruk Nipis 700ml', 4, 13000, 16000, 14500, 60, 12],
            ['Deterjen Bubuk Rinso Molto Anti Noda 770g', 4, 18500, 22500, 20500, 45, 10],
            ['Pewangi Pakaian Downy Mystique Refill 650ml', 4, 24000, 29000, 26500, 38, 8],
            ['Pembersih Lantai So Klin Lantai Pouch 780ml', 4, 9500, 12000, 10800, 50, 10],
            ['Tisu Wajah Paseo Smart 250 Sheets 2 Ply', 4, 11000, 14000, 12500, 65, 12],
            ['Tisu Basah Dettol Anti Bakteri isi 10 Lembar', 4, 7500, 10000, 8800, 55, 10],

            // --- Obat & Farmasi Ringan / Apotek Desa (86 - 94) ---
            ['Minyak Kayu Putih Cap Lang Botol 60ml', 5, 19000, 23500, 21500, 48, 10],
            ['Tolak Angin Cair Sidomuncul Box isi 5', 5, 18000, 22000, 20000, 42, 10],
            ['Paracetamol Tablet 500mg Strip isi 10', 5, 3000, 4500, 3800, 90, 20],
            ['Promag Tablet Obat Maag Strip isi 10', 5, 7500, 9500, 8500, 70, 15],
            ['Betadine Larutan Antiseptik Luka 15ml', 5, 13000, 16500, 15000, 40, 8],
            ['Hansaplast Plester Luka Elastis isi 10', 5, 4500, 6500, 5500, 75, 15],
            ['Bodrex Tablet Sakit Kepala Strip isi 20', 5, 8000, 10500, 9200, 55, 10],
            ['Salonpas Koyo Pereda Nyeri isi 10 Lembar', 5, 6500, 8500, 7500, 60, 12],
            ['Safe Care Minyak Angin Aromatherapy Roll-on 10ml', 5, 14000, 17500, 15800, 45, 10],

            // --- Merchandise & Perlengkapan Toko KOPMA MART (95 - 100) ---
            ['Kaos Polo KOPMA MART Katun Bordir', 6, 65000, 85000, 75000, 35, 8],
            ['Topi Baseball Logo KOPMA MART', 6, 25000, 35000, 30000, 40, 10],
            ['Tumbler Stainless Vacuum KOPMA MART 500ml', 6, 42000, 55000, 48000, 45, 10],
            ['Tote Bag Kanvas Premium KOPMA MART', 6, 20000, 30000, 25000, 55, 12],
            ['Lanyard & Holder ID Card KOPMA MART', 6, 12000, 18000, 15000, 70, 15],
            ['Pin Bros Logam KOPMA MART', 6, 6000, 10000, 8000, 90, 20],
        ];

        // URL gambar produk ASLI per-item (gaya minimarket Indomaret/Alfamart)
        $gambarProduk = [
            // Sembako (1-15)
            'https://assets.klikindomaret.com/products/20242565/20242565_thumb.jpg',  // Beras 5kg
            'https://assets.klikindomaret.com/products/20041137/20041137_thumb.jpg',  // Minyak Goreng 1L
            'https://assets.klikindomaret.com/products/20063282/20063282_thumb.jpg',  // Bimoli 2L
            'https://assets.klikindomaret.com/products/20025455/20025455_thumb.jpg',  // Gulaku 1kg
            'https://assets.klikindomaret.com/products/20024799/20024799_thumb.jpg',  // Segitiga Biru
            'https://assets.klikindomaret.com/products/20217434/20217434_thumb.jpg',  // Telur 1kg
            'https://assets.klikindomaret.com/products/20024807/20024807_thumb.jpg',  // Garam 500g
            'https://assets.klikindomaret.com/products/20024811/20024811_thumb.jpg',  // Kecap Bango
            'https://assets.klikindomaret.com/products/20024810/20024810_thumb.jpg',  // Saus ABC
            'https://assets.klikindomaret.com/products/20025429/20025429_thumb.jpg',  // Santan Kara
            'https://assets.klikindomaret.com/products/20024777/20024777_thumb.jpg',  // Frisian Flag
            'https://assets.klikindomaret.com/products/20025009/20025009_thumb.jpg',  // Blue Band
            'https://assets.klikindomaret.com/products/20074283/20074283_thumb.jpg',  // Bawang Merah
            'https://assets.klikindomaret.com/products/20074286/20074286_thumb.jpg',  // Bawang Putih
            'https://assets.klikindomaret.com/products/20123252/20123252_thumb.jpg',  // Gas LPG 3kg
            // Makanan & Minuman (16-45)
            'https://assets.klikindomaret.com/products/20024965/20024965_thumb.jpg',  // Indomie Goreng
            'https://assets.klikindomaret.com/products/20024966/20024966_thumb.jpg',  // Indomie Ayam Bawang
            'https://assets.klikindomaret.com/products/20024968/20024968_thumb.jpg',  // Indomie Soto
            'https://assets.klikindomaret.com/products/20025128/20025128_thumb.jpg',  // Mie Sedaap
            'https://assets.klikindomaret.com/products/20024974/20024974_thumb.jpg',  // Pop Mie Goreng
            'https://assets.klikindomaret.com/products/20025016/20025016_thumb.jpg',  // Pop Mie Baso
            'https://assets.klikindomaret.com/products/20228858/20228858_thumb.jpg',  // Le Minerale
            'https://assets.klikindomaret.com/products/20024995/20024995_thumb.jpg',  // Aqua 600ml
            'https://assets.klikindomaret.com/products/20024993/20024993_thumb.jpg',  // Aqua Galon
            'https://assets.klikindomaret.com/products/20024849/20024849_thumb.jpg',  // Teh Sosro
            'https://assets.klikindomaret.com/products/20106073/20106073_thumb.jpg',  // Teh Pucuk
            'https://assets.klikindomaret.com/products/20101987/20101987_thumb.jpg',  // Floridina
            'https://assets.klikindomaret.com/products/20025073/20025073_thumb.jpg',  // Ultra Milk Cokelat
            'https://assets.klikindomaret.com/products/20025074/20025074_thumb.jpg',  // Ultra Milk Full Cream
            'https://assets.klikindomaret.com/products/20025068/20025068_thumb.jpg',  // Bear Brand
            'https://assets.klikindomaret.com/products/20024928/20024928_thumb.jpg',  // Kapal Api
            'https://assets.klikindomaret.com/products/20074461/20074461_thumb.jpg',  // Good Day
            'https://assets.klikindomaret.com/products/20024929/20024929_thumb.jpg',  // Nescafe
            'https://assets.klikindomaret.com/products/20024841/20024841_thumb.jpg',  // Sariwangi
            'https://assets.klikindomaret.com/products/20025052/20025052_thumb.jpg',  // Roma Kelapa
            'https://assets.klikindomaret.com/products/20025010/20025010_thumb.jpg',  // Khong Guan
            'https://assets.klikindomaret.com/products/20074578/20074578_thumb.jpg',  // Nabati Richeese
            'https://assets.klikindomaret.com/products/20074560/20074560_thumb.jpg',  // Oreo
            'https://assets.klikindomaret.com/products/20025059/20025059_thumb.jpg',  // Chitato
            'https://assets.klikindomaret.com/products/20108541/20108541_thumb.jpg',  // Qtela
            'https://assets.klikindomaret.com/products/20025044/20025044_thumb.jpg',  // SilverQueen
            'https://assets.klikindomaret.com/products/20025046/20025046_thumb.jpg',  // Beng-Beng
            'https://assets.klikindomaret.com/products/20025050/20025050_thumb.jpg',  // Yupi
            'https://assets.klikindomaret.com/products/20025005/20025005_thumb.jpg',  // Sari Roti
            'https://assets.klikindomaret.com/products/20025006/20025006_thumb.jpg',  // Ceres Spread
            // ATK (46-75)
            'https://assets.klikindomaret.com/products/20025231/20025231_thumb.jpg',  // Buku KIKY 38
            'https://assets.klikindomaret.com/products/20025232/20025232_thumb.jpg',  // Buku KIKY 58
            'https://assets.klikindomaret.com/products/20025234/20025234_thumb.jpg',  // Campus Big Boss
            'https://assets.klikindomaret.com/products/20025238/20025238_thumb.jpg',  // Spiral A5
            'https://assets.klikindomaret.com/products/20025245/20025245_thumb.jpg',  // PaperOne A4
            'https://assets.klikindomaret.com/products/20025246/20025246_thumb.jpg',  // Sinar Dunia F4
            'https://assets.klikindomaret.com/products/20025248/20025248_thumb.jpg',  // Folio Bergaris
            'https://assets.klikindomaret.com/products/20025259/20025259_thumb.jpg',  // Standard AE7 Hitam
            'https://assets.klikindomaret.com/products/20025260/20025260_thumb.jpg',  // Standard AE7 Biru
            'https://assets.klikindomaret.com/products/20025261/20025261_thumb.jpg',  // Standard AE7 Merah
            'https://assets.klikindomaret.com/products/20074697/20074697_thumb.jpg',  // Joyko GP-180
            'https://assets.klikindomaret.com/products/20025265/20025265_thumb.jpg',  // Pilot Balliner
            'https://assets.klikindomaret.com/products/20025267/20025267_thumb.jpg',  // Faber-Castell 2B
            'https://assets.klikindomaret.com/products/20025271/20025271_thumb.jpg',  // Penghapus FC
            'https://assets.klikindomaret.com/products/20025275/20025275_thumb.jpg',  // Rautan Joyko
            'https://assets.klikindomaret.com/products/20025278/20025278_thumb.jpg',  // Correction Tape
            'https://assets.klikindomaret.com/products/20025280/20025280_thumb.jpg',  // Correction Pen
            'https://assets.klikindomaret.com/products/20074710/20074710_thumb.jpg',  // Stabilo Boss
            'https://assets.klikindomaret.com/products/20074712/20074712_thumb.jpg',  // Stabilo Pastel
            'https://assets.klikindomaret.com/products/20025285/20025285_thumb.jpg',  // Penggaris 30cm
            'https://assets.klikindomaret.com/products/20025288/20025288_thumb.jpg',  // Gunting Joyko
            'https://assets.klikindomaret.com/products/20025290/20025290_thumb.jpg',  // Lem Glukol
            'https://assets.klikindomaret.com/products/20025292/20025292_thumb.jpg',  // UHU Glue Stick
            'https://assets.klikindomaret.com/products/20025295/20025295_thumb.jpg',  // Stapler HD-10
            'https://assets.klikindomaret.com/products/20025297/20025297_thumb.jpg',  // Isi Stapler
            'https://assets.klikindomaret.com/products/20025299/20025299_thumb.jpg',  // Paper Clip
            'https://assets.klikindomaret.com/products/20025301/20025301_thumb.jpg',  // Binder Clip
            'https://assets.klikindomaret.com/products/20025305/20025305_thumb.jpg',  // Clear Holder
            'https://assets.klikindomaret.com/products/20025308/20025308_thumb.jpg',  // Snelhechter
            'https://assets.klikindomaret.com/products/20025310/20025310_thumb.jpg',  // Sticky Notes
            // Kebersihan (76-85)
            'https://assets.klikindomaret.com/products/20025325/20025325_thumb.jpg',  // Lifebuoy
            'https://assets.klikindomaret.com/products/20025345/20025345_thumb.jpg',  // Pantene
            'https://assets.klikindomaret.com/products/20025330/20025330_thumb.jpg',  // Pepsodent
            'https://assets.klikindomaret.com/products/20025335/20025335_thumb.jpg',  // Sikat Gigi
            'https://assets.klikindomaret.com/products/20025360/20025360_thumb.jpg',  // Sunlight
            'https://assets.klikindomaret.com/products/20025365/20025365_thumb.jpg',  // Rinso
            'https://assets.klikindomaret.com/products/20025370/20025370_thumb.jpg',  // Downy
            'https://assets.klikindomaret.com/products/20025375/20025375_thumb.jpg',  // So Klin
            'https://assets.klikindomaret.com/products/20025380/20025380_thumb.jpg',  // Paseo
            'https://assets.klikindomaret.com/products/20025385/20025385_thumb.jpg',  // Dettol
            // Obat & Farmasi (86-94)
            'https://assets.klikindomaret.com/products/20025390/20025390_thumb.jpg',  // Kayu Putih
            'https://assets.klikindomaret.com/products/20025395/20025395_thumb.jpg',  // Tolak Angin
            'https://assets.klikindomaret.com/products/20025400/20025400_thumb.jpg',  // Paracetamol
            'https://assets.klikindomaret.com/products/20025405/20025405_thumb.jpg',  // Promag
            'https://assets.klikindomaret.com/products/20025410/20025410_thumb.jpg',  // Betadine
            'https://assets.klikindomaret.com/products/20025415/20025415_thumb.jpg',  // Hansaplast
            'https://assets.klikindomaret.com/products/20025420/20025420_thumb.jpg',  // Bodrex
            'https://assets.klikindomaret.com/products/20025425/20025425_thumb.jpg',  // Salonpas
            'https://assets.klikindomaret.com/products/20025430/20025430_thumb.jpg',  // Safe Care
            // Merchandise (95-100)
            'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&q=80',  // Kaos Polo
            'https://images.unsplash.com/photo-1588850561407-ed78c334e67a?w=400&q=80',  // Topi Baseball
            'https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=400&q=80',  // Tumbler
            'https://images.unsplash.com/photo-1544816155-12df9643f363?w=400&q=80',  // Tote Bag
            'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?w=400&q=80',  // Lanyard
            'https://images.unsplash.com/photo-1612872087720-bb876e2e67d1?w=400&q=80',  // Pin Bros
        ];

        $barangRows = [];
        $i = 1;
        foreach ($barangs as $idx => $b) {
            $barangRows[] = [
                'id_barang'          => $i,
                'kode_barcode'       => sprintf('899100000%03d', $i),
                'nama_barang'        => $b[0],
                'id_kategori'        => $b[1],
                'harga_beli'         => $b[2],
                'harga_jual_umum'    => $b[3],
                'harga_jual_anggota' => $b[4],
                'stok'               => $b[5],
                'stok_minimum'       => $b[6],
                'gambar'             => $gambarProduk[$idx] ?? $gambarProduk[0],
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
            $i++;
        }

        DB::table('barangs')->insert($barangRows);
    }
}
