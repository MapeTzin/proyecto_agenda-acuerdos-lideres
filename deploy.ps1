param(
    [string]$Commit = 'HEAD',
    [string]$DebianHost = '192.168.100.14',
    [string]$DebianUser = 'mapetzinweb',
    [switch]$SkipTests,
    [switch]$SkipBuild,
    [switch]$SaveVault,
    [string]$LogFile = ''
)

$ErrorActionPreference = 'Stop'
try {
    $Host.UI.RawUI.WindowTitle = "Despliegue Agenda de Acuerdos -> WEB01 ($DebianHost)"
} catch {}

function Write-Step([string]$Msg, [string]$Color = 'Cyan') {
    Write-Host $Msg -ForegroundColor $Color
    if ($LogFile) {
        Add-Content -LiteralPath $LogFile -Value ("[{0}] {1}" -f (Get-Date -Format 'HH:mm:ss'), $Msg) -Encoding utf8
    }
}

if ($DebianHost -ne '192.168.100.14' -or $DebianUser -ne 'mapetzinweb') {
    throw 'Este script de despliegue esta restringido exclusivamente a mapetzinweb@192.168.100.14 (WEB01).'
}

$RepoRoot = $PSScriptRoot
Push-Location $RepoRoot
try {
    if (-not $SkipBuild) {
        if (-not (Test-Path -LiteralPath (Join-Path $RepoRoot 'node_modules'))) {
            Write-Step '0/6 Instalando dependencias Node (npm ci)...'
            npm ci
            if ($LASTEXITCODE -ne 0) { throw 'Fallo la instalacion de dependencias Node (npm ci).' }
        }
        Write-Step '1/6 Compilando assets de produccion con Vite...'
        npm run build
        if ($LASTEXITCODE -ne 0) { throw 'Fallo la compilacion de Vite (npm run build).' }
    }

    if (-not $SkipTests) {
        Write-Step '2/6 Verificando sintaxis PHP y autoload...'
        php -l app/Providers/AppServiceProvider.php | Out-Null
        php -l routes/web.php | Out-Null
        php -l config/database.php | Out-Null
        php -l app/Livewire/KpisAreas.php | Out-Null
        php -l ops/verify-agenda-release.php | Out-Null
        if ($LASTEXITCODE -ne 0) { throw 'Fallo la validacion de sintaxis PHP.' }
    }

    $FullCommit = (git rev-parse "$Commit^{commit}").Trim()
    $ShortCommit = $FullCommit.Substring(0, 7)
    $ScratchDir = Join-Path ([System.IO.Path]::GetTempPath()) ("agenda-deploy-" + $ShortCommit + "-" + [Guid]::NewGuid().ToString('N').Substring(0, 8))
    New-Item -ItemType Directory -Path $ScratchDir -Force | Out-Null

    try {
        $LocalArtifact = Join-Path $ScratchDir 'agenda-artifact.tar.gz'
        Write-Step "3/6 Construyendo artefacto deterministico desde commit $FullCommit..."
        $BuildJson = python (Join-Path $RepoRoot 'ops\build-agenda-artifact.py') --commit $FullCommit --output $LocalArtifact
        if ($LASTEXITCODE -ne 0) { throw 'Fallo ops/build-agenda-artifact.py.' }
        $BuildInfo = $BuildJson | ConvertFrom-Json
        $Digest = $BuildInfo.sha256

        # Obtener y validar credencial sudo para mapetzinweb@192.168.100.14
        $VaultPath = Join-Path $env:USERPROFILE '.codex\credentials\WEB01-admin.clixml'
        $SudoPassword = $null
        $CredToSave = $null
        if (Test-Path -LiteralPath $VaultPath) {
            $VaultObj = Import-Clixml -LiteralPath $VaultPath
            if ($VaultObj -and $VaultObj.LinuxAdmin) {
                $SudoPassword = $VaultObj.LinuxAdmin.GetNetworkCredential().Password
            }
        }

        if ([string]::IsNullOrEmpty($SudoPassword)) {
            if (-not [Environment]::UserInteractive -or [Console]::IsInputRedirected) {
                throw "No existe boveda DPAPI en $VaultPath y la sesion actual no tiene terminal interactivo para solicitar la contrasena sudo de $DebianUser."
            }
            Write-Host ''
            Write-Host '=========================================================================' -ForegroundColor Yellow
            Write-Host "  AUTORIZACION SUDO REQUERIDA EN WEB01 ($DebianUser@$DebianHost)" -ForegroundColor Yellow
            Write-Host "  Commit a publicar: $ShortCommit | SHA-256: $($Digest.Substring(0, 16))..." -ForegroundColor Yellow
            Write-Host '=========================================================================' -ForegroundColor Yellow
            for ($attempt = 1; $attempt -le 3; $attempt++) {
                $SecPass = Read-Host -Prompt "Contrasena sudo de $DebianUser ($attempt/3)" -AsSecureString
                if (-not $SecPass -or $SecPass.Length -eq 0) { continue }
                $CandidateCred = New-Object System.Management.Automation.PSCredential ($DebianUser, $SecPass)
                $CandidatePass = $CandidateCred.GetNetworkCredential().Password

                Write-Host 'Verificando credencial sudo en WEB01...' -ForegroundColor DarkGray
                $CheckPayload = @{
                    host     = $DebianHost
                    user     = $DebianUser
                    sudo     = $true
                    password = $CandidatePass
                    command  = 'id -u'
                } | ConvertTo-Json -Compress
                $CheckOut = $CheckPayload | python (Join-Path $RepoRoot 'ops\ssh-web01.py') 2>$null
                if ($LASTEXITCODE -eq 0) {
                    $SudoPassword = $CandidatePass
                    if ($SaveVault) { $CredToSave = $CandidateCred }
                    Write-Step 'Credencial sudo verificada correctamente.' 'Green'
                    break
                }
                Write-Host 'Contrasena sudo incorrecta. Intente nuevamente.' -ForegroundColor Red
            }
            if ([string]::IsNullOrEmpty($SudoPassword)) {
                throw 'No se pudo autenticar sudo en WEB01 tras 3 intentos.'
            }
        }

        if ($CredToSave) {
            $VaultDir = Split-Path -Parent $VaultPath
            if (-not (Test-Path -LiteralPath $VaultDir)) {
                New-Item -ItemType Directory -Path $VaultDir -Force | Out-Null
            }
            [PSCustomObject]@{
                Server       = $DebianHost
                CreatedAtUtc = [DateTime]::UtcNow.ToString('o')
                LinuxAdmin   = $CredToSave
            } | Export-Clixml -LiteralPath $VaultPath
            $CredToSave = $null
        }

        $RemoteArtifact = "/var/tmp/agenda-artifact-$Digest.tar.gz"
        $RemotePublisher = "/var/tmp/agenda-publish-$ShortCommit.sh"
        $RemoteVerifier = "/var/tmp/agenda-verify-$ShortCommit.php"

        Write-Step "4/6 Transfiriendo artefacto ($Digest) y publicando en WEB01 ($DebianHost)..."
        $Payload = @{
            host     = $DebianHost
            user     = $DebianUser
            sudo     = $true
            password = $SudoPassword
            uploads  = @(
                @{ local = $LocalArtifact; remote = $RemoteArtifact; overwrite = $true },
                @{ local = (Join-Path $RepoRoot 'ops\publish-agenda-artifact.sh'); remote = $RemotePublisher; overwrite = $true },
                @{ local = (Join-Path $RepoRoot 'ops\verify-agenda-release.php'); remote = $RemoteVerifier; overwrite = $true }
            )
            command  = "bash $RemotePublisher $RemoteArtifact $Digest $FullCommit $RemoteVerifier && rm -f $RemoteArtifact $RemotePublisher $RemoteVerifier"
        } | ConvertTo-Json -Depth 5 -Compress

        $RemoteOut = $Payload | python (Join-Path $RepoRoot 'ops\ssh-web01.py')
        $ExitCode = $LASTEXITCODE
        $SudoPassword = $null
        $Payload = $null
        if ($ExitCode -ne 0) {
            Write-Step "Error remoto en WEB01: $RemoteOut" 'Red'
            throw 'Fallo la publicacion remota en WEB01; se ejecuto rollback automatico si procedia.'
        }

        $RemoteResult = $RemoteOut | ConvertFrom-Json
        Write-Step $RemoteResult.stdout 'Green'

        Write-Step '5/6 Verificando respuesta HTTPS externa hacia https://agenda-siamt.mt.local/login...'
        $ExternalStatus = curl.exe --silent --show-error --max-time 10 --resolve "agenda-siamt.mt.local:443:$DebianHost" "https://agenda-siamt.mt.local/login" --output NUL --write-out "%{http_code}"
        if ($LASTEXITCODE -ne 0 -or $ExternalStatus -ne '302') {
            throw "Fallo la verificacion HTTPS externa (HTTP $ExternalStatus, esperado 302)."
        }

        Write-Step '6/6 Registrando recibo de release verificado...'
        $ReceiptPath = Join-Path $RepoRoot "ops\agenda-release-$ShortCommit.json"
        [ordered]@{
            published_at_utc    = [DateTime]::UtcNow.ToString('o')
            release             = $Digest
            source_commit       = $FullCommit
            files_in_artifact   = $BuildInfo.files
            artifact_bytes      = $BuildInfo.bytes
            external_https_code = [int]$ExternalStatus
            url                 = 'https://agenda-siamt.mt.local'
        } | ConvertTo-Json -Depth 4 | Set-Content -Encoding utf8 $ReceiptPath

        Write-Step "Despliegue completado y verificado: commit=$ShortCommit release=$Digest HTTP=$ExternalStatus" 'Green'
        Start-Sleep -Seconds 2
    } finally {
        Remove-Item -LiteralPath $ScratchDir -Recurse -Force -ErrorAction SilentlyContinue
    }
} catch {
    Write-Step ("ERROR: " + $_.Exception.Message) 'Red'
    Start-Sleep -Seconds 4
    exit 1
} finally {
    Pop-Location
}
