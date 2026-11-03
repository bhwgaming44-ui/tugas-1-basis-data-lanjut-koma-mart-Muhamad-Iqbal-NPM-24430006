$ProgressPreference='SilentlyContinue'
$base='http://127.0.0.1:8000'
$s=New-Object Microsoft.PowerShell.Commands.WebRequestSession
$r=Invoke-WebRequest "$base/login/pelanggan" -WebSession $s -UseBasicParsing
$t=([regex]::Match($r.Content,'name="_token" value="([^"]+)"')).Groups[1].Value
# login akun milik pesanan 8 (anggota 10) - coba dulu testerfoto (anggota 9) utk pesanan 9 sblmnya
# utk pesanan 8 anggota 10, kita cek apakah bisa diakses; jika tidak, uji logika via pesanan 9 stlh dibatalkan bayar
# Cara paling andal: buat pesanan BARU belum bayar dgn metode transfer
$r=Invoke-WebRequest "$base/login/pelanggan" -Method POST -WebSession $s -UseBasicParsing -Body @{_token=$t;login='testerfoto@x.test';password='secret123'}
$tt=([regex]::Match($r.Content,'name="_token" value="([^"]+)"')).Groups[1].Value
Invoke-WebRequest "$base/belanja/tambah" -Method POST -WebSession $s -UseBasicParsing -Body @{_token=$tt;id_barang='2';qty='1'} | Out-Null
$rK=Invoke-WebRequest "$base/keranjang" -WebSession $s -UseBasicParsing
$tc=([regex]::Match($rK.Content,'name="_token" value="([^"]+)"')).Groups[1].Value
$rC=Invoke-WebRequest "$base/belanja/checkout" -Method POST -WebSession $s -UseBasicParsing -Body @{_token=$tc;metode_bayar='transfer';metode_antar='antar';alamat='Jl. Kenanga No. 5, Cikole';catatan='hubungi satpam'}
$uri=$rC.BaseResponse.ResponseUri.AbsoluteUri
$rB=Invoke-WebRequest $uri -WebSession $s -UseBasicParsing
"BARU($uri) -> BAYAR_SEKARANG: $($rB.Content -match 'Saya Sudah Bayar') RINCIAN: $($rB.Content -match 'Rincian Pesanan') PETA: $($rB.Content -match 'Tujuan Pengantaran') NO_ERROR: (-not $($rB.Content -match 'Whoops|syntax error'))"
