@echo off
setlocal EnableExtensions EnableDelayedExpansion

REM %~dp0 = directory of this script and go back one level
set ROOT=%~dp0..
cd /d "%ROOT%" || exit /b 1

REM Find PowerShell
where powershell >nul 2>&1 || (
  echo [ERRORE] DOVE CAZZO È POWERSHELL.
  exit /b 1
)

REM Run jslint.ps1
powershell ^
  -NoLogo ^
  -NoProfile ^
  -NonInteractive ^
  -ExecutionPolicy Bypass ^
  -File "scripts\jslint.ps1"

set EXITCODE=%ERRORLEVEL%
exit /b %EXITCODE%
