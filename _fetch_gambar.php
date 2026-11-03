<?php
/**
 * Ambil GAMBAR PRODUK NYATA dari Wikimedia Commons (API publik, lisensi bebas).
 * Bukan gambar AI — sesuai aturan "real product image".
 */
$pdo = new PDO('mysql:host=127.0.0.1;dbname=kopma_npm;charset=utf8mb4', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$ua = 'KopmaMartBot/1.0 (student project; coursework)';
/** Kata lemah (ukuran/generik) yang tidak boleh jadi patokan relevansi. */
function kataLemah(): array
{
    return ['botol', 'pouch', 'box', 'isi', 'g', 'kg', 'ml', 'liter', 'lembar', 'premium',
        'serbaguna', 'spesial', 'anti', 'dan', 'sachet', 'renteng', 'strip', 'kaleng',
        'refill', 'pack', 'kecil', 'besar', 'ras', 'kotak', 'jar', 'tub', 'logam', 'hitam'];
}

/** Apakah judul file Commons benar-benar cocok dengan nama produk? */
function cocok(string $judul, string $nama): bool
{
    $judul  = strtolower($judul);
    $stop   = kataLemah();
    $semua  = preg_split('/\s+/', strtolower($nama));
    $kata   = array_values(array_filter($semua, fn ($k) => strlen($k) >= 4 && !in_array($k, $stop)));
    if (!$kata) return false;

    $cocok = 0;
    foreach ($kata as $k) {
        if (preg_match('/\b' . preg_quote($k, '/') . 's?\b/', $judul)) $cocok++;
    }
    // Terima bila minimal 2 kata produk cocok, ATAU merek (kata pertama) cocok.
    $merek = $kata[0];
    return $cocok >= 2 || (bool) preg_match('/\b' . preg_quote($merek, '/') . 's?\b/', $judul);
}

/** Cari thumb URL dari Commons — hanya yang lolos filter dokumen + relevansi. */
function cariThumb(string $kata, string $nama, string $ua): ?string
{
    $url = 'https://commons.wikimedia.org/w/api.php?action=query&generator=search'
        . '&gsrsearch=' . rawurlencode($kata) . '&gsrlimit=6&gsrnamespace=6'
        . '&prop=imageinfo&iiprop=url&iiurlwidth=600&format=json';
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20,
        CURLOPT_USERAGENT => $ua, CURLOPT_FOLLOWLOCATION => true]);
    $body = curl_exec($ch); curl_close($ch);
    if (!$body) return null;
    $j = json_decode($body, true);
    $pages = $j['query']['pages'] ?? [];
    usort($pages, fn ($a, $b) => ($a['index'] ?? 99) <=> ($b['index'] ?? 99));
    foreach ($pages as $p) {
        $t = $p['imageinfo'][0]['thumburl'] ?? null;
        if (!$t) continue;
        $t = explode('?', $t)[0];
        if (preg_match('/\.(pdf|djvu|djv|svg)(\/|$)/i', $t)) continue;
        if (!preg_match('/\.(jpe?g|png|webp)$/i', $t)) continue;
        $judul = str_replace(['File:', '.jpg', '.jpeg', '.png', '.webp'], '', $p['title'] ?? '');
        if (!cocok($judul, $nama)) continue;
        return $t;
    }
    return null;
}

function kandidat(string $nama): array
{
    $k = [$nama];
    $tanpaAngka = preg_replace('/\s+/', ' ', trim(preg_replace('/\b\d[\d.,]*\s*(ml|g|kg|l|liter|lembar|isi|sheets|pcs|rim|dus|box|botol|pouch|refill|roll|strip|renteng|kaleng|pack)?\b/i', '', $nama)));
    if ($tanpaAngka !== '') $k[] = $tanpaAngka;
    $tanpaUkuran = trim(preg_replace('/\b(botol|pouch|box|kaleng|refill|strip|isi|renteng|pack|tub|jar|kotak|sachet|premium|serbaguna|spesial)\b/i', '', $tanpaAngka));
    if ($tanpaUkuran !== '') $k[] = $tanpaUkuran;
    $kata = preg_split('/\s+/', $nama);
    if (count($kata) >= 3) $k[] = implode(' ', array_slice($kata, 0, 3));
    if (count($kata) >= 2) $k[] = implode(' ', array_slice($kata, 0, 2));
    if (count($kata) >= 1) $k[] = $kata[0];
    return array_values(array_unique(array_filter($k)));
}
$rows = $pdo->query("SELECT id_barang, nama_barang FROM barangs ORDER BY id_barang")->fetchAll(PDO::FETCH_ASSOC);
echo 'Total produk: ' . count($rows) . PHP_EOL;
$isi = 0;
foreach ($rows as $r) {
    $thumb = null;
    foreach (kandidat($r['nama_barang']) as $kata) {
        $thumb = cariThumb($kata, $r['nama_barang'], $ua);
        if ($thumb) break;
        usleep(150000);
    }
    if ($thumb) {
        $pdo->prepare('UPDATE barangs SET gambar = ? WHERE id_barang = ?')->execute([$thumb, $r['id_barang']]);
        $isi++;
        echo "OK  {$r['id_barang']}  {$r['nama_barang']}\n";
    } else {
        echo "--  {$r['id_barang']}  {$r['nama_barang']} (tidak ketemu)\n";
    }
    usleep(150000);
}
echo PHP_EOL . 'Terisi: ' . $isi . ' / ' . count($rows) . PHP_EOL;
