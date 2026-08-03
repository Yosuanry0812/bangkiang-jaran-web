@echo off
rem Backup database MySQL bangkiang_jaran ke folder backups\
set BACKUP_DIR=%~dp0..\backups
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"
set STAMP=%date:~-4%%date:~3,2%%date:~0,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set STAMP=%STAMP: =0%
"C:\xampp\mysql\bin\mysqldump.exe" -u root bangkiang_jaran > "%BACKUP_DIR%\bangkiang_jaran_%STAMP%.sql"
echo Backup selesai: %BACKUP_DIR%\bangkiang_jaran_%STAMP%.sql
pause
