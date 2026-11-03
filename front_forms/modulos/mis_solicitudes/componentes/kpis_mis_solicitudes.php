<?php
/**
 * Componente: Tarjetas de Resumen KPI para el Solicitante (Ultra Adaptable Móvil)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/mis_solicitudes/componentes/kpis_mis_solicitudes.php
 */
?>
<div class="row g-2 g-md-3 mb-3 mb-md-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100 p-2 p-sm-3">
            <div class="d-flex align-items-center justify-content-between gap-1">
                <div class="pe-1 text-truncate">
                    <span class="text-muted text-uppercase fw-semibold d-block text-truncate" style="font-size: 0.68rem; letter-spacing: 0.3px;">Total Solicitudes</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1 fs-5 fs-sm-4" id="kpiMisTotal">-</h3>
                </div>
                <div class="caja-icono-kpi bg-primary-subtle text-primary flex-shrink-0">
                    <i class="bi bi-archive-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100 p-2 p-sm-3">
            <div class="d-flex align-items-center justify-content-between gap-1">
                <div class="pe-1 text-truncate">
                    <span class="text-muted text-uppercase fw-semibold d-block text-truncate" style="font-size: 0.68rem; letter-spacing: 0.3px;">En Espera</span>
                    <h3 class="fw-bold mb-0 text-warning mt-1 fs-5 fs-sm-4" id="kpiMisPendientes">-</h3>
                </div>
                <div class="caja-icono-kpi bg-warning-subtle text-warning flex-shrink-0">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100 p-2 p-sm-3">
            <div class="d-flex align-items-center justify-content-between gap-1">
                <div class="pe-1 text-truncate">
                    <span class="text-muted text-uppercase fw-semibold d-block text-truncate" style="font-size: 0.68rem; letter-spacing: 0.3px;">En Atención</span>
                    <h3 class="fw-bold mb-0 text-info mt-1 fs-5 fs-sm-4" id="kpiMisEnProceso">-</h3>
                </div>
                <div class="caja-icono-kpi bg-info-subtle text-info flex-shrink-0">
                    <i class="bi bi-gear-wide-connected"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100 p-2 p-sm-3">
            <div class="d-flex align-items-center justify-content-between gap-1">
                <div class="pe-1 text-truncate">
                    <span class="text-muted text-uppercase fw-semibold d-block text-truncate" style="font-size: 0.68rem; letter-spacing: 0.3px;">Atendidas</span>
                    <h3 class="fw-bold mb-0 text-success mt-1 fs-5 fs-sm-4" id="kpiMisResueltas">-</h3>
                </div>
                <div class="caja-icono-kpi bg-success-subtle text-success flex-shrink-0">
                    <i class="bi bi-check2-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>
