@echo off
setlocal
cd /d "%~dp0"
title Bangkiang Jaran - Demo

echo === Bangkiang Jaran - Demo Starter ===
echo.

REM -- 1. Build frontend jika belum ada
if not exist "public\build\manifest.json" (
  echo [1/3] Build frontend...
  call npm run build
)

REM -- 2. Cek token ngrok (1 token bisa untuk 2 website, gantian)
set "NGROK_BIN=%~dp0node_modules\ngrok\bin\ngrok.exe"
set "NGROK_FALLBACK=0"
if not exist "%NGROK_BIN%" set "NGROK_FALLBACK=1"

if %NGROK_FALLBACK%==0 (
  "%NGROK_BIN%" config check >nul 2>&1
) else (
  npx ngrok config check >nul 2>&1
)
if %errorlevel% neq 0 (
  echo.
  echo Token ngrok belum terpasang.
  echo Pakai token existing dari website lain ^(gantian, bukan bareng^).
  set /p NGROK_TOKEN=Masukkan token ngrok: 
  if %NGROK_FALLBACK%==0 (
    "%NGROK_BIN%" config add-authtoken %NGROK_TOKEN%
  ) else (
    npx ngrok config add-authtoken %NGROK_TOKEN%
  )
  if %errorlevel% neq 0 (
    echo Gagal set token. Cek token di https://dashboard.ngrok.com/get-started/your-authtoken
    pause
    exit /b 1
  )
)

REM -- 3. Jalankan Laravel
echo.
echo [2/3] Menjalankan Laravel di http://localhost:8000 ...
start "Laravel 8000" cmd /k "cd /d "%~dp0" && php artisan serve --port=8000"
timeout /t 3 >nul

REM -- 4. Jalankan Ngrok
echo [3/3] Menjalankan ngrok HTTPS...
if %NGROK_FALLBACK%==0 (
  start "Ngrok HTTPS" cmd /k ""%NGROK_BIN%" http 8000"
) else (
  start "Ngrok HTTPS" cmd /k "npx ngrok http 8000"
)
timeout /t 3 >nul

echo.
echo Selesai!
echo - Lokal : http://localhost:8000
echo - HTTPS : cek jendela "Ngrok HTTPS" untuk link https://xxxx.ngrok-free.app
echo - Buka link HTTPS di HP untuk test scan kamera ^(butuh HTTPS^)
echo   Login pengelola: pengelola / Password123
echo   Login wisatawan: wisatawan / Password123
echo.
echo Tip: 1 token untuk 2 website bisa, asal gantian. Tutup jendela Ngrok sebelum buka website lain.
echo.
start http://localhost:8000
pause
