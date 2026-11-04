<#
.SYNOPSIS
    Agente Nativo de Telemetria, Diagnostico y Auditoria de PC - Cosmol R.L.
.DESCRIPTION
    Recopila metricas de hardware, rendimiento, almacenamiento SMART, seguridad
    y aplicaciones recientes. Guarda una copia local en cache y envia los datos
    al backend API mediante POST JSON.
.PARAMETER TipoEvento
    Evento que dispara el diagnostico: 'programado', 'inicio_jornada', 'alerta_critica', 'manual', 'ticket_soporte'.
.PARAMETER RutaConfig
    Ruta del archivo configuracion_agente.json (opcional).
.PARAMETER SoloGuardarLocal
    Si se especifica, solo actualiza la cache local y no envia por red.
#>

[CmdletBinding()]
param (
    [ValidateSet('programado', 'inicio_jornada', 'alerta_critica', 'manual', 'ticket_soporte')]
    [string]$TipoEvento = 'programado',

    [string]$RutaConfig = "$PSScriptRoot\configuracion_agente.json",

    [switch]$SoloGuardarLocal
)

# -----------------------------------------------------------------------------
# 1. CARGA DE CONFIGURACION
# -----------------------------------------------------------------------------
$config = @{
    url_backend             = "http://192.168.11.22:83/api/diagnostico/recibir.php"
    ruta_cache_local        = "C:\ProgramData\Cosmol"
    archivo_cache           = "ultimo_diagnostico.json"
    tiempo_espera_segundos  = 15
    max_apps_prefetch       = 20
    max_descargas_recientes = 10
}

if (Test-Path -Path $RutaConfig) {
    try {
        $configJson = Get-Content -Path $RutaConfig -Raw | ConvertFrom-Json
        if ($configJson.url_backend)             { $config.url_backend = $configJson.url_backend }
        if ($configJson.ruta_cache_local)        { $config.ruta_cache_local = $configJson.ruta_cache_local }
        if ($configJson.archivo_cache)           { $config.archivo_cache = $configJson.archivo_cache }
        if ($configJson.tiempo_espera_segundos)  { $config.tiempo_espera_segundos = [int]$configJson.tiempo_espera_segundos }
        if ($configJson.max_apps_prefetch)       { $config.max_apps_prefetch = [int]$configJson.max_apps_prefetch }
        if ($configJson.max_descargas_recientes) { $config.max_descargas_recientes = [int]$configJson.max_descargas_recientes }
    } catch {
        Write-Warning "No se pudo leer la configuracion personalizada. Usando valores por defecto."
    }
}

Write-Verbose "Iniciando recopilacion de telemetria Cosmol (Evento: $TipoEvento)..."

# -----------------------------------------------------------------------------
# 2. IDENTIFICACION DE RED Y MAC UNIVOCA
#    Busca la interfaz fisica activa con la ruta por defecto (Gateway en la LAN)
# -----------------------------------------------------------------------------
$macAddress = "00:00:00:00:00:00"
$ipLocal    = "127.0.0.1"
$gateway    = ""
$nombreRed  = "Desconocida"
$tipoConex  = "Desconocido"

try {
    $rutaDefecto = Get-NetRoute -DestinationPrefix '0.0.0.0/0' -ErrorAction SilentlyContinue | 
                   Sort-Object { $_.RouteMetric + $_.InterfaceMetric } | 
                   Select-Object -First 1

    if ($rutaDefecto) {
        $interfaz = Get-NetAdapter -InterfaceIndex $rutaDefecto.InterfaceIndex -ErrorAction SilentlyContinue
        if ($interfaz) {
            $macAddress = $interfaz.MacAddress.ToUpper().Replace('-', ':')
            $nombreRed  = $interfaz.InterfaceDescription
            $tipoConex  = if ($interfaz.PhysicalMediaType -match '802.3|Ethernet') { "Cableada (LAN)" } else { "Inalambrica (Wi-Fi)" }
        }

        $ipObj = Get-NetIPAddress -InterfaceIndex $rutaDefecto.InterfaceIndex -AddressFamily IPv4 -ErrorAction SilentlyContinue | Select-Object -First 1
        if ($ipObj) {
            $ipLocal = $ipObj.IPAddress
        }
        $gateway = $rutaDefecto.NextHop
    } else {
        # Fallback a la primera interfaz fisica UP si no hay ruta por defecto activa
        $interfazFallback = Get-NetAdapter -Physical -ErrorAction SilentlyContinue | Where-Object { $_.Status -eq 'Up' } | Select-Object -First 1
        if ($interfazFallback) {
            $macAddress = $interfazFallback.MacAddress.ToUpper().Replace('-', ':')
            $nombreRed  = $interfazFallback.InterfaceDescription
            $ipObj = Get-NetIPAddress -InterfaceIndex $interfazFallback.InterfaceIndex -AddressFamily IPv4 -ErrorAction SilentlyContinue | Select-Object -First 1
            if ($ipObj) { $ipLocal = $ipObj.IPAddress }
        }
    }
} catch {
    Write-Warning "Fallo al identificar la red: $($_.Exception.Message)"
}

