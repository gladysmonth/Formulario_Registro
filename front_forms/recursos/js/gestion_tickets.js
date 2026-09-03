/**
 * Lógica del Panel de Gestión y Atención de Tickets de Soporte
 * Archivo: gestion_tickets.js
 */

document.addEventListener('DOMContentLoaded', () => {
    const tablaCuerpo = document.getElementById('cuerpoTablaTickets');
    const filtroEstado = document.getElementById('filtroEstado');
    const filtroPrioridad = document.getElementById('filtroPrioridad');
    const inputBuscar = document.getElementById('inputBuscar');
    const btnRefrescar = document.getElementById('btnRefrescar');

    // Elementos del Modal de Atención
    const modalElemento = document.getElementById('modalAtencionTicket');
    const modalAtencion = modalElemento ? new bootstrap.Modal(modalElemento) : null;
    const formAtencion = document.getElementById('formAtencionTicket');
    const btnGuardarAtencion = document.getElementById('btnGuardarAtencion');

    let ticketsActuales = [];

    // Cargar tickets inicialmente
    cargarTickets();

    // Eventos de filtrado
    if (filtroEstado) filtroEstado.addEventListener('change', cargarTickets);
    if (filtroPrioridad) filtroPrioridad.addEventListener('change', cargarTickets);
    if (btnRefrescar) btnRefrescar.addEventListener('click', cargarTickets);

    if (inputBuscar) {
        let timerBusqueda;
        inputBuscar.addEventListener('input', () => {
            clearTimeout(timerBusqueda);
            timerBusqueda = setTimeout(cargarTickets, 400);
        });
    }

    async function cargarTickets() {
        if (tablaCuerpo) {
            tablaCuerpo.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        Cargando tickets de soporte...
                    </td>
                </tr>
            `;
        }

        const filtros = {
            estado: filtroEstado ? filtroEstado.value : '',
            prioridad: filtroPrioridad ? filtroPrioridad.value : '',
            buscar: inputBuscar ? inputBuscar.value.trim() : ''
        };

        try {
            const respuesta = await clienteApi.listarTicketsSoporte(filtros);
            if (respuesta.exito) {
                ticketsActuales = respuesta.datos.tickets;
                actualizarMetricas(respuesta.datos.metricas);
                renderizarTabla(ticketsActuales);
            }
        } catch (error) {
            if (tablaCuerpo) {
                tablaCuerpo.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-4 text-danger">
                            <i class="bi bi-exclamation-octagon fs-4 d-block mb-2"></i>
                            No se pudo cargar la lista de tickets. Verifique que el servicio backend esté activo.
                        </td>
                    </tr>
                `;
            }
        }
    }

    function actualizarMetricas(metricas) {
        if (!metricas) return;
        const elTotal = document.getElementById('metricaTotal');
        const elPendientes = document.getElementById('metricaPendientes');
        const elEnProceso = document.getElementById('metricaEnProceso');
        const elResueltos = document.getElementById('metricaResueltos');

        if (elTotal) elTotal.textContent = metricas.total || 0;
        if (elPendientes) elPendientes.textContent = metricas.pendientes || 0;
        if (elEnProceso) elEnProceso.textContent = metricas.en_proceso || 0;
        if (elResueltos) elResueltos.textContent = metricas.resueltos || 0;
    }

    function renderizarTabla(tickets) {
        if (!tablaCuerpo) return;

        if (tickets.length === 0) {
            tablaCuerpo.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        No se encontraron tickets con los criterios de búsqueda seleccionados.
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        tickets.forEach(ticket => {
            const badgePrioridad = obtenerBadgePrioridad(ticket.prioridad);
            const badgeEstado = obtenerBadgeEstado(ticket.estado);
            const tipoSoporte = [];
            if (ticket.soporte_hardware) tipoSoporte.push('<span class="badge bg-secondary-subtle text-dark border"><i class="bi bi-cpu me-1"></i>Hardware</span>');
            if (ticket.soporte_software) tipoSoporte.push('<span class="badge bg-secondary-subtle text-dark border"><i class="bi bi-code-slash me-1"></i>Software</span>');

            html += `
                <tr>
                    <td class="fw-semibold text-nowrap">
                        <span class="text-primary">${escapeHtml(ticket.codigo_ticket)}</span>
                    </td>
                    <td>
                        <div class="fw-medium">${escapeHtml(ticket.nombre_solicitante)}</div>
                        <small class="text-muted">${escapeHtml(ticket.departamento_area)}</small>
                    </td>
                    <td class="text-nowrap">${ticket.fecha_solicitud}</td>
                    <td>
                        <div class="d-flex flex-wrap gap-1">${tipoSoporte.join(' ')}</div>
                    </td>
                    <td>${badgePrioridad}</td>
                    <td>${badgeEstado}</td>
                    <td>
                        <small class="text-secondary">${ticket.tecnico_asignado ? escapeHtml(ticket.tecnico_asignado) : '<em class="text-muted">Sin asignar</em>'}</small>
                    </td>
                    <td class="text-end text-nowrap">
                        <button class="btn btn-sm btn-outline-primary btn-atender" data-id="${ticket.id}">
                            <i class="bi bi-pencil-square me-1"></i> Atender
                        </button>
                    </td>
                </tr>
            `;
        });

        tablaCuerpo.innerHTML = html;

        // Asignar eventos de clic a los botones "Atender"
        document.querySelectorAll('.btn-atender').forEach(boton => {
            boton.addEventListener('click', () => {
                const id = boton.getAttribute('data-id');
                abrirModalAtencion(id);
            });
        });
    }

    function abrirModalAtencion(id) {
        const ticket = ticketsActuales.find(t => String(t.id) === String(id));
        if (!ticket || !modalAtencion) return;

        // Llenar información de cabecera en el modal
        document.getElementById('modalTicketCodigo').textContent = ticket.codigo_ticket;
        document.getElementById('modalTicketSolicitante').textContent = ticket.nombre_solicitante;
        document.getElementById('modalTicketArea').textContent = ticket.departamento_area;
        document.getElementById('modalTicketDescripcion').textContent = ticket.descripcion_problema;

        // Equipo afectado
        const equipoInfo = [];
        if (ticket.numero_serie) equipoInfo.push(`N° Serie: ${ticket.numero_serie}`);
        if (ticket.marca_modelo) equipoInfo.push(`Marca/Modelo: ${ticket.marca_modelo}`);
        if (ticket.sistema_operativo) equipoInfo.push(`S.O.: ${ticket.sistema_operativo}`);
        document.getElementById('modalTicketEquipo').textContent = equipoInfo.length > 0 ? equipoInfo.join(' | ') : 'No especificado / No aplica';

        // Llenar campos editables
        document.getElementById('atencion_ticket_id').value = ticket.id;
        document.getElementById('atencion_prioridad').value = ticket.prioridad || 'media';
        document.getElementById('atencion_estado').value = ticket.estado || 'pendiente';
        document.getElementById('atencion_tecnico').value = ticket.tecnico_asignado || '';
        
        // Fecha hora de atención
        const inputFechaHora = document.getElementById('atencion_fecha_hora');
        if (inputFechaHora) {
            if (ticket.fecha_hora_atencion) {
                inputFechaHora.value = ticket.fecha_hora_atencion.replace(' ', 'T').substring(0, 16);
            } else {
                const ahora = new Date();
                const pad = (n) => String(n).padStart(2, '0');
                const fechaLocal = `${ahora.getFullYear()}-${pad(ahora.getMonth()+1)}-${pad(ahora.getDate())}T${pad(ahora.getHours())}:${pad(ahora.getMinutes())}`;
                inputFechaHora.value = fechaLocal;
            }
        }

        document.getElementById('atencion_diagnostico').value = ticket.diagnostico || '';
        document.getElementById('atencion_solucion').value = ticket.solucion_aplicada || '';
        document.getElementById('atencion_tipo_resolucion').value = ticket.tipo_resolucion || '';
        document.getElementById('atencion_observaciones').value = ticket.observaciones_recomendacion || '';

        modalAtencion.show();
    }

    if (formAtencion) {
        formAtencion.addEventListener('submit', async (e) => {
            e.preventDefault();

            const datosActualizados = {
                id: document.getElementById('atencion_ticket_id').value,
                prioridad: document.getElementById('atencion_prioridad').value,
                estado: document.getElementById('atencion_estado').value,
                tecnico_asignado: document.getElementById('atencion_tecnico').value,
                fecha_hora_atencion: document.getElementById('atencion_fecha_hora').value,
                diagnostico: document.getElementById('atencion_diagnostico').value,
                solucion_aplicada: document.getElementById('atencion_solucion').value,
                tipo_resolucion: document.getElementById('atencion_tipo_resolucion').value,
                observaciones_recomendacion: document.getElementById('atencion_observaciones').value
            };

            const textoOriginal = btnGuardarAtencion ? btnGuardarAtencion.innerHTML : 'Guardar';
            if (btnGuardarAtencion) {
                btnGuardarAtencion.disabled = true;
                btnGuardarAtencion.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';
            }

            try {
                const res = await clienteApi.actualizarTicketSoporte(datosActualizados);
                if (res.exito) {
                    if (modalAtencion) modalAtencion.hide();
                    clienteApi.mostrarToast('Ticket de soporte actualizado correctamente', 'exito');
                    cargarTickets();
                }
            } catch (err) {
                Swal.fire({
                    title: 'Error al actualizar',
                    text: err.message || 'No se pudo guardar la atención del ticket',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            } finally {
                if (btnGuardarAtencion) {
                    btnGuardarAtencion.disabled = false;
                    btnGuardarAtencion.innerHTML = textoOriginal;
                }
            }
        });
    }

    function obtenerBadgePrioridad(prioridad) {
        switch ((prioridad || '').toLowerCase()) {
            case 'urgente':
                return '<span class="badge bg-danger"><i class="bi bi-exclamation-diamond me-1"></i>Urgente</span>';
            case 'alta':
                return '<span class="badge bg-warning text-dark"><i class="bi bi-arrow-up-circle me-1"></i>Alta</span>';
            case 'media':
                return '<span class="badge bg-primary-subtle text-primary border"><i class="bi bi-dash-circle me-1"></i>Media</span>';
            case 'baja':
                return '<span class="badge bg-success-subtle text-success border"><i class="bi bi-arrow-down-circle me-1"></i>Baja</span>';
            default:
                return `<span class="badge bg-light text-dark">${escapeHtml(prioridad)}</span>`;
        }
    }

    function obtenerBadgeEstado(estado) {
        switch ((estado || '').toLowerCase()) {
            case 'pendiente':
                return '<span class="badge badge-pendiente"><i class="bi bi-clock me-1"></i>Pendiente</span>';
            case 'en_proceso':
                return '<span class="badge badge-en-proceso"><i class="bi bi-gear me-1"></i>En Proceso</span>';
            case 'resuelto':
                return '<span class="badge badge-resuelto"><i class="bi bi-check2-circle me-1"></i>Resuelto</span>';
            case 'cancelado':
                return '<span class="badge badge-cancelado"><i class="bi bi-x-circle me-1"></i>Cancelado</span>';
            default:
                return `<span class="badge bg-secondary">${escapeHtml(estado)}</span>`;
        }
    }

    function escapeHtml(texto) {
        if (!texto) return '';
        const div = document.createElement('div');
        div.textContent = texto;
        return div.innerHTML;
    }
});
