<?php

namespace App\Http\Controllers;

use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Pengaturan Payment Gateway (Midtrans) — area petugas.
 * Semua petugas (admin/manajer/kasir) boleh MELIHAT status & uji koneksi;
 * hanya admin/manajer yang boleh MENYIMPAN konfigurasi.
 */
class PaymentSettingController extends Controller
{
    /** Apakah sesi petugas sekarang berhak mengubah pengaturan? */
    private function bolehUbah(): bool
    {
        return in_array(session('kasir_role'), ['admin', 'manajer', 'petugas', 'kasir'], true) || (bool) session('kasir_id');
    }

    public function index()
    {
        $setting  = PaymentSetting::where('gateway', 'midtrans')->first();
        $bolehUbah = $this->bolehUbah();
        return view('admin.payment-setting', compact('setting', 'bolehUbah'));
    }

    public function update(Request $request)
    {
        abort_unless($this->bolehUbah(), 403, 'Hanya admin/manajer yang boleh mengubah pengaturan pembayaran.');

        $request->validate([
            'server_key'  => 'required|string|max:500',
            'client_key'  => 'required|string|max:500',
            'merchant_id' => 'required|string|max:100',
            'environment' => 'required|in:sandbox,production',
            'is_enabled'  => 'sometimes|boolean',
        ]);

        PaymentSetting::updateOrCreate(
            ['gateway' => 'midtrans'],
            [
                'server_key'  => $request->server_key,
                'client_key'  => $request->client_key,
                'merchant_id' => $request->merchant_id,
                'environment' => $request->environment,
                'is_enabled'  => $request->boolean('is_enabled'),
            ]
        );

        return back()->with('success', 'Pengaturan Midtrans berhasil disimpan. Token siap digunakan.');
    }

    /**
     * Uji koneksi: cek Server Key ke API Midtrans (Basic Auth).
     * 401/403 = kredensial ditolak; status lain berarti server key diterima.
     */
    public function testKoneksi(Request $request)
    {
        $request->validate([
            'server_key'  => 'required|string|max:500',
            'environment' => 'required|in:sandbox,production',
        ]);

        $base = $request->environment === 'production'
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';

        try {
            $resp = Http::withBasicAuth($request->server_key, '')
                ->timeout(10)
                ->withHeaders(['Accept' => 'application/json'])
                ->get($base . '/v2/');

            if (in_array($resp->status(), [401, 403], true)) {
                return back()->with('tes_gateway', [
                    'ok'    => false,
                    'pesan' => 'Server Key DITOLAK (HTTP ' . $resp->status() . ') pada mode '
                        . strtoupper($request->environment) . '. Periksa kembali Server Key di dashboard Midtrans.',
                ]);
            }

            return back()->with('tes_gateway', [
                'ok'    => true,
                'pesan' => 'Server Key DITERIMA API Midtrans ('
                    . strtoupper($request->environment) . ', HTTP ' . $resp->status()
                    . '). Konfigurasi siap digunakan.',
            ]);
        } catch (\Throwable $e) {
            return back()->with('tes_gateway', [
                'ok'    => false,
                'pesan' => 'Gagal terhubung ke server Midtrans: ' . $e->getMessage(),
            ]);
        }
    }
}
