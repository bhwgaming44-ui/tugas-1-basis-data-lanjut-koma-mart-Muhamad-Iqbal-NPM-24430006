$ProgressPreference = 'SilentlyContinue'
$log = "c:\Users\ASUS\Documents\Dokumen\semester 5 file pdf spada\basis data lanjut semester 5\Dokumen coding python\kopma_npm\laravel_kopma\tpe2e.log"
if (Test-Path $log) { Remove-Item $log }
function W($m) { Add-Content -Path $log -Value $m }

$base = "http://127.0.0.1:8000"
$s = New-Object Microsoft.PowerShell.Commands.WebRequestSession

try {
  # 1. Login pelanggan
  $r = Invoke-WebRequest -Uri "$base/login/pelanggan" -WebSession $s -UseBasicParsing
  $token = ([regex]::Match($r.Content, 'name="_token" value="([^"]+)"')).Groups[1].Value
  $r = Invoke-WebRequest -Uri "$base/login/pelanggan" -Method POST -WebSession $s -UseBasicParsing -Body @{
    _token = $token; login = 'testerfoto@x.test'; password = 'secret123'
  }
  W "LOGIN: $($r.StatusCode)"

  # 2. Isi keranjang: 1x Beras Merah (id=1, Rp68.000)
  $tokenT = ([regex]::Match($r.Content, 'name="_token" value="([^"]+)"')).Groups[1].Value
  $r = Invoke-WebRequest -Uri "$base/belanja/tambah" -Method POST -WebSession $s -UseBasicParsing -Body @{
    _token = $tokenT; id_barang = '1'; qty = '1'
  }
  W "TAMBAH_CART: $($r.StatusCode)"

  # 3. Halaman keranjang -> saldo awal + token checkout
  $rK = Invoke-WebRequest -Uri "$base/keranjang" -WebSession $s -UseBasicParsing
  $saldoAwal = [regex]::Match($rK.Content, 'Kopma Point \(Saldo: Rp ([\d\.]+)').Groups[1].Value
  W "SALDO_AWAL: $saldoAwal CART_ITEMS: $($rK.Content -match 'Beras Merah')"

  # 4. Checkout: antar ke rumah, bayar pakai Kopma Point
  $tokenC = ([regex]::Match($rK.Content, 'name="_token" value="([^"]+)"')).Groups[1].Value
  $rC = Invoke-WebRequest -Uri "$base/belanja/checkout" -Method POST -WebSession $s -UseBasicParsing -Body @{
    _token = $tokenC; metode_bayar = 'kopma_point'; metode_antar = 'antar'
    alamat = 'Jl. Merdeka No. 10, Kost RT 03, Sukabumi'; catatan = 'titip di pos ronda'
  }
  W "CHECKOUT_REDIRECT: $($rC.BaseResponse.ResponseUri.AbsoluteUri)"

  # 5. Halaman bayar -> LUNAS + peta tujuan
  $rB = Invoke-WebRequest -Uri $rC.BaseResponse.ResponseUri.AbsoluteUri -WebSession $s -UseBasicParsing
  W "BAYAR_LUNAS: $($rB.Content -match 'SUDAH LUNAS') PETA_TUJUAN: $($rB.Content -match 'maps\.google\.com')"

  # 6. Kopma Point: saldo berkurang + mutasi pembayaran
  $r2 = Invoke-WebRequest -Uri "$base/kopma-point" -WebSession $s -UseBasicParsing
  W "MUTASI_BAYAR: $($r2.Content -match 'Pembayaran pesanan') SALDO_132K: $($r2.Content -match 'Rp 132\.000')"

  # 7. Pesanan Saya: sidebar peta + tombol rute
  $rP = Invoke-WebRequest -Uri "$base/pesanan" -WebSession $s -UseBasicParsing
  W "PESANAN_MAP_IFRAME: $($rP.Content -match 'mapFrame') LACAK: $($rP.Content -match 'Lacak Pengantaran') RUTE_BTN: $($rP.Content -match 'Buka Rute di Google Maps')"

  W "ALL_DONE"
} catch {
  W ("ERROR: " + $_.Exception.Message + " @LINE: " + $_.InvocationInfo.ScriptLineNumber)
}
