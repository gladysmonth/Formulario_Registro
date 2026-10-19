<?php
/**
 * Componente Métricas: 4 KPIs de Tensión y Atención Operativa (ITSM / ITIL)
 * Archivo: front_forms/componentes_metricas/bloque_kpis_operativos.php
 */
?>
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-speedometer2 text-primary"></i>
            <span>Indicadores Operativos en Tiempo Real</span>
        </h2>
        <small class="text-muted" id="textoUltimaActualizacion">Sincronizando datos...</small>
    </div>

    <div class="row g-3">
        <!-- 1. CARGA DE TRABAJO (PENDIENTES POR FORMULARIO) -->
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 shadow-sm border-0 border-top border-4 border-primary rounded-3 bg-white tarjeta-kpi-cosmol">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                CARGA DE TRABAJO
                            </span>
                            <div class="text-muted small">Requerimientos Abiertos</div>
                        </div>
                        <div class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-inboxes-fill fs-4"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 my-1">
                        <span class="display-5 fw-bold text-dark" id="kpiTotalAbiertos">-</span>
                        <span class="text-muted small">pendientes</span>
                    </div>
                    <div class="p-2 rounded bg-light border border-light-subtle my-2" style="font-size: 0.75rem;">
                        <div class="text-secondary fw-semibold mb-1 d-flex align-items-center gap-1">
                            <i class="bi bi-signpost-split text-primary"></i>
                            <span>Por atender según formulario:</span>
                        </div>
                        <div class="d-flex flex-column gap-1" id="kpiAbiertosDesglose">
                            <span class="text-muted">Cargando desglose...</span>
                        </div>
                    </div>
                    <div class="pt-2 border-top border-light d-flex justify-content-between align-items-center small text-muted">
                        <span>Estado de atención:</span>
                        <span class="badge bg-primary bg-opacity-15 text-primary fw-bold" id="kpiAbiertosEstatus">En seguimiento</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. TICKETS CRÍTICOS / ALTOS -->
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 shadow-sm border-0 border-top border-4 border-danger rounded-3 bg-white tarjeta-kpi-cosmol">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                CRITICIDAD ALTA
                            </span>
                            <div class="text-muted small">Fallas Críticas Activas</div>
                        </div>
                        <div class="p-2 rounded-3 bg-danger bg-opacity-10 text-danger">
                            <i class="bi bi-exclamation-octagon-fill fs-4"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 my-1">
                        <span class="display-5 fw-bold text-danger" id="kpiTotalCriticos">-</span>
                        <span class="text-muted small">urgentes</span>
                    </div>
                    <div class="p-2 rounded bg-light border border-light-subtle my-2" style="font-size: 0.75rem;">
                        <div class="text-secondary fw-semibold mb-1 d-flex align-items-center gap-1">
                            <i class="bi bi-hdd-network text-danger"></i>
                            <span>Plataformas comprometidas:</span>
                        </div>
                        <div class="small" id="kpiCriticosDetalleTexto">
                            <span class="badge bg-danger bg-opacity-15 text-danger px-1 py-0" id="kpiCriticosBadge">0 Críticas</span> en plataformas
                        </div>
                    </div>
                    <div class="pt-2 border-top border-light d-flex justify-content-between align-items-center small text-muted">
                        <span>Nivel de alerta:</span>
                        <span class="badge bg-success bg-opacity-15 text-success fw-bold" id="kpiCriticosEstatus">Estable</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. TICKETS RESUELTOS Y ATENDIDOS -->
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 shadow-sm border-0 border-top border-4 border-success rounded-3 bg-white tarjeta-kpi-cosmol">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                RESOLUCIÓN Y CIERRE
                            </span>
                            <div class="text-muted small">Tickets Atendidos con Éxito</div>
                        </div>
                        <div class="p-2 rounded-3 bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check-circle-fill fs-4"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 my-1">
                        <span class="display-5 fw-bold text-success" id="kpiTotalResueltos">-</span>
                        <span class="text-muted small">resueltos</span>
                    </div>
                    <div class="p-2 rounded bg-light border border-light-subtle my-2" style="font-size: 0.75rem;">
                        <div class="text-secondary fw-semibold mb-1 d-flex align-items-center gap-1">
                            <i class="bi bi-check2-all text-success"></i>
                            <span>Eficacia de atención:</span>
                        </div>
                        <div class="small text-muted" id="kpiResueltosDetalle">
                            Tickets cerrados en soporte, incidencias y accesos
                        </div>
                    </div>
                    <div class="pt-2 border-top border-light d-flex justify-content-between align-items-center small text-muted">
                        <span>Estado de resolución:</span>
                        <span class="badge bg-success bg-opacity-15 text-success fw-bold" id="kpiResueltosEstatus">Atendidos</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. MANTENIMIENTOS PREVENTIVOS REALIZADOS -->
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 shadow-sm border-0 border-top border-4 border-info rounded-3 bg-white tarjeta-kpi-cosmol">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                PREVENTIVOS EJECUTADOS
                            </span>
                            <div class="text-muted small">Fichas de PC / Laptop</div>
                        </div>
                        <div class="p-2 rounded-3 bg-info bg-opacity-10 text-info">
                            <i class="bi bi-tools fs-4"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 my-1">
                        <span class="display-5 fw-bold text-dark" id="kpiTotalMantenimientos">-</span>
                        <span class="text-muted small">fichas OK</span>
                    </div>
                    <div class="p-2 rounded bg-light border border-light-subtle my-2" style="font-size: 0.75rem;">
                        <div class="text-secondary fw-semibold mb-1 d-flex align-items-center gap-1">
                            <i class="bi bi-shield-check text-info"></i>
                            <span>Equipos inspeccionados:</span>
                        </div>
                        <div class="small text-muted" id="kpiMantenimientoDetalle">
                            Limpieza física, lógica y doble firma digital
                        </div>
                    </div>
                    <div class="pt-2 border-top border-light d-flex justify-content-between align-items-center small text-muted">
                        <span>Cumplimiento preventivo:</span>
                        <span class="badge bg-info bg-opacity-15 text-info fw-bold" id="kpiMantenimientoEstatus">Al día</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
