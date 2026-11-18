# Mendaftarkan tugas Windows agar penjadwal Laravel berjalan otomatis saat login.
# Jalankan sekali dari PowerShell: powershell -ExecutionPolicy Bypass -File scripts\register-scheduler-task.ps1
# Hapus dengan: Unregister-ScheduledTask -TaskName 'BangkiangJaran-Scheduler' -Confirm:$false
$project = Split-Path -Parent $PSScriptRoot
$action = New-ScheduledTaskAction -Execute 'cmd.exe' -Argument "/c `"$project\scripts\scheduler-worker.bat`"" -WorkingDirectory $project
$trigger = New-ScheduledTaskTrigger -AtLogOn
$settings = New-ScheduledTaskSettingsSet -StartWhenAvailable -RestartCount 3 -RestartInterval (New-TimeSpan -Minutes 1) -ExecutionTimeLimit ([TimeSpan]::Zero)
Register-ScheduledTask -TaskName 'BangkiangJaran-Scheduler' -Action $action -Trigger $trigger -Settings $settings -Description 'Penjadwal Laravel Bangkiang Jaran' -Force | Out-Null
Write-Host 'Tugas BangkiangJaran-Scheduler terdaftar. Akan berjalan saat login berikutnya.'
