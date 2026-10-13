<?php
/**
 * Vista: Panel de Monitoreo y Diagnóstico de Equipos PC
 * Compatible con PHP 7.3 y PostgreSQL 13
 * Archivo: monitoreo_equipos.php
 */

$titulo_pagina = 'Monitoreo y Diagnóstico de Equipos';
$pagina_activa = 'diagnostico_monitoreo';
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
            <li class="breadcrumb-item text-muted">Auditoría Técnica</li>
            <li class="breadcrumb-item active fw-semibold" aria-current="page">Monitoreo de Equipos PC</li>
        </ol>
    </nav>

    <!-- Banner / Encabezado Principal -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white border-start border-4 border-primary">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill fw-semibold">
                            <i class="bi bi-cpu-fill me-1"></i> Telemetría y Salud de Hardware
                        </span>
                        <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill small" id="contadorActualizacion">
                            <i class="bi bi-clock me-1"></i> Auto-refresco activo
                        </span>
                    </div>
                    <h1 class="h3 fw-bold text-dark mb-1">Panel de Diagnóstico y Monitoreo de PCs</h1>
                    <p class="text-muted small mb-0">
                        Visualización en tiempo real del estado de hardware (CPU, RAM, Discos/SMART), antivirus y aplicaciones de los puestos de trabajo.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2 px-3 py-2 rounded-3" id="btnActualizar">
                        <i class="bi bi-arrow-clockwise fs-6" id="iconoRefresco"></i>
                        <span class="fw-semibold">Actualizar Ahora</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas KPI de Resumen Global -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="p-3 bg-primary-subtle text-primary rounded-3 fs-3">
                        <i class="bi bi-pc-display"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Equipos Monitoreados</div>
                        <div class="fs-4 fw-bold text-dark" id="kpiTotal">--</div>
                        <small class="text-muted" style="font-size: 0.72rem;">Registrados por agente</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="p-3 bg-success-subtle text-success rounded-3 fs-3">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Salud Óptima</div>
                        <div class="fs-4 fw-bold text-success" id="kpiOptimos">--</div>
                        <small class="text-muted" style="font-size: 0.72rem;">Sin anomalías de HW</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="p-3 bg-warning-subtle text-warning-emphasis rounded-3 fs-3">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Con Advertencias</div>
                        <div class="fs-4 fw-bold text-warning-emphasis" id="kpiAdvertencias">--</div>
                        <small class="text-muted" style="font-size: 0.72rem;">Espacio o RAM elevados</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="p-3 bg-danger-subtle text-danger rounded-3 fs-3">
                        <i class="bi bi-x-octagon-fill"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Estado Crítico</div>
                        <div class="fs-4 fw-bold text-danger" id="kpiCriticos">--</div>
                        <small class="text-muted" style="font-size: 0.72rem;">Falla SMART o disco < 10GB</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de Filtros y Búsqueda -->
    <div class="card border-0 shadow-sm rounded-3 bg-white mb-3">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-6 col-lg-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control border-start-0" id="inputBuscar" placeholder="Buscar por Hostname, MAC, Usuario o Marca...">
                    </div>
                </div>
                <div class="col-md-6 col-lg-7 d-flex justify-content-md-end gap-1 flex-wrap" id="filtrosEstado">
                    <button class="btn btn-sm btn-primary active-filtro btn-filtro" data-filtro="todos">Todos</button>
                    <button class="btn btn-sm btn-outline-success btn-filtro" data-filtro="optimo">Óptimos</button>
                    <button class="btn btn-sm btn-outline-warning btn-filtro" data-filtro="advertencia">Advertencias</button>
                    <button class="btn btn-sm btn-outline-danger btn-filtro" data-filtro="critico">Críticos</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla Principal de Monitoreo -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tablaEquipos">
                <thead class="table-light border-bottom text-secondary small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4">Equipo / Responsable</th>
                        <th>Red / MAC</th>
                        <th>CPU (Carga)</th>
                        <th>Memoria RAM</th>
                        <th>Almacenamiento</th>
                        <th>Antivirus</th>
                        <th>Último Reporte</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody id="cuerpoTablaEquipos">
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                            <div>Cargando telemetría de equipos...</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cuerpoTabla = document.getElementById('cuerpoTablaEquipos');
    const inputBuscar = document.getElementById('inputBuscar');
    const btnActualizar = document.getElementById('btnActualizar');
    const iconoRefresco = document.getElementById('iconoRefresco');
    const botonesFiltro = document.querySelectorAll('.btn-filtro');

    const kpiTotal = document.getElementById('kpiTotal');
    const kpiOptimos = document.getElementById('kpiOptimos');
    const kpiAdvertencias = document.getElementById('kpiAdvertencias');
    const kpiCriticos = document.getElementById('kpiCriticos');

    let equiposDatos = [];
    let filtroActivo = 'todos';

    // Determinar URL del backend dinámicamente
    const urlBackendBase = window.URL_BACKEND || 'http://' + window.location.hostname + ':83';
    const urlApiListar = urlBackendBase + '/api/diagnostico/listar.php';

    async function cargarEquipos() {
        iconoRefresco.classList.add('bi-arrow-repeat', 'spinner-border-sm');
        try {
            const resp = await fetch(urlApiListar);
            const data = await resp.json();

            if (data.exito && Array.isArray(data.datos)) {
                equiposDatos = data.datos;
                actualizarKPIs(equiposDatos);
                renderizarTabla();
            } else {
                cuerpoTabla.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> No se pudo cargar la información: ${data.mensaje || 'Error desconocido'}</td></tr>`;
            }
        } catch (err) {
            cuerpoTabla.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger"><i class="bi bi-wifi-off me-1"></i> No se pudo conectar al servidor de backend en ${urlBackendBase}</td></tr>`;
        } finally {
            iconoRefresco.classList.remove('bi-arrow-repeat', 'spinner-border-sm');
        }
    }

    function actualizarKPIs(lista) {
        kpiTotal.textContent = lista.length;
        const optimos = lista.filter(e => e.estado_salud_general === 'optimo' || e.estado_salud_general === 'bueno').length;
        const adv = lista.filter(e => e.estado_salud_general === 'advertencia').length;
        const crit = lista.filter(e => e.estado_salud_general === 'critico').length;

        kpiOptimos.textContent = optimos;
        kpiAdvertencias.textContent = adv;
        kpiCriticos.textContent = crit;
    }

    function renderizarTabla() {
        const busqueda = (inputBuscar.value || '').toLowerCase().trim();

        const filtrados = equiposDatos.filter(eq => {
            // Filtro por estado
            if (filtroActivo === 'optimo' && eq.estado_salud_general !== 'optimo' && eq.estado_salud_general !== 'bueno') return false;
            if (filtroActivo === 'advertencia' && eq.estado_salud_general !== 'advertencia') return false;
            if (filtroActivo === 'critico' && eq.estado_salud_general !== 'critico') return false;

            // Filtro por texto
            if (busqueda) {
                const texto = [
                    eq.hostname, eq.direccion_mac, eq.usuario_windows, 
                    eq.marca_modelo, eq.ip_local
                ].join(' ').toLowerCase();
                return texto.includes(busqueda);
            }
            return true;
        });

        if (filtrados.length === 0) {
            cuerpoTabla.innerHTML = `<tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>No se encontraron equipos que coincidan con el criterio.</td></tr>`;
            return;
        }

        let html = '';
        filtrados.forEach(eq => {
            // Badge general de salud
            let badgeSalud = '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Óptimo</span>';
            if (eq.estado_salud_general === 'advertencia') {
                badgeSalud = '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill">Advertencia</span>';
            } else if (eq.estado_salud_general === 'critico') {
                badgeSalud = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Crítico</span>';
            }

            // CPU
            const cpu = eq.detalle_cpu || {};
            const cpuCarga = parseFloat(eq.uso_cpu_porcentaje) || 0;
            let cpuColor = 'bg-primary';
            if (cpu.estado === 'advertencia') cpuColor = 'bg-warning';
            if (cpu.estado === 'critico') cpuColor = 'bg-danger';

            // RAM
            const ram = eq.detalle_ram || {};
            const ramUso = parseFloat(eq.uso_ram_porcentaje) || 0;
            let ramColor = 'bg-info';
            if (ram.estado === 'advertencia') ramColor = 'bg-warning';
            if (ram.estado === 'critico') ramColor = 'bg-danger';

            // Discos
            const discos = Array.isArray(eq.detalle_almacenamiento) ? eq.detalle_almacenamiento : [];
            let discosHtml = '';
            discos.forEach(d => {
                let badgeDisco = 'bg-light text-dark border';
                if (d.estado === 'advertencia') badgeDisco = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                if (d.estado === 'critico') badgeDisco = 'bg-danger-subtle text-danger border border-danger-subtle';
                discosHtml += `<span class="badge ${badgeDisco} me-1 mb-1" title="${d.alerta || 'Salud OK'}">${d.unidad} (${d.libre_gb} GB)</span>`;
            });
            if (!discosHtml) discosHtml = '<span class="text-muted small">--</span>';

            // Antivirus
            let avHtml = eq.antivirus_activo 
                ? '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill"><i class="bi bi-shield-check me-1"></i>Activo</span>'
                : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill"><i class="bi bi-shield-x me-1"></i>Inactivo</span>';

            // Fecha formateada
            const fechaStr = formatearFecha(eq.fecha_diagnostico);

            html += `
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 bg-light rounded-3 text-secondary border">
                                <i class="bi bi-laptop fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                    ${eq.hostname}
                                    ${badgeSalud}
                                </div>
                                <div class="small text-muted" style="font-size: 0.78rem;">
                                    <i class="bi bi-person me-1"></i>${eq.usuario_windows || 'Sesión no identificada'}
                                </div>
                                <div class="text-secondary" style="font-size: 0.72rem;">${eq.marca_modelo || 'Genérico'}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div><code class="text-dark bg-light px-2 py-1 rounded small border">${eq.direccion_mac}</code></div>
                        <div class="small text-muted mt-1" style="font-size: 0.75rem;"><i class="bi bi-hdd-network me-1"></i>${eq.ip_local}</div>
                    </td>
                    <td style="min-width: 140px;">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Carga:</span>
                            <span class="fw-semibold">${cpuCarga}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar ${cpuColor}" style="width: ${Math.min(cpuCarga, 100)}%"></div>
                        </div>
                        <small class="text-muted d-block mt-1 text-truncate" style="font-size: 0.7rem;" title="${cpu.modelo || ''}">${cpu.modelo || ''}</small>
                    </td>
                    <td style="min-width: 140px;">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">${ram.usado_gb || 0} / ${ram.total_gb || 0} GB</span>
                            <span class="fw-semibold">${ramUso}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar ${ramColor}" style="width: ${Math.min(ramUso, 100)}%"></div>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">Libre: ${ram.libre_gb || 0} GB</small>
                    </td>
                    <td>
                        <div class="d-flex flex-wrap">${discosHtml}</div>
                    </td>
                    <td>${avHtml}</td>
                    <td>
                        <div class="small text-dark fw-medium">${fechaStr.hora}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">${fechaStr.fecha}</div>
                    </td>
                    <td class="text-end pe-4">
                        <a href="detalle_equipo.php?mac=${encodeURIComponent(eq.direccion_mac)}" class="btn btn-sm btn-outline-primary px-3 rounded-pill d-inline-flex align-items-center gap-1 shadow-sm">
                            <i class="bi bi-speedometer2"></i>
                            <span>Ficha HW</span>
                        </a>
                    </td>
                </tr>
            `;
        });

        cuerpoTabla.innerHTML = html;
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

    // Eventos
    inputBuscar.addEventListener('input', renderizarTabla);

    btnActualizar.addEventListener('click', cargarEquipos);

    botonesFiltro.forEach(btn => {
        btn.addEventListener('click', function() {
            botonesFiltro.forEach(b => b.classList.remove('btn-primary', 'btn-success', 'btn-warning', 'btn-danger'));
            botonesFiltro.forEach(b => {
                const tipo = b.getAttribute('data-filtro');
                b.className = `btn btn-sm btn-filtro ${tipo === 'todos' ? 'btn-outline-primary' : (tipo === 'optimo' ? 'btn-outline-success' : (tipo === 'advertencia' ? 'btn-outline-warning' : 'btn-outline-danger'))}`;
            });

            this.classList.remove('btn-outline-primary', 'btn-outline-success', 'btn-outline-warning', 'btn-outline-danger');
            const f = this.getAttribute('data-filtro');
            if (f === 'todos') this.classList.add('btn-primary');
            else if (f === 'optimo') this.classList.add('btn-success');
            else if (f === 'advertencia') this.classList.add('btn-warning');
            else if (f === 'critico') this.classList.add('btn-danger');

            filtroActivo = f;
            renderizarTabla();
        });
    });

    // Carga inicial y auto-refresco cada 30 segundos
    cargarEquipos();
    setInterval(cargarEquipos, 30000);
});
</script>

<?php
require_once __DIR__ . '/../../componentes/pie_pagina.php';
?>
