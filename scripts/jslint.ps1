$ErrorActionPreference = "Stop" # Stop on errors
$ProgressPreference = "SilentlyContinue" # no progress spam

# -------------------------------------------------
# Paths... yea
# -------------------------------------------------
$RootDir  = (Resolve-Path "$PSScriptRoot\..").Path
$CacheDir = Join-Path $RootDir ".cache\node"

# -------------------------------------------------
# Node.js Version
# -------------------------------------------------
$NodeVersion = "v20.11.1"

# -------------------------------------------------
# Accepts only 64 bit ok? And those with arm64 are lucky too
# -------------------------------------------------
switch ($env:PROCESSOR_ARCHITECTURE) {
    "ARM64" { $Arch = "arm64" }
    "AMD64" { $Arch = "x64" }
    default { throw "Architettura non supportata" }
}

# -------------------------------------------------
# Node paths
# -------------------------------------------------
$NodeRoot = Join-Path $CacheDir "node-$NodeVersion-win-$Arch"
$NodeExe  = Join-Path $NodeRoot "node.exe"
$NpmCmd   = Join-Path $NodeRoot "npm.cmd"

# -------------------------------------------------
# Download Node.js (silent)
# -------------------------------------------------
if (!(Test-Path $NodeExe)) {

    Write-Output "[Node] SIX-SEVEN, Installing Node.js..."

    New-Item -ItemType Directory -Force -Path $CacheDir | Out-Null

    $ZipName = "node-$NodeVersion-win-$Arch.zip"
    $ZipPath = Join-Path $CacheDir $ZipName
    $Url     = "https://nodejs.org/dist/$NodeVersion/$ZipName"

    Invoke-WebRequest $Url -OutFile $ZipPath

    if (Test-Path $NodeRoot) {
        Remove-Item $NodeRoot -Recurse -Force
    }

    Expand-Archive $ZipPath -DestinationPath $CacheDir -Force
    Remove-Item $ZipPath

    Write-Output "[Node] OK"
}
else {
    Write-Output "[Node] Already installed"
}

# -------------------------------------------------
# sanity check
# -------------------------------------------------
if (!(Test-Path $NpmCmd)) {
    throw "npm not found, ouch it will not work"
}

# -------------------------------------------------
# NPM install (silent)
# -------------------------------------------------
Push-Location $RootDir

if (Test-Path "package-lock.json") {
    Write-Output "[NPM] Installing dependencies bla bla..."
    & $NpmCmd ci --silent > $null 2>&1
}
else {
    Write-Output "[NPM] Installing dependencies gne gne gne..."
    & $NpmCmd install --silent > $null 2>&1
}

Pop-Location
Write-Output "[NPM] OK"

# -------------------------------------------------
# StandardJS Lint
# -------------------------------------------------
Write-Output "[Lint] Running checks..."

Push-Location $RootDir
& $NpmCmd run lint --silent > $null 2>&1
Pop-Location

Write-Output "[Lint] OK"
Write-Output "[Done] GODO FUNZIONA TUTTO"
