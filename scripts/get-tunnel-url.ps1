# Helper script to retrieve active ngrok public HTTPS URL
$maxRetries = 20
$ngrokUrl = ""

for ($i = 0; $i -lt $maxRetries; $i++) {
    Start-Sleep -Milliseconds 800
    try {
        $res = Invoke-RestMethod -Uri "http://127.0.0.1:4040/api/tunnels" -ErrorAction Stop
        if ($res.tunnels -and $res.tunnels.Count -gt 0) {
            $tunnel = $res.tunnels | Where-Object { $_.public_url -like 'https://*' } | Select-Object -First 1
            if (-not $tunnel) {
                $tunnel = $res.tunnels[0]
            }
            if ($tunnel.public_url) {
                $ngrokUrl = $tunnel.public_url
                break
            }
        }
    } catch {
        # Waiting for ngrok web service
    }
}

$localIp = (Get-NetIPAddress -AddressFamily IPv4 | Where-Object { $_.InterfaceAlias -notmatch "Loopback|vEthernet" -and $_.IPAddress -notlike "169.254*" } | Select-Object -First 1).IPAddress

Write-Host ""
Write-Host "====================================================================" -ForegroundColor Green
Write-Host "          BANGKIANG JARAN - UJI COBA SCAN QR VIA HP" -ForegroundColor Green
Write-Host "====================================================================" -ForegroundColor Green
Write-Host ""
Write-Host "1. Akses Lokal (Komputer ini):" -ForegroundColor Cyan
Write-Host "   - http://localhost:8000" -ForegroundColor White
if ($localIp) {
    Write-Host "   - http://${localIp}:8000 (Jaringan Wi-Fi/Lokal)" -ForegroundColor White
}
Write-Host ""
if ($ngrokUrl) {
    Write-Host "2. Link HTTPS Publik untuk HP (Wajib untuk Akses Kamera):" -ForegroundColor Yellow
    Write-Host "   $ngrokUrl" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "   Link Langsung Halaman Scan Tiket Pengelola:" -ForegroundColor Green
    Write-Host "   $ngrokUrl/pengelola/scan" -ForegroundColor Green
} else {
    Write-Host "2. Menunggu tunnel ngrok aktif..." -ForegroundColor Yellow
    Write-Host "   Silakan periksa jendela 'Ngrok Tunnel' untuk melihat status koneksi." -ForegroundColor Yellow
}
Write-Host ""
Write-Host "3. Akun Pengelola untuk Uji Coba Scan:" -ForegroundColor Cyan
Write-Host "   - Username : pengelola" -ForegroundColor White
Write-Host "   - Password : Password123" -ForegroundColor White
Write-Host ""
Write-Host "4. Langkah Pengujian di HP:" -ForegroundColor Cyan
Write-Host "   a. Buka link ($ngrokUrl/pengelola/scan) di browser HP (Chrome/Safari)." -ForegroundColor White
Write-Host "   b. Login menggunakan akun pengelola di atas." -ForegroundColor White
Write-Host "   c. Di halaman Scan Tiket, klik tombol 'Scan via Kamera'." -ForegroundColor White
Write-Host "   d. Berikan izin saat browser HP meminta akses kamera." -ForegroundColor White
Write-Host "   e. Kamera belakang HP akan aktif dan siap scan QR code tiket." -ForegroundColor White
Write-Host "====================================================================" -ForegroundColor Green
Write-Host ""
