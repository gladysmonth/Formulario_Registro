<#
.SYNOPSIS
    Instalador y Programador de Tarea Automática para el Agente Cosmol (Cosmol-DiagnosticoP)
.DESCRIPTION
    Copia los archivos del agente a C:\ProgramData\Cosmol\Agente y registra una Tarea
    Programada de Windows en segundo plano (oculta) para enviar telemetría de forma periódica.
.PARAMETER IntervaloHoras
    Frecuencia de envío automático en horas (Por defecto: 1 hora).
.PARAMETER Desinstalar
    Si se especifica, elimina la tarea programada y detiene la telemetría automática.
#>

[CmdletBinding()]
param (
    [int]$IntervaloHoras = 1,
    [switch]$Desinstalar
)

$NombreTarea = "Cosmol_Telemetria_PC"
$RutaDestino = "C:\ProgramData\Cosmol\Agente"

# Comprobar privilegios de Administrador
$esAdmin = ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
if (-not $esAdmin) {
    Write-Warning "AVISO: Se recomienda ejecutar este script como Administrador para registrar la tarea en el sistema."
}

if ($Desinstalar) {
    Write-Host "Desinstalando tarea programada '$NombreTarea'..." -ForegroundColor Yellow
    try {
        Unregister-ScheduledTask -TaskName $NombreTarea -Confirm:$false -ErrorAction Stop
        Write-Host "Tarea programada eliminada con éxito." -ForegroundColor Green
    } catch {
        Write-Warning "No se encontró la tarea programada o ya fue eliminada: $($_.Exception.Message)"
    }
    return
}

Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host " INSTALADOR DE TELEMETRÍA AUTOMÁTICA - COSMOL R.L." -ForegroundColor Cyan
Write-Host "==========================================================" -ForegroundColor Cyan

# 1. Crear carpeta destino y copiar archivos
try {
    if (-not (Test-Path -Path $RutaDestino)) {
        New-Item -ItemType Directory -Path $RutaDestino -Force | Out-Null
    }

    $origenScript = Join-Path -Path $PSScriptRoot -ChildPath "agente_telemetria_cosmol.ps1"
    $origenConfig = Join-Path -Path $PSScriptRoot -ChildPath "configuracion_agente.json"

    if (Test-Path -Path $origenScript) {
        Copy-Item -Path $origenScript -Destination (Join-Path -Path $RutaDestino -ChildPath "agente_telemetria_cosmol.ps1") -Force
    }
    if (Test-Path -Path $origenConfig) {
        Copy-Item -Path $origenConfig -Destination (Join-Path -Path $RutaDestino -ChildPath "configuracion_agente.json") -Force
    }

    Write-Host "[OK] Archivos del agente desplegados en: $RutaDestino" -ForegroundColor Green
} catch {
    Write-Error "Error al desplegar archivos en $RutaDestino: $($_.Exception.Message)"
    return
}

# 2. Registrar Tarea Programada en Windows
$scriptFinal = Join-Path -Path $RutaDestino -ChildPath "agente_telemetria_cosmol.ps1"
$argumentos = "-NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File `"$scriptFinal`" -TipoEvento programado"

try {
    # Eliminar si ya existía una versión anterior
    $tareaPrevia = Get-ScheduledTask -TaskName $NombreTarea -ErrorAction SilentlyContinue
    if ($tareaPrevia) {
        Unregister-ScheduledTask -TaskName $NombreTarea -Confirm:$false
    }

    $Accion = New-ScheduledTaskAction -Execute "powershell.exe" -Argument $argumentos
    $Disparador = New-ScheduledTaskTrigger -Once -At (Get-Date) -RepetitionInterval (New-TimeSpan -Hours $IntervaloHoras) -RepetitionDuration ([TimeSpan]::MaxValue)
    $DisparadorInicio = New-ScheduledTaskTrigger -AtLogOn
    $Configuracion = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable -MultipleInstances IgnoreNew

    # Registrar para ejecutar bajo la cuenta del usuario actual o SYSTEM
    Register-ScheduledTask -TaskName $NombreTarea `
                           -Action $Accion `
                           -Trigger @($Disparador, $DisparadorInicio) `
                           -Settings $Configuracion `
                           -Description "Envío periódico de métricas de salud y telemetría de PC al servidor central Cosmol R.L." `
                           -Force | Out-Null

    Write-Host "[OK] Tarea programada '$NombreTarea' registrada con éxito." -ForegroundColor Green
    Write-Host "     Frecuencia: Cada $IntervaloHoras hora(s) y al iniciar sesión." -ForegroundColor Gray
    Write-Host "     Ejecutable: $scriptFinal" -ForegroundColor Gray

    # Ejecutar primera prueba de telemetría de inmediato
    Write-Host "`nEjecutando primera prueba en segundo plano..." -ForegroundColor Yellow
    Start-ScheduledTask -TaskName $NombreTarea
    Write-Host "[OK] Tarea iniciada. Las métricas periódicas se enviarán automáticamente." -ForegroundColor Green

} catch {
    Write-Error "No se pudo registrar la tarea programada: $($_.Exception.Message)"
}
