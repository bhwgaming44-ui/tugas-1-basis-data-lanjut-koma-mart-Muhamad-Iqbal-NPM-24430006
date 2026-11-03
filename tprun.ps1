$ErrorActionPreference = 'Continue'
try {
  & "c:\Users\ASUS\Documents\Dokumen\semester 5 file pdf spada\basis data lanjut semester 5\Dokumen coding python\kopma_npm\laravel_kopma\tpe2e.ps1"
  Write-Output "SCRIPT_DONE exit=$LASTEXITCODE"
} catch {
  Write-Output ("CAUGHT: " + $_.Exception.Message)
  Write-Output $_.ScriptStackTrace
}
