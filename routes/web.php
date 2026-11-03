<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StrukturController;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\BelanjaController;
use App\Http\Controllers\KopmaPointController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatTemplateController;
use App\Http\Controllers\TopupController;
use App\Http\Controllers\PaymentSettingController;
use App\Http\Controllers\PromoController;

/*
|--------------------------------------------------------------------------
| Web Routes - KOPMA MART (Sistem Informasi Minimarket / Point of Sale)
|--------------------------------------------------------------------------
*/

Route::get('/', [TokoController::class, 'index'])->name('dashboard');

// Halaman Metode Pembayaran (BCA, BRI, Mandiri, QRIS, DANA, PayPal)
Route::view('/pembayaran', 'pembayaran')->name('pembayaran');

// Katalog Produk (Area Pelanggan - Publik) + pencarian (?q=) + pagination 12/halaman
Route::get('/toko', [TokoController::class, 'index'])->name('toko.index');
Route::post('/toko', [TokoController::class, 'store'])->name('toko.store');
Route::post('/toko/{barang}/gambar', [TokoController::class, 'updateGambar'])->name('toko.update-gambar');
Route::delete('/toko/{barang}', [TokoController::class, 'destroy'])->name('toko.destroy');

// ===== LOGIN / DAFTAR PELANGGAN (kastemer) =====
Route::get('/daftar', [PelangganController::class, 'showDaftar'])->name('pelanggan.daftar');
Route::post('/daftar', [PelangganController::class, 'daftar']);
Route::get('/login/pelanggan', [PelangganController::class, 'showLogin'])->name('pelanggan.login');
Route::post('/login/pelanggan', [PelangganController::class, 'login']);
Route::post('/logout/pelanggan', [PelangganController::class, 'logout'])->name('pelanggan.logout');

// ===== PROFIL PELANGGAN =====
Route::get('/profil', [PelangganController::class, 'profil'])->name('pelanggan.profil');
Route::post('/profil/foto', [PelangganController::class, 'ubahFoto'])->name('pelanggan.foto');
// Foto profil TERPADU semua peran (member / petugas / kurir) — via dropdown avatar navbar
Route::post('/profil/foto/ubah', [\App\Http\Controllers\ProfilController::class, 'simpanFoto'])->name('foto.profil.update');
// Ganti nama tampilan (user / petugas / kurir — mengikuti mode aktif)
Route::post('/profil/nama/ubah', [\App\Http\Controllers\ProfilController::class, 'ubahNama'])->name('profil.nama.update');
Route::post('/profil/lokasi', [PelangganController::class, 'simpanLokasi'])->name('pelanggan.lokasi');

// ===== BELANJA ONLINE =====
Route::post('/belanja/tambah', [BelanjaController::class, 'tambah'])->name('belanja.tambah');
Route::get('/keranjang', [BelanjaController::class, 'keranjang'])->name('keranjang.index');
Route::post('/keranjang/update', [BelanjaController::class, 'update'])->name('keranjang.update');
Route::post('/keranjang/hapus', [BelanjaController::class, 'hapus'])->name('keranjang.hapus');
Route::post('/belanja/checkout', [BelanjaController::class, 'checkout'])->name('belanja.checkout');
Route::get('/belanja/bayar/{id}', [BelanjaController::class, 'bayar'])->name('belanja.bayar');
Route::post('/belanja/bayar/{id}/konfirmasi', [BelanjaController::class, 'konfirmasiBayar'])->name('belanja.konfirmasi');
// Ganti metode pembayaran langsung dari halaman bayar — tanpa input ulang checkout
Route::post('/belanja/bayar/{id}/metode', [BelanjaController::class, 'ubahMetodeBayar'])->name('belanja.metode');

