<?php
function cariKlikIndomaretImage($query) {
    // Cari lewat DuckDuckGo Lite / HTML untuk gambar Klik Indomaret
    $q = urlencode("site:assets.klikindomaret.com/products " . $query);
    $url = "https://html.duckduckgo.com/html/?q=" . $q;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        CURLOPT_TIMEOUT => 8,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    if (preg_match('/https:\/\/assets\.klikindomaret\.com\/products\/[a-zA-Z0-9_\/.-]+/i', $res, $m)) {
        return $m[0];
    }
    return null;
}

$test = ["Indomie Goreng", "Bimoli", "Sariwangi", "Pepsodent", "Sunlight"];
foreach ($test as $t) {
    echo "$t: " . (cariKlikIndomaretImage($t) ?: "none") . "\n";
    usleep(200000);
}
