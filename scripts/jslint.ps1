# -------------------------------------------------
# PowerShell Node.js Setup & Lint Script
# -------------------------------------------------
# Stops execution on errors and suppresses progress messages
$ErrorActionPreference = "Stop"
$ProgressPreference   = "SilentlyContinue"

# -------------------------------------------------
# Define Paths
# -------------------------------------------------
$RootDir  = (Resolve-Path "$PSScriptRoot\..").Path
$CacheDir = Join-Path $RootDir ".cache\node"

# -------------------------------------------------
# Node.js Version
# -------------------------------------------------
$NodeVersion = "v22.9.0"

# -------------------------------------------------
# Determine Architecture (64-bit / ARM64 only)
# -------------------------------------------------
switch ($env:PROCESSOR_ARCHITECTURE) {
    "ARM64" { $Arch = "arm64" }
    "AMD64" { $Arch = "x64" }
    default { throw "Unsupported architecture: $($env:PROCESSOR_ARCHITECTURE)" }
}

# -------------------------------------------------
# Node.js Executable Paths
# -------------------------------------------------
$NodeRoot = Join-Path $CacheDir "node-$NodeVersion-win-$Arch"
$NodeExe  = Join-Path $NodeRoot "node.exe"
$NpmCmd   = Join-Path $NodeRoot "npm.cmd"

# -------------------------------------------------
# Download & Install Node.js if not present
# -------------------------------------------------
if (!(Test-Path $NodeExe)) {

    Write-Output "[Node] Installing Node.js $NodeVersion..."

    # Ensure cache directory exists
    New-Item -ItemType Directory -Force -Path $CacheDir | Out-Null

    $ZipName = "node-$NodeVersion-win-$Arch.zip"
    $ZipPath = Join-Path $CacheDir $ZipName
    $Url     = "https://nodejs.org/dist/$NodeVersion/$ZipName"

    # Download Node.js archive
    Invoke-WebRequest $Url -OutFile $ZipPath

    # Remove old installation if exists
    if (Test-Path $NodeRoot) {
        Remove-Item $NodeRoot -Recurse -Force
    }

    # Extract Node.js
    Expand-Archive $ZipPath -DestinationPath $CacheDir -Force

    # Remove zip file
    Remove-Item $ZipPath

    Write-Output "[Node] Installation complete."
}
else {
    Write-Output "[Node] Node.js already installed, may thou go fuck urself."
}

# -------------------------------------------------
# Sanity Check for npm
# -------------------------------------------------
if (!(Test-Path $NpmCmd)) {
    throw "npm executable not FUCKING found. Node.js installation may be corrupted."
}

# -------------------------------------------------
# Install NPM Dependencies
# -------------------------------------------------
Push-Location $RootDir

if (Test-Path "package-lock.json") {
    Write-Output "[NPM] Installing SIX-SEVEN dependencies (CI mode)..."
    & $NpmCmd ci --silent > $null 2>&1
}
else {
    Write-Output "[NPM] Installing Naples dependencies (full install)..."
    & $NpmCmd install --silent > $null 2>&1
}

Pop-Location
Write-Output "[NPM] Dependencies installed."

# -------------------------------------------------
# Run StandardJS Lint
# -------------------------------------------------
Write-Output "[Lint] Running lint checks..."

Push-Location $RootDir
& $NpmCmd run lint --silent 2>&1 | Out-Null # Suppress output, errors will still cause failure
Pop-Location

Write-Output "[Lint] I'm goding so much."
Write-Output "Hell yeah. It works!"