// ===== MIDTRANS SNAP (pembayaran online) =====
Route::post('/belanja/midtrans/snap/{id}', [\App\Http\Controllers\MidtransController::class, 'snapToken'])->name('midtrans.snap');
// Cek status langsung ke API Midtrans (fallback polling bila webhook tak terjangkau)
Route::get('/belanja/midtrans/status/{id}', [\App\Http\Controllers\MidtransController::class, 'cekStatus'])->name('midtrans.status');
// Webhook notifikasi Midtrans (dikecualikan dari CSRF di VerifyCsrfToken)
Route::post('/midtrans/notification', [\App\Http\Controllers\MidtransController::class, 'notification'])->name('midtrans.notification');
Route::get('/pesanan', [BelanjaController::class, 'pesanan'])->name('pesanan.index');
Route::delete('/pesanan/{id}/batalkan', [BelanjaController::class, 'batalkan'])->name('pesanan.batalkan');

// ===== LOGIN KURIR =====
Route::get('/masuk', [AuthController::class, 'showMitraLogin'])->name('masuk');
Route::post('/kurir/login', [AuthController::class, 'loginMitra'])->name('mitra.login.post');
Route::post('/kurir/logout', [AuthController::class, 'logoutMitra'])->name('mitra.logout');
// PINDAH PERAN 1 tab (tanpa logout, tanpa tab baru): /mode/user|petugas|kurir
Route::get('/mode/{mode}', [AuthController::class, 'gantiMode'])->name('mode.ganti');

// ===== APLIKASI KURIR =====
Route::middleware('mitra.auth')->group(function () {
    Route::get('/kurir', [BelanjaController::class, 'kurir'])->name('kurir.index');
    Route::post('/kurir/{id}/ambil', [BelanjaController::class, 'kurirAmbil'])->name('kurir.ambil');
    Route::post('/kurir/{id}/status', [BelanjaController::class, 'kurirUpdate'])->name('kurir.update');
});

// ===== KOPMA POINT =====
Route::get('/kopma-point', [KopmaPointController::class, 'index'])->name('kopma.point.index');
Route::post('/kopma-point/topup', [KopmaPointController::class, 'topup'])->name('kopma.point.topup');

// ===== TOP UP =====
Route::post('/kopma-point/topup/ajukan', [TopupController::class, 'ajukan'])->name('topup.ajukan');
Route::get('/kopma-point/topup/{kode}/bayar', [TopupController::class, 'bayar'])->name('topup.bayar');
Route::post('/kopma-point/topup/{kode}/konfirmasi', [TopupController::class, 'konfirmasiBayar'])->name('topup.konfirmasi');
Route::get('/kopma-point/topup/{kode}/status', [TopupController::class, 'status'])->name('topup.status');

// ===== CHAT =====
Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
Route::post('/chat/kirim', [ChatController::class, 'kirimUser'])->name('chat.kirim');
Route::get('/chat/pesan', [ChatController::class, 'pesan'])->name('chat.pesan');

// Chatbot
Route::post('/chatbot/ask', [ChatbotController::class, 'ask'])->middleware('throttle:30,1')->name('chatbot.ask');
Route::get('/chatbot/katalog', [ChatbotController::class, 'katalog'])->middleware('throttle:60,1')->name('chatbot.katalog');

// Struk PDF
Route::get('/struk/pdf/{id}', [KasirController::class, 'strukPdf'])->name('kasir.struk.pdf');

// Modul 1: Anggota
Route::resource('anggota', AnggotaController::class)->only(['index', 'store']);

// ===== AREA PETUGAS / KASIR =====
Route::get('/kasir/login', [AuthController::class, 'showLogin'])->name('kasir.login');
Route::post('/kasir/login', [AuthController::class, 'login'])->name('kasir.login.post');
Route::post('/kasir/logout', [AuthController::class, 'logout'])->name('kasir.logout');

