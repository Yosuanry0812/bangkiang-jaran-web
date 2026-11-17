param(
    [Parameter(Mandatory = $true)]
    [ValidateSet('Mail', 'Scheduler')][string]$Role,
    [Parameter(Mandatory = $true)][string]$PhpBinary
)

$ErrorActionPreference = 'Stop'
$projectRoot = (Resolve-Path -LiteralPath (Join-Path $PSScriptRoot '..')).Path
$artisan = Join-Path $projectRoot 'artisan'
$php = (Resolve-Path -LiteralPath $PhpBinary).Path
$runtime = Join-Path $projectRoot 'storage\app\private\operations'
New-Item -ItemType Directory -Force -Path $runtime | Out-Null
Set-Location -LiteralPath $projectRoot
$roleName = $Role.ToLowerInvariant()
$stateFile = Join-Path $runtime "$roleName.json"
$stopFile = Join-Path $runtime "$roleName.stop"
$hash = [System.Security.Cryptography.SHA256]::Create()
try { $identity = ([BitConverter]::ToString($hash.ComputeHash([Text.Encoding]::UTF8.GetBytes($projectRoot.ToLowerInvariant())))).Replace('-', '').Substring(0, 12) }
finally { $hash.Dispose() }
$mutex = New-Object System.Threading.Mutex($false, "Local\BangkiangJaran-$identity-$roleName")
$ownsMutex = $false
$child = $null

function Write-OperationState([string]$State, [int]$ChildId = 0, [int]$LastExit = 0) {
    $state = @{ state = $State; pid = $PID; child_pid = $ChildId; updated_at = [DateTime]::UtcNow.ToString('o'); last_exit = $LastExit }
    $temporary = "$stateFile.$PID.tmp"
    [IO.File]::WriteAllText($temporary, ($state | ConvertTo-Json -Compress))
    Move-Item -LiteralPath $temporary -Destination $stateFile -Force
}

function Test-ApplicationReady {
    try {
        $output = & $php $artisan operations:check --json --no-ansi 2>$null
        if ($LASTEXITCODE -ne 0) { return $false }
        $check = ($output -join "`n") | ConvertFrom-Json
        return ($check.ready -and $check.database.driver -eq 'mysql' -and $check.database.name -eq 'bangkiang_jaran')
    } catch { return $false }
}

try {
    try { $ownsMutex = $mutex.WaitOne(0) } catch [System.Threading.AbandonedMutexException] { $ownsMutex = $true }
    if (-not $ownsMutex) { exit 0 }
    Write-OperationState 'starting'
    while (-not (Test-Path -LiteralPath $stopFile)) {
        if (-not (Test-ApplicationReady)) {
            Write-OperationState 'waiting_for_database_schema'
            for ($tick = 0; $tick -lt 30 -and -not (Test-Path -LiteralPath $stopFile); $tick++) { Start-Sleep -Seconds 1 }
            continue
        }
        if ($Role -eq 'Mail') {
            # One job per child allows a requested stop to finish the current email.
            $command = 'queue:work database --queue=mail --once --sleep=0 --tries=5 --timeout=60 --no-interaction --no-ansi'
        } else {
            $command = 'schedule:run --no-interaction --no-ansi'
        }
        $arguments = '"' + $artisan + '" ' + $command
        $child = Start-Process -FilePath $php -ArgumentList $arguments -WorkingDirectory $projectRoot -WindowStyle Hidden -PassThru -RedirectStandardOutput (Join-Path $runtime "$roleName-last-run.log") -RedirectStandardError (Join-Path $runtime "$roleName-last-error.log")
        $startedAt = [DateTime]::UtcNow
        Write-OperationState 'running' $child.Id
        while (-not $child.WaitForExit(1000)) {
            Write-OperationState 'running' $child.Id
            # SMTP has a 15 second timeout; prevent a broken child from living forever.
            if (([DateTime]::UtcNow - $startedAt).TotalSeconds -gt 180) {
                $child.Kill()
                $child.WaitForExit()
            }
        }
        $child.WaitForExit()
        $lastExit = $child.ExitCode
        $child.Dispose()
        $child = $null
        Write-OperationState 'idle' 0 $lastExit
        $pause = if ($Role -eq 'Mail') { 3 } else { 60 - [DateTime]::Now.Second }
        if ($lastExit -ne 0) { $pause = 30 }
        for ($tick = 0; $tick -lt $pause -and -not (Test-Path -LiteralPath $stopFile); $tick++) { Start-Sleep -Seconds 1 }
    }
    Write-OperationState 'stopped'
} catch {
    # No exception text: process/environment failures may contain connection details.
    if ($ownsMutex) { Write-OperationState 'supervisor_failed' 0 1 }
    exit 1
} finally {
    if ($null -ne $child) {
        if (-not $child.HasExited) { $child.Kill(); $child.WaitForExit() }
        $child.Dispose()
    }
    if ($ownsMutex) { $mutex.ReleaseMutex() }
    $mutex.Dispose()
}
