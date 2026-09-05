<?php
/**
 * Componente: Tarjetas de Métricas Rápidas (KPIs) de Mantenimiento Preventivo
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/mantenimiento_preventivo/componentes_gestion/panel_metricas.php
 */
?>
<!-- Tarjetas de Métricas Rápidas -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">TOTAL REGISTROS</div>
                    <div class="h3 fw-bold mb-0 text-dark" id="metricaTotalMP">0</div>
                </div>
                <div class="p-3 bg-primary-subtle text-primary rounded-3">
                    <i class="bi bi-clipboard2-check fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">EQUIPOS PC</div>
                    <div class="h3 fw-bold mb-0 text-info" id="metricaTotalPC">0</div>
                </div>
                <div class="p-3 bg-info-subtle text-info rounded-3">
                    <i class="bi bi-pc-display fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-secondary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">EQUIPOS LAPTOP</div>
                    <div class="h3 fw-bold mb-0 text-secondary" id="metricaTotalLaptop">0</div>
                </div>
                <div class="p-3 bg-secondary-subtle text-secondary rounded-3">
                    <i class="bi bi-laptop fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">ESTE MES</div>
                    <div class="h3 fw-bold mb-0 text-success" id="metricaEsteMes">0</div>
                </div>
                <div class="p-3 bg-success-subtle text-success rounded-3">
                    <i class="bi bi-calendar-check fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>
