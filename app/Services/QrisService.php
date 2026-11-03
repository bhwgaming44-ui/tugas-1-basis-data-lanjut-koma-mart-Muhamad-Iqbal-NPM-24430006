<?php

namespace App\Services;

/**
 * QrisService — penyusun payload QRIS DINAMIS standar EMVCo (Merchant Presented Mode).
 *
 * Cara kerja:
 *  1. Seluruh data dirangkai sebagai TLV (Tag 2 digit + Length 2 digit + Value).
 *  2. Tag 01 diisi "12" -> menandakan QR DINAMIS (sekali pakai, nominal sudah melekat).
 *     QR statis memakai "11" dan nominalnya masih harus diketik manual oleh pembeli.
 *  3. Tag 54 berisi nominal transaksi; inilah pembeda utama QR dinamis.
 *  4. Tag 63 berisi CRC16-CCITT-FALSE dari seluruh payload (termasuk "6304").
 *
 * Hasilnya berupa string yang bisa dibaca aplikasi bank / e-wallet apa pun
 * (GoPay, OVO, DANA, ShopeePay, BCA Mobile, ...) karena mengikuti struktur QRIS resmi.
 * Ganti QRIS_NMID / QRIS_MERCHANT_ID di .env dengan data dari acquirer agar
 * transaksi benar-benar masuk ke rekening toko.
 */
class QrisService
{
    /** Batas panjang teks menurut spesifikasi EMVCo. */
    private const MAKS_NAMA_MERCHANT = 25;
    private const MAKS_KOTA_MERCHANT = 15;
    private const MAKS_REFERENSI     = 25;

    /**
     * Rangkai satu TLV: tag + panjang (2 digit) + value.
     * Contoh: tlv('54', '50000') => "540550000"
     */
    public function tlv(string $tag, string $value): string
    {
        return $tag . str_pad((string) strlen($value), 2, '0', STR_PAD_LEFT) . $value;
    }

    /**
     * Payload QRIS dinamis untuk satu transaksi.
     * $nominal kosong / 0 -> menghasilkan payload QR statis (tag 01 = "11").
     */
    public function payload(?float $nominal = null, ?string $referensi = null): string
    {
        $cfg = (array) config('services.qris', []);

        $namaMerchant = $this->potong((string) ($cfg['merchant_name'] ?? 'KOPMA MART'), self::MAKS_NAMA_MERCHANT);
        $kotaMerchant = $this->potong((string) ($cfg['merchant_city'] ?? 'YOGYAKARTA'), self::MAKS_KOTA_MERCHANT);
        $referensi    = $this->potong((string) ($referensi ?? ''), self::MAKS_REFERENSI);
        $dinamis      = ($nominal !== null && $nominal > 0);

        // --- Tag 00: versi format payload (selalu "01") ---
        $payload = $this->tlv('00', '01');

        // --- Tag 01: 12 = QR dinamis (sekali pakai), 11 = QR statis ---
        $payload .= $this->tlv('01', $dinamis ? '12' : '11');

        // --- Tag 26: Merchant Account Information (data NMID domestik) ---
        $payload .= $this->tlv('26', $this->merchantAccount($cfg));

        // --- Tag 51: Merchant Account Information (switching domestik) ---
        $payload .= $this->tlv('51', $this->merchantAccountDomestik($cfg));

        // --- Tag 52: Merchant Category Code (5411 = minimarket / toko kelontong) ---
        $payload .= $this->tlv('52', (string) ($cfg['mcc'] ?? '5411'));

        // --- Tag 53: mata uang transaksi, 360 = IDR ---
        $payload .= $this->tlv('53', (string) ($cfg['currency'] ?? '360'));

        // --- Tag 54: NOMINAL (inti QR dinamis) ---
        if ($dinamis) {
            $payload .= $this->tlv('54', $this->nominal($nominal));
        }

        // --- Tag 58/59/60/61: negara, nama merchant, kota, kode pos ---
        $payload .= $this->tlv('58', (string) ($cfg['country'] ?? 'ID'));
        $payload .= $this->tlv('59', $namaMerchant);
        $payload .= $this->tlv('60', $kotaMerchant);
        if (filled($cfg['postal_code'] ?? null)) {
            $payload .= $this->tlv('61', (string) $cfg['postal_code']);
        }

        // --- Tag 62: Additional Data (nomor referensi / bill number + label terminal) ---
        $tambahan = '';
        if ($referensi !== '') {
            $tambahan .= $this->tlv('01', $referensi);
        }
        if (filled($cfg['terminal_label'] ?? null)) {
            $tambahan .= $this->tlv('07', $this->potong((string) $cfg['terminal_label'], 25));
        }
        if ($tambahan !== '') {
            $payload .= $this->tlv('62', $tambahan);
        }

        // --- Tag 63: CRC16 dihitung dari payload + "6304" ---
        return $payload . '6304' . $this->crc16($payload . '6304');
    }

