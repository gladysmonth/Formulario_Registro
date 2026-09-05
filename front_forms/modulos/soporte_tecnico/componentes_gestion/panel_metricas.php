<?php
/**
 * Componente: Tarjetas de Métricas Rápidas (KPIs)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/soporte_tecnico/componentes_gestion/panel_metricas.php
 */
?>
<!-- Tarjetas de Métricas Rápidas -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">TOTAL TICKETS</div>
                    <div class="h3 fw-bold mb-0 text-dark" id="metricaTotal">0</div>
                </div>
                <div class="p-3 bg-primary-subtle text-primary rounded-3">
                    <i class="bi bi-ticket-detailed fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">PENDIENTES</div>
                    <div class="h3 fw-bold mb-0 text-warning" id="metricaPendientes">0</div>
                </div>
                <div class="p-3 bg-warning-subtle text-warning rounded-3">
                    <i class="bi bi-clock-history fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">EN PROCESO</div>
                    <div class="h3 fw-bold mb-0 text-info" id="metricaEnProceso">0</div>
                </div>
                <div class="p-3 bg-info-subtle text-info rounded-3">
                    <i class="bi bi-gear-wide-connected fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">RESUELTOS</div>
                    <div class="h3 fw-bold mb-0 text-success" id="metricaResueltos">0</div>
                </div>
                <div class="p-3 bg-success-subtle text-success rounded-3">
                    <i class="bi bi-check-circle fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>
