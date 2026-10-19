<?php
/**
 * Componente: Tarjetas KPI de Resumen Global de Monitoreo
 * Archivo: front_forms/modulos/diagnostico/componentes/kpis_monitoreo.php
 */
?>
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-primary-subtle text-primary rounded-3 fs-3">
                    <i class="bi bi-pc-display"></i>
                </div>
                <div>
                    <div class="text-secondary small fw-medium">Equipos Monitoreados</div>
                    <div class="fs-4 fw-bold text-dark" id="kpiTotal">--</div>
                    <small class="text-muted" style="font-size: 0.72rem;">Registrados por agente</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-success-subtle text-success rounded-3 fs-3">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="text-secondary small fw-medium">Salud Óptima</div>
                    <div class="fs-4 fw-bold text-success" id="kpiOptimos">--</div>
                    <small class="text-muted" style="font-size: 0.72rem;">Sin anomalías de HW</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-warning-subtle text-warning-emphasis rounded-3 fs-3">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <div class="text-secondary small fw-medium">Con Advertencias</div>
                    <div class="fs-4 fw-bold text-warning-emphasis" id="kpiAdvertencias">--</div>
                    <small class="text-muted" style="font-size: 0.72rem;">Espacio o RAM elevados</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-danger-subtle text-danger rounded-3 fs-3">
                    <i class="bi bi-x-octagon-fill"></i>
                </div>
                <div>
                    <div class="text-secondary small fw-medium">Estado Crítico</div>
                    <div class="fs-4 fw-bold text-danger" id="kpiCriticos">--</div>
                    <small class="text-muted" style="font-size: 0.72rem;">Falla SMART o disco < 10GB</small>
                </div>
            </div>
        </div>
    </div>
</div>