$hostname       = $env:COMPUTERNAME
$usuarioWindows = [System.Security.Principal.WindowsIdentity]::GetCurrent().Name
$timestampIso   = (Get-Date).ToUniversalTime().ToString("yyyy-MM-ddTHH:mm:ssZ")

# -----------------------------------------------------------------------------
# 3. HARDWARE: CPU, TEMPERATURA Y RENDIMIENTO
# -----------------------------------------------------------------------------
$cpuInfo = Get-CimInstance Win32_Processor -ErrorAction SilentlyContinue | Select-Object -First 1
$cpuNombre  = if ($cpuInfo) { $cpuInfo.Name.Trim() } else { "Procesador Desconocido" }
$cpuNucleos = if ($cpuInfo) { [int]$cpuInfo.NumberOfCores } else { 1 }
$cpuHilos   = if ($cpuInfo) { [int]$cpuInfo.NumberOfLogicalProcessors } else { 1 }
$cpuMaxMhz  = if ($cpuInfo) { [math]::Round($cpuInfo.MaxClockSpeed / 1000, 2) } else { 0 }

# Muestreo de uso de CPU (%)
$usoCpu = 0
try {
    $perfCpu = Get-CimInstance Win32_PerfFormattedData_PerfOS_Processor -Filter "Name='_Total'" -ErrorAction SilentlyContinue
    if ($perfCpu) {
        $usoCpu = [math]::Round([double]$perfCpu.PercentProcessorTime, 1)
    }
} catch {
    $usoCpu = 0
}

# Sensor de Temperatura (si esta disponible en BIOS/WMI)
$tempCpu = $null
try {
    $tempWmi = Get-CimInstance -Namespace root\wmi -ClassName MSAcpi_ThermalZoneTemperature -ErrorAction SilentlyContinue | Select-Object -First 1
    if ($tempWmi -and $tempWmi.CurrentTemperature -gt 0) {
        # Conversion de decimas de Kelvin a Celsius
        $tempCalculada = [math]::Round(($tempWmi.CurrentTemperature - 2732) / 10, 1)
        if ($tempCalculada -gt 15 -and $tempCalculada -lt 120) {
            $tempCpu = $tempCalculada
        }
    }
} catch {
    $tempCpu = $null
}

# -----------------------------------------------------------------------------
# 4. HARDWARE: MEMORIA RAM
# -----------------------------------------------------------------------------
$osInfo = Get-CimInstance Win32_OperatingSystem -ErrorAction SilentlyContinue
$totalRamGb = 0
$libreRamGb = 0
$usoRamPorc = 0

if ($osInfo) {
    $totalRamGb = [math]::Round([double]$osInfo.TotalVisibleMemorySize / 1048576, 2)
    $libreRamGb = [math]::Round([double]$osInfo.FreePhysicalMemory / 1048576, 2)
    if ($totalRamGb -gt 0) {
        $usoRamPorc = [math]::Round((($totalRamGb - $libreRamGb) / $totalRamGb) * 100, 1)
    }
}

$slotsRam = @()
try {
    $chipsRam = Get-CimInstance Win32_PhysicalMemory -ErrorAction SilentlyContinue
    foreach ($chip in $chipsRam) {
        $slotsRam += @{
            banco       = $chip.DeviceLocator
            capacidadGb = [math]::Round([double]$chip.Capacity / 1073741824, 1)
            velocidad   = "$($chip.Speed) MHz"
            fabricante  = $chip.Manufacturer
        }
    }
} catch { }

