<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransController extends Controller
{
    private function initMidtrans(): array
    {
        $cfg = PaymentSetting::getMidtransConfig();

        Config::$serverKey      = $cfg['server_key'];
        Config::$clientKey      = $cfg['client_key'];
        Config::$isProduction   = ($cfg['environment'] === 'production');
        Config::$isSanitized    = true;
        Config::$is3ds          = true;

        return $cfg;
    }

    public function snapToken(Request $request, $id)
    {
        $this->initMidtrans();

        $penjualan = Penjualan::with('details.barang', 'anggota')
            ->where('id_anggota', session('pelanggan_id'))
            ->where('id_penjualan', $id)
            ->firstOrFail();

        if ($penjualan->metode_bayar !== 'midtrans') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini tidak menggunakan metode pembayaran Midtrans.',
            ], 422);
        }

        // Token lama hanya dipakai bila transaksinya masih aktif (pending).
        // Bila sudah expire / batal / ditolak, token lama membuat popup Snap
        // ERROR ("transaction expired") — maka jatuhkan & buat token baru
        // untuk order_id yang sama (Midtrans menerima order_id ulang yang expired).
        if ($penjualan->midtrans_snap_token) {
            try {
                $st = Transaction::status($penjualan->midtrans_order_id);

                if ($st->transaction_status === 'pending') {
                    // Masih menunggu pembayaran — token lama masih berlaku.
                    return response()->json([
                        'success'    => true,
                        'snap_token' => $penjualan->midtrans_snap_token,
                        'order_id'   => $penjualan->midtrans_order_id,
                        'no_struk'   => $penjualan->no_struk,
                        'total'      => (float) $penjualan->total_netto,
                    ]);
                }

                if (in_array($st->transaction_status, ['settlement', 'capture'], true)) {
                    // Sudah terbayar — beri tahu client agar reload ke kartu LUNAS.
                    return response()->json([
                        'success' => true,
                        'paid'    => true,
                        'message' => 'Pembayaran ini sudah lunas.',
                        'no_struk'=> $penjualan->no_struk,
                    ]);
                }
                // expire / cancel / deny -> lanjut buat token baru di bawah.
            } catch (\Throwable $e) {
                if ((int) $e->getCode() !== 404) {
                    // Error tak terduga saat cek status: pakai token lama saja
                    // agar tidak memutus pembayaran yang mungkin sedang berjalan.
                    return response()->json([
                        'success'    => true,
                        'snap_token' => $penjualan->midtrans_snap_token,
                        'order_id'   => $penjualan->midtrans_order_id,
                        'no_struk'   => $penjualan->no_struk,
                        'total'      => (float) $penjualan->total_netto,
                    ]);
                }
                // 404 "Transaction doesn't exist" -> transaksi belum pernah
                // dibuat di popup: token lama tak berguna, buat baru di bawah.
            }
        }

        $orderId = 'KOPMA-' . $penjualan->no_struk;
        $penjualan->update(['midtrans_order_id' => $orderId]);

        $itemDetails = [];
        foreach ($penjualan->details as $d) {
            $itemDetails[] = [
                'id'       => $d->id_barang,
                'price'    => (int) $d->harga_satuan,
                'quantity' => (int) $d->jumlah,
                'name'     => $d->barang->nama_barang ?? 'Produk KOPMA MART',
            ];
        }

        $customerDetails = [
            'first_name' => $penjualan->anggota->nama ?? 'Pelanggan',
            'email'      => $penjualan->anggota->email ?? null,
            'phone'      => $penjualan->anggota->no_hp ?? null,
        ];

        $transactionDetails = [
            'order_id'     => $orderId,
            'gross_amount' => (int) $penjualan->total_netto,
        ];

        try {
            $snapToken = Snap::createTransaction([
                'transaction_details' => $transactionDetails,
                'item_details'        => $itemDetails,
                'customer_details'    => $customerDetails,
                // Daftar metode di popup Snap - tampil sesuai yang AKTIF di akun
                // merchant (dashboard.midtrans.com -> Settings -> Payment Settings):
                // kartu (VISA/Mastercard/JCB/Amex), virtual account (BCA/BNI/BRI/
                // Mandiri/Permata/ATM Bersama/dll), e-wallet (GoPay/OVO/DANA/
                // ShopeePay/LinkAja), QRIS, gerai (Indomaret/Alfamart), paylater
                // (Akulaku/Kredivo).
                'enabled_payments'    => [
                    'credit_card', 'virtual_account', 'qris',
                    'gopay', 'shopeepay', 'ovo', 'dana', 'linkaja',
                    'indomaret', 'alfamart', 'akulaku', 'kredivo',
                ],
            ])->token;

            $penjualan->update(['midtrans_snap_token' => $snapToken]);

            return response()->json([
                'success'    => true,
                'snap_token' => $snapToken,
                'order_id'   => $orderId,
                'no_struk'   => $penjualan->no_struk,
                'total'      => (float) $penjualan->total_netto,
            ]);
        } catch (\Throwable $e) {
            Log::error('Midtrans snap token error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate Snap Token: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cek status transaksi LANGSUNG ke API Midtrans (bukan simulasi).
     * Menjadi fallback otomatis bila webhook Midtrans tidak terjangkau
     * (misal aplikasi berjalan di localhost / tanpa URL publik).
     * Dipanggil polling JS dari halaman bayar.
     */
    public function cekStatus(Request $request, $id)
    {
        $penjualan = Penjualan::where('id_anggota', session('pelanggan_id'))
            ->where('id_penjualan', $id)
            ->firstOrFail();

        // Tidak lagi memfilter metode_bayar: pesanan yang sempat diganti metodenya
        // (mis. midtrans -> qris lewat fitur "Ganti Metode") TETAP harus bisa
        // disinkronkan bila pembayaran Midtrans-nya ternyata sudah masuk.
        if (!$penjualan->midtrans_order_id) {
            return response()->json(['success' => false, 'message' => 'Bukan pesanan Midtrans.'], 422);
        }

        if ($penjualan->status_bayar === 'lunas') {
            return response()->json(['success' => true, 'paid' => true, 'status_bayar' => 'lunas']);
        }

        try {
            $this->initMidtrans();
            $status = Transaction::status($penjualan->midtrans_order_id);

            $update = [
                'midtrans_payment_type'      => $status->payment_type ?? $penjualan->midtrans_payment_type,
                'midtrans_transaction_time'  => $status->transaction_time
                    ? \Carbon\Carbon::parse($status->transaction_time)
                    : $penjualan->midtrans_transaction_time,
                'midtrans_gross_amount'      => $status->gross_amount ? (float) $status->gross_amount : $penjualan->midtrans_gross_amount,
                'midtrans_status_code'       => $status->status_code ?? $penjualan->midtrans_status_code,
                'midtrans_raw_response'      => json_encode($status, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ];

            // Verifikasi nominal: hanya tandai lunas bila jumlah yang dibayar
            // cocok dengan total pesanan (toleransi Rp1) — cegah salah order_id.
            $jumlahCocok = isset($status->gross_amount)
                && abs((float) $status->gross_amount - (float) $penjualan->total_netto) < 1;

            switch ($status->transaction_status) {
                case 'settlement':
                    if ($jumlahCocok) {
                        $update['status_bayar']   = 'lunas';
                        $update['status_pesanan'] = 'diproses';
                    } else {
                        Log::warning("Midtrans nominal tidak cocok untuk {$penjualan->no_struk}: "
                            . ($status->gross_amount ?? '?') . ' vs ' . $penjualan->total_netto);
                    }
                    break;
                case 'capture':
                    if (($status->fraud_status ?? '') === 'accept' && $jumlahCocok) {
                        $update['status_bayar']   = 'lunas';
                        $update['status_pesanan'] = 'diproses';
                    }
                    break;
                case 'deny':
                case 'expire':
                case 'cancel':
                    $update['status_bayar']   = 'belum_bayar';
                    $update['status_pesanan'] = 'menunggu';
                    break;
                // 'pending' -> tetap menunggu pembayaran
            }

            $penjualan->update($update);

            return response()->json([
                'success'            => true,
                'paid'               => $penjualan->status_bayar === 'lunas',
                'status_bayar'       => $penjualan->status_bayar,
                'transaction_status' => $status->transaction_status,
                'payment_type'       => $status->payment_type,
            ]);
        } catch (\Throwable $e) {
            // Midtrans membalas 404 "Transaction doesn't exist" bila pelanggan
            // BELUM memilih metode bayar di popup (transaksi belum dibuat).
            // Ini bukan error - anggap saja belum dibayar, polling tetap jalan.
            if ((int) $e->getCode() === 404 && str_contains($e->getMessage(), "doesn't exist")) {
                return response()->json([
                    'success'            => true,
                    'paid'               => false,
                    'status_bayar'       => $penjualan->status_bayar,
                    'transaction_status' => null,
                ]);
            }

            Log::warning('Midtrans cek status error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal cek status ke Midtrans.'], 502);
        }
    }

    public function notification(Request $request)
    {
        $this->initMidtrans();

        $notification = $request->all();

        Log::info('Midtrans notification received', $notification);

        $orderId       = $notification['order_id'] ?? null;
        $statusCode    = $notification['status_code'] ?? null;
        $grossAmount   = $notification['gross_amount'] ?? null;
        $signatureKey  = $notification['signature_key'] ?? null;

        if (!$orderId || !$statusCode || !$signatureKey) {
            Log::warning('Midtrans notification missing required fields');
            return response()->json(['success' => false, 'message' => 'Invalid notification'], 400);
        }

        // Prioritas: server key dari tabel payment_settings, fallback ke config/services
        $serverKey = PaymentSetting::getMidtransConfig()['server_key']
            ?: config('services.midtrans.server_key', '');
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signatureKey !== $expectedSignature) {
            Log::warning('Midtrans signature mismatch for order_id: ' . $orderId);
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 403);
        }

        $penjualan = Penjualan::where('midtrans_order_id', $orderId)->first();
        if (!$penjualan) {
            Log::warning('Midtrans notification for unknown order_id: ' . $orderId);
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        $transactionStatus = $notification['transaction_status'] ?? null;
        $fraudStatus       = $notification['fraud_status'] ?? null;
        $paymentType       = $notification['payment_type'] ?? null;
        $transactionTime   = $notification['transaction_time'] ?? null;

        $penjualan->update([
            'midtrans_payment_type'  => $paymentType,
            'midtrans_transaction_time' => $transactionTime ? \Carbon\Carbon::parse($transactionTime) : null,
            'midtrans_gross_amount'  => $grossAmount ? (float) $grossAmount : null,
            'midtrans_signature_key' => $signatureKey,
            'midtrans_status_code'   => $statusCode,
            'midtrans_raw_response'  => json_encode($notification, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        $update = [];
        switch ($transactionStatus) {
            case 'settlement':
                if ($fraudStatus === 'accept' || $fraudStatus === null) {
                    $update['status_bayar'] = 'lunas';
                    $update['status_pesanan'] = 'diproses';
                }
                break;
            case 'capture':
                if ($fraudStatus === 'accept') {
                    $update['status_bayar'] = 'lunas';
                    $update['status_pesanan'] = 'diproses';
                }
                break;
            case 'pending':
                $update['status_bayar'] = 'belum_bayar';
                $update['status_pesanan'] = 'menunggu';
                break;
            case 'deny':
                $update['status_bayar'] = 'belum_bayar';
                $update['status_pesanan'] = 'menunggu';
                break;
            case 'expire':
                $update['status_bayar'] = 'belum_bayar';
                $update['status_pesanan'] = 'menunggu';
                break;
            case 'cancel':
                $update['status_bayar'] = 'belum_bayar';
                $update['status_pesanan'] = 'menunggu';
                break;
            default:
                break;
        }

        if ($update) {
            $penjualan->update($update);
            Log::info('Pesanan updated via Midtrans webhook: ' . $penjualan->no_struk . ' -> ' . json_encode($update));
        }

        return response()->json(['success' => true]);
    }
}
