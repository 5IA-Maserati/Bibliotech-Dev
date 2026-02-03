@echo off
setlocal

set NODE_DIR=%~dp0.cache\node\node-v20.11.1-win-x64
set PATH=%NODE_DIR%;%PATH%

"%NODE_DIR%\npm.cmd" %*
endlocal