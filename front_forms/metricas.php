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
                        <i class="bi bi-speedometer2 me-1"></i>MÉTRICA &bull; Pantalla Principal
                    </li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <span class="badge bg-primary p-2 rounded-3">
                    <i class="bi bi-speedometer2 fs-4"></i>
                </span>
                <span>MÉTRICA &bull; Dashboard de Gestión Técnica</span>
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
         1. INDICADORES CLAVE EN VIVO (KPIS DE TICKETS Y CASOS)
         ==================================================================== -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-graph-up text-primary"></i>
                <span>Indicadores de Atención en Tiempo Real</span>
            </h2>
            <small class="text-muted" id="textoUltimaActualizacion">Sincronizando datos...</small>
        </div>

        <div class="row g-3">
            <!-- 1. Tickets Pendientes / Abiertos -->
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm border-0 border-start border-4 border-warning rounded-3 bg-white">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-warning bg-opacity-25 text-dark fw-semibold small mb-1">
                                    <i class="bi bi-hourglass-split me-1"></i>Prioritarios
                                </span>
                                <div class="text-secondary small fw-medium">Abiertos / Pendientes</div>
                            </div>
                            <div class="p-2 rounded-circle bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-inbox-fill fs-4"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-2 my-2">
                            <span class="display-5 fw-bold text-dark" id="kpiTicketsPendientes">-</span>
                            <span class="text-muted small">tickets</span>
                        </div>
                        <a href="modulos/soporte_tecnico/gestion_tickets.php?estado=pendiente" class="btn btn-sm btn-outline-warning text-dark w-100 mt-2 fw-semibold d-flex align-items-center justify-content-center gap-1">
                            <span>Ver Tickets Abiertos</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. Tickets En Proceso -->
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm border-0 border-start border-4 border-info rounded-3 bg-white">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-info bg-opacity-25 text-info-emphasis fw-semibold small mb-1">
                                    <i class="bi bi-clock-history me-1"></i>En Atención
                                </span>
                                <div class="text-secondary small fw-medium">En Proceso</div>
                            </div>
                            <div class="p-2 rounded-circle bg-info bg-opacity-10 text-info">
                                <i class="bi bi-gear-wide-connected fs-4"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-2 my-2">
                            <span class="display-5 fw-bold text-dark" id="kpiTicketsEnProceso">-</span>
                            <span class="text-muted small">tickets</span>
                        </div>
                        <a href="modulos/soporte_tecnico/gestion_tickets.php?estado=en_proceso" class="btn btn-sm btn-outline-info w-100 mt-2 fw-semibold d-flex align-items-center justify-content-center gap-1">
                            <span>Ver En Proceso</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. Tickets Resueltos -->
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm border-0 border-start border-4 border-success rounded-3 bg-white">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-success bg-opacity-25 text-success fw-semibold small mb-1">
                                    <i class="bi bi-check-circle-fill me-1"></i>Completados
                                </span>
                                <div class="text-secondary small fw-medium">Resueltos / Cerrados</div>
                            </div>
                            <div class="p-2 rounded-circle bg-success bg-opacity-10 text-success">
                                <i class="bi bi-check-all fs-4"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-2 my-2">
                            <span class="display-5 fw-bold text-dark" id="kpiTicketsResueltos">-</span>
                            <span class="text-muted small">tickets</span>
                        </div>
                        <a href="modulos/soporte_tecnico/gestion_tickets.php?estado=resuelto" class="btn btn-sm btn-outline-success w-100 mt-2 fw-semibold d-flex align-items-center justify-content-center gap-1">
                            <span>Ver Resueltos</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 4. Fuerza Mayor / Contingencia -->
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm border-0 border-start border-4 rounded-3 bg-white" style="border-left-color: #a855f7 !important;">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge fw-semibold small mb-1" style="background-color: rgba(168, 85, 247, 0.2); color: #7e22ce;">
                                    <i class="bi bi-shield-slash me-1"></i>Excepcionales
                                </span>
                                <div class="text-secondary small fw-medium">Fuerza Mayor</div>
                            </div>
                            <div class="p-2 rounded-circle" style="background-color: rgba(168, 85, 247, 0.1); color: #9333ea;">
                                <i class="bi bi-shield-exclamation fs-4"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-2 my-2">
                            <span class="display-5 fw-bold text-dark" id="kpiTicketsFuerzaMayor">-</span>
                            <span class="text-muted small">casos</span>
                        </div>
                        <a href="modulos/soporte_tecnico/gestion_tickets.php?estado=fuerza_mayor" class="btn btn-sm w-100 mt-2 fw-semibold d-flex align-items-center justify-content-center gap-1" style="border: 1px solid #a855f7; color: #7e22ce;">
                            <span>Ver Contingencias</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================================================================
         2. TELEMETRÍA Y AUDITORÍA DE PCs (INTEGRACIÓN COSMOL-DIAGNOSTICOP)
         ==================================================================== -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-cpu text-warning"></i>
                <span>Telemetría y Salud de Hardware &bull; Agente Cosmol</span>
            </h2>
        </div>

        <div class="row g-3">
            <!-- Tarjeta: PCs en Alerta Técnica -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 border-start border-4 border-danger rounded-3 bg-white">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-danger bg-opacity-25 text-danger fw-semibold small mb-1">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Hardware Crítico / Advertencia
                                </span>
                                <div class="text-secondary small fw-medium">PCs en Alerta de Hardware</div>
                            </div>
                            <div class="p-2 rounded-circle bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-pc-display-horizontal fs-4"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-2 my-2">
                            <span class="display-5 fw-bold text-dark" id="kpiPcsAlerta">-</span>
                            <span class="text-muted small">equipos</span>
                        </div>
                        <a href="modulos/diagnostico/monitoreo_equipos.php?estado_salud=advertencia" class="btn btn-sm btn-outline-danger w-100 mt-2 fw-semibold d-flex align-items-center justify-content-center gap-1">
                            <span>Ver Equipos en Alerta</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta: Tablero General de PCs Auditadas -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 border-start border-4 border-primary rounded-3 bg-white">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-primary bg-opacity-25 text-primary fw-semibold small mb-1">
                                    <i class="bi bi-activity me-1"></i>Monitoreo en Red
                                </span>
                                <div class="text-secondary small fw-medium">Auditoría Total de Computadoras</div>
                            </div>
                            <div class="p-2 rounded-circle bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-motherboard fs-4"></i>
                            </div>
                        </div>
                        <p class="text-muted small my-2">
                            Inspección profunda de procesadores, SMART de almacenamiento, memoria y antivirus de toda la red institucional.
                        </p>
                        <a href="modulos/diagnostico/monitoreo_equipos.php" class="btn btn-sm btn-outline-primary w-100 mt-2 fw-semibold d-flex align-items-center justify-content-center gap-1">
                            <span>Abrir Tablero de PCs</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
/**
 * Función para consultar y actualizar en vivo los contadores del Dashboard de Métricas
 */
async function actualizarDashboardMetricas() {
    const btn = document.getElementById('btnRefrescarMetricas');
    const icono = document.getElementById('iconoRefrescarMetricas');
    const textoHora = document.getElementById('textoUltimaActualizacion');

    if (icono) icono.classList.add('spin-animado');
    if (btn) btn.disabled = true;

    try {
        if (typeof clienteApi !== 'undefined' && typeof clienteApi.actualizarKpisMenuOffcanvas === 'function') {
            await clienteApi.actualizarKpisMenuOffcanvas();
        }
        if (textoHora) {
            const ahora = new Date();
            textoHora.textContent = 'Actualizado a las ' + ahora.toLocaleTimeString();
        }
    } catch (e) {
        console.error('Error al actualizar métricas:', e);
    } finally {
        if (icono) icono.classList.remove('spin-animado');
        if (btn) btn.disabled = false;
    }
}

// Cargar métricas frescas al iniciar la pantalla
document.addEventListener('DOMContentLoaded', function() {
    actualizarDashboardMetricas();
});
</script>

<style>
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
