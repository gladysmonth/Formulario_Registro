/**
 * Controlador JavaScript: Consulta y Seguimiento de Mis Solicitudes
 * Compatible con ES6 / Navegadores modernos
 * Archivo: front_forms/recursos/js/mis_solicitudes.js
 */

(function () {
    'use strict';

    let listaSolicitudes = [];
    const modalElemento = document.getElementById('modalDetalleSolicitud');
    let modalDetalleBS = null;

    document.addEventListener('DOMContentLoaded', () => {
        if (modalElemento && typeof bootstrap !== 'undefined') {
            modalDetalleBS = new bootstrap.Modal(modalElemento);
        }

        inicializarFiltros();
        cargarSolicitudes();
    });

    function inicializarFiltros() {
        const inputBuscar = document.getElementById('filtroBusquedaSolicitud');
        if (inputBuscar) {
            inputBuscar.addEventListener('input', filtrarSolicitudes);
        }

        const selectModulo = document.getElementById('filtroModuloSolicitud');
        if (selectModulo) {
            selectModulo.addEventListener('change', filtrarSolicitudes);
        }

        const selectEstado = document.getElementById('filtroEstadoSolicitud');
        if (selectEstado) {
            selectEstado.addEventListener('change', filtrarSolicitudes);
        }
    }

    async function cargarSolicitudes() {
        const tbody = document.getElementById('cuerpoTablaMisSolicitudes');
        if (!tbody) return;

        try {
            const respuesta = await clienteApi.listarMisSolicitudes();
            if (respuesta.exito && respuesta.datos) {
                listaSolicitudes = respuesta.datos.solicitudes || [];
                actualizarKpis(respuesta.datos.kpis || {});
                renderizarTabla(listaSolicitudes);
            }
        } catch (error) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        ${error.message || 'Error al consultar las solicitudes'}
                    </td>
                </tr>
            `;
        }
    }

    function actualizarKpis(kpis) {
        const elTotal = document.getElementById('kpiMisTotal');
        const elPendientes = document.getElementById('kpiMisPendientes');
        const elEnProceso = document.getElementById('kpiMisEnProceso');
        const elResueltas = document.getElementById('kpiMisResueltas');

        if (elTotal) elTotal.textContent = kpis.total || 0;
        if (elPendientes) elPendientes.textContent = kpis.pendientes || 0;
        if (elEnProceso) elEnProceso.textContent = kpis.en_proceso || 0;
        if (elResueltas) elResueltas.textContent = kpis.resueltas || 0;
    }

    function obtenerBadgeEstado(estado) {
        const est = (estado || '').toLowerCase();
        if (est === 'pendiente' || est === 'abierta') {
            return '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-2 py-1"><i class="bi bi-hourglass-split me-1"></i>En Espera</span>';
        } else if (est === 'en_proceso' || est === 'en_atencion') {
            return '<span class="badge bg-info-subtle text-info-emphasis border border-info px-2 py-1"><i class="bi bi-gear-wide-connected me-1"></i>En Atención</span>';
        } else if (est === 'resuelto' || est === 'atendido' || est === 'cerrada') {
            return '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i>Atendido</span>';
        } else if (est === 'cancelado' || est === 'rechazado') {
            return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="bi bi-x-circle-fill me-1"></i>Rechazado</span>';
        }
        return `<span class="badge bg-secondary px-2 py-1">${estado}</span>`;
    }

    function renderizarTabla(solicitudes) {
        const tbody = document.getElementById('cuerpoTablaMisSolicitudes');
        if (!tbody) return;

        if (solicitudes.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                        <p class="mb-1 fw-semibold text-dark">Aún no tienes requerimientos registrados</p>
                        <small class="text-muted d-block mb-3">Cuando envíes una solicitud de soporte, incidencia o accesos, podrás consultar su avance aquí.</small>
                        <a href="../../index.php" class="btn btn-outline-primary btn-sm fw-semibold">
                            <i class="bi bi-pencil-square me-1"></i> Ir al Catálogo de Formularios
                        </a>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = solicitudes.map(s => {
            let colorModulo = 'primary';
            let iconoModulo = 'bi-headset';
            if (s.modulo === 'incidencia') {
                colorModulo = 'danger';
                iconoModulo = 'bi-shield-exclamation';
            } else if (s.modulo === 'accesos') {
                colorModulo = 'info';
                iconoModulo = 'bi-shield-lock';
            }

            const badgeEstado = obtenerBadgeEstado(s.estado);
            const resumenCorto = s.resumen ? (s.resumen.length > 60 ? s.resumen.substring(0, 60) + '...' : s.resumen) : 'Sin detalle';

            return `
                <tr>
                    <td class="ps-4">
                        <strong class="font-monospace text-primary">${s.codigo}</strong>
                    </td>
                    <td>
                        <span class="badge bg-${colorModulo}-subtle text-${colorModulo} border border-${colorModulo}-subtle px-2 py-1">
                            <i class="bi ${iconoModulo} me-1"></i>${s.modulo_nombre}
                        </span>
                    </td>
                    <td class="text-muted">${s.fecha || '-'}</td>
                    <td>
                        <span class="text-dark d-inline-block text-truncate" style="max-width: 280px;" title="${s.resumen || ''}">
                            ${resumenCorto}
                        </span>
                    </td>
                    <td class="text-center">${badgeEstado}</td>
                    <td class="text-secondary small">
                        <i class="bi bi-person-fill text-muted me-1"></i>${s.tecnico || 'Pendiente'}
                    </td>
                    <td class="text-end pe-4">
                        <button type="button" class="btn btn-outline-secondary btn-sm" title="Ver detalle completo" onclick="window.seguimientoSolicitudes.abrirDetalle('${s.codigo}')">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function filtrarSolicitudes() {
        const texto = (document.getElementById('filtroBusquedaSolicitud')?.value || '').toLowerCase().trim();
        const modulo = document.getElementById('filtroModuloSolicitud')?.value || '';
        const estadoFiltro = document.getElementById('filtroEstadoSolicitud')?.value || '';

        const filtradas = listaSolicitudes.filter(s => {
            const coincideTexto = !texto ||
                s.codigo.toLowerCase().includes(texto) ||
                (s.resumen && s.resumen.toLowerCase().includes(texto)) ||
                (s.tecnico && s.tecnico.toLowerCase().includes(texto));

            const coincideModulo = !modulo || (s.modulo === modulo);

            let coincideEstado = true;
            if (estadoFiltro) {
                const est = (s.estado || '').toLowerCase();
                if (estadoFiltro === 'pendiente') {
                    coincideEstado = (est === 'pendiente' || est === 'abierta');
                } else if (estadoFiltro === 'en_proceso') {
                    coincideEstado = (est === 'en_proceso' || est === 'en_atencion');
                } else if (estadoFiltro === 'resuelto') {
                    coincideEstado = (est === 'resuelto' || est === 'atendido' || est === 'cerrada');
                }
            }

            return coincideTexto && coincideModulo && coincideEstado;
        });

        renderizarTabla(filtradas);
    }

    function abrirDetalle(codigo) {
        const s = listaSolicitudes.find(item => item.codigo === codigo);
        if (!s) return;

        document.getElementById('modalDetalleCodigo').textContent = s.codigo;
        document.getElementById('modalDetalleBadgeEstado').innerHTML = obtenerBadgeEstado(s.estado);
        document.getElementById('modalDetalleModulo').textContent = s.modulo_nombre;
        document.getElementById('modalDetalleFecha').textContent = s.fecha || 'No disponible';
        document.getElementById('modalDetalleResumen').textContent = s.resumen || 'Sin descripción registrada.';
        document.getElementById('modalDetalleTecnico').querySelector('span').textContent = s.tecnico || 'Pendiente de asignación';
        document.getElementById('modalDetallePrioridad').textContent = s.prioridad || 'Normal';

        if (modalDetalleBS) modalDetalleBS.show();
    }

    // Exponer función para botones de la tabla
    window.seguimientoSolicitudes = {
        abrirDetalle: abrirDetalle
    };
})();