    /** Sub-tag Merchant Account Information (tag 26). */
    private function merchantAccount(array $cfg): string
    {
        $mai  = $this->tlv('00', (string) ($cfg['guid'] ?? 'ID.CO.QRIS.WWW'));
        $mai .= $this->tlv('01', (string) ($cfg['nmid'] ?? '9360001234567'));
        $mai .= $this->tlv('02', (string) ($cfg['merchant_criteria'] ?? 'UMI'));
        if (filled($cfg['merchant_id'] ?? null)) {
            $mai .= $this->tlv('03', (string) $cfg['merchant_id']);
        }
        return $mai;
    }

    /** Sub-tag Merchant Account Information switching domestik (tag 51). */
    private function merchantAccountDomestik(array $cfg): string
    {
        $mai  = $this->tlv('00', (string) ($cfg['guid'] ?? 'ID.CO.QRIS.WWW'));
        $mai .= $this->tlv('02', (string) ($cfg['nmid'] ?? '9360001234567'));
        $mai .= $this->tlv('03', (string) ($cfg['merchant_criteria'] ?? 'UMI'));
        return $mai;
    }

    /** CRC16-CCITT-FALSE (poly 0x1021, init 0xFFFF) — wajib untuk tag 63 QRIS. */
    public function crc16(string $data): string
    {
        $crc = 0xFFFF;
        for ($i = 0, $n = strlen($data); $i < $n; $i++) {
            $crc ^= (ord($data[$i]) << 8);
            for ($bit = 0; $bit < 8; $bit++) {
                $crc = ($crc & 0x8000) ? (($crc << 1) ^ 0x1021) : ($crc << 1);
                $crc &= 0xFFFF;
            }
        }
        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }

    /** Nominal QRIS: tanpa pemisah ribuan, maksimal 2 desimal ("50000" / "1500.5"). */
    public function nominal(float $nominal): string
    {
        $bulat = number_format($nominal, 2, '.', '');
        // Buang nol di belakang koma: "50000.00" -> "50000", "1500.50" -> "1500.5".
        return rtrim(rtrim($bulat, '0'), '.');
    }

    /** Cek apakah CRC pada payload masih valid (dipakai untuk pengujian). */
    public function crcValid(string $payload): bool
    {
        if (strlen($payload) < 4) {
            return false;
        }
        $isi = substr($payload, 0, -4);
        $crc = substr($payload, -4);
        return hash_equals($this->crc16($isi), strtoupper($crc));
    }

    /** Baca kembali payload menjadi array tag => value (dipakai untuk pengujian). */
    public function parse(string $payload): array
    {
        $hasil = [];
        $i     = 0;
        $n     = strlen($payload);
        while ($i + 4 <= $n) {
            $tag    = substr($payload, $i, 2);
            $length = (int) substr($payload, $i + 2, 2);
            $value  = substr($payload, $i + 4, $length);
            $hasil[$tag] = $value;
            $i += 4 + $length;
        }
        return $hasil;
    }

    /** Potong teks sesuai batas EMVCo (aman untuk UTF-8). */
    private function potong(string $teks, int $maks): string
    {
        return mb_substr(trim($teks), 0, $maks, 'UTF-8');
    }
}
