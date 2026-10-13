<?php
/**
 * Vista: Radiografía Técnica Detallada de Equipo (Estilo HWMonitor + Forense)
 * Compatible con PHP 7.3 y PostgreSQL 13
 * Archivo: detalle_equipo.php
 */

$titulo_pagina = 'Ficha Técnica de Equipo - Diagnóstico';
$pagina_activa = 'diagnostico_detalle';
$nivel_ruta = '../../';
$roles_requeridos = array('admin', 'tecnico');

require_once __DIR__ . '/../../componentes/encabezado.php';
require_once __DIR__ . '/../../componentes/barra_navegacion.php';
?>

<main class="container-fluid px-3 px-lg-4 my-4">
    <!-- Migas de Pan (Breadcrumbs) -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="../../index.php" class="text-decoration-none"><i class="bi bi-house-door me-1"></i>Inicio</a></li>
            <li class="breadcrumb-item"><a href="monitoreo_equipos.php" class="text-decoration-none">Monitoreo de Equipos</a></li>
            <li class="breadcrumb-item active fw-semibold" aria-current="page" id="breadcrumbHostname">Cargando Ficha...</li>
        </ol>
    </nav>

    <!-- Barra Superior con Botón de Regreso -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="monitoreo_equipos.php" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2 px-3 py-1 rounded-3">
            <i class="bi bi-arrow-left"></i>
            <span class="fw-semibold">Volver a la Lista de Equipos</span>
        </a>
        <div class="d-flex gap-2">
            <button class="btn btn-light border btn-sm d-inline-flex align-items-center gap-2 px-3 py-1 rounded-3" onclick="window.print()">
                <i class="bi bi-printer"></i>
                <span>Imprimir Reporte</span>
            </button>
            <button class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-2 px-3 py-1 rounded-3" id="btnRecargar">
                <i class="bi bi-arrow-clockwise" id="iconoRecarga"></i>
                <span>Refrescar</span>
            </button>
        </div>
    </div>

    <!-- Spinner de Carga -->
    <div id="cargandoDetalle" class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white mb-4">
        <div class="spinner-border text-primary mx-auto mb-3" role="status"></div>
        <h5 class="text-dark fw-bold mb-1">Cargando telemetría del equipo...</h5>
        <p class="text-muted small mb-0">Consultando hardware, SMART y auditoría forense en PostgreSQL.</p>
    </div>

    <!-- Contenedor Principal de la Ficha (Oculto mientras carga) -->
    <div id="contenidoDetalle" style="display: none;">

        <!-- Cabecera de la Ficha del Equipo -->
        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden border-top border-4 border-primary">
            <div class="card-body p-4">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-primary-subtle text-primary rounded-4 fs-2">
                                <i class="bi bi-display"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h2 class="h3 fw-bold text-dark mb-0" id="txtHostname">--</h2>
                                    <span id="badgeSaludGlobal"></span>
                                    <span class="badge bg-light text-secondary border rounded-pill" id="txtTipoEvento">Programado</span>
                                </div>
                                <div class="text-muted small mt-1">
                                    <span class="me-3"><i class="bi bi-person-badge me-1"></i><strong class="text-dark" id="txtUsuario">--</strong></span>
                                    <span class="me-3"><i class="bi bi-hdd-network me-1"></i>IP: <span id="txtIpLocal">--</span></span>
                                    <span><i class="bi bi-upc-scan me-1"></i>MAC: <code class="text-dark" id="txtMac">--</code></span>
                                </div>
                                <div class="text-secondary small mt-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-motherboard me-1"></i>Placa / Modelo: <span id="txtPlacaModelo">--</span>
                                    &bull; Serial: <span id="txtSerial">--</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="text-muted small">Último reporte recibido:</div>
                        <div class="fw-bold text-dark fs-6" id="txtFechaReporte">--</div>
                        <div class="text-muted small" style="font-size: 0.72rem;" id="txtFechaRelativa">--</div>
                    </div>
                </div>
            </div>

            <!-- Alertas Activas en Cabecera (si las hay) -->
            <div id="alertaBanner" class="p-3 bg-warning-subtle border-top border-warning-subtle d-none">
                <div class="d-flex align-items-center gap-2 text-warning-emphasis small fw-medium">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div id="alertaBannerTexto">Se han detectado advertencias en este equipo.</div>
                </div>
            </div>
        </div>

        <!-- Pestañas de Navegación de la Ficha -->
        <ul class="nav nav-pills gap-2 mb-4 p-2 bg-white rounded-3 shadow-sm border" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active px-4 py-2 rounded-3 d-flex align-items-center gap-2 fw-semibold" id="tab-hardware-tab" data-bs-toggle="pill" data-bs-target="#tab-hardware" type="button" role="tab">
                    <i class="bi bi-speedometer2"></i> Hardware & Sensores
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-4 py-2 rounded-3 d-flex align-items-center gap-2 fw-semibold" id="tab-seguridad-tab" data-bs-toggle="pill" data-bs-target="#tab-seguridad" type="button" role="tab">
                    <i class="bi bi-shield-check"></i> Software & Auditoría Forense
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-4 py-2 rounded-3 d-flex align-items-center gap-2 fw-semibold" id="tab-historial-tab" data-bs-toggle="pill" data-bs-target="#tab-historial" type="button" role="tab">
                    <i class="bi bi-clock-history"></i> Historial de Diagnósticos
                </button>
            </li>
        </ul>

        <!-- Contenido de las Pestañas -->
        <div class="tab-content" id="pills-tabContent">

            <!-- PESTAÑA 1: HARDWARE Y SENSORES (HWMonitor) -->
            <div class="tab-pane fade show active" id="tab-hardware" role="tabpanel">
                <div class="row g-4 mb-4">
                    
                    <!-- Tarjeta Procesador (CPU) -->
                    <div class="col-md-6 col-xl-6">
                        <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-2 bg-primary-subtle text-primary rounded-3">
                                        <i class="bi bi-cpu fs-5"></i>
                                    </div>
                                    <h5 class="fw-bold mb-0 text-dark">Procesador (CPU)</h5>
                                </div>
                                <span id="badgeEstadoCpu"></span>
                            </div>
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-dark mb-3" id="txtCpuModelo">--</h6>
                                
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="text-muted">Carga del Procesador:</span>
                                        <span class="fw-bold fs-6" id="txtCpuCargaValor">0%</span>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-primary" id="progresoCpu" style="width: 0%"></div>
                                    </div>
                                </div>

                                <div class="row g-3 text-center border-top pt-3">
                                    <div class="col-4 border-end">
                                        <div class="text-muted small" style="font-size: 0.72rem;">Núcleos Físicos</div>
                                        <div class="fs-5 fw-bold text-dark" id="txtCpuNucleos">--</div>
                                    </div>
                                    <div class="col-4 border-end">
                                        <div class="text-muted small" style="font-size: 0.72rem;">Hilos Lógicos</div>
                                        <div class="fs-5 fw-bold text-dark" id="txtCpuHilos">--</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-muted small" style="font-size: 0.72rem;">Temperatura</div>
                                        <div class="fs-5 fw-bold text-dark" id="txtCpuTemp">N/A</div>
                                    </div>
                                </div>
                                
                                <div id="alertaCpuCaja" class="alert alert-warning py-2 px-3 small mt-3 mb-0 d-none"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta Memoria RAM -->
                    <div class="col-md-6 col-xl-6">
                        <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-2 bg-info-subtle text-info-emphasis rounded-3">
                                        <i class="bi bi-memory fs-5"></i>
                                    </div>
                                    <h5 class="fw-bold mb-0 text-dark">Memoria RAM</h5>
                                </div>
                                <span id="badgeEstadoRam"></span>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-baseline mb-2">
                                    <span class="text-muted small">Uso de Memoria Física:</span>
                                    <span class="fw-bold fs-6" id="txtRamUsoValor">0%</span>
                                </div>
                                
                                <div class="progress mb-4" style="height: 10px;">
                                    <div class="progress-bar bg-info" id="progresoRam" style="width: 0%"></div>
                                </div>

                                <div class="row g-3 text-center border-top pt-3">
                                    <div class="col-3 border-end">
                                        <div class="text-muted small" style="font-size: 0.72rem;">Total</div>
                                        <div class="fs-5 fw-bold text-dark" id="txtRamTotal">--</div>
                                    </div>
                                    <div class="col-3 border-end">
                                        <div class="text-muted small" style="font-size: 0.72rem;">En Uso</div>
                                        <div class="fs-5 fw-bold text-primary" id="txtRamUsado">--</div>
                                    </div>
                                    <div class="col-3 border-end">
                                        <div class="text-muted small" style="font-size: 0.72rem;">Libre</div>
                                        <div class="fs-5 fw-bold text-success" id="txtRamLibre">--</div>
                                    </div>
                                    <div class="col-3">
                                        <div class="text-muted small" style="font-size: 0.72rem;">Velocidad</div>
                                        <div class="fs-5 fw-bold text-secondary" id="txtRamVelocidad">--</div>
                                    </div>
                                </div>

                                <div id="alertaRamCaja" class="alert alert-warning py-2 px-3 small mt-3 mb-0 d-none"></div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Unidades de Almacenamiento y Salud SMART -->
                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 bg-secondary-subtle text-dark rounded-3">
                                <i class="bi bi-hdd-stack fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">Almacenamiento y Salud de Discos (SMART)</h5>
                                <small class="text-muted">Particiones lógicas y estado físico preventivo</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3" id="contenedorDiscos">
                            <!-- Inyectado dinámicamente -->
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de Placa Madre y Red -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="bi bi-motherboard text-primary fs-5"></i>
                                <h6 class="fw-bold mb-0 text-dark">Placa Base & BIOS</h6>
                            </div>
                            <div class="small text-muted mb-2">Fabricante: <strong class="text-dark" id="txtPlacaFab">--</strong></div>
                            <div class="small text-muted mb-2">Modelo de Tarjeta: <strong class="text-dark" id="txtPlacaMod">--</strong></div>
                            <div class="small text-muted">Número de Serie: <strong class="text-dark" id="txtPlacaSer">--</strong></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-ethernet text-success fs-5"></i>
                                    <h6 class="fw-bold mb-0 text-dark">Adaptador de Red</h6>
                                </div>
                                <span id="badgeEstadoRed"></span>
                            </div>
                            <div class="small text-muted mb-2">Controlador: <strong class="text-dark" id="txtRedNombre">--</strong></div>
                            <div class="small text-muted mb-2">Puerta de Enlace (Gateway): <strong class="text-dark" id="txtRedGateway">--</strong></div>
                            <div class="small text-muted">Servidores DNS: <span id="txtRedDns">--</span></div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- PESTAÑA 2: AUDITORÍA FORENSE & SOFTWARE -->
            <div class="tab-pane fade" id="tab-seguridad" role="tabpanel">
                
                <!-- Estado Antivirus Windows Defender -->
                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-3 bg-success-subtle text-success rounded-3 fs-3" id="iconoAvBox">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="fw-bold mb-0 text-dark" id="txtAvNombre">Windows Defender</h5>
                                        <span id="badgeEstadoAv"></span>
                                    </div>
                                    <div class="text-muted small mt-1" id="txtAvDetalle">Protección activa y definiciones al día.</div>
                                </div>
                            </div>
                            <div class="text-md-end">
                                <div class="small text-muted">Última actualización de firmas:</div>
                                <div class="fw-semibold text-dark small" id="txtAvFirmasFecha">--</div>
                                <div class="small text-danger fw-bold" id="txtAvAmenazas">0 Amenazas detectadas</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Últimas Apps (Prefetch) -->
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h5 class="fw-bold mb-0 text-dark">Aplicaciones Recientes</h5>
                                        <small class="text-muted">Auditoría de ejecución (Windows Prefetch / Recent)</small>
                                    </div>
                                    <span class="badge bg-light text-secondary border">Últimos accesos</span>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle mb-0">
                                        <thead class="table-light text-secondary small" style="font-size: 0.72rem;">
                                            <tr>
                                                <th>Ejecutable / Aplicación</th>
                                                <th>Última Ejecución</th>
                                                <th class="text-end">Fuente</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cuerpoTablaPrefetch">
                                            <tr><td colspan="3" class="text-center py-3 text-muted">Cargando aplicaciones...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Descargas Recientes y Alertas -->
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                                <h5 class="fw-bold mb-0 text-dark">Descargas Recientes</h5>
                                <small class="text-muted">Inspección de archivos en carpeta Downloads</small>
                            </div>
                            <div class="card-body p-4">
                                <div class="list-group list-group-flush" id="listaDescargas">
                                    <div class="text-muted small text-center py-3">Cargando descargas...</div>
                                </div>
                            </div>
                        </div>

                        <!-- Resumen de Alertas -->
                        <div class="card border-0 shadow-sm rounded-4 bg-white">
                            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                                <h5 class="fw-bold mb-0 text-dark">Registro de Alertas</h5>
                                <small class="text-muted">Advertencias generadas durante el escaneo</small>
                            </div>
                            <div class="card-body p-4" id="contenedorAlertasLog">
                                <div class="text-muted small text-center py-2">Sin alertas activas.</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- PESTAÑA 3: HISTORIAL DE TELEMETRÍA -->
            <div class="tab-pane fade" id="tab-historial" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Historial de Reportes Cronológicos</h5>
                            <small class="text-muted">Evolución de salud, CPU, RAM y almacenamiento en el tiempo</small>
                        </div>
                        <span class="badge bg-secondary-subtle text-secondary border">Últimos 20 snapshots</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary small text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha y Hora</th>
                                    <th>Tipo Evento</th>
                                    <th>Estado Salud</th>
                                    <th>Carga CPU</th>
                                    <th>Uso RAM</th>
                                    <th>Espacio C:</th>
                                    <th>SMART</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpoTablaHistorial">
                                <tr><td colspan="8" class="text-center py-4 text-muted">Cargando historial...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const macParam = params.get('mac') || '';
    const idParam = params.get('id') || '';

    const cargando = document.getElementById('cargandoDetalle');
    const contenido = document.getElementById('contenidoDetalle');
    const iconoRecarga = document.getElementById('iconoRecarga');
    const btnRecargar = document.getElementById('btnRecargar');

    const urlBackendBase = window.URL_BACKEND || 'http://' + window.location.hostname + ':83';
    let urlApiDetalle = urlBackendBase + '/api/diagnostico/detalle.php';
    if (macParam) {
        urlApiDetalle += '?mac=' + encodeURIComponent(macParam);
    } else if (idParam) {
        urlApiDetalle += '?id=' + encodeURIComponent(idParam);
    }

    async function cargarDetalle() {
        iconoRecarga.classList.add('bi-arrow-repeat', 'spinner-border-sm');
        try {
            const resp = await fetch(urlApiDetalle);
            const data = await resp.json();

            if (data.exito && data.datos && data.datos.actual) {
                renderizarFicha(data.datos.actual, data.datos.historial || []);
                cargando.style.display = 'none';
                contenido.style.display = 'block';
            } else {
                cargando.innerHTML = `
                    <div class="text-danger py-4">
                        <i class="bi bi-x-circle fs-1 mb-2 d-block"></i>
                        <h5>No se encontró el equipo</h5>
                        <p class="small text-muted">${data.mensaje || 'No hay diagnósticos disponibles con los parámetros solicitados.'}</p>
                        <a href="monitoreo_equipos.php" class="btn btn-outline-primary btn-sm mt-2">Volver al Monitoreo</a>
                    </div>
                `;
            }
        } catch (err) {
            cargando.innerHTML = `
                <div class="text-danger py-4">
                    <i class="bi bi-wifi-off fs-1 mb-2 d-block"></i>
                    <h5>Error de conexión con el backend</h5>
                    <p class="small text-muted">No se pudo obtener respuesta desde ${urlBackendBase}</p>
                    <button class="btn btn-primary btn-sm mt-2" onclick="location.reload()">Reintentar</button>
                </div>
            `;
        } finally {
            iconoRecarga.classList.remove('bi-arrow-repeat', 'spinner-border-sm');
        }
    }

    function renderizarFicha(d, historial) {
        // Cabecera
        document.getElementById('breadcrumbHostname').textContent = d.hostname || 'Equipo';
        document.getElementById('txtHostname').textContent = d.hostname || '--';
        document.getElementById('txtUsuario').textContent = d.usuario_windows || 'Sesión no identificada';
        document.getElementById('txtIpLocal').textContent = d.ip_local || '--';
        document.getElementById('txtMac').textContent = d.direccion_mac || '--';
        document.getElementById('txtTipoEvento').textContent = (d.tipo_evento || 'programado').toUpperCase();

        const placa = d.detalle_placa_bios || {};
        document.getElementById('txtPlacaModelo').textContent = `${placa.fabricante || ''} ${placa.modelo || ''}`.trim() || 'Genérico';
        document.getElementById('txtSerial').textContent = placa.serial || d.numero_serie || 'N/A';

        // Badge global
        const badgeSalud = document.getElementById('badgeSaludGlobal');
        if (d.estado_salud_general === 'critico') {
            badgeSalud.className = 'badge bg-danger text-white rounded-pill px-3 py-1';
            badgeSalud.textContent = 'Crítico';
        } else if (d.estado_salud_general === 'advertencia') {
            badgeSalud.className = 'badge bg-warning text-dark rounded-pill px-3 py-1';
            badgeSalud.textContent = 'Advertencia';
        } else {
            badgeSalud.className = 'badge bg-success text-white rounded-pill px-3 py-1';
            badgeSalud.textContent = 'Salud Óptima';
        }

        // Fechas
        const fReporte = formatearFecha(d.fecha_diagnostico);
        document.getElementById('txtFechaReporte').textContent = `${fReporte.fecha} ${fReporte.hora}`;
        document.getElementById('txtFechaRelativa').textContent = 'Hora del servidor UTC/Local';

        // Alertas banner
        const alertas = Array.isArray(d.alertas_detectadas) ? d.alertas_detectadas : [];
        const bannerAlerta = document.getElementById('alertaBanner');
        if (alertas.length > 0) {
            bannerAlerta.classList.remove('d-none');
            document.getElementById('alertaBannerTexto').textContent = `${alertas.length} advertencia(s) detectada(s): ${alertas[0]}`;
        } else {
            bannerAlerta.classList.add('d-none');
        }

        // 1. CPU
        const cpu = d.detalle_cpu || {};
        document.getElementById('txtCpuModelo').textContent = cpu.modelo || 'Procesador Windows';
        const cpuCarga = parseFloat(d.uso_cpu_porcentaje) || 0;
        document.getElementById('txtCpuCargaValor').textContent = `${cpuCarga}%`;
        const progCpu = document.getElementById('progresoCpu');
        progCpu.style.width = `${Math.min(cpuCarga, 100)}%`;
        progCpu.className = `progress-bar ${cpu.estado === 'critico' ? 'bg-danger' : (cpu.estado === 'advertencia' ? 'bg-warning' : 'bg-primary')}`;

        document.getElementById('txtCpuNucleos').textContent = cpu.nucleos || '--';
        document.getElementById('txtCpuHilos').textContent = cpu.hilos || '--';
        document.getElementById('txtCpuTemp').textContent = cpu.temperatura_c ? `${cpu.temperatura_c} °C` : 'N/A';

        const badgeCpu = document.getElementById('badgeEstadoCpu');
        badgeCpu.className = `badge ${cpu.estado === 'critico' ? 'bg-danger-subtle text-danger' : (cpu.estado === 'advertencia' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success')} border rounded-pill`;
        badgeCpu.textContent = (cpu.estado || 'optimo').toUpperCase();

        const cajaAlertaCpu = document.getElementById('alertaCpuCaja');
        if (cpu.alerta) {
            cajaAlertaCpu.textContent = cpu.alerta;
            cajaAlertaCpu.classList.remove('d-none');
        } else {
            cajaAlertaCpu.classList.add('d-none');
        }

        // 2. RAM
        const ram = d.detalle_ram || {};
        const ramUso = parseFloat(d.uso_ram_porcentaje) || 0;
        document.getElementById('txtRamUsoValor').textContent = `${ramUso}%`;
        const progRam = document.getElementById('progresoRam');
        progRam.style.width = `${Math.min(ramUso, 100)}%`;
        progRam.className = `progress-bar ${ram.estado === 'critico' ? 'bg-danger' : (ram.estado === 'advertencia' ? 'bg-warning' : 'bg-info')}`;

        document.getElementById('txtRamTotal').textContent = `${ram.total_gb || 0} GB`;
        document.getElementById('txtRamUsado').textContent = `${ram.usado_gb || 0} GB`;
        document.getElementById('txtRamLibre').textContent = `${ram.libre_gb || 0} GB`;
        document.getElementById('txtRamVelocidad').textContent = ram.velocidad_mhz ? `${ram.velocidad_mhz} MHz` : '--';

        const badgeRam = document.getElementById('badgeEstadoRam');
        badgeRam.className = `badge ${ram.estado === 'critico' ? 'bg-danger-subtle text-danger' : (ram.estado === 'advertencia' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success')} border rounded-pill`;
        badgeRam.textContent = (ram.estado || 'optimo').toUpperCase();

        const cajaAlertaRam = document.getElementById('alertaRamCaja');
        if (ram.alerta) {
            cajaAlertaRam.textContent = ram.alerta;
            cajaAlertaRam.classList.remove('d-none');
        } else {
            cajaAlertaRam.classList.add('d-none');
        }

        // 3. Discos
        const discos = Array.isArray(d.detalle_almacenamiento) ? d.detalle_almacenamiento : [];
        const contDiscos = document.getElementById('contenedorDiscos');
        contDiscos.innerHTML = '';
        discos.forEach(disco => {
            const pctUsado = disco.tamano_gb > 0 ? Math.round(((disco.tamano_gb - disco.libre_gb) / disco.tamano_gb) * 100) : 0;
            let barraColor = 'bg-success';
            if (disco.estado === 'advertencia') barraColor = 'bg-warning';
            if (disco.estado === 'critico') barraColor = 'bg-danger';

            contDiscos.innerHTML += `
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold fs-5 text-dark">${disco.unidad}</span>
                                <span class="badge ${disco.estado === 'critico' ? 'bg-danger text-white' : (disco.estado === 'advertencia' ? 'bg-warning text-dark' : 'bg-success text-white')} rounded-pill" style="font-size: 0.65rem;">
                                    ${(disco.estado || 'OK').toUpperCase()}
                                </span>
                            </div>
                            <div class="text-secondary small mb-2 text-truncate" title="${disco.modelo || ''}">${disco.modelo || 'Disco Físico'}</div>
                            
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Libre: <strong>${disco.libre_gb} GB</strong></span>
                                <span>Total: ${disco.tamano_gb} GB</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar ${barraColor}" style="width: ${pctUsado}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between align-items-center small pt-2 border-top">
                                <span class="text-muted" style="font-size: 0.72rem;">Salud SMART:</span>
                                <span class="badge ${disco.smart_status === 'OK' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'} border rounded-pill">
                                    ${disco.smart_status || 'OK'}
                                </span>
                            </div>
                            ${disco.alerta ? `<div class="small text-danger fw-medium mt-2" style="font-size: 0.72rem;"><i class="bi bi-exclamation-triangle me-1"></i>${disco.alerta}</div>` : ''}
                        </div>
                    </div>
                </div>
            `;
        });

        // Placa y Red
        document.getElementById('txtPlacaFab').textContent = placa.fabricante || 'N/A';
        document.getElementById('txtPlacaMod').textContent = placa.modelo || 'N/A';
        document.getElementById('txtPlacaSer').textContent = placa.serial || 'N/A';

        const red = d.detalle_red || {};
        document.getElementById('txtRedNombre').textContent = red.adaptador || 'Adaptador genérico';
        document.getElementById('txtRedGateway').textContent = red.gateway || 'Sin puerta de enlace';
        document.getElementById('txtRedDns').textContent = Array.isArray(red.dns) ? red.dns.join(', ') : (red.dns || '--');

        const badgeRed = document.getElementById('badgeEstadoRed');
        badgeRed.className = `badge ${red.estado === 'advertencia' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success'} border rounded-pill`;
        badgeRed.textContent = (red.estado || 'optimo').toUpperCase();

        // 4. Antivirus
        const av = d.seguridad_antivirus || {};
        document.getElementById('txtAvNombre').textContent = av.nombre || 'Antivirus de Windows';
        document.getElementById('txtAvFirmasFecha').textContent = av.ultima_definicion || 'Al día';
        document.getElementById('txtAvAmenazas').textContent = `${av.amenazas_activas || 0} Amenazas detectadas`;

        const badgeAv = document.getElementById('badgeEstadoAv');
        badgeAv.className = `badge ${av.estado === 'critico' ? 'bg-danger text-white' : (av.estado === 'advertencia' ? 'bg-warning text-dark' : 'bg-success text-white')} rounded-pill px-3 py-1`;
        badgeAv.textContent = (av.estado || 'optimo').toUpperCase();

        // 5. Prefetch Apps
        const prefetch = Array.isArray(d.apps_recientes_prefetch) ? d.apps_recientes_prefetch : [];
        const tablaPf = document.getElementById('cuerpoTablaPrefetch');
        if (prefetch.length > 0) {
            let pfHtml = '';
            prefetch.forEach(app => {
                pfHtml += `
                    <tr>
                        <td>
                            <div class="fw-semibold text-dark small"><i class="bi bi-app me-1 text-primary"></i>${app.ejecutable}</div>
                        </td>
                        <td class="small text-muted">${app.ultima_ejecucion || '--'}</td>
                        <td class="text-end"><span class="badge bg-light text-secondary border">${app.fuente || 'Prefetch'}</span></td>
                    </tr>
                `;
            });
            tablaPf.innerHTML = pfHtml;
        } else {
            tablaPf.innerHTML = `<tr><td colspan="3" class="text-center py-3 text-muted small">Sin registros recientes de Prefetch.</td></tr>`;
        }

        // 6. Descargas
        const descargas = Array.isArray(d.descargas_recientes) ? d.descargas_recientes : [];
        const listaDesc = document.getElementById('listaDescargas');
        if (descargas.length > 0) {
            let descHtml = '';
            descargas.forEach(f => {
                descHtml += `
                    <div class="list-group-item px-0 py-2 border-0 border-bottom d-flex justify-content-between align-items-center">
                        <div class="text-truncate me-2" style="max-width: 250px;">
                            <div class="small fw-semibold text-dark text-truncate" title="${f.archivo}">${f.archivo}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">${f.fecha || ''} &bull; ${f.tamano_kb} KB</div>
                        </div>
                        ${f.riesgo_potencial ? '<span class="badge bg-danger-subtle text-danger border rounded-pill" style="font-size: 0.65rem;">Riesgo .exe/.zip</span>' : '<span class="badge bg-light text-muted border" style="font-size: 0.65rem;">Seguro</span>'}
                    </div>
                `;
            });
            listaDesc.innerHTML = descHtml;
        } else {
            listaDesc.innerHTML = `<div class="text-muted small text-center py-3">No hay descargas registradas en los últimos días.</div>`;
        }

        // 7. Alertas Log
        const logAlertas = document.getElementById('contenedorAlertasLog');
        if (alertas.length > 0) {
            let aHtml = '';
            alertas.forEach(a => {
                aHtml += `<div class="alert alert-warning py-2 px-3 small mb-2 d-flex align-items-center gap-2"><i class="bi bi-exclamation-triangle-fill"></i><span>${a}</span></div>`;
            });
            logAlertas.innerHTML = aHtml;
        } else {
            logAlertas.innerHTML = `<div class="alert alert-success py-2 px-3 small mb-0 d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill"></i><span>Todo en orden. No se detectaron fallos de hardware ni amenazas.</span></div>`;
        }

        // 8. Historial
        const tablaHist = document.getElementById('cuerpoTablaHistorial');
        if (historial.length > 0) {
            let histHtml = '';
            historial.forEach(h => {
                const fH = formatearFecha(h.fecha_diagnostico);
                let badgeH = '<span class="badge bg-success-subtle text-success">Óptimo</span>';
                if (h.estado_salud_general === 'advertencia') badgeH = '<span class="badge bg-warning-subtle text-warning-emphasis">Advertencia</span>';
                if (h.estado_salud_general === 'critico') badgeH = '<span class="badge bg-danger-subtle text-danger">Crítico</span>';

                histHtml += `
                    <tr>
                        <td class="fw-bold">#${h.id}</td>
                        <td class="small">
                            <span class="text-dark fw-medium">${fH.fecha}</span>
                            <span class="text-muted ms-1">${fH.hora}</span>
                        </td>
                        <td><span class="badge bg-light text-dark border text-uppercase" style="font-size: 0.65rem;">${h.tipo_evento}</span></td>
                        <td>${badgeH}</td>
                        <td class="small">${h.uso_cpu_porcentaje}%</td>
                        <td class="small">${h.uso_ram_porcentaje}%</td>
                        <td class="small">${h.espacio_libre_c_gb} GB</td>
                        <td><span class="badge ${h.salud_disco_smart === 'OK' ? 'bg-success-subtle text-success' : 'bg-danger text-white'}">${h.salud_disco_smart}</span></td>
                    </tr>
                `;
            });
            tablaHist.innerHTML = histHtml;
        } else {
            tablaHist.innerHTML = `<tr><td colspan="8" class="text-center py-3 text-muted small">No hay historial previo registrado para este equipo.</td></tr>`;
        }
    }

    function formatearFecha(isoString) {
        if (!isoString) return { fecha: '--', hora: '--' };
        try {
            const d = new Date(isoString);
            return {
                fecha: d.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' }),
                hora: d.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })
            };
        } catch {
            return { fecha: isoString, hora: '' };
        }
    }

    btnRecargar.addEventListener('click', cargarDetalle);

    cargarDetalle();
});
</script>

<?php
require_once __DIR__ . '/../../componentes/pie_pagina.php';
?>
