<?php
/**
 * Componente Métricas: Monitoreo Activo de Hardware (Telemetría en Vivo de PCs)
 * Archivo: front_forms/componentes_metricas/bloque_telemetria_hardware.php
 */
?>
<div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
    <div class="card-header bg-white border-bottom border-light py-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="p-2 rounded-circle bg-success bg-opacity-10 text-success">
                <i class="bi bi-cpu-fill"></i>
            </span>
            <div>
                <h3 class="h6 fw-bold mb-0 text-dark">Salud del Parque Computacional (Telemetría en Vivo)</h3>
                <small class="text-secondary" style="font-size: 0.72rem;">Auditoría automática de procesador, memoria RAM, discos (SMART) y Windows Defender</small>
            </div>
        </div>
        <a href="modulos/diagnostico/monitoreo_equipos.php" class="btn btn-sm btn-outline-primary fw-semibold d-flex align-items-center gap-1">
            <span>Ver Tablero Completo de PCs</span>
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="card-body p-3">
        <div class="row g-4 align-items-center">
            <!-- Semáforo de Equipos -->
            <div class="col-lg-6">
                <div class="text-uppercase text-secondary fw-bold mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    ESTADO DE SALUD DEL HARDWARE
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <div class="p-2 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25 text-center">
                            <div class="fs-4 fw-bold text-success" id="telemetriaOptimos">0</div>
                            <small class="text-success fw-semibold" style="font-size: 0.7rem;">Salud Óptima</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 text-center">
                            <div class="fs-4 fw-bold text-dark" id="telemetriaAdvertencias">0</div>
                            <small class="text-secondary fw-semibold" style="font-size: 0.7rem;">Advertencias</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 text-center">
                            <div class="fs-4 fw-bold text-danger" id="telemetriaCriticos">0</div>
                            <small class="text-danger fw-semibold" style="font-size: 0.7rem;">Críticos</small>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light small">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check text-success fs-6"></i>
                        <span>Cobertura Antivirus Defender:</span>
                    </span>
                    <strong class="text-dark" id="telemetriaAntivirusTexto">100% Protegido</strong>
                </div>
            </div>

            <!-- Alertas Preventivas de Hardware Inmediatas -->
            <div class="col-lg-6">
                <div class="text-uppercase text-secondary fw-bold mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    ALERTAS DE HARDWARE PREVENTIVAS
                </div>
                <div id="contenedorAlertasHardware" class="d-flex flex-column gap-2">
                    <div class="p-3 rounded-3 bg-light border text-center text-muted small">
                        <i class="bi bi-check-circle-fill text-success fs-5 d-block mb-1"></i>
                        <span>Todos los discos (SMART) y memorias operan dentro de los parámetros seguros.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
