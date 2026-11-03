<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$a = App\Models\Anggota::all();
echo "Total anggota: " . $a->count() . "\n";
foreach ($a as $item) {
    echo "{$item->id_anggota} | {$item->nama} | Pass: " . ($item->password ? "Ada" : "Kosong") . "\n";
}
