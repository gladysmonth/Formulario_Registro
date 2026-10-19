<?php
/**
 * Componente Métricas: Distribución Mensual por Formulario (Dona) y Demanda por Departamentos
 * Archivo: front_forms/componentes_metricas/bloque_distribucion_departamentos.php
 */
?>
<div class="row g-4 mb-4">
    <!-- GRÁFICA DONA INTERACTIVA: DISTRIBUCIÓN POR FORMULARIO -->
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm border-0 rounded-3 bg-white">
            <div class="card-header bg-white border-bottom border-light py-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="p-2 rounded-circle bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-pie-chart-fill"></i>
                    </span>
                    <div>
                        <h3 class="h6 fw-bold mb-0 text-dark">Distribución Mensual por Formulario</h3>
                        <small class="text-secondary" style="font-size: 0.72rem;">Participación porcentual de la demanda técnica &bull; <span id="nombreMesGrafica">Mes Actual</span></small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <select class="form-select form-select-sm border-light-subtle shadow-none text-secondary" id="selectMesMetricas" onchange="cambiarPeriodoMetricas()" style="font-size: 0.78rem;">
                        <option value="1">Enero</option>
                        <option value="2">Febrero</option>
                        <option value="3">Marzo</option>
                        <option value="4">Abril</option>
                        <option value="5">Mayo</option>
                        <option value="6">Junio</option>
                        <option value="7">Julio</option>
                        <option value="8">Agosto</option>
                        <option value="9">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>
                    </select>
                </div>
            </div>
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="row align-items-center g-3 my-auto">
                    <!-- Contenedor Canvas del gráfico dona -->
                    <div class="col-sm-6 text-center position-relative">
                        <div style="max-height: 220px; max-width: 220px; margin: 0 auto;" class="position-relative">
                            <canvas id="graficoDonaFormularios"></canvas>
                        </div>
                    </div>
                    <!-- Leyenda interactiva con porcentajes y totales -->
                    <div class="col-sm-6">
                        <div class="d-flex flex-column gap-2" id="leyendaGraficaDona">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border-start border-3 border-primary">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary p-1 rounded-circle" style="width: 8px; height: 8px;"> </span>
                                    <span class="small fw-semibold text-dark">Soporte Técnico</span>
                                </div>
                                <span class="small fw-bold text-dark" id="pctSoporte">0%</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border-start border-3 border-danger">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-danger p-1 rounded-circle" style="width: 8px; height: 8px;"> </span>
                                    <span class="small fw-semibold text-dark">Incidencias</span>
                                </div>
                                <span class="small fw-bold text-dark" id="pctIncidencias">0%</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border-start border-3 border-success">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success p-1 rounded-circle" style="width: 8px; height: 8px;"> </span>
                                    <span class="small fw-semibold text-dark">Accesos</span>
                                </div>
                                <span class="small fw-bold text-dark" id="pctAccesos">0%</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border-start border-3 border-info">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-info p-1 rounded-circle" style="width: 8px; height: 8px;"> </span>
                                    <span class="small fw-semibold text-dark">Mant. Preventivo</span>
                                </div>
                                <span class="small fw-bold text-dark" id="pctMantenimiento">0%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-top border-light mt-3 d-flex justify-content-between align-items-center text-muted small">
                    <span>Mayor requerimiento en el mes:</span>
                    <strong class="text-primary fw-bold" id="moduloMayorDemanda">-</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- DEMANDA POR DEPARTAMENTOS / ÁREAS (TOP 5) -->
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm border-0 rounded-3 bg-white">
            <div class="card-header bg-white border-bottom border-light py-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="p-2 rounded-circle bg-info bg-opacity-10 text-info">
                        <i class="bi bi-buildings-fill"></i>
                    </span>
                    <div>
                        <h3 class="h6 fw-bold mb-0 text-dark">Demanda por Departamentos / Áreas</h3>
                        <small class="text-secondary" style="font-size: 0.72rem;">Concentración de tickets en dependencias de Cosmol R.L.</small>
                    </div>
                </div>
                <span class="badge bg-light text-secondary border small px-2 py-1">
                    <i class="bi bi-bar-chart-fill text-info me-1"></i>Ranking Institucional
                </span>
            </div>
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div id="contenedorTopDepartamentos" class="d-flex flex-column gap-2">
                    <div class="text-center py-3 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        Calculando demanda por departamento...
                    </div>
                </div>
                <div class="pt-3 border-top border-light mt-3 d-flex justify-content-between align-items-center text-muted small flex-wrap gap-2">
                    <div>
                        <i class="bi bi-geo-alt-fill text-primary me-1"></i>
                        <span>Área con mayor requerimiento: </span>
                        <strong class="text-dark" id="areaMayorDemanda">-</strong>
                    </div>
                    <div>
                        <span>Dependencias registradas: </span>
                        <strong class="text-dark fs-6 ms-1" id="totalAreasAtendidas">0</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