Route::middleware('kasir.auth')->group(function () {
    Route::get('/petugas/dashboard', [DashboardController::class, 'petugas'])->name('petugas.dashboard');
    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
    Route::post('/kasir/checkout', [KasirController::class, 'checkout'])->name('kasir.checkout');
    // Susunan katalog produk (gaya kelola Promo) — petugas
    Route::post('/kasir/urutan', [KasirController::class, 'aturUrutan'])->name('kasir.atur-urutan');
    Route::get('/kasir/riwayat', [KasirController::class, 'riwayat'])->name('kasir.riwayat');
    Route::get('/kasir/struk/{id}', [KasirController::class, 'struk'])->name('kasir.struk');
    Route::post('/kasir/stok/{barang}/tambah', [KasirController::class, 'tambahStok'])->name('kasir.stok.tambah');
    Route::delete('/kasir/stok/{barang}/hapus', [KasirController::class, 'hapusStok'])->name('kasir.stok.hapus');
    // Kelola katalog stok: hapus produk stok habis (per item / otomatis semua) — petugas
    Route::delete('/kasir/barang/{barang}/hapus', [KasirController::class, 'hapusBarang'])->name('kasir.barang.hapus');
    Route::delete('/kasir/barang/kosong/hapus-semua', [KasirController::class, 'hapusBarangKosong'])->name('kasir.barang.hapus-kosong');
    Route::delete('/kasir/riwayat/{penjualan}', [KasirController::class, 'hapusStruk'])->name('kasir.riwayat.hapus');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/data', [LaporanController::class, 'data'])->name('laporan.data');
    // Analisis cerdas: perbandingan tren, produk terlaris, proyeksi stok & rencana
    Route::get('/laporan/analisis', [LaporanController::class, 'analisis'])->name('laporan.analisis');

    // ===== PENGATURAN PAYMENT GATEWAY (MIDTRANS) =====
    // Semua petugas boleh MELIHAT status; hanya admin/manajer boleh MENYIMPAN (guard di controller).
    Route::get('/admin/payment-setting', [PaymentSettingController::class, 'index'])->name('admin.payment-setting');
    Route::post('/admin/payment-setting', [PaymentSettingController::class, 'update'])->name('admin.payment-setting.update');
    Route::post('/admin/payment-setting/test', [PaymentSettingController::class, 'testKoneksi'])->name('admin.payment-setting.test');
    Route::get('/pegawai', [StrukturController::class, 'index'])->name('pegawai.index');
    Route::get('/pegawai/cetak', [StrukturController::class, 'cetak'])->name('pegawai.cetak');
    Route::get('/petugas/topup', [TopupController::class, 'index'])->name('petugas.topup');

    // ===== KELOLA PROMO & TAMPILAN (khusus Petugas) =====
    Route::controller(PromoController::class)->prefix('/petugas/promo')->name('petugas.promo.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{promo}', 'update')->name('update');
        Route::delete('/{promo}', 'destroy')->name('destroy');
        Route::get('/render', 'render')->name('render');
    });

    // ===== KELOLA PRODUK (khusus Petugas) — gaya Kelola Promo =====
    // Halaman katalog lengkap: semua produk tampil, bisa tambah/ubah/hapus/atur urutan.
    Route::controller(TokoController::class)->prefix('/petugas/produk')->name('petugas.produk.')->group(function () {
        Route::get('/', 'kelola')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{barang}', 'update')->name('update');
        Route::delete('/{barang}', 'destroy')->name('destroy');
        Route::post('/urutan', [\App\Http\Controllers\KasirController::class, 'aturUrutan'])->name('atur-urutan');
    });
    Route::post('/petugas/tema-warna', [PromoController::class, 'temaWarna'])->name('petugas.tema-warna');
    Route::post('/petugas/logo-toko', [PromoController::class, 'logoToko'])->name('petugas.logo-toko');
    Route::post('/petugas/topup/{id}/setujui', [TopupController::class, 'setujui'])->name('petugas.topup.setujui');
    Route::post('/petugas/topup/{id}/tolak', [TopupController::class, 'tolak'])->name('petugas.topup.tolak');
    Route::post('/petugas/topup/tunai', [TopupController::class, 'topupTunai'])->name('petugas.topup.tunai');
    Route::post('/pegawai', [StrukturController::class, 'store'])->name('pegawai.store');
    Route::put('/pegawai/{user}', [StrukturController::class, 'update'])->name('pegawai.update');
    Route::delete('/pegawai/{user}', [StrukturController::class, 'destroy'])->name('pegawai.destroy');
});