# -----------------------------------------------------------------------------
# 5. HARDWARE: ALMACENAMIENTO Y SALUD SMART
# -----------------------------------------------------------------------------
$particiones = @()
$espacioLibreCGb = 0

$discosLogicos = Get-CimInstance Win32_LogicalDisk -Filter "DriveType=3" -ErrorAction SilentlyContinue
foreach ($disco in $discosLogicos) {
    $tamanoGb = [math]::Round([double]$disco.Size / 1073741824, 2)
    $libreGb  = [math]::Round([double]$disco.FreeSpace / 1073741824, 2)
    $usoPorc  = if ($tamanoGb -gt 0) { [math]::Round((($tamanoGb - $libreGb) / $tamanoGb) * 100, 1) } else { 0 }

    if ($disco.DeviceID -eq 'C:') {
        $espacioLibreCGb = $libreGb
    }

    $particiones += @{
        letra            = $disco.DeviceID
        etiqueta         = $disco.VolumeName
        tamano_totalGb   = $tamanoGb
        libreGb          = $libreGb
        uso_porcentaje   = $usoPorc
        sistema_archivos = $disco.FileSystem
    }
}

# Salud SMART
$saludSmartGlobal = "OK"
try {
    $smartStatus = Get-CimInstance -Namespace root\wmi -ClassName MSStorageDriver_FailurePredictStatus -ErrorAction SilentlyContinue
    if ($smartStatus) {
        foreach ($status in $smartStatus) {
            if ($status.PredictFailure -eq $true) {
                $saludSmartGlobal = "FalloInminente"
                break
            }
        }
    } else {
        # Fallback a Get-PhysicalDisk en Windows 10/11
        $discosFisicos = Get-PhysicalDisk -ErrorAction SilentlyContinue
        foreach ($df in $discosFisicos) {
            if ($df.HealthStatus -ne 'Healthy') {
                $saludSmartGlobal = $df.HealthStatus
                break
            }
        }
    }
} catch {
    $saludSmartGlobal = "OK"
}

# -----------------------------------------------------------------------------
# 6. HARDWARE: PLACA MADRE Y BIOS
# -----------------------------------------------------------------------------
$placaInfo = Get-CimInstance Win32_BaseBoard -ErrorAction SilentlyContinue | Select-Object -First 1
$biosInfo  = Get-CimInstance Win32_BIOS -ErrorAction SilentlyContinue | Select-Object -First 1

$placaMadre = @{
    fabricante   = if ($placaInfo) { $placaInfo.Manufacturer.Trim() } else { "Desconocido" }
    modelo       = if ($placaInfo) { $placaInfo.Product.Trim() } else { "Generico" }
    serial       = if ($placaInfo -and $placaInfo.SerialNumber -ne '') { $placaInfo.SerialNumber.Trim() } else { $macAddress }
    bios_version = if ($biosInfo) { $biosInfo.SMBIOSBIOSVersion } else { "" }
}

# -----------------------------------------------------------------------------
# 7. SOFTWARE Y SEGURIDAD (ANTIVIRUS, PREFETCH, DESCARGAS, ALERTAS)
# -----------------------------------------------------------------------------
$antivirusActivo = $true
$amenazasActivas = 0
$detalleAntivirus = @{
    nombre          = "Windows Defender"
    activo          = $true
    firmas_version  = "Actualizado"
    ultima_revision = (Get-Date).ToString("yyyy-MM-dd HH:mm:ss")
}

try {
    $mpStatus = Get-MpComputerStatus -ErrorAction SilentlyContinue
    if ($mpStatus) {
        $antivirusActivo = [bool]$mpStatus.AntivirusEnabled -and [bool]$mpStatus.RealTimeProtectionEnabled
        $amenazasActivas = [int]$mpStatus.ThreatStatusSummary
        $detalleAntivirus = @{
            nombre          = "Microsoft Defender Antivirus"
            activo          = $antivirusActivo
            firmas_version  = $mpStatus.AntivirusSignatureVersion
            motor_version   = $mpStatus.AMEngineVersion
            ultima_revision = $mpStatus.AntivirusSignatureLastUpdated
        }
    }
} catch {
    # Silencioso si no esta activo Defender o no hay permisos
}

