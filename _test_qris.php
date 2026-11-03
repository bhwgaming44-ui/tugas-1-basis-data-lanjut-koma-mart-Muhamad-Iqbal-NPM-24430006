<?php
// Uji mandiri QrisService tanpa framework (dijalankan via CLI).
// 1) Vektor uji resmi EMVCo: CRC harus A13A
// 2) Payload QRIS dinamis KOPMA: struktur tag & CRC harus valid

require __DIR__ . '/app/Services/QrisService.php';

// --- Stub helper Laravel yang dipakai service ---
if (!function_exists('config')) {
    function config($key, $default = null) {
        if ($key === 'services.qris') {
            return [
                'guid' => 'ID.CO.QRIS.WWW', 'nmid' => '9360001234567',
                'merchant_id' => '93600012345670001', 'merchant_criteria' => 'UMI',
                'merchant_name' => 'KOPMA MART', 'merchant_city' => 'YOGYAKARTA',
                'postal_code' => '55281', 'mcc' => '5411', 'currency' => '360',
                'country' => 'ID', 'terminal_label' => 'KOPMA01',
            ];
        }
        return $default;
    }
}
if (!function_exists('filled')) {
    function filled($v) { return !(is_null($v) || $v === '' || $v === []); }
}

use App\Services\QrisService;

$svc = new QrisService();
$ok  = true;

// ===== 1) Vektor uji EMVCo (contoh standar spesifikasi) =====
$emv = '00020101021229300012D156000000000510A93FO3230Q31280012D15600000001030812345678520441115802CN5914BEST TRANSPORT6007BEIJING64200002ZH0104最佳运输0202北京540523.7253031565502016233030412340603***0708A60086670902ME91320016A0112233449988770708123456786304';
$crcDiharapkan = 'A13A';
$crcHasil = $svc->crc16($emv);
echo "[1] CRC16 vektor EMVCo : $crcHasil (harus $crcDiharapkan) -> " . ($crcHasil === $crcDiharapkan ? "LULUS" : "GAGAL") . "\n";
if ($crcHasil !== $crcDiharapkan) { $ok = false; }

// ===== 2) Payload QRIS dinamis KOPMA MART =====
$payload = $svc->payload(50000, 'TOPUP-KPM001');
echo "[2] Payload QRIS dinamis:\n    $payload\n";

$tag = $svc->parse($payload);
$cek = [
    '00' => '01',        // versi payload
    '01' => '12',        // 12 = DINAMIS
    '54' => '50000',     // nominal melekat
    '53' => '360',       // IDR
    '58' => 'ID',        // negara
    '59' => 'KOPMA MART',
    '60' => 'YOGYAKARTA',
];
foreach ($cek as $t => $harap) {
    $ada = $tag[$t] ?? '(tidak ada)';
    $lulus = ($ada === $harap);
    echo "    tag $t = $ada (harus $harap) -> " . ($lulus ? "OK" : "GAGAL") . "\n";
    if (!$lulus) { $ok = false; }
}

// CRC payload buatan sendiri harus valid
$crcOk = $svc->crcValid($payload);
echo "[3] CRC payload KOPMA valid -> " . ($crcOk ? "LULUS" : "GAGAL") . "\n";
if (!$crcOk) { $ok = false; }

// Tag 26 harus memuat NMID
$nmidAda = str_contains($tag['26'] ?? '', '9360001234567');
echo "[4] NMID ada di tag 26 -> " . ($nmidAda ? "LULUS" : "GAGAL") . "\n";
if (!$nmidAda) { $ok = false; }

// Tag 62 harus memuat referensi
$refAda = str_contains($tag['62'] ?? '', 'TOPUP-KPM001');
echo "[5] Referensi ada di tag 62 -> " . ($refAda ? "LULUS" : "GAGAL") . "\n";
if (!$refAda) { $ok = false; }

// ===== 6) QR statis (tanpa nominal) -> tag 01 = 11, tanpa tag 54 =====
$statis = $svc->parse($svc->payload(null));
$statisOk = (($statis['01'] ?? '') === '11') && !isset($statis['54']);
echo "[6] QR statis: tag01=" . ($statis['01'] ?? '-') . ", tag54=" . (isset($statis['54']) ? 'ada' : 'tidak ada') . " -> " . ($statisOk ? "LULUS" : "GAGAL") . "\n";
if (!$statisOk) { $ok = false; }

// ===== 7) Nominal desimal & pembulatan =====
$n1 = $svc->nominal(50000);      echo "[7] nominal(50000)   = $n1 (harus 50000) -> " . ($n1 === '50000' ? "LULUS" : "GAGAL") . "\n";
$n2 = $svc->nominal(1500.5);     echo "[7] nominal(1500.5)  = $n2 (harus 1500.5) -> " . ($n2 === '1500.5' ? "LULUS" : "GAGAL") . "\n";
if ($n1 !== '50000' || $n2 !== '1500.5') { $ok = false; }

echo "\n" . ($ok ? "=== SEMUA UJI LULUS ===" : "=== ADA UJI GAGAL ===") . "\n";
exit($ok ? 0 : 1);
