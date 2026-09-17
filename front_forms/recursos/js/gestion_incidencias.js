/**
 * Lógica del Panel de Gestión de Incidencias de Sistemas (FOR_RIS_001)
 * Compatible con PHP 7.3 y Bootstrap 5
 * Archivo: front_forms/recursos/js/gestion_incidencias.js
 */

document.addEventListener('DOMContentLoaded', () => {
    // Referencias al DOM
    const cuerpoTabla = document.getElementById('cuerpoTablaIncidencias');
    const textoResumen = document.getElementById('textoResumenTablaRI');
    const filtroBuscar = document.getElementById('filtroBuscarRI');
    const filtroCriticidad = document.getElementById('filtroCriticidadRI');
    const filtroEstado = document.getElementById('filtroEstadoRI');
    const filtroSistema = document.getElementById('filtroSistemaRI');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltrosRI');

    // Métricas KPI
    const kpiTotal = document.getElementById('kpiTotalIncidencias');
    const kpiCriticas = document.getElementById('kpiCriticasNivel1');
    const kpiPendientes = document.getElementById('kpiPendientes');
    const kpiResueltas = document.getElementById('kpiResueltas');

    // Modal
    const modalElemento = document.getElementById('modalDetalleIncidencia');
    const bsModal = modalElemento ? new bootstrap.Modal(modalElemento) : null;
    const btnImprimir = document.getElementById('btnImprimirFichaRI');
    const btnToggleAtencion = document.getElementById('btnToggleFormularioAtencion');
    const contenedorFormAtencion = document.getElementById('modalFormularioAtencion');
    const btnGuardarSolucionModal = document.getElementById('btnGuardarSolucionModal');

    let incidenciaActualCargada = null;

    // ==========================================================
    // 1. CARGA DE REGISTROS Y MÉTRICAS
    // ==========================================================
    async function cargarIncidencias() {
        cuerpoTabla.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-danger me-2" role="status"></div>
                    Consultando incidencias...
                </td>
            </tr>
        `;

        const params = {
            buscar: filtroBuscar ? filtroBuscar.value.trim() : '',
            criticidad: filtroCriticidad ? filtroCriticidad.value : '',
            estado: filtroEstado ? filtroEstado.value : '',
            sistema: filtroSistema ? filtroSistema.value : ''
        };

        try {
            const res = await clienteApi.listarIncidencias(params);

            if (res.exito) {
                // Actualizar KPIs
                const m = res.datos.metricas || {};
                if (kpiTotal) kpiTotal.textContent = m.total || 0;
                if (kpiCriticas) kpiCriticas.textContent = m.criticas_nivel_1 || 0;
                if (kpiPendientes) kpiPendientes.textContent = m.abiertas_atencion || 0;
                if (kpiResueltas) kpiResueltas.textContent = m.resueltas_cerradas || 0;

                renderizarTabla(res.datos.registros || []);
            } else {
                cuerpoTabla.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-4 text-danger">
                            <i class="bi bi-exclamation-triangle me-1"></i> ${res.mensaje || 'Error al obtener registros.'}
                        </td>
                    </tr>
                `;
            }
        } catch (error) {
            console.error('Error al cargar incidencias:', error);
            cuerpoTabla.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-danger">
                        <i class="bi bi-wifi-off me-1"></i> No fue posible conectar con el servidor backend.
                    </td>
                </tr>
            `;
        }
    }

    // ==========================================================
    // 2. RENDERIZAR TABLA DE INCIDENCIAS
    // ==========================================================
    function renderizarTabla(registros) {
        if (registros.length === 0) {
            cuerpoTabla.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                        No se encontraron incidencias registradas con los filtros seleccionados.
                    </td>
                </tr>
            `;
            if (textoResumen) textoResumen.textContent = 'Mostrando 0 incidencias';
            return;
        }

        if (textoResumen) {
            textoResumen.textContent = `Mostrando ${registros.length} incidencia${registros.length > 1 ? 's' : ''}`;
        }

        cuerpoTabla.innerHTML = registros.map(item => {
            // Badges de Sistemas
            const sistemas = [];
            if (item.sistema_erp_sai) sistemas.push('<span class="badge bg-primary-subtle text-primary border">ERP SAI</span>');
            if (item.sistema_cobranzas_netcob) sistemas.push('<span class="badge bg-success-subtle text-success border">NETCOB</span>');
            if (item.sistema_otros) sistemas.push('<span class="badge bg-warning-subtle text-dark border">Otros</span>');
            const sistemasHtml = sistemas.length > 0 ? sistemas.join(' ') : '<span class="text-muted small">N/A</span>';

            // Badge Criticidad
            let criticidadHtml = '';
            if (item.nivel_criticidad === 'critica_nivel_1') {
                criticidadHtml = '<span class="badge bg-danger text-white"><i class="bi bi-exclamation-triangle-fill me-1"></i> Nivel 1: Crítica</span>';
            } else if (item.nivel_criticidad === 'alta_nivel_2') {
                criticidadHtml = '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-circle me-1"></i> Nivel 2: Alta</span>';
            } else {
                criticidadHtml = '<span class="badge bg-primary text-white">Nivel 3: Media/Baja</span>';
            }

            // Badge Estado
            let estadoHtml = '';
            if (item.estado === 'abierta') {
                estadoHtml = '<span class="badge bg-danger-subtle text-danger border">Abierta</span>';
            } else if (item.estado === 'en_atencion') {
                estadoHtml = '<span class="badge bg-warning-subtle text-dark border">En Atención</span>';
            } else if (item.estado === 'resuelta') {
                estadoHtml = '<span class="badge bg-success-subtle text-success border">Resuelta</span>';
            } else if (item.estado === 'cerrada') {
                estadoHtml = '<span class="badge bg-success text-white">Cerrada</span>';
            }

            // Formato de fecha
            const fechaFormateada = item.fecha_hora_reporte 
                ? item.fecha_hora_reporte.substring(0, 16).replace('T', ' ') 
                : '-';

            const esCerrada = item.estado === 'resuelta' || item.estado === 'cerrada';
            const btnAccion = esCerrada
                ? `<button type="button" class="btn btn-outline-secondary btn-sm btn-ver-incidencia" data-id="${item.id}" title="Ver Ficha Oficial">
                       <i class="bi bi-eye me-1"></i> Ver Ficha
                   </button>`
                : `<button type="button" class="btn btn-outline-danger btn-sm btn-ver-incidencia" data-id="${item.id}" title="Atender o Resolver">
                       <i class="bi bi-tools me-1"></i> Atender
                   </button>`;

            return `
                <tr>
                    <td class="ps-3 fw-bold font-monospace text-danger">${item.nro_incidencia}</td>
                    <td class="small text-secondary">${fechaFormateada}</td>
                    <td class="fw-semibold text-dark">${item.responsable_reporte}</td>
                    <td>${sistemasHtml}</td>
                    <td>${criticidadHtml}</td>
                    <td>${estadoHtml}</td>
                    <td class="text-end pe-3">
                        ${btnAccion}
                    </td>
                </tr>
            `;
        }).join('');

        // Vincular clics de botones "Ver / Atender"
        document.querySelectorAll('.btn-ver-incidencia').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                abrirModalDetalle(id);
            });
        });
    }

    // ==========================================================
    // 3. ABRIR MODAL Y CARGAR FICHA TÉCNICA
    // ==========================================================
    async function abrirModalDetalle(id) {
        if (!bsModal) return;

        try {
            const res = await clienteApi.obtenerIncidencia(id);
            if (!res.exito || !res.datos) {
                Swal.fire('Error', res.mensaje || 'No se pudo cargar la incidencia', 'error');
                return;
            }

            const item = res.datos;
            incidenciaActualCargada = item;

            // Datos de cabecera
            document.getElementById('modalCodigoIncidencia').textContent = item.nro_incidencia;
            document.getElementById('modalNroIncidencia').textContent = item.nro_incidencia;
            document.getElementById('modalFechaHoraReporte').textContent = item.fecha_hora_reporte 
                ? item.fecha_hora_reporte.substring(0, 16).replace('T', ' ') 
                : '-';
            document.getElementById('modalResponsableReporte').textContent = item.responsable_reporte;
            document.getElementById('modalPieResponsable').textContent = item.responsable_reporte;

            // Estado Badge en Header
            const badgeEstado = document.getElementById('modalBadgeEstado');
            if (badgeEstado) {
                badgeEstado.textContent = item.estado ? item.estado.toUpperCase() : 'ABIERTA';
                badgeEstado.className = item.estado === 'resuelta' || item.estado === 'cerrada'
                    ? 'badge bg-white text-success fw-bold px-3 py-2 fs-6 rounded-pill'
                    : 'badge bg-white text-danger fw-bold px-3 py-2 fs-6 rounded-pill';
            }

            // Criticidad Badge
            const critBadge = document.getElementById('modalCriticidadBadge');
            if (critBadge) {
                if (item.nivel_criticidad === 'critica_nivel_1') {
                    critBadge.innerHTML = '<span class="badge bg-danger text-white">Nivel 1: Crítica (Facturación/Cajas)</span>';
                } else if (item.nivel_criticidad === 'alta_nivel_2') {
                    critBadge.innerHTML = '<span class="badge bg-warning text-dark">Nivel 2: Alta (Módulo Importante)</span>';
                } else {
                    critBadge.innerHTML = '<span class="badge bg-primary text-white">Nivel 3: Media / Baja</span>';
                }
            }

            // Plataformas
            const contenedorSistemas = document.getElementById('modalSistemasBadges');
            if (contenedorSistemas) {
                const arr = [];
                if (item.sistema_erp_sai) arr.push('<span class="badge bg-primary fs-6 px-3 py-2"><i class="bi bi-hdd-network me-1"></i> SISTEMA ERP - SAI</span>');
                if (item.sistema_cobranzas_netcob) arr.push('<span class="badge bg-success fs-6 px-3 py-2"><i class="bi bi-cash-coin me-1"></i> SISTEMA DE COBRANZAS - NETCOB</span>');
                if (item.sistema_otros) arr.push('<span class="badge bg-warning text-dark fs-6 px-3 py-2"><i class="bi bi-puzzle me-1"></i> OTROS SISTEMAS</span>');
                contenedorSistemas.innerHTML = arr.length > 0 ? arr.join(' ') : '<span class="text-muted">Ninguno seleccionado</span>';
            }

            const otrosDetalleDiv = document.getElementById('modalOtrosSistemasDetalle');
            const textoOtros = document.getElementById('modalTextoOtrosSistemas');
            if (item.sistema_otros && item.sistema_otros_detalle) {
                if (otrosDetalleDiv) otrosDetalleDiv.classList.remove('d-none');
                if (textoOtros) textoOtros.textContent = item.sistema_otros_detalle;
            } else {
                if (otrosDetalleDiv) otrosDetalleDiv.classList.add('d-none');
            }

            // Naturaleza del Fallo
            // BD
            const badgeBD = document.getElementById('modalBadgeFalloBD');
            const detalleBD = document.getElementById('modalDetalleFalloBD');
            if (item.fallo_base_datos) {
                badgeBD.innerHTML = '<span class="badge bg-danger">Falla Reportada</span>';
                detalleBD.innerHTML = `<strong class="text-dark">${item.detalle_base_datos || 'Sin detalle'}</strong>`;
            } else {
                badgeBD.innerHTML = '<span class="badge bg-secondary-subtle text-muted">Sin Falla</span>';
                detalleBD.innerHTML = '<span class="text-muted">Sin fallo reportado</span>';
            }

            // Servidor
            const badgeServ = document.getElementById('modalBadgeFalloServidor');
            const detalleServ = document.getElementById('modalDetalleFalloServidor');
            if (item.fallo_infraestructura_servidor) {
                badgeServ.innerHTML = '<span class="badge bg-danger">Falla Reportada</span>';
                detalleServ.innerHTML = `<strong class="text-dark">${item.detalle_infraestructura_servidor || 'Sin detalle'}</strong>`;
            } else {
                badgeServ.innerHTML = '<span class="badge bg-secondary-subtle text-muted">Sin Falla</span>';
                detalleServ.innerHTML = '<span class="text-muted">Sin fallo reportado</span>';
            }

            // Conectividad
            const badgeRed = document.getElementById('modalBadgeFalloRed');
            const detalleRed = document.getElementById('modalDetalleFalloRed');
            if (item.fallo_enlaces_conectividad) {
                badgeRed.innerHTML = '<span class="badge bg-danger">Falla Reportada</span>';
                detalleRed.innerHTML = `<strong class="text-dark">${item.detalle_enlaces_conectividad || 'Sin detalle'}</strong>`;
            } else {
                badgeRed.innerHTML = '<span class="badge bg-secondary-subtle text-muted">Sin Falla</span>';
                detalleRed.innerHTML = '<span class="text-muted">Sin fallo reportado</span>';
            }

            // Logs y Descripción
            document.getElementById('modalDescripcionLogs').textContent = item.descripcion_tecnica_logs || '-';

            // Solución Aplicada
            const badgeSolucion = document.getElementById('modalBadgeSolucion');
            const pAccion = document.getElementById('modalAccionRealizada');
            const pDetalleTec = document.getElementById('modalDetalleTecnico');
            const spanFechaCierre = document.getElementById('modalFechaHoraCierre');

            if (item.accion_realizada) {
                if (badgeSolucion) badgeSolucion.innerHTML = '<span class="badge bg-success-subtle text-success border">Solución Registrada</span>';
                if (pAccion) pAccion.textContent = item.accion_realizada;
                if (pDetalleTec) pDetalleTec.textContent = item.detalle_tecnico || 'Sin comandos registrados';
                if (spanFechaCierre) {
                    spanFechaCierre.textContent = item.fecha_hora_cierre 
                        ? item.fecha_hora_cierre.substring(0, 16).replace('T', ' ') 
                        : 'No cerrada';
                }
            } else {
                if (badgeSolucion) badgeSolucion.innerHTML = '<span class="badge bg-warning-subtle text-dark border">Pendiente de Solución</span>';
                if (pAccion) pAccion.textContent = 'Pendiente de atención técnica por el Dpto. de Sistemas.';
                if (pDetalleTec) pDetalleTec.textContent = 'Sin comandos registrados.';
                if (spanFechaCierre) spanFechaCierre.textContent = 'En proceso';
            }

            // Observaciones
            const obsTexto = document.getElementById('modalObservacionesTexto');
            if (obsTexto) {
                obsTexto.textContent = item.observaciones_recomendaciones || 'Sin observaciones o recomendaciones registradas.';
            }

            // Firmas Digitales
            const imgFirmaResp = document.getElementById('modalImgFirmaResponsable');
            const spanFirmaRespVacia = document.getElementById('modalFirmaResponsableVacia');
            if (item.firma_responsable_reporte) {
                imgFirmaResp.src = item.firma_responsable_reporte;
                imgFirmaResp.style.display = 'block';
                spanFirmaRespVacia.style.display = 'none';
            } else {
                imgFirmaResp.style.display = 'none';
                spanFirmaRespVacia.style.display = 'block';
            }

            const imgFirmaSis = document.getElementById('modalImgFirmaSistemas');
            const spanFirmaSisVacia = document.getElementById('modalFirmaSistemasVacia');
            if (item.firma_sistemas) {
                imgFirmaSis.src = item.firma_sistemas;
                imgFirmaSis.style.display = 'block';
                spanFirmaSisVacia.style.display = 'none';
            } else {
                imgFirmaSis.style.display = 'none';
                spanFirmaSisVacia.style.display = 'block';
            }

            // Formulario de Atención en Modal (oculto por defecto)
            if (contenedorFormAtencion) contenedorFormAtencion.classList.add('d-none');
            document.getElementById('modalInputIdIncidencia').value = item.id;
            document.getElementById('modalInputAccionRealizada').value = item.accion_realizada || '';
            document.getElementById('modalInputDetalleTecnico').value = item.detalle_tecnico || '';
            document.getElementById('modalSelectEstadoSolucion').value = item.estado === 'resuelta' || item.estado === 'cerrada' ? item.estado : 'resuelta';
            document.getElementById('modalInputObservaciones').value = item.observaciones_recomendaciones || '';

            bsModal.show();

        } catch (error) {
            console.error('Error al abrir detalle:', error);
            Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
        }
    }

    // Toggle Formulario de Atención en Modal
    if (btnToggleAtencion && contenedorFormAtencion) {
        btnToggleAtencion.addEventListener('click', () => {
            contenedorFormAtencion.classList.toggle('d-none');
            if (!contenedorFormAtencion.classList.contains('d-none')) {
                document.getElementById('modalInputAccionRealizada')?.focus();
            }
        });
    }

    // Guardar Solución desde el Modal (Sin necesidad de firmar nuevamente)
    if (btnGuardarSolucionModal) {
        btnGuardarSolucionModal.addEventListener('click', async () => {
            const id = document.getElementById('modalInputIdIncidencia')?.value;
            const accion = document.getElementById('modalInputAccionRealizada')?.value.trim();
            const detalle = document.getElementById('modalInputDetalleTecnico')?.value.trim();
            const estado = document.getElementById('modalSelectEstadoSolucion')?.value;
            const fechaCierre = document.getElementById('modalInputFechaCierre')?.value;
            const observaciones = document.getElementById('modalInputObservaciones')?.value.trim();

            if (!accion) {
                Swal.fire('Atención', 'La "Acción Realizada" es obligatoria para registrar la solución.', 'warning');
                return;
            }

            btnGuardarSolucionModal.disabled = true;
            btnGuardarSolucionModal.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Guardando...';

            try {
                const res = await clienteApi.actualizarIncidencia({
                    id: parseInt(id),
                    estado: estado,
                    accion_realizada: accion,
                    detalle_tecnico: detalle,
                    fecha_hora_cierre: fechaCierre,
                    observaciones_recomendaciones: observaciones
                });

                if (res.exito) {
                    Swal.fire('¡Actualizado!', 'La solución técnica y estado han sido guardados correctamente.', 'success');
                    abrirModalDetalle(id);
                    cargarIncidencias();
                } else {
                    Swal.fire('Error', res.mensaje || 'No fue posible actualizar la incidencia.', 'error');
                }
            } catch (err) {
                console.error('Error al guardar solución:', err);
                Swal.fire('Error', 'Fallo de conexión al actualizar.', 'error');
            } finally {
                btnGuardarSolucionModal.disabled = false;
                btnGuardarSolucionModal.innerHTML = '<i class="bi bi-check-circle me-1"></i> Guardar Solución y Cerrar';
            }
        });
    }

    // ==========================================================
    // 4. IMPRESIÓN OFICIAL FOR_RIS_001
    // ==========================================================
    if (btnImprimir) {
        btnImprimir.addEventListener('click', () => {
            window.print();
        });
    }

    // ==========================================================
    // 5. EVENTOS DE FILTRADO REACTIVO
    // ==========================================================
    let temporizadorFiltro = null;
    if (filtroBuscar) {
        filtroBuscar.addEventListener('input', () => {
            clearTimeout(temporizadorFiltro);
            temporizadorFiltro = setTimeout(cargarIncidencias, 300);
        });
    }

    [filtroCriticidad, filtroEstado, filtroSistema].forEach(sel => {
        if (sel) sel.addEventListener('change', cargarIncidencias);
    });

    if (btnLimpiarFiltros) {
        btnLimpiarFiltros.addEventListener('click', () => {
            if (filtroBuscar) filtroBuscar.value = '';
            if (filtroCriticidad) filtroCriticidad.value = '';
            if (filtroEstado) filtroEstado.value = '';
            if (filtroSistema) filtroSistema.value = '';
            cargarIncidencias();
        });
    }

    // Carga inicial
    cargarIncidencias();
});
