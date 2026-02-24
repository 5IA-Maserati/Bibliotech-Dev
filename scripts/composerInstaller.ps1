# -------------------------------------------------
# PowerShell Composer Setup Script (Portable)
# -------------------------------------------------
$ErrorActionPreference = "Stop"
$ProgressPreference   = "SilentlyContinue"

# -------------------------------------------------
# Define Paths
# -------------------------------------------------
$RootDir     = (Resolve-Path "$PSScriptRoot\..").Path
$CacheDir    = Join-Path $RootDir ".cache\composer"
$ComposerPhar = Join-Path $CacheDir "composer.phar"
$ComposerBat  = Join-Path $CacheDir "composer.bat"

# -------------------------------------------------
# Ensure PHP Exists
# -------------------------------------------------
try {
    php -v > $null 2>&1
}
catch {
    throw "PHP is not installed or not in PATH."
}

# -------------------------------------------------
# Download Composer if Not Present
# -------------------------------------------------
if (!(Test-Path $ComposerPhar)) {

    Write-Output "[Composer] Installing Composer (portable)..."

    New-Item -ItemType Directory -Force -Path $CacheDir | Out-Null

    $InstallerPath = Join-Path $CacheDir "composer-setup.php"

    # Download installer
    Invoke-WebRequest "https://getcomposer.org/installer" -OutFile $InstallerPath

    # Get expected signature
    $ExpectedSig = (Invoke-WebRequest "https://composer.github.io/installer.sig" -UseBasicParsing).Content
    $ExpectedSig = [System.Text.Encoding]::UTF8.GetString($ExpectedSig).Trim()

    # Verify signature
    $ActualSig = (Get-FileHash $InstallerPath -Algorithm SHA384).Hash.ToLower()

    if ($ActualSig -ne $ExpectedSig) {
        Remove-Item $InstallerPath
        throw "Composer installer signature verification failed."
    }

    # Install composer.phar locally
    php $InstallerPath --install-dir=$CacheDir --filename=composer.phar

    Remove-Item $InstallerPath

    # Create local composer.bat wrapper
    @"
@php "%~dp0composer.phar" %*
"@ | Set-Content -Encoding ASCII $ComposerBat

    Write-Output "[Composer] Installation complete."
}
else {
    Write-Output "[Composer] Composer already installed."
}
