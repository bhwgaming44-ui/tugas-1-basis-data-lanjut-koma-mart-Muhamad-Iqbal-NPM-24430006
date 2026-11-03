<?php
// Uji CRUD Petugas (store/update/destroy) + hash password.
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

session()->put('kasir_id', 1); // login sebagai admin id 1
$ctrl = new App\Http\Controllers\StrukturController();

// --- STORE (tambah petugas) ---
$req = Illuminate\Http\Request::create('/pegawai', 'POST', [
    'nama_lengkap' => 'Uji Coba Petugas',
    'username'     => 'ujicoba123',
    'jabatan'      => 'Kasir Uji',
    'role'         => 'kasir',
    'password'     => 'rahasia123',
]);
$ctrl->store($req);
$u = User::where('username', 'ujicoba123')->first();
echo "STORE: " . ($u ? "OK (id={$u->id_user})" : "GAGAL") . PHP_EOL;
echo "   password ter-hash: " . (Hash::check('rahasia123', $u->password) ? 'YA (aman)' : 'TIDAK') . PHP_EOL;
echo "   bukan plaintext: " . ($u->password !== 'rahasia123' ? 'YA' : 'TIDAK') . PHP_EOL;

// --- UPDATE (ubah data + ganti password) ---
$req2 = Illuminate\Http\Request::create('/pegawai/' . $u->id_user, 'POST', [
    '_method'      => 'PUT',
    'nama_lengkap' => 'Uji Cobo Update',
    'username'     => 'ujicoba123',
    'jabatan'      => 'Supervisor Uji',
    'role'         => 'manajer',
    'password'     => 'passwordbaru99',
]);
$ctrl->update($req2, $u->fresh());
$u->refresh();
echo "UPDATE: nama={$u->nama_lengkap} role={$u->role} jabatan={$u->jabatan}" . PHP_EOL;
echo "   password baru ter-hash: " . (Hash::check('passwordbaru99', $u->password) ? 'YA' : 'TIDAK') . PHP_EOL;

// --- UPDATE tanpa ganti password (password lama tetap) ---
$req3 = Illuminate\Http\Request::create('/pegawai/' . $u->id_user, 'POST', [
    '_method'      => 'PUT',
    'nama_lengkap' => 'Uji Tanpa Ganti Pass',
    'username'     => 'ujicoba123',
    'role'         => 'kasir',
    'password'     => '',
]);
$ctrl->update($req3, $u->fresh());
$u->refresh();
echo "UPDATE tanpa password: nama={$u->nama_lengkap}, password lama masih valid: "
    . (Hash::check('passwordbaru99', $u->password) ? 'YA' : 'TIDAK') . PHP_EOL;

// --- Validasi username unik (harus gagal) ---
$req4 = Illuminate\Http\Request::create('/pegawai', 'POST', [
    'nama_lengkap' => 'Duplikat', 'username' => 'admin', 'role' => 'kasir', 'password' => 'aaaa',
]);
try {
    $ctrl->store($req4);
    echo "VALIDASI username unik: GAGAL (seharusnya ditolak)" . PHP_EOL;
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "VALIDASI username unik: DITOLAK (OK)" . PHP_EOL;
}

// --- DESTROY (hapus) ---
$idHapus = $u->id_user;
$ctrl->destroy($u);
echo "DESTROY: masih ada=" . (User::where('id_user', $idHapus)->exists() ? 'YA(GAGAL)' : 'TIDAK(OK)') . PHP_EOL;

// --- DESTROY diri sendiri ditolak ---
session()->put('kasir_id', 1);
$admin = User::find(1);
try {
    $ctrl->destroy($admin);
    echo "DESTROY diri sendiri: GAGAL (seharusnya ditolak)" . PHP_EOL;
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "DESTROY diri sendiri: DITOLAK (OK)" . PHP_EOL;
}
echo "   admin id=1 masih ada: " . (User::where('id_user',1)->exists() ? 'YA(OK)' : 'TIDAK(GAGAL)') . PHP_EOL;
