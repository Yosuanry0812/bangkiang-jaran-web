@echo off
cd /d "%~dp0.."
php artisan tourism:backup
if errorlevel 1 (
    echo Backup gagal. Periksa konfigurasi dan izin direktori privat.
    exit /b 1
)
echo Backup database dan berkas selesai diverifikasi.
