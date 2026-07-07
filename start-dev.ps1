# start-dev.ps1 - launch the full InboxPilot local stack (MySQL + Laravel + Vite)
# Usage:  right-click > Run with PowerShell   OR   ./start-dev.ps1
# Stop:   close the two spawned windows, or run ./start-dev.ps1 -Stop

param([switch]$Stop)

$ErrorActionPreference = "Stop"
$XamppPhp   = "C:\xampp\php\php.exe"
$MysqlStart = "C:\xampp\mysql_start.bat"
$ProjectDir = $PSScriptRoot

function Test-Port($port) {
    [bool](Get-NetTCPConnection -LocalPort $port -State Listen -ErrorAction SilentlyContinue)
}

if ($Stop) {
    Write-Host "Stopping Laravel (php) and Vite (node) dev processes..." -ForegroundColor Yellow
    Get-Process php  -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue
    Get-Process node -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue
    Write-Host "Done. (MySQL left running - stop it from the XAMPP Control Panel if you want.)"
    return
}

# 1) MySQL (XAMPP / MariaDB) on 3306
if (Test-Port 3306) {
    Write-Host "[1/3] MySQL already running on :3306" -ForegroundColor Green
} else {
    Write-Host "[1/3] Starting MySQL..." -ForegroundColor Cyan
    Start-Process -FilePath "cmd.exe" -ArgumentList "/c", "`"$MysqlStart`"" -WorkingDirectory "C:\xampp" -WindowStyle Minimized
    for ($i = 0; $i -lt 20; $i++) {
        Start-Sleep -Seconds 1
        if (Test-Port 3306) { break }
    }
    if (Test-Port 3306) {
        Write-Host "      MySQL is up on :3306" -ForegroundColor Green
    } else {
        Write-Host "      MySQL did NOT start. Check C:\xampp\mysql\data\mysql_error.log" -ForegroundColor Red
        Write-Host "      Known XAMPP bug: delete multi-master.info + master-*.info + relay-log-*.info from the data dir, then retry." -ForegroundColor Red
        return
    }
}

# 2) Laravel app server on 8000
if (Test-Port 8000) {
    Write-Host "[2/3] Laravel already serving on :8000" -ForegroundColor Green
} else {
    Write-Host "[2/3] Starting Laravel (php artisan serve) on :8000..." -ForegroundColor Cyan
    Start-Process -FilePath $XamppPhp -ArgumentList "artisan","serve","--host=127.0.0.1","--port=8000" -WorkingDirectory $ProjectDir
}

# 3) Vite dev server on 5173
if (Test-Port 5173) {
    Write-Host "[3/3] Vite already running on :5173" -ForegroundColor Green
} else {
    Write-Host "[3/3] Starting Vite (npm run dev) on :5173..." -ForegroundColor Cyan
    Start-Process -FilePath "cmd.exe" -ArgumentList "/k", "npm run dev" -WorkingDirectory $ProjectDir
}

Write-Host ""
Write-Host "All set. Open  http://localhost:8000" -ForegroundColor Green
Write-Host "Stop everything later with:  ./start-dev.ps1 -Stop"
