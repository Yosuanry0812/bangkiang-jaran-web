@echo off
setlocal enabledelayedexpansion
cd /d "%~dp0"
title Bangkiang Jaran - Uji Coba Scan QR Tiket via HP

echo ====================================================================
echo        BANGKIANG JARAN - SETUP UJI COBA SCAN QR VIA HP
echo ====================================================================
echo.

REM -- 1. Cek binary ngrok
set "NGROK_BIN=%~dp0node_modules\ngrok\bin\ngrok.exe"
if not exist "%NGROK_BIN%" (
  if exist "%LOCALAPPDATA%\Microsoft\WinGet\Packages\Ngrok.Ngrok_Microsoft.Winget.Source_8wekyb3d8bbwe\ngrok.exe" (
    set "NGROK_BIN=%LOCALAPPDATA%\Microsoft\WinGet\Packages\Ngrok.Ngrok_Microsoft.Winget.Source_8wekyb3d8bbwe\ngrok.exe"
  ) else (
    set "NGROK_BIN=ngrok"
  )
)

REM -- 2. Cek token di .env jika ada
if exist ".env" (
  for /f "tokens=1,2 delims==" %%A in ('findstr /i "^NGROK_AUTHTOKEN=" .env') do (
    set "ENV_TOKEN=%%B"
  )
  if defined ENV_TOKEN (
    if not "!ENV_TOKEN!"=="" (
      echo Mengonfigurasi token ngrok dari .env...
      "%NGROK_BIN%" config add-authtoken !ENV_TOKEN! >nul 2>&1
    )
  )
)

REM -- 3. Cek apakah token ngrok sudah terpasang
"%NGROK_BIN%" config check >nul 2>&1
if %errorlevel% neq 0 (
  echo.
  echo [PENTING] Token ngrok belum terpasang.
  echo Ngrok membutuhkan authtoken gratis agar link HTTPS bisa aktif.
  echo 1. Daftar/Login gratis di: https://dashboard.ngrok.com/signup
  echo 2. Ambil token di: https://dashboard.ngrok.com/get-started/your-authtoken
  echo.
  set /p NGROK_TOKEN=Masukkan Authtoken Ngrok Anda: 
  "%NGROK_BIN%" config add-authtoken !NGROK_TOKEN!
  if %errorlevel% neq 0 (
    echo Gagal menyimpan token. Silakan periksa kembali token Anda.
    pause
    exit /b 1
  )
  echo Token berhasil disimpan!
  echo.
)

REM -- 4. Build frontend jika belum ada
if not exist "public\build\manifest.json" (
  echo Mempersiapkan aset frontend...
  call npm run build
)

REM -- 5. Jalankan Laravel di background
echo [1/2] Menjalankan server Laravel di port 8000 (host: 0.0.0.0)...
start "Laravel Server" cmd /k "cd /d "%~dp0" && php artisan serve --host=0.0.0.0 --port=8000"
timeout /t 2 >nul

REM -- 6. Jalankan Ngrok di background
echo [2/2] Menjalankan Ngrok HTTPS tunnel...
start "Ngrok Tunnel" cmd /k ""%NGROK_BIN%" http 8000"
timeout /t 3 >nul

REM -- 7. Panggil helper untuk menampilkan link dan petunjuk di layar
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0scripts\get-tunnel-url.ps1"

pause
