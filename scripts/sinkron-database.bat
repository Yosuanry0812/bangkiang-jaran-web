@echo off
rem Menyamakan database MySQL lokal dengan repo: buat DB (XAMPP), migrasi, lalu isi data snapshot.
rem Jalankan dari mana saja: scripts\sinkron-database.bat [ya]
rem PERINGATAN: seeder MENGGANTI isi tabel users, tiket, pemesanan, detail_pemesanan, pembayaran.
rem Prasyarat: MySQL (XAMPP) menyala, PHP dan Composer terpasang.

cd /d "%~dp0.."

if /i not "%~1"=="ya" (
    echo Skrip ini akan MENGGANTI data users, tiket, pemesanan, detail_pemesanan, pembayaran.
    set /p OK=Lanjut? ketik ya :
    if /i not "%OK%"=="ya" ( echo Dibatalkan. & exit /b 1 )
)

if not exist .env (
    copy .env.example .env >nul
    echo .env dibuat dari .env.example. Sesuaikan DB_USERNAME/DB_PASSWORD bila perlu.
)
if not exist vendor ( call composer install --no-interaction || exit /b 1 )

findstr /b /c:"APP_KEY=base64:" .env >nul || call php artisan key:generate --force

if exist "C:\xampp\mysql\bin\mysql.exe" (
    "C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS bangkiang_jaran CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
)

call php artisan migrate --force || exit /b 1
call php artisan db:snapshot-import || exit /b 1
call php artisan storage:link 2>nul
echo Selesai. Database sama dengan snapshot di repo.
