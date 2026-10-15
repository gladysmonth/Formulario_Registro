<?php
/**
 * Vista: Pantalla Completa de Métricas y Monitoreo Institucional
 * FASE 4: Centro de Control y Dashboard
 * Compatible con PHP 7.3
 * Archivo: front_forms/metricas.php
 */

$titulo_pagina = 'MÉTRICA - Dashboard de Monitoreo';
$pagina_activa = 'metricas';
$nivel_ruta = '';
$roles_requeridos = array('admin', 'tecnico');

require_once __DIR__ . '/componentes/encabezado.php';
require_once __DIR__ . '/componentes/barra_navegacion.php';

$usuario_actual_sesion = obtener_usuario_sesion();
$rol_actual = obtener_rol_actual();
?>

<main class="container my-4 my-md-5">
    
    <!-- Cabecera de la Pantalla de Métricas con Breadcrumbs y Acciones -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">
                        <i class="bi bi-speedometer2 me-1"></i>Panel Principal
                    </li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <span class="badge bg-primary p-2 rounded-3">
                    <i class="bi bi-speedometer2 fs-4"></i>
                </span>
                <span>Dashboard de Gestión Técnica</span>
            </h1>
            <p class="text-muted small mb-0 mt-1">
                Monitoreo en tiempo real de requerimientos institucionales, salud de hardware y filtración rápida de tickets &bull; Cosmol R.L.
            </p>
        </div>

        <!-- Botones de Acción Superior -->
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill small d-none d-sm-inline-flex align-items-center">
                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Conexión en Vivo
            </span>
            <button type="button" class="btn btn-outline-primary btn-sm px-3 py-2 d-flex align-items-center gap-2 rounded-3 shadow-sm" onclick="actualizarDashboardMetricas()" id="btnRefrescarMetricas" title="Actualizar datos desde la base de datos">
                <i class="bi bi-arrow-clockwise fs-6" id="iconoRefrescarMetricas"></i>
                <span class="fw-semibold">Actualizar Datos</span>
            </button>
        </div>
    </div>

    <!-- ====================================================================
         1. BLOQUE A: 4 KPIS DE TENSIÓN Y ATENCIÓN OPERATIVA (ITSM / ITIL)
         ==================================================================== -->
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
                        <!-- Desglose por formulario para saber a dónde dirigirse -->
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

    <!-- ====================================================================
         2. BANNER DE ALERTA TEMPRANA / ESTADO DEL SISTEMA
         ==================================================================== -->
    <div class="mb-4" id="contenedorBannerAlerta">
        <div class="alert alert-success border-0 shadow-sm rounded-3 py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2 mb-0" id="bannerAlertaDashboard">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-5 text-success" id="iconoBannerAlerta"></i>
                <span class="small" id="textoBannerAlerta">
                    <strong>Sistemas Institucionales Estables:</strong> No se registran incidencias críticas ni alertas de hardware pendientes en este momento.
                </span>
            </div>
            <div class="d-flex align-items-center gap-2" id="accionesBannerAlerta">
                <span class="badge bg-success bg-opacity-25 text-success small px-2 py-1" id="badgeEstadoGeneral">Operativo</span>
                <a href="#" class="btn btn-danger btn-sm px-3 py-1 fw-semibold d-none align-items-center gap-1 shadow-sm" id="btnAccionAlerta">
                    <span>Ir a atender</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- ====================================================================
         3. BLOQUE B: DISTRIBUCIÓN MENSUAL POR FORMULARIO (DONA) Y DEMANDA POR ÁREAS
         ==================================================================== -->
    <div class="row g-4 mb-4">
        <!-- Columna Izquierda: Gráfico de Dona Mensual de los 4 Formularios (Centrada) -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-header bg-white border-bottom border-light py-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="p-2 rounded-circle bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-pie-chart-fill"></i>
                        </span>
                        <div>
                            <h3 class="h6 fw-bold mb-0 text-dark">Distribución Mensual por Formulario</h3>
                            <small class="text-secondary" style="font-size: 0.72rem;">Volumen y proporción de los 4 formularios en el mes</small>
                        </div>
                    </div>
                    <!-- Selector y Navegador de Meses -->
                    <div class="d-flex align-items-center gap-1 bg-light p-1 rounded-3 border">
                        <button type="button" class="btn btn-sm btn-white border-0 shadow-none px-2 py-1 text-secondary" id="btnMesAnterior" title="Mes anterior" onclick="if(window.clienteApi) clienteApi.cambiarMesDashboard(-1)">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <span class="fw-semibold px-2 text-dark small text-nowrap d-flex align-items-center gap-1" id="textoMesSeleccionado">
                            <i class="bi bi-calendar3 text-primary me-1"></i>Calculando mes...
                        </span>
                        <button type="button" class="btn btn-sm btn-white border-0 shadow-none px-2 py-1 text-secondary" id="btnMesSiguiente" title="Mes siguiente" onclick="if(window.clienteApi) clienteApi.cambiarMesDashboard(1)">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-primary py-0 px-2 small fw-semibold ms-1 d-none" id="btnVolverMesActual" title="Volver al mes actual" onclick="if(window.clienteApi) clienteApi.cambiarMesDashboard(0)">
                            Mes actual
                        </button>
                    </div>
                </div>
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <!-- Dona Interactiva Centrada con Contador en el Centro -->
                    <div class="d-flex justify-content-center align-items-center py-3 my-auto">
                        <div style="position: relative; width: 220px; height: 220px;">
                            <canvas id="graficoDonaFormularios"></canvas>
                            <!-- Centro de la Dona -->
                            <div class="position-absolute top-50 start-50 translate-middle text-center" style="pointer-events: none;">
                                <span class="fs-2 fw-bold text-dark d-block lh-1" id="donaTotalTicketsCentro">0</span>
                                <span class="text-secondary small fw-medium" style="font-size: 0.72rem;" id="donaTextoMesCentro">tickets</span>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen Inferior del Mes: Solo Mayor Demanda -->
                    <div class="pt-2 border-top border-light mt-3 d-flex align-items-center text-muted small gap-2">
                        <i class="bi bi-graph-up-arrow text-primary"></i>
                        <span>Mayor demanda en el mes:</span>
                        <strong class="text-dark fw-bold" id="formularioMayorUsoMes">-</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Demanda por Departamentos / Áreas -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-header bg-white border-bottom border-light py-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="p-2 rounded-circle bg-info bg-opacity-10 text-info">
                            <i class="bi bi-diagram-3-fill"></i>
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

    <!-- ====================================================================
         4. BLOQUE C: MONITOREO ACTIVO DE HARDWARE (TELEMETRÍA EN VIVO DE PCS)
         ==================================================================== -->
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
</main>

