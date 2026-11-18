@echo off
REM Menjalankan penjadwal Laravel (check-out otomatis, pesanan kedaluwarsa) terus-menerus.
REM Biarkan jendela ini terbuka selama operasional, atau daftarkan lewat Task Scheduler:
REM   scripts\register-scheduler-task.ps1
cd /d "%~dp0.."
title Bangkiang Jaran - Penjadwal
php artisan schedule:work
