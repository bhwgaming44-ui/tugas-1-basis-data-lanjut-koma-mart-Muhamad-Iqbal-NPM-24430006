<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->pastikanMysqlHidup();
        View::share('waBusinessNumber', $this->nomorWaBisnis());
        View::share('waBusinessName', (string) config('services.wa.name', 'KOPMA MART'));
        // Skema ERD dibuat HANYA via migration (2026_10_17 selaras ERD).
        // Jangan auto-create tabel di sini agar tidak bentrok dengan ERD.
    }

    private function pastikanMysqlHidup(): void
    {
        if ($this->dbSiap()) {
            return;
        }

        $this->nyalakanMysqld();

        // Tunggu maksimal ~20 detik sampai MySQL benar-benar siap.
        for ($i = 0; $i < 20; $i++) {
            sleep(1);
            if ($this->dbSiap()) {
                return;
            }
        }
        // Kalau tetap gagal, biarkan error Laravel tampil seperti biasa
        // (pesan error tetap jelas, bukan halaman kosong).
    }

    /** Cek cepat: bisa ping database? */
    private function dbSiap(): bool
    {
        try {
            DB::purge('mysql');
            DB::reconnect('mysql');
            DB::connection('mysql')->getPdo();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Nyalakan mysqld XAMPP di background tanpa jendela baru.
     * Berjalan di Windows (cara utama) + fallback Linux/Mac.
     */
    private function nyalakanMysqld(): void
    {
        try {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $mysqld = 'C:\\xampp\\mysql\\bin\\mysqld.exe';
                $myIni  = 'C:\\xampp\\mysql\\bin\\my.ini';
                if (!is_file($mysqld)) {
                    return;
                }
                $cmd = 'start "" /B "' . $mysqld . '" --defaults-file="' . $myIni . '" --standalone';
                @pclose(@popen($cmd, 'r'));
            } else {
                @exec('(service mysql start || service mariadb start || mysqld_safe --skip-grant-tables &) > /dev/null 2>&1 &');
            }
        } catch (\Throwable $e) {
            // Abaikan â€” pesan error DB tetap tampil bila benar-benar gagal.
        }
    }

    /**
     * Normalisasi ke format internasional: hanya digit, awalan 62.
     * "0812..." -> "62812...", "+62..." -> "62...", "62..." tetap.
     */
    private function nomorWaBisnis(): string
    {
        $raw = (string) config('services.wa.number', '');
        $digit = preg_replace('/\D+/', '', $raw) ?? '';
        if ($digit === '') {
            return '';
        }
        if (str_starts_with($digit, '0')) {
            $digit = '62' . substr($digit, 1);
        } elseif (str_starts_with($digit, '8')) {
            $digit = '62' . $digit;
        }
        return $digit;
    }
}
