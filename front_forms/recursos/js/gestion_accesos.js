/**
 * Lógica del Panel de Gestión y Bandeja de Solicitudes de Accesos
 * Compatible con PHP 7.3 y Bootstrap 5
 * Archivo: front_forms/recursos/js/gestion_accesos.js
 */

document.addEventListener('DOMContentLoaded', () => {
    // Elementos de la interfaz
    const cuerpoTabla = document.getElementById('cuerpoTablaAccesos');
    const estadoVacio = document.getElementById('estadoVacioAccesos');
    const filtroBuscar = document.getElementById('filtroBuscarAccesos');
    const filtroEstado = document.getElementById('filtroEstadoAccesos');
    const filtroSistema = document.getElementById('filtroSistemaAccesos');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltrosAccesos');

    // Métricas KPI
    const kpiTotal = document.getElementById('kpiTotalAccesos');
    const kpiPendientes = document.getElementById('kpiPendientesAccesos');
    const kpiEnProceso = document.getElementById('kpiEnProcesoAccesos');
    const kpiAtendidas = document.getElementById('kpiAtendidasAccesos');

    // Modal
    const modalEl = document.getElementById('modalDetalleAccesos');
    const modalBs = modalEl ? new bootstrap.Modal(modalEl) : null;
    const btnImprimir = document.getElementById('btnImprimirFichaAccesos');

    // Formulario de edición rápida en modal
    const btnEditarAtencion = document.getElementById('btnEditarAtencionModal');
    const btnCancelarEdicion = document.getElementById('btnCancelarEdicionModal');
    const formAtencionModal = document.getElementById('formAtencionModalAccesos');
    const vistaLecturaAtencion = document.getElementById('vistaLecturaAtencion');

    let debounceTimer = null;
    let datosSolicitudActual = null;

    // ==========================================================
    // 1. CARGA DE REGISTROS Y MÉTRICAS KPI
    // ==========================================================
    async function cargarSolicitudes() {
        const filtros = {
            busqueda: filtroBuscar ? filtroBuscar.value.trim() : '',
            estado:   filtroEstado ? filtroEstado.value : '',
            sistema:  filtroSistema ? filtroSistema.value : ''
        };

        cuerpoTabla.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Actualizando solicitudes...
                </td>
            </tr>
        `;
        estadoVacio.classList.add('d-none');

        try {
            const respuesta = await clienteApi.listarSolicitudesAccesos(filtros);
            if (respuesta.exito) {
                // Actualizar métricas KPI
                const m = respuesta.datos.metricas;
                if (kpiTotal) kpiTotal.textContent = m.total;
                if (kpiPendientes) kpiPendientes.textContent = m.pendientes;
                if (kpiEnProceso) kpiEnProceso.textContent = m.en_proceso;
                if (kpiAtendidas) kpiAtendidas.textContent = m.atendidas;

                renderizarTabla(respuesta.datos.registros);
            } else {
                cuerpoTabla.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center text-danger py-4">
                            <i class="bi bi-exclamation-circle me-1"></i> ${respuesta.mensaje || 'Error al obtener datos'}
                        </td>
                    </tr>
                `;
            }
        } catch (error) {
            console.error('Error cargando solicitudes:', error);
            cuerpoTabla.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center text-danger py-4">
                        <i class="bi bi-wifi-off me-1"></i> Error de conexión con el servidor.
                    </td>
                </tr>
            `;
        }
    }

    function renderizarTabla(registros) {
        if (!Array.isArray(registros) || registros.length === 0) {
            cuerpoTabla.innerHTML = '';
            estadoVacio.classList.remove('d-none');
            return;
        }

        estadoVacio.classList.add('d-none');
        let html = '';

        registros.forEach(r => {
            // Badges de Estado
            let badgeEstado = '';
            if (r.estado === 'pendiente') {
                badgeEstado = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Pendiente</span>';
            } else if (r.estado === 'en_proceso') {
                badgeEstado = '<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">En Proceso</span>';
            } else if (r.estado === 'atendido') {
                badgeEstado = '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Atendido</span>';
            } else if (r.estado === 'rechazado') {
                badgeEstado = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Rechazado</span>';
            }

            // Badges de Plataformas
            let badgesSistemas = [];
            if (r.sistema_erp_sai) {
                badgesSistemas.push('<span class="badge bg-primary-subtle text-primary border me-1">SAI</span>');
            }
            if (r.sistema_cobranzas_netcob) {
                badgesSistemas.push('<span class="badge bg-info-subtle text-dark border me-1">NETCOB</span>');
            }
            if (r.sistema_otros) {
                badgesSistemas.push('<span class="badge bg-secondary-subtle text-secondary border">Otros</span>');
            }

            // Condición Usuario y Username
            let textoUsuario = r.es_usuario_nuevo 
                ? '<span class="badge bg-success-subtle text-success border me-1">Nuevo</span>' 
                : '<span class="badge bg-secondary-subtle text-secondary border me-1">Existente</span>';
            if (r.nombre_usuario_detalles) {
                textoUsuario += `<code class="small text-dark">${r.nombre_usuario_detalles}</code>`;
            }

            // Firmas
            let firmasHtml = `
                <span title="Solicitante: ${r.tiene_firma_solicitante ? 'Firmado' : 'Pendiente'}" class="badge ${r.tiene_firma_solicitante ? 'bg-success' : 'bg-secondary'} rounded-circle p-1 me-1">
                    <i class="bi bi-person" style="font-size: 0.65rem;"></i>
                </span>
                <span title="Autorización: ${r.tiene_firma_autoriza ? 'Firmado' : 'Sin firma'}" class="badge ${r.tiene_firma_autoriza ? 'bg-primary' : 'bg-secondary'} rounded-circle p-1 me-1">
                    <i class="bi bi-shield-check" style="font-size: 0.65rem;"></i>
                </span>
                <span title="Sistemas: ${r.tiene_firma_sistemas ? 'Firmado' : 'Sin firma'}" class="badge ${r.tiene_firma_sistemas ? 'bg-info' : 'bg-secondary'} rounded-circle p-1">
                    <i class="bi bi-cpu" style="font-size: 0.65rem;"></i>
                </span>
            `;

            // Botón de Acción
            let botonAccion = '';
            if (r.estado === 'atendido' || r.estado === 'rechazado') {
                botonAccion = `
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-ver-accesos" data-id="${r.id}" title="Ver Ficha">
                        <i class="bi bi-eye me-1"></i> Ver Ficha
                    </button>
                `;
            } else {
                botonAccion = `
                    <button type="button" class="btn btn-sm btn-outline-primary btn-atender-accesos" data-id="${r.id}" title="Atender Solicitud">
                        <i class="bi bi-tools me-1"></i> Atender
                    </button>
                `;
            }

            const fechaFormat = r.fecha_solicitud ? r.fecha_solicitud.substring(0, 16).replace('T', ' ') : '-';

            html += `
                <tr>
                    <td class="ps-3 font-monospace fw-bold text-primary">${r.nro_solicitud}</td>
                    <td class="text-nowrap text-secondary">${fechaFormat}</td>
                    <td class="fw-semibold text-dark">${r.nombre_solicitante}</td>
                    <td>
                        <div class="text-dark">${r.area_departamento}</div>
                        <small class="text-muted">${r.cargo_solicitante}</small>
                    </td>
                    <td>${badgesSistemas.join('') || '<span class="text-muted">-</span>'}</td>
                    <td>${textoUsuario}</td>
                    <td class="text-center">${badgeEstado}</td>
                    <td class="text-center">${firmasHtml}</td>
                    <td class="pe-3 text-end">${botonAccion}</td>
                </tr>
            `;
        });

        cuerpoTabla.innerHTML = html;

        // Listeners de botones en tabla
        document.querySelectorAll('.btn-ver-accesos').forEach(btn => {
            btn.addEventListener('click', () => abrirModalDetalle(btn.dataset.id, 'ver'));
        });

        document.querySelectorAll('.btn-atender-accesos').forEach(btn => {
            btn.addEventListener('click', () => abrirModalDetalle(btn.dataset.id, 'atender'));
        });
    }

    // ==========================================================
    // 2. MODAL DE DETALLE / ATENCIÓN
    // ==========================================================
    async function abrirModalDetalle(id, modo = 'ver') {
        try {
            const resp = await clienteApi.obtenerSolicitudAccesos(id);
            if (!resp.exito || !resp.datos) {
                clienteApi.mostrarToast('No se pudo cargar la información de la solicitud', 'error');
                return;
            }

            const d = resp.datos;
            datosSolicitudActual = d;

            // Datos Generales
            document.getElementById('modalCodigoAccesos').textContent = d.nro_solicitud;
            document.getElementById('modalNroSolicitud').textContent = d.nro_solicitud;
            document.getElementById('modalFechaSolicitud').textContent = d.fecha_solicitud ? d.fecha_solicitud.replace('T', ' ') : '-';
            document.getElementById('modalNombreSolicitante').textContent = d.nombre_solicitante;
            document.getElementById('modalCargoSolicitante').textContent = d.cargo_solicitante;
            document.getElementById('modalAreaDepartamento').textContent = d.area_departamento;

            // Badge de Estado
            const badgeEst = document.getElementById('modalBadgeEstado');
            badgeEst.textContent = d.estado.toUpperCase().replace('_', ' ');
            if (d.estado === 'pendiente') {
                badgeEst.className = 'badge bg-warning text-dark fw-bold px-3 py-2 fs-6 rounded-pill';
            } else if (d.estado === 'en_proceso') {
                badgeEst.className = 'badge bg-info text-white fw-bold px-3 py-2 fs-6 rounded-pill';
            } else if (d.estado === 'atendido') {
                badgeEst.className = 'badge bg-success text-white fw-bold px-3 py-2 fs-6 rounded-pill';
            } else {
                badgeEst.className = 'badge bg-danger text-white fw-bold px-3 py-2 fs-6 rounded-pill';
            }

            // Plataformas
            const badgeSai = document.getElementById('badgeModalSai');
            const badgeNetcob = document.getElementById('badgeModalNetcob');
            const badgeOtros = document.getElementById('badgeModalOtros');
            const detOtros = document.getElementById('modalDetalleOtros');

            badgeSai.className = d.sistema_erp_sai ? 'badge bg-primary text-white border' : 'badge bg-light text-muted border';
            badgeSai.textContent = d.sistema_erp_sai ? 'SOLICITADO' : 'No';

            badgeNetcob.className = d.sistema_cobranzas_netcob ? 'badge bg-primary text-white border' : 'badge bg-light text-muted border';
            badgeNetcob.textContent = d.sistema_cobranzas_netcob ? 'SOLICITADO' : 'No';

            badgeOtros.className = d.sistema_otros ? 'badge bg-primary text-white border' : 'badge bg-light text-muted border';
            badgeOtros.textContent = d.sistema_otros ? 'SOLICITADO' : 'No';
            detOtros.textContent = d.sistema_otros_detalle ? `Detalle: ${d.sistema_otros_detalle}` : '';

            // Datos Usuario
            const badgeCond = document.getElementById('modalCondicionUsuario');
            badgeCond.textContent = d.es_usuario_nuevo ? 'USUARIO NUEVO' : 'CUENTA EXISTENTE';
            badgeCond.className = d.es_usuario_nuevo ? 'badge bg-success-subtle text-success border px-2 py-1 fs-6 mt-1' : 'badge bg-secondary-subtle text-secondary border px-2 py-1 fs-6 mt-1';

            document.getElementById('modalUsernameDetalles').textContent = d.nombre_usuario_detalles || '(Por asignar)';

            // Requerimientos
            document.getElementById('modalRequerimientosAccesos').textContent = d.requerimientos_accesos;

            // Atención de Sistemas
            document.getElementById('modalAtendidoPor').textContent = d.atendido_por || 'No asignado aún';
            document.getElementById('modalFechaAtencion').textContent = d.fecha_hora_atencion ? d.fecha_hora_atencion.replace('T', ' ') : 'Pendiente';
            document.getElementById('modalComentariosSistemas').textContent = d.comentarios_sistemas || 'Sin observaciones registradas';

            // Firmas
            renderizarFirmaModal('modalImgFirmaSolicitante', 'modalSinFirmaSolicitante', d.firma_solicitante);
            renderizarFirmaModal('modalImgFirmaAutoriza', 'modalSinFirmaAutoriza', d.firma_autoriza);
            renderizarFirmaModal('modalImgFirmaSistemas', 'modalSinFirmaSistemas', d.firma_sistemas);

            document.getElementById('modalNombrePieSolicitante').textContent = d.nombre_solicitante ? d.nombre_solicitante.toUpperCase() : 'SOLICITANTE';
            document.getElementById('modalNombrePieAutoriza').textContent = d.nombre_autoriza ? d.nombre_autoriza.toUpperCase() : 'AUTORIZA (JEFATURA)';

            // Modo de visualización vs atención
            if (modo === 'atender') {
                mostrarFormularioEdicion(d);
            } else {
                ocultarFormularioEdicion();
            }

            modalBs.show();
        } catch (error) {
            console.error('Error al abrir detalle:', error);
            clienteApi.mostrarToast('Ocurrió un error al consultar el registro', 'error');
        }
    }

    function renderizarFirmaModal(imgId, textoId, base64) {
        const img = document.getElementById(imgId);
        const txt = document.getElementById(textoId);
        if (!img || !txt) return;

        if (base64 && base64.startsWith('data:image/')) {
            img.src = base64;
            img.style.display = 'inline-block';
            txt.style.display = 'none';
        } else {
            img.src = '';
            img.style.display = 'none';
            txt.style.display = 'block';
        }
    }

    function mostrarFormularioEdicion(d) {
        if (!formAtencionModal || !vistaLecturaAtencion) return;
        document.getElementById('editAccesosId').value = d.id;
        document.getElementById('editAtendidoPor').value = d.atendido_por || '';
        document.getElementById('editFechaAtencion').value = d.fecha_hora_atencion ? d.fecha_hora_atencion.substring(0, 16) : new Date().toISOString().substring(0, 16);
        document.getElementById('editEstadoAccesos').value = d.estado || 'atendido';
        document.getElementById('editComentariosSistemas').value = d.comentarios_sistemas || '';

        vistaLecturaAtencion.classList.add('d-none');
        formAtencionModal.classList.remove('d-none');
    }

    function ocultarFormularioEdicion() {
        if (!formAtencionModal || !vistaLecturaAtencion) return;
        formAtencionModal.classList.add('d-none');
        vistaLecturaAtencion.classList.remove('d-none');
    }

    if (btnEditarAtencion) {
        btnEditarAtencion.addEventListener('click', () => {
            if (datosSolicitudActual) {
                mostrarFormularioEdicion(datosSolicitudActual);
            }
        });
    }

    if (btnCancelarEdicion) {
        btnCancelarEdicion.addEventListener('click', () => {
            ocultarFormularioEdicion();
        });
    }

    // Guardar Atención Técnica desde el Modal
    if (formAtencionModal) {
        formAtencionModal.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = parseInt(document.getElementById('editAccesosId').value, 10);
            const atendidoPor = document.getElementById('editAtendidoPor').value.trim();
            const fechaAtencion = document.getElementById('editFechaAtencion').value;
            const estado = document.getElementById('editEstadoAccesos').value;
            const comentarios = document.getElementById('editComentariosSistemas').value.trim();

            if (!atendidoPor) {
                alert('Debe especificar el nombre del técnico responsable.');
                return;
            }

            const btnGuardar = document.getElementById('btnGuardarEdicionModal');
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

            try {
                const resp = await clienteApi.actualizarSolicitudAccesos({
                    id:                  id,
                    atendido_por:        atendidoPor,
                    fecha_hora_atencion: fechaAtencion,
                    estado:              estado,
                    comentarios_sistemas: comentarios
                });

                if (resp.exito) {
                    clienteApi.mostrarToast('¡Atención técnica actualizada con éxito!', 'exito');
                    ocultarFormularioEdicion();
                    modalBs.hide();
                    cargarSolicitudes();
                } else {
                    alert('Error: ' + (resp.mensaje || 'No se pudo actualizar'));
                }
            } catch (error) {
                console.error('Error al actualizar atención:', error);
                alert('Error de conexión al actualizar la atención.');
            } finally {
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Guardar Atención';
            }
        });
    }

    // ==========================================================
    // 3. IMPRESIÓN OFICIAL DE LA FICHA TÉCNICA
    // ==========================================================
    if (btnImprimir) {
        btnImprimir.addEventListener('click', () => {
            window.print();
        });
    }

    // ==========================================================
    // 4. EVENTOS DE FILTROS Y BÚSQUEDA
    // ==========================================================
    if (filtroBuscar) {
        filtroBuscar.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(cargarSolicitudes, 300);
        });
    }

    if (filtroEstado) {
        filtroEstado.addEventListener('change', cargarSolicitudes);
    }

    if (filtroSistema) {
        filtroSistema.addEventListener('change', cargarSolicitudes);
    }

    if (btnLimpiarFiltros) {
        btnLimpiarFiltros.addEventListener('click', () => {
            if (filtroBuscar) filtroBuscar.value = '';
            if (filtroEstado) filtroEstado.value = '';
            if (filtroSistema) filtroSistema.value = '';
            cargarSolicitudes();
        });
    }

    // Carga inicial
    cargarSolicitudes();
});