<!-- Chart.js para Renderizado de la Dona Interactiva -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/**
 * Orquestador Dinámico del Dashboard de Gestión Técnica
 */
async function actualizarDashboardMetricas() {
    const btn = document.getElementById('btnRefrescarMetricas');
    const icono = document.getElementById('iconoRefrescarMetricas');
    const textoHora = document.getElementById('textoUltimaActualizacion');

    if (icono) icono.classList.add('spin-animado');
    if (btn) btn.disabled = true;

    try {
        if (typeof clienteApi !== 'undefined' && typeof clienteApi.cargarDashboardGestionTecnica === 'function') {
            await clienteApi.cargarDashboardGestionTecnica();
        }
        if (textoHora) {
            const ahora = new Date();
            textoHora.textContent = 'Actualizado a las ' + ahora.toLocaleTimeString();
        }
    } catch (e) {
        console.error('Error al actualizar dashboard:', e);
    } finally {
        if (icono) icono.classList.remove('spin-animado');
        if (btn) btn.disabled = false;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    actualizarDashboardMetricas();
});
</script>

<style>
.tarjeta-kpi-cosmol {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.tarjeta-kpi-cosmol:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
}
.spin-animado {
    animation: rotar 0.8s linear infinite;
}
@keyframes rotar {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<?php
require_once __DIR__ . '/componentes/pie_pagina.php';
?>