// ===== AKTIVASI / NONAKTIVASI MODE KURIR UNTUK MEMBER ====
// Route ini di luar group mitra.auth karena diakses SEBELUM kurir_aktif diset.
// Hanya boleh diakses bila pelanggan_id sudah login (dicek di controller).
Route::post('/kurir/aktifkan', [AuthController::class, 'aktifkanKurir'])->name('kurir.aktifkan');
Route::post('/kurir/nonaktifkan', [AuthController::class, 'nonaktifkanKurir'])->name('kurir.nonaktifkan');

// CHAT KURIR
Route::middleware('mitra.auth')->group(function () {
    Route::get('/kurir/chat', [ChatController::class, 'kurir'])->name('kurir.chat');
    Route::post('/kurir/chat/kirim', [ChatController::class, 'kirimKurir'])->name('kurir.chat.kirim');
    Route::get('/kurir/chat/pesan', [ChatController::class, 'pesan'])->name('kurir.chat.pesan');
    Route::get('/kurir/chat/percakapan', [ChatController::class, 'percakapan'])->name('kurir.chat.percakapan');
    // Lokasi kurir real-time (tracking peta)
    Route::post('/kurir/lokasi', [ChatController::class, 'kurirLokasi'])->name('kurir.lokasi');
});

// CHAT TEMPLATE (user & kurir boleh kelola template milik sendiri)
Route::middleware(['mitra.auth', 'web'])->prefix('/chat/template')->name('chat.template.')->group(function () {
    Route::get('/', [ChatTemplateController::class, 'index'])->name('index');
    Route::post('/', [ChatTemplateController::class, 'store'])->name('store');
    Route::put('/{template}', [ChatTemplateController::class, 'update'])->name('update');
    Route::delete('/{template}', [ChatTemplateController::class, 'destroy'])->name('destroy');
    Route::post('/cari-otomatis', [ChatTemplateController::class, 'cariOtomatis'])->name('cari-otomatis');
});

// Lokasi kurir untuk user (tracking peta)
Route::get('/kurir/lokasi-terbaru', [ChatController::class, 'kurirLokasiTerbaru'])->name('kurir.lokasi-terbaru');

// Live Reload
Route::get('/live-reload-hash', function () {
    $dbDir = database_path();
    $latestDb = 0;
    if (is_dir($dbDir)) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dbDir));
        foreach ($iterator as $file) {
            if ($file->isFile() && in_array($file->getExtension(), ['php', 'sql'])) {
                $mtime = $file->getMTime();
                if ($mtime > $latestDb) {
                    $latestDb = $mtime;
                }
            }
        }
    }
    $lastSyncFile = storage_path('framework/last_db_sync.txt');
    $lastSync = file_exists($lastSyncFile) ? (int) @file_get_contents($lastSyncFile) : 0;
    $dbUpdated = false;
    if (!Schema::hasTable('migrations')) {
        Schema::create('migrations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('migration');
            $table->integer('batch');
        });
    }
    if ($lastSync > 0 && $latestDb > $lastSync) {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate:install');
            \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
            file_put_contents($lastSyncFile, $latestDb);
            $dbUpdated = true;
        } catch (\Throwable $e) {
            file_put_contents(storage_path('logs/sync_error.log'), $e->getMessage());
        }
    } elseif ($lastSync === 0) {
        @file_put_contents($lastSyncFile, $latestDb);
    }
    $dirs = [
        resource_path('views'),
        app_path(),
        base_path('routes'),
        database_path(),
    ];
    $latest = $latestDb;
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) continue;
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $ext = $file->getExtension();
                if (in_array($ext, ['php', 'blade.php', 'css', 'js', 'json', 'env', 'sql'])) {
                    $mtime = $file->getMTime();
                    if ($mtime > $latest) {
                        $latest = $mtime;
                    }
                }
            }
        }
    }
    return response()->json(['timestamp' => $latest, 'db_updated' => $dbUpdated]);
});


// Redirect /login bawaan (guard 'auth' tidak dipakai sistem petugas; login via /kasir/login)
Route::redirect('/login', '/masuk')->name('login');