# Prefetch (Ultimas aplicaciones ejecutadas)
$appsPrefetch = @()
$rutaPrefetch = "C:\Windows\Prefetch"
if (Test-Path -Path $rutaPrefetch) {
    try {
        $archivosPf = Get-ChildItem -Path $rutaPrefetch -Filter "*.pf" -ErrorAction SilentlyContinue | 
                      Sort-Object LastWriteTime -Descending | 
                      Select-Object -First $config.max_apps_prefetch

        foreach ($pf in $archivosPf) {
            # Limpiar el hash de prefetch: CALC.EXE-12345678.pf -> CALC.EXE
            $nombreLimpio = ($pf.Name -split '-')[0]
            $appsPrefetch += @{
                aplicacion     = $nombreLimpio
                ultima_corrida = $pf.LastWriteTime.ToString("yyyy-MM-dd HH:mm:ss")
                tamano_kb      = [math]::Round($pf.Length / 1024, 1)
            }
        }
    } catch { }
}

# Descargas recientes del usuario
$descargasRecientes = @()
$rutaDownloads = [System.IO.Path]::Combine($env:USERPROFILE, "Downloads")
if (Test-Path -Path $rutaDownloads) {
    try {
        $archivosDescargas = Get-ChildItem -Path $rutaDownloads -File -ErrorAction SilentlyContinue | 
                             Sort-Object LastWriteTime -Descending | 
                             Select-Object -First $config.max_descargas_recientes

        foreach ($arch in $archivosDescargas) {
            $descargasRecientes += @{
                archivo    = $arch.Name
                tamano_mb  = [math]::Round($arch.Length / 1048576, 2)
                fecha      = $arch.LastWriteTime.ToString("yyyy-MM-dd HH:mm:ss")
                extension  = $arch.Extension
            }
        }
    } catch { }
}

# Generacion de Alertas Automaticas
$alertas = @()
if ($espacioLibreCGb -lt 10) {
    $alertas += @{ tipo = "almacenamiento"; nivel = "critico"; mensaje = "Espacio critico en disco C: (Menos de 10 GB libres: $espacioLibreCGb GB)" }
} elseif ($espacioLibreCGb -lt 25) {
    $alertas += @{ tipo = "almacenamiento"; nivel = "advertencia"; mensaje = "Espacio bajo en disco C: ($espacioLibreCGb GB disponibles)" }
}

if ($usoRamPorc -gt 90) {
    $alertas += @{ tipo = "memoria"; nivel = "critico"; mensaje = "Uso excesivo de Memoria RAM: $usoRamPorc%" }
} elseif ($usoRamPorc -gt 80) {
    $alertas += @{ tipo = "memoria"; nivel = "advertencia"; mensaje = "Memoria RAM alta: $usoRamPorc%" }
}

if ($saludSmartGlobal -ne "OK") {
    $alertas += @{ tipo = "disco_smart"; nivel = "critico"; mensaje = "Prediccion de fallo fisico en disco duro SMART: $saludSmartGlobal" }
}

if (-not $antivirusActivo) {
    $alertas += @{ tipo = "seguridad"; nivel = "critico"; mensaje = "Proteccion en tiempo real del antivirus DESACTIVADA" }
}

if ($amenazasActivas -gt 0) {
    $alertas += @{ tipo = "seguridad"; nivel = "critico"; mensaje = "Se detectaron $amenazasActivas amenazas de seguridad sin resolver" }
}

# -----------------------------------------------------------------------------
# 8. CALCULO DEL ESTADO DE SALUD GENERAL
# -----------------------------------------------------------------------------
$estadoSaludGeneral = "optimo"

$hayCritico = ($alertas | Where-Object { $_.nivel -eq 'critico' }).Count -gt 0
$hayAdvertencia = ($alertas | Where-Object { $_.nivel -eq 'advertencia' }).Count -gt 0

if ($hayCritico) {
    $estadoSaludGeneral = "critico"
} elseif ($hayAdvertencia) {
    $estadoSaludGeneral = "advertencia"
} elseif ($usoRamPorc -gt 65 -or $usoCpu -gt 65) {
    $estadoSaludGeneral = "bueno"
} else {
    $estadoSaludGeneral = "optimo"
}

