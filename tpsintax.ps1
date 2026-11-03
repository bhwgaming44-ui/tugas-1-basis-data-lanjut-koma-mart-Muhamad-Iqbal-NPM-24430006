$php = "C:\xampp\php\php.exe"
$base = "c:\Users\ASUS\Documents\Dokumen\semester 5 file pdf spada\basis data lanjut semester 5\Dokumen coding python\kopma_npm\laravel_kopma"
$files = @(
  'app\Providers\AppServiceProvider.php',
  'app\Models\Anggota.php',
  'app\Models\KopmaPointLog.php',
  'app\Http\Controllers\KopmaPointController.php',
  'app\Http\Controllers\BelanjaController.php',
  'app\Http\Controllers\TokoController.php',
  'routes\web.php'
)
foreach ($f in $files) {
  & $php -l (Join-Path $base $f)
}
