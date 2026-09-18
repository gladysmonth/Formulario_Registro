<?php
/**
 * Subcomponente: Tarjetas de Métricas Rápidas KPI (Asignación de Accesos)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/asignacion_accesos/componentes_gestion/panel_metricas.php
 */
?>
<div class="row g-3 mb-4">
    <!-- Total Solicitudes -->
    <div class="col-6 col-md-3">
        <div class="tarjeta-formulario p-3 border-start border-4 border-primary h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Total Solicitudes</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1" id="kpiTotalAccesos">-</h3>
                </div>
                <div class="p-3 bg-primary-subtle text-primary rounded-3">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendientes -->
    <div class="col-6 col-md-3">
        <div class="tarjeta-formulario p-3 border-start border-4 border-warning h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Pendientes</span>
                    <h3 class="fw-bold mb-0 text-warning mt-1" id="kpiPendientesAccesos">-</h3>
                </div>
                <div class="p-3 bg-warning-subtle text-warning rounded-3">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- En Proceso -->
    <div class="col-6 col-md-3">
        <div class="tarjeta-formulario p-3 border-start border-4 border-info h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">En Proceso</span>
                    <h3 class="fw-bold mb-0 text-info mt-1" id="kpiEnProcesoAccesos">-</h3>
                </div>
                <div class="p-3 bg-info-subtle text-info rounded-3">
                    <i class="bi bi-gear-wide-connected fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Atendidas / Habilitadas -->
    <div class="col-6 col-md-3">
        <div class="tarjeta-formulario p-3 border-start border-4 border-success h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Atendidas</span>
                    <h3 class="fw-bold mb-0 text-success mt-1" id="kpiAtendidasAccesos">-</h3>
                </div>
                <div class="p-3 bg-success-subtle text-success rounded-3">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>
