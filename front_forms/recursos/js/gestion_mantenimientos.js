/**
 * Lógica del Panel de Gestión de Mantenimientos Preventivos (PC / Laptop)
 * Archivo: gestion_mantenimientos.js
 */

document.addEventListener('DOMContentLoaded', () => {
    const tablaCuerpo = document.getElementById('cuerpoTablaMP');
    const filtroTipo = document.getElementById('filtroTipoEquipoMP');
    const inputBuscar = document.getElementById('inputBuscarMP');
    const btnRefrescar = document.getElementById('btnRefrescarMP');

    const modalElemento = document.getElementById('modalDetalleMP');
    const modalDetalle = modalElemento ? new bootstrap.Modal(modalElemento) : null;

    let registrosActuales = [];

    // Carga inicial
    cargarMantenimientos();

    // Eventos
    if (filtroTipo) filtroTipo.addEventListener('change', cargarMantenimientos);
    if (btnRefrescar) btnRefrescar.addEventListener('click', cargarMantenimientos);

    if (inputBuscar) {
        let timer;
        inputBuscar.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(cargarMantenimientos, 400);
        });
    }

    async function cargarMantenimientos() {
        if (tablaCuerpo) {
            tablaCuerpo.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        Cargando registros de mantenimiento preventivo...
                    </td>
                </tr>
            `;
        }

        const filtros = {
            tipo_equipo: filtroTipo ? filtroTipo.value : '',
            buscar: inputBuscar ? inputBuscar.value.trim() : ''
        };

        try {
            const respuesta = await clienteApi.listarMantenimientosPreventivos(filtros);
            if (respuesta.exito) {
                registrosActuales = respuesta.datos.registros;
                actualizarMetricas(respuesta.datos.metricas);
                renderizarTabla(registrosActuales);
            }
        } catch (error) {
            if (tablaCuerpo) {
                tablaCuerpo.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-4 text-danger">
                            <i class="bi bi-exclamation-octagon fs-4 d-block mb-2"></i>
                            No se pudo cargar la lista de registros de mantenimiento.
                        </td>
                    </tr>
                `;
            }
        }
    }

    function actualizarMetricas(m) {
        if (!m) return;
        const elTotal = document.getElementById('metricaTotalMP');
        const elPC = document.getElementById('metricaTotalPC');
        const elLap = document.getElementById('metricaTotalLaptop');
        const elMes = document.getElementById('metricaEsteMes');

        if (elTotal) elTotal.textContent = m.total || 0;
        if (elPC) elPC.textContent = m.total_pc || 0;
        if (elLap) elLap.textContent = m.total_laptop || 0;
        if (elMes) elMes.textContent = m.este_mes || 0;
    }

    function renderizarTabla(registros) {
        if (!tablaCuerpo) return;

        if (registros.length === 0) {
            tablaCuerpo.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        No se encontraron registros de mantenimiento preventivo.
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        registros.forEach(r => {
            const badgeTipo = r.tipo_equipo === 'LAPTOP'
                ? '<span class="badge bg-secondary-subtle text-dark border"><i class="bi bi-laptop me-1"></i>Laptop</span>'
                : '<span class="badge bg-primary-subtle text-primary border"><i class="bi bi-pc-display me-1"></i>PC</span>';

            html += `
                <tr>
                    <td class="fw-semibold text-nowrap">
                        <span class="text-primary">${escapeHtml(r.codigo_mantenimiento)}</span>
                    </td>
                    <td class="text-nowrap small">${escapeHtml(r.fecha_mantenimiento)}</td>
                    <td>
                        <div class="fw-medium">${escapeHtml(r.tecnico_responsable)}</div>
                    </td>
                    <td>
                        <small class="text-secondary">${escapeHtml(r.ubicacion_equipo)}</small>
                    </td>
                    <td>${badgeTipo}</td>
                    <td>
                        <div class="fw-medium">${escapeHtml(r.nombre_equipo || r.marca_modelo || 'S/N')}</div>
                        <small class="text-muted">${r.codigo_activo ? 'Activo: ' + escapeHtml(r.codigo_activo) : ''}</small>
                    </td>
                    <td>
                        <small class="text-muted d-block">${escapeHtml(r.sistema_operativo || 'No especificado')}</small>
                        <span class="badge bg-light text-dark border py-0 px-1" style="font-size: 0.7rem;">${escapeHtml(r.tipo_red || 'N/A')}</span>
                    </td>
                    <td class="text-end text-nowrap">
                        <button class="btn btn-sm btn-outline-primary btn-ver-ficha" data-id="${r.id}">
                            <i class="bi bi-eye me-1"></i> Ver Ficha
                        </button>
                    </td>
                </tr>
            `;
        });

        tablaCuerpo.innerHTML = html;

        document.querySelectorAll('.btn-ver-ficha').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                abrirModalDetalle(id);
            });
        });
    }

    async function abrirModalDetalle(id) {
        if (!modalDetalle) return;

        try {
            const res = await clienteApi.obtenerMantenimientoPreventivo(id);
            if (!res.exito) return;

            const mp = res.datos;

            // Encabezado
            document.getElementById('modalCodigoMP').textContent = mp.codigo_mantenimiento;
            const elPrintCod = document.getElementById('mdlCodigoPrint');
            if (elPrintCod) elPrintCod.textContent = mp.codigo_mantenimiento;

            // Sección 1: Datos Generales
            document.getElementById('mdlTecnico').textContent = mp.tecnico_responsable || '-';
            document.getElementById('mdlFecha').textContent = mp.fecha_mantenimiento || '-';
            document.getElementById('mdlUbicacion').textContent = mp.ubicacion_equipo || '-';

            // Sección 2: Información del Equipo
            document.getElementById('mdlTipoEquipo').innerHTML = mp.tipo_equipo === 'LAPTOP' 
                ? '<span class="badge bg-secondary">Laptop</span>' 
                : '<span class="badge bg-primary">PC de Escritorio</span>';
            document.getElementById('mdlNombreEquipo').textContent = mp.nombre_equipo || 'No especificado';
            document.getElementById('mdlCodActivo').textContent = mp.codigo_activo || 'No especificado';
            document.getElementById('mdlMarcaModelo').textContent = mp.marca_modelo || 'No especificado';
            document.getElementById('mdlSO').textContent = mp.sistema_operativo || 'No especificado';
            document.getElementById('mdlTipoRed').textContent = mp.tipo_red || 'No especificado';
            document.getElementById('mdlCPU').textContent = mp.procesador || 'No especificado';
            document.getElementById('mdlRAM').textContent = mp.memoria_ram || 'No especificado';
            document.getElementById('mdlDisco').textContent = mp.almacenamiento || 'No especificado';
            document.getElementById('mdlIP').textContent = mp.direccion_ip || 'No especificado';

            // Sección 3: Mantenimiento Externo
            const chkExt = [];
            chkExt.push(renderCheckItem('Limpieza de carcasa, ventiladores y componentes', mp.limpieza_carcasa_componentes));
            chkExt.push(renderCheckItem('Limpieza de pantalla, teclado y touchpad', mp.limpieza_pantalla_teclado));
            chkExt.push(renderCheckItem('Verificación de conectores (USB, HDMI, etc.)', mp.verificacion_conectores));
            document.getElementById('mdlCheckExterno').innerHTML = chkExt.join('');

            const contOtrosExt = document.getElementById('mdlOtrosExternoCont');
            const txtOtrosExt = document.getElementById('mdlOtrosExterno');
            if (mp.limpieza_otros) {
                txtOtrosExt.textContent = mp.limpieza_otros;
                contOtrosExt.classList.remove('d-none');
            } else {
                contOtrosExt.classList.add('d-none');
            }

            // Sección 4: Mantenimiento Interno
            const chkInt = [];
            chkInt.push(renderCheckItem('Actualización del sistema operativo', mp.actualizacion_so));
            chkInt.push(renderCheckItem('Eliminación de archivos temporales y caché', mp.eliminacion_temporales));
            chkInt.push(renderCheckItem('Desfragmentación (HDD) / Optimización (SSD)', mp.desfragmentacion_optimizacion));
            chkInt.push(renderCheckItem('Escaneo antivirus / anti-malware', mp.escaneo_antivirus));
            chkInt.push(renderCheckItem('Verificación de drivers y actualizaciones', mp.verificacion_drivers));
            chkInt.push(renderCheckItem('Copia de seguridad de datos críticos', mp.copia_seguridad));
            document.getElementById('mdlCheckInterno').innerHTML = chkInt.join('');

            const contOtrosInt = document.getElementById('mdlOtrosInternoCont');
            const txtOtrosInt = document.getElementById('mdlOtrosInterno');
            if (mp.mantenimiento_interno_otros) {
                txtOtrosInt.textContent = mp.mantenimiento_interno_otros;
                contOtrosInt.classList.remove('d-none');
            } else {
                contOtrosInt.classList.add('d-none');
            }

            // Sección 5: Verificación de Funcionamiento
            const chkVer = [];
            chkVer.push(renderCheckItem('Encendido / Apagado correcto', mp.verificacion_encendido_apagado));
            chkVer.push(renderCheckItem('Rendimiento general fluido', mp.verificacion_rendimiento));
            chkVer.push(renderCheckItem('Conectividad Wi-Fi / Red funcional', mp.verificacion_red));
            chkVer.push(renderCheckItem('Periféricos funcionales (Mouse, Teclado, etc.)', mp.verificacion_perifericos));
            chkVer.push(renderCheckItem('Sin sobrecalentamiento / anomalías térmicas', mp.verificacion_temperatura_anomalias));
            document.getElementById('mdlCheckVerificacion').innerHTML = chkVer.join('');

            // Sección 6: Observaciones
            document.getElementById('mdlObservaciones').innerHTML = mp.observaciones_incidencias 
                ? escapeHtml(mp.observaciones_incidencias).replace(/\n/g, '<br>')
                : '<em>Sin observaciones registradas.</em>';

            // Sección 7: Firmas
            const imgResp = document.getElementById('mdlFirmaResponsable');
            const imgSis = document.getElementById('mdlFirmaSistemas');
            if (imgResp) imgResp.src = mp.firma_responsable_equipo || '';
            if (imgSis) imgSis.src = mp.firma_sistemas || '';

            modalDetalle.show();

        } catch (e) {
            Swal.fire('Error', 'No se pudo cargar el detalle del mantenimiento.', 'error');
        }
    }

    function renderCheckItem(label, estado) {
        if (estado) {
            return `<li class="text-success mb-1"><i class="bi bi-check-circle-fill me-1"></i> <strong>${label}</strong></li>`;
        } else {
            return `<li class="text-muted mb-1 opacity-75"><i class="bi bi-dash-circle me-1"></i> <span>${label}</span></li>`;
        }
    }

    const btnImprimirFichaMP = document.getElementById('btnImprimirFichaMP');
    if (btnImprimirFichaMP) {
        btnImprimirFichaMP.addEventListener('click', () => {
            window.print();
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
});
