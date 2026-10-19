/**
 * Lógica Cliente: Monitoreo y Diagnóstico de Equipos PC
 * Archivo: front_forms/recursos/js/monitoreo_equipos.js
 */

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
        if (iconoRefresco) {
            iconoRefresco.classList.add('bi-arrow-repeat', 'spinner-border-sm');
        }
        try {
            const resp = await fetch(urlApiListar);
            const data = await resp.json();

            if (data.exito && data.datos) {
                const lista = Array.isArray(data.datos.equipos) ? data.datos.equipos : (Array.isArray(data.datos) ? data.datos : []);
                equiposDatos = lista;
                if (data.datos.metricas) {
                    if (kpiTotal) kpiTotal.textContent = data.datos.metricas.total || lista.length;
                    if (kpiOptimos) kpiOptimos.textContent = (parseInt(data.datos.metricas.optimos, 10) || 0) + (parseInt(data.datos.metricas.buenos, 10) || 0);
                    if (kpiAdvertencias) kpiAdvertencias.textContent = data.datos.metricas.advertencias || 0;
                    if (kpiCriticos) kpiCriticos.textContent = data.datos.metricas.criticos || 0;
                } else {
                    actualizarKPIs(equiposDatos);
                }
                renderizarTabla();
            } else {
                if (cuerpoTabla) {
                    cuerpoTabla.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> No se pudo cargar la información: ${data.mensaje || 'Error desconocido'}</td></tr>`;
                }
            }
        } catch (err) {
            if (cuerpoTabla) {
                cuerpoTabla.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger"><i class="bi bi-wifi-off me-1"></i> No se pudo conectar al servidor de backend en ${urlBackendBase}</td></tr>`;
            }
        } finally {
            if (iconoRefresco) {
                iconoRefresco.classList.remove('bi-arrow-repeat', 'spinner-border-sm');
            }
        }
    }

    function actualizarKPIs(lista) {
        if (kpiTotal) kpiTotal.textContent = lista.length;
        const optimos = lista.filter(e => e.estado_salud_general === 'optimo' || e.estado_salud_general === 'bueno').length;
        const adv = lista.filter(e => e.estado_salud_general === 'advertencia').length;
        const crit = lista.filter(e => e.estado_salud_general === 'critico').length;

        if (kpiOptimos) kpiOptimos.textContent = optimos;
        if (kpiAdvertencias) kpiAdvertencias.textContent = adv;
        if (kpiCriticos) kpiCriticos.textContent = crit;
    }

    function renderizarTabla() {
        if (!cuerpoTabla) return;
        const busqueda = (inputBuscar ? inputBuscar.value : '').toLowerCase().trim();

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
        } catch (e) {
            return { fecha: isoString, hora: '' };
        }
    }

    // Eventos
    if (inputBuscar) {
        inputBuscar.addEventListener('input', renderizarTabla);
    }

    if (btnActualizar) {
        btnActualizar.addEventListener('click', cargarEquipos);
    }

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
