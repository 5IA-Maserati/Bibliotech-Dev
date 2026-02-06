@echo off
setlocal EnableExtensions EnableDelayedExpansion

REM --------------------------------------------------------------------
REM Run JavaScript linter via PowerShell
REM --------------------------------------------------------------------

REM %~dp0 = directory of this script
REM Go back one level to project root
set ROOT=%~dp0..
cd /d "%ROOT%" || exit /b 1

REM Check if PowerShell is available
where powershell >nul 2>&1 || (
  echo [ERROR] PowerShell not found in PATH.
  exit /b 1
)

REM Execute the linter script
powershell ^
  -NoLogo ^
  -NoProfile ^
  -NonInteractive ^
  -ExecutionPolicy Bypass ^
  -File "scripts\jslint.ps1"

REM Capture and return the linter exit code
set EXITCODE=%ERRORLEVEL%
exit /b %EXITCODE%
