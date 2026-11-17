param(
    [ValidateSet('Install', 'Start', 'Stop', 'Status', 'Uninstall')][string]$Action = 'Status',
    [string]$PhpBinary = 'php'
)

$ErrorActionPreference = 'Stop'
$projectRoot = (Resolve-Path -LiteralPath (Join-Path $PSScriptRoot '..')).Path
$runner = Join-Path $PSScriptRoot 'run-local-operation.ps1'
$runtime = Join-Path $projectRoot 'storage\app\private\operations'
New-Item -ItemType Directory -Force -Path $runtime | Out-Null
$php = (Get-Command $PhpBinary -CommandType Application -ErrorAction Stop).Source
$powerShell = Join-Path $PSHOME 'powershell.exe'
if (-not (Test-Path -LiteralPath $powerShell)) { $powerShell = (Get-Command powershell.exe -CommandType Application).Source }
$hash = [System.Security.Cryptography.SHA256]::Create()
try { $identity = ([BitConverter]::ToString($hash.ComputeHash([Text.Encoding]::UTF8.GetBytes($projectRoot.ToLowerInvariant())))).Replace('-', '').Substring(0, 12) }
finally { $hash.Dispose() }
$startup = [Environment]::GetFolderPath('Startup')
$userId = [Security.Principal.WindowsIdentity]::GetCurrent().Name

function Get-RunnerArguments([string]$Role) {
    return '-NoProfile -NonInteractive -ExecutionPolicy Bypass -WindowStyle Hidden -File "' + $runner + '" -Role ' + $Role + ' -PhpBinary "' + $php + '"'
}

function Get-OperationState([string]$Role) {
    $file = Join-Path $runtime ($Role.ToLowerInvariant() + '.json')
    if (Test-Path -LiteralPath $file) {
        try { return Get-Content -LiteralPath $file -Raw | ConvertFrom-Json } catch { }
    }
    return $null
}

function Test-OwnSupervisor($State, [string]$Role) {
    if (-not $State) { return $false }
    $process = Get-CimInstance Win32_Process -Filter "ProcessId = $([int]$State.pid)" -ErrorAction SilentlyContinue
    return ($process -and $process.ExecutablePath -eq $powerShell -and $process.CommandLine.Contains($runner) -and $process.CommandLine.Contains("-Role $Role "))
}

foreach ($role in @('Mail', 'Scheduler')) {
    $roleName = $role.ToLowerInvariant()
    $taskName = "BangkiangJaran-$identity-$role"
    $stopFile = Join-Path $runtime "$roleName.stop"
    $shortcut = Join-Path $startup "$taskName.lnk"
    $task = Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue
    if ($Action -eq 'Install') {
        try {
            $taskAction = New-ScheduledTaskAction -Execute $powerShell -Argument (Get-RunnerArguments $role) -WorkingDirectory $projectRoot
            $trigger = New-ScheduledTaskTrigger -AtLogOn -User $userId
            $principal = New-ScheduledTaskPrincipal -UserId $userId -LogonType Interactive -RunLevel Limited
            $settings = New-ScheduledTaskSettingsSet -ExecutionTimeLimit ([TimeSpan]::Zero) -MultipleInstances IgnoreNew -RestartCount 3 -RestartInterval (New-TimeSpan -Minutes 1) -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries
            $task = Register-ScheduledTask -TaskName $taskName -Action $taskAction -Trigger $trigger -Principal $principal -Settings $settings -Description 'Worker/scheduler proyek Bangkiang Jaran; menunggu schema yang diperlukan.' -Force
            Write-Output "$role : task Windows terdaftar untuk pengguna saat ini."
        } catch {
            # Current-user Startup is usable without administrator/task registration rights.
            $shell = New-Object -ComObject WScript.Shell
            $link = $shell.CreateShortcut($shortcut)
            $link.TargetPath = $powerShell
            $link.Arguments = Get-RunnerArguments $role
            $link.WorkingDirectory = $projectRoot
            $link.Save()
            Write-Output "$role : autostart Startup pengguna disiapkan; registrasi task tidak diizinkan."
        }
    }
    if ($Action -in @('Install', 'Start')) {
        if (Test-Path -LiteralPath $stopFile) { Remove-Item -LiteralPath $stopFile }
        $state = Get-OperationState $role
        if (-not (Test-OwnSupervisor $state $role)) {
            if ($task) {
                Start-ScheduledTask -TaskName $taskName
            } else {
                Start-Process -FilePath $powerShell -ArgumentList (Get-RunnerArguments $role) -WorkingDirectory $projectRoot -WindowStyle Hidden | Out-Null
            }
        }
    }
    if ($Action -in @('Stop', 'Uninstall')) {
        [IO.File]::WriteAllText($stopFile, [DateTime]::UtcNow.ToString('o'))
        $state = Get-OperationState $role
        $deadline = [DateTime]::UtcNow.AddSeconds(190)
        while ((Test-OwnSupervisor $state $role) -and [DateTime]::UtcNow -lt $deadline) { Start-Sleep -Seconds 1 }
        if (Test-OwnSupervisor $state $role) { throw "$role belum berhenti; proses lain tidak dihentikan." }
    }
    if ($Action -eq 'Uninstall') {
        if ($task) { Unregister-ScheduledTask -TaskName $taskName -Confirm:$false }
        if (Test-Path -LiteralPath $shortcut) { Remove-Item -LiteralPath $shortcut }
    }
    if ($Action -eq 'Status') {
        $state = Get-OperationState $role
        [pscustomobject]@{
            Role = $role
            Autostart = $(if ($task) { 'ScheduledTask' } elseif (Test-Path -LiteralPath $shortcut) { 'Startup' } else { 'belum dipasang' })
            SupervisorAlive = [bool](Test-OwnSupervisor $state $role)
            State = $(if ($state) { $state.state } else { 'belum dimulai' })
            UpdatedUtc = $(if ($state) { $state.updated_at } else { $null })
        }
    }
}
