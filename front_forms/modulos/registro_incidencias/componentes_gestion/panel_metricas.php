<?php
/**
 * Subcomponente: Tarjetas de Métricas Rápidas KPI (FOR_RIS_001, V-1)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/registro_incidencias/componentes_gestion/panel_metricas.php
 */
?>
<div class="row g-3 mb-4">
    <!-- Total Incidencias -->
    <div class="col-6 col-md-3">
        <div class="card h-100 p-3 border rounded-3 bg-white shadow-sm border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small d-block">Total Incidencias</span>
                    <h3 class="fw-bold mb-0 text-dark" id="kpiTotalIncidencias">0</h3>
                </div>
                <div class="p-3 bg-primary-subtle text-primary rounded-circle">
                    <i class="bi bi-folder2-open fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Críticas (Nivel 1) -->
    <div class="col-6 col-md-3">
        <div class="card h-100 p-3 border rounded-3 bg-white shadow-sm border-start border-4 border-danger">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small d-block">Críticas (Nivel 1)</span>
                    <h3 class="fw-bold mb-0 text-danger" id="kpiCriticasNivel1">0</h3>
                </div>
                <div class="p-3 bg-danger-subtle text-danger rounded-circle">
                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Abiertas / En Atención -->
    <div class="col-6 col-md-3">
        <div class="card h-100 p-3 border rounded-3 bg-white shadow-sm border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small d-block">Pendientes / Atención</span>
                    <h3 class="fw-bold mb-0 text-warning" id="kpiPendientes">0</h3>
                </div>
                <div class="p-3 bg-warning-subtle text-warning rounded-circle">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Resueltas / Cerradas -->
    <div class="col-6 col-md-3">
        <div class="card h-100 p-3 border rounded-3 bg-white shadow-sm border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small d-block">Resueltas / Cerradas</span>
                    <h3 class="fw-bold mb-0 text-success" id="kpiResueltas">0</h3>
                </div>
                <div class="p-3 bg-success-subtle text-success rounded-circle">
                    <i class="bi bi-check2-circle fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>