# -----------------------------------------------------------------------------
# 9. CONSTRUCCION DEL PAYLOAD FINAL
# -----------------------------------------------------------------------------
$payload = @{
    metadatos = @{
        hostname        = $hostname
        direccion_mac   = $macAddress
        ip_local        = $ipLocal
        usuario_windows = $usuarioWindows
        tipo_evento     = $TipoEvento
        timestamp       = $timestampIso
    }
    resumen_salud = @{
        estado_salud_general = $estadoSaludGeneral
        temperatura_cpu      = $tempCpu
        uso_cpu_porcentaje   = $usoCpu
        uso_ram_porcentaje   = $usoRamPorc
        espacio_libre_c_gb   = $espacioLibreCGb
        salud_disco_smart    = $saludSmartGlobal
        antivirus_activo     = $antivirusActivo
        amenazas_activas     = $amenazasActivas
    }
    hardware = @{
        cpu = @{
            nombre            = $cpuNombre
            nucleos_fisicos   = $cpuNucleos
            hilos_logicos     = $cpuHilos
            velocidad_max_ghz = $cpuMaxMhz
        }
        ram = @{
            total_gb        = $totalRamGb
            libre_gb        = $libreRamGb
            uso_porcentaje  = $usoRamPorc
            slots           = $slotsRam
        }
        almacenamiento = $particiones
        red = @{
            adaptador      = $nombreRed
            direccion_mac  = $macAddress
            ip_local       = $ipLocal
            gateway        = $gateway
            tipo_conexion  = $tipoConex
        }
        placa_madre = $placaMadre
    }
    software_y_seguridad = @{
        antivirus               = $detalleAntivirus
        alertas_detectadas      = $alertas
        apps_recientes_prefetch = $appsPrefetch
        descargas_recientes     = $descargasRecientes
    }
}

# Serializar a JSON asegurando profundidad suficiente (-Depth 10)
$jsonPayload = $payload | ConvertTo-Json -Depth 10 -Compress

# -----------------------------------------------------------------------------
# 10. PERSISTENCIA LOCAL (CACHE OFFLINE)
# -----------------------------------------------------------------------------
try {
    if (-not (Test-Path -Path $config.ruta_cache_local)) {
        New-Item -ItemType Directory -Path $config.ruta_cache_local -Force | Out-Null
    }
    $rutaArchivoCache = Join-Path -Path $config.ruta_cache_local -ChildPath $config.archivo_cache
    [System.IO.File]::WriteAllText($rutaArchivoCache, $jsonPayload, [System.Text.Encoding]::UTF8)
    Write-Verbose "Telemetria guardada en cache local: $rutaArchivoCache"
} catch {
    Write-Warning "No se pudo escribir el archivo de cache local: $($_.Exception.Message)"
}

# -----------------------------------------------------------------------------
# 11. TRANSMISION HTTP AL SERVIDOR (BACKEND API)
# -----------------------------------------------------------------------------
if ($SoloGuardarLocal) {
    Write-Host "Modo local activado. No se envio peticion HTTP." -ForegroundColor Cyan
    return $payload
}

Write-Verbose "Enviando telemetria a: $($config.url_backend)..."
$bytesPayload = [System.Text.Encoding]::UTF8.GetBytes($jsonPayload)

try {
    $parametrosRest = @{
        Uri         = $config.url_backend
        Method      = 'Post'
        Body        = $bytesPayload
        ContentType = 'application/json; charset=utf-8'
        TimeoutSec  = [int]$config.tiempo_espera_segundos
    }
    $respuesta = Invoke-RestMethod @parametrosRest

    if ($respuesta.exito) {
        $diagId = $respuesta.datos.diagnostico_id
        $saludDesc = $respuesta.datos.estado_salud
        Write-Host "OK [Cosmol-DiagnosticoP] Telemetria enviada con exito (ID Diagnostico: $diagId, Salud: $saludDesc)." -ForegroundColor Green
    } else {
        $servidorMsg = $respuesta.mensaje
        Write-Warning "El servidor respondio con advertencia: $servidorMsg"
    }
} catch {
    $errMensaje = $_.Exception.Message
    Write-Warning "No se pudo contactar al servidor: $errMensaje"
}

return $payload
