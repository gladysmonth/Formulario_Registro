/**
 * Cliente Centralizado de Comunicación con la API Backend
 * Archivo: cliente_api.js
 */

const CONFIGURACION_API = {
    // Si se accede desde navegador, el backend responde en el puerto 83 mapeado por Docker
    urlBase: window.URL_BACKEND || ((window.location && window.location.hostname) ? `http://${window.location.hostname}:83` : 'http://192.168.11.70:83')
};

const clienteApi = {
    /**
     * Realiza una petición genérica con manejo de errores
     */
    async peticion(ruta, opciones = {}) {
        const urlCompleta = `${CONFIGURACION_API.urlBase}${ruta}`;
        const cabecerasPorDefecto = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        if (window.USUARIO_SESION && window.USUARIO_SESION.id) {
            cabecerasPorDefecto['X-Usuario-Id'] = window.USUARIO_SESION.id;
        }

        const config = {
            ...opciones,
            headers: {
                ...cabecerasPorDefecto,
                ...(opciones.headers || {})
            }
        };

        try {
            const respuesta = await fetch(urlCompleta, config);
            const datos = await respuesta.json();

            if (!respuesta.ok) {
                const error = new Error(datos.mensaje || 'Error en la petición al servidor');
                error.detalles = datos.errores || null;
                error.codigo = respuesta.status;
                throw error;
            }

            return datos;
        } catch (error) {
            console.error(`[API Error] ${ruta}:`, error);
            throw error;
        }
    },

    /**
     * Comprueba el estado del backend y actualiza el indicador visual
     */
    async verificarSaludBackend() {
        const elementoTexto = document.getElementById('texto-estado-api');
        const indicador = document.querySelector('.indicador-pulso');

        try {
            const respuesta = await this.peticion('/index.php');
            if (respuesta.exito) {
                if (elementoTexto) {
                    const bdEstado = respuesta.datos.base_de_datos.estado === 'conectado' ? 'BD OK' : 'BD Pendiente';
                    elementoTexto.textContent = `Backend: PHP 7.3 (${bdEstado})`;
                }
                if (indicador) {
                    indicador.classList.remove('bg-danger', 'bg-warning');
                    indicador.classList.add('bg-success');
                }
                return true;
            }
        } catch (e) {
            if (elementoTexto) {
                elementoTexto.textContent = 'Backend: Desconectado';
            }
            if (indicador) {
                indicador.classList.remove('bg-success');
                indicador.classList.add('bg-warning');
            }
            return false;
        }
    },

    // --- Módulo: Soporte Técnico ---
    async buscarEquipos(termino = '') {
        const query = termino ? `?q=${encodeURIComponent(termino)}` : '';
        return await this.peticion(`/api/soporte_tecnico/buscar_equipos.php${query}`, {
            method: 'GET'
        });
    },

    async detectarMiPC(ipOpcional = '') {
        const query = ipOpcional ? `?ip=${encodeURIComponent(ipOpcional)}` : '';
        return await this.peticion(`/api/diagnostico/detectar_mi_pc.php${query}`, {
            method: 'GET'
        });
    },

    async crearTicketSoporte(datos) {
        return await this.peticion('/api/soporte_tecnico/crear_ticket.php', {
            method: 'POST',
            body: JSON.stringify(datos)
        });
    },

    async listarTicketsSoporte(filtros = {}) {
        const params = new URLSearchParams();
        if (filtros.estado) params.append('estado', filtros.estado);
        if (filtros.prioridad) params.append('prioridad', filtros.prioridad);
        if (filtros.buscar) params.append('buscar', filtros.buscar);
        if (filtros.limite) params.append('limite', filtros.limite);

        const query = params.toString() ? `?${params.toString()}` : '';
        return await this.peticion(`/api/soporte_tecnico/listar_tickets.php${query}`, {
            method: 'GET'
        });
    },

    async obtenerTicketSoporte(id) {
        return await this.peticion(`/api/soporte_tecnico/obtener_ticket.php?id=${encodeURIComponent(id)}`, {
            method: 'GET'
        });
    },

    async actualizarTicketSoporte(datos) {
        return await this.peticion('/api/soporte_tecnico/actualizar_ticket.php', {
            method: 'POST',
            body: JSON.stringify(datos)
        });
    },

    // --- Módulo: Mantenimiento Preventivo (PC / Laptop) ---
    async crearMantenimientoPreventivo(datos) {
        return await this.peticion('/api/mantenimiento_preventivo/crear_mantenimiento.php', {
            method: 'POST',
            body: JSON.stringify(datos)
        });
    },

    async listarMantenimientosPreventivos(filtros = {}) {
        const params = new URLSearchParams();
        if (filtros.tipo_equipo) params.append('tipo_equipo', filtros.tipo_equipo);
        if (filtros.buscar) params.append('buscar', filtros.buscar);
        if (filtros.limite) params.append('limite', filtros.limite);
        if (filtros.offset) params.append('offset', filtros.offset);

        const query = params.toString() ? `?${params.toString()}` : '';
        return await this.peticion(`/api/mantenimiento_preventivo/listar_mantenimientos.php${query}`, {
            method: 'GET'
        });
    },

    async obtenerMantenimientoPreventivo(id) {
        return await this.peticion(`/api/mantenimiento_preventivo/obtener_mantenimiento.php?id=${encodeURIComponent(id)}`, {
            method: 'GET'
        });
    },

    async actualizarMantenimientoPreventivo(datos) {
        return await this.peticion('/api/mantenimiento_preventivo/actualizar_mantenimiento.php', {
            method: 'POST',
            body: JSON.stringify(datos)
        });
    },

    // ==========================================================
    // 4. MÓDULO: REGISTRO DE INCIDENCIAS DE SISTEMAS (FOR_RIS_001)
    // ==========================================================
    async crearIncidencia(datos) {
        return await this.peticion('/api/registro_incidencias/crear_incidencia.php', {
            method: 'POST',
            body: JSON.stringify(datos)
        });
    },

    async listarIncidencias(filtros = {}) {
        const query = new URLSearchParams();
        if (filtros.buscar) query.append('buscar', filtros.buscar);
        if (filtros.criticidad) query.append('criticidad', filtros.criticidad);
        if (filtros.estado) query.append('estado', filtros.estado);
        if (filtros.sistema) query.append('sistema', filtros.sistema);

        const qs = query.toString() ? `?${query.toString()}` : '';
        return await this.peticion(`/api/registro_incidencias/listar_incidencias.php${qs}`, {
            method: 'GET'
        });
    },

    async obtenerIncidencia(id) {
        return await this.peticion(`/api/registro_incidencias/obtener_incidencia.php?id=${encodeURIComponent(id)}`, {
            method: 'GET'
        });
    },

    async actualizarIncidencia(datos) {
        return await this.peticion('/api/registro_incidencias/actualizar_incidencia.php', {
            method: 'POST',
            body: JSON.stringify(datos)
        });
    },

    // ==========================================================
    // 5. MÓDULO: CREACIÓN Y ASIGNACIÓN DE ACCESOS
    // ==========================================================
    async crearSolicitudAccesos(datos) {
        return await this.peticion('/api/asignacion_accesos/crear_solicitud.php', {
            method: 'POST',
            body: JSON.stringify(datos)
        });
    },

    async listarSolicitudesAccesos(filtros = {}) {
        const query = new URLSearchParams();
        if (filtros.busqueda) query.append('busqueda', filtros.busqueda);
        if (filtros.estado) query.append('estado', filtros.estado);
        if (filtros.sistema) query.append('sistema', filtros.sistema);

        const qs = query.toString() ? `?${query.toString()}` : '';
        return await this.peticion(`/api/asignacion_accesos/listar_solicitudes.php${qs}`, {
            method: 'GET'
        });
    },

    async obtenerSolicitudAccesos(id) {
        return await this.peticion(`/api/asignacion_accesos/obtener_solicitud.php?id=${encodeURIComponent(id)}`, {
            method: 'GET'
        });
    },

    async actualizarSolicitudAccesos(datos) {
        return await this.peticion('/api/asignacion_accesos/actualizar_solicitud.php', {
            method: 'POST',
            body: JSON.stringify(datos)
        });
    },

    async listarMisSolicitudes() {
        return await this.peticion('/api/solicitudes_usuario/listar_mis_solicitudes.php', {
            method: 'GET'
        });
    },

    // ==========================================================
    // 6. MÓDULO: AUDITORÍA Y TRAZABILIDAD
    // ==========================================================
    async listarAuditoria(filtros = {}) {
        const query = new URLSearchParams();
        if (filtros.modulo) query.append('modulo', filtros.modulo);
        if (filtros.registro_id) query.append('registro_id', filtros.registro_id);
        if (filtros.codigo) query.append('codigo', filtros.codigo);
        if (filtros.limite) query.append('limite', filtros.limite);
        if (filtros.offset) query.append('offset', filtros.offset);

        const qs = query.toString() ? `?${query.toString()}` : '';
        return await this.peticion(`/api/auditoria/listar.php${qs}`, {
            method: 'GET'
        });
    },

    /**
     * Muestra una notificación Toast flotante
     */
    mostrarToast(mensaje, tipo = 'info') {
        const toastEl = document.getElementById('toastNotificacion');
        const mensajeEl = document.getElementById('toastMensaje');
        const iconoEl = document.getElementById('toastIcono');

        if (!toastEl || !mensajeEl) return;

        mensajeEl.textContent = mensaje;
        toastEl.className = 'toast align-items-center text-white border-0 shadow';

        if (tipo === 'exito') {
            toastEl.classList.add('bg-success');
            if (iconoEl) iconoEl.className = 'bi bi-check-circle-fill fs-5';
        } else if (tipo === 'error') {
            toastEl.classList.add('bg-danger');
            if (iconoEl) iconoEl.className = 'bi bi-exclamation-triangle-fill fs-5';
        } else if (tipo === 'advertencia') {
            toastEl.classList.add('bg-warning', 'text-dark');
            if (iconoEl) iconoEl.className = 'bi bi-exclamation-circle-fill fs-5';
        } else {
            toastEl.classList.add('bg-primary');
            if (iconoEl) iconoEl.className = 'bi bi-info-circle-fill fs-5';
        }

        const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
        toast.show();
    },

    // ==========================================================
    // FASE 4: DASHBOARD Y MÉTRICAS GLOBALES DEL MENÚ LATERAL
    // ==========================================================
    async obtenerMetricasDashboard() {
        const resultado = {
            tickets: {
                total: 0,
                pendientes: 0,
                en_proceso: 0,
                resueltos: 0,
                fuerza_mayor: 0
            },
            pcsAlerta: 0
        };

        try {
            const resp = await this.peticion('/api/metricas/dashboard.php');
            if (resp && resp.exito && resp.datos) {
                const k = resp.datos.kpis_globales || {};
                const d = k.desglose_pendientes || {};
                const t = resp.datos.telemetria_hardware || {};

                resultado.tickets.total = (k.total_abiertos || 0) + (k.total_resueltos || 0);
                resultado.tickets.pendientes = d.soporte || 0;
                resultado.tickets.en_proceso = 0;
                resultado.tickets.resueltos = k.total_resueltos || 0;
                resultado.tickets.fuerza_mayor = 0;
                resultado.pcsAlerta = (t.criticos || 0) + (t.advertencias || 0);
            }
        } catch (e) {
            console.warn('[Dashboard] Error al obtener métricas consolidadas:', e);
        }

        return resultado;
    },

    /**
     * Actualiza los números del Dashboard en el Menú Lateral Offcanvas
     */
    async actualizarKpisMenuOffcanvas() {
        const elPendientes = document.getElementById('kpiTicketsPendientes');
        if (!elPendientes) return;

        const elEnProceso   = document.getElementById('kpiTicketsEnProceso');
        const elResueltos   = document.getElementById('kpiTicketsResueltos');
        const elFuerzaMayor = document.getElementById('kpiTicketsFuerzaMayor');
        const elPcsAlerta   = document.getElementById('kpiPcsAlerta');

        try {
            const metricas = await this.obtenerMetricasDashboard();
            if (!metricas) return;

            if (elPendientes) elPendientes.textContent = metricas.tickets.pendientes;
            if (elEnProceso) elEnProceso.textContent = metricas.tickets.en_proceso;
            if (elResueltos) elResueltos.textContent = metricas.tickets.resueltos;
            if (elFuerzaMayor) elFuerzaMayor.textContent = metricas.tickets.fuerza_mayor;
            if (elPcsAlerta) elPcsAlerta.textContent = metricas.pcsAlerta;
        } catch (err) {
            console.warn('[Dashboard Offcanvas] Error al actualizar contadores:', err);
        }
    },

    _offsetMesDashboard: 0,
    _instanciaGraficoDona: null,

    /**
     * Calcula el mes y año objetivo según el offset
     */
    obtenerPeriodoSeleccionado(offsetMeses = 0) {
        const ahora = new Date();
        const fechaTarget = new Date(ahora.getFullYear(), ahora.getMonth() + (offsetMeses || 0), 1);
        return {
            mes: fechaTarget.getMonth() + 1,
            anio: fechaTarget.getFullYear()
        };
    },

    /**
     * Permite avanzar o retroceder de mes en la Dona de Distribución Mensual
     */
    async cambiarMesDashboard(direccion) {
        if (direccion === 0) {
            this._offsetMesDashboard = 0;
        } else {
            this._offsetMesDashboard += direccion;
        }
        await this.cargarDashboardGestionTecnica();
    },

    /**
     * Carga y orquesta en vivo todos los indicadores del Dashboard de Gestión Técnica (Bloques A, B y C)
     * Consumiendo directamente el endpoint unificado de alto rendimiento: GET /api/metricas/dashboard.php
     */
    async cargarDashboardGestionTecnica() {
        const periodo = this.obtenerPeriodoSeleccionado(this._offsetMesDashboard);
        const urlDashboard = `/api/metricas/dashboard.php?mes=${periodo.mes}&anio=${periodo.anio}`;

        let datosDashboard = null;
        try {
            const resp = await this.peticion(urlDashboard);
            if (resp && resp.exito && resp.datos) {
                datosDashboard = resp.datos;
            }
        } catch (err) {
            console.error('[Dashboard] Error al consultar /api/metricas/dashboard.php:', err);
            return;
        }

        if (!datosDashboard) return;

        const kpis = datosDashboard.kpis_globales || {};
        const desglose = kpis.desglose_pendientes || {};
        const distMensual = datosDashboard.distribucion_mensual || {};
        const topDeptos = datosDashboard.top_departamentos || {};
        const telemetria = datosDashboard.telemetria_hardware || {};
        const alerta = datosDashboard.alerta_temprana || {};
        const infoPeriodo = datosDashboard.periodo || {};

        // ====================================================================
        // RENDERIZADO: SELECTOR DE PERÍODO / MES
        // ====================================================================
        const elTextoMes = document.getElementById('textoMesSeleccionado');
        const elBtnVolver = document.getElementById('btnVolverMesActual');

        let textoMes = `${infoPeriodo.nombre_mes || 'Mes'} ${infoPeriodo.anio || ''}`;
        if (infoPeriodo.es_actual) {
            textoMes += ' (Actual)';
        }

        if (elTextoMes) {
            elTextoMes.innerHTML = `<i class="bi bi-calendar3 text-primary me-1"></i>${textoMes}`;
        }
        if (elBtnVolver) {
            elBtnVolver.classList.toggle('d-none', this._offsetMesDashboard === 0);
        }

        // ====================================================================
        // RENDERIZADO: BLOQUE A (KPIS EJECUTIVOS)
        // ====================================================================
        // KPI 1: Carga de Trabajo y Desglose de Pendientes por Formulario
        const elTotAb = document.getElementById('kpiTotalAbiertos');
        const elAbEst = document.getElementById('kpiAbiertosEstatus');
        const elAbDesglose = document.getElementById('kpiAbiertosDesglose');
        const totalAbiertos = kpis.total_abiertos || 0;

        if (elTotAb) elTotAb.textContent = totalAbiertos;
        if (elAbEst) {
            elAbEst.textContent = totalAbiertos > 0 ? (totalAbiertos + ' pendientes') : 'Al día';
            elAbEst.className = totalAbiertos > 0 ? 'badge bg-primary bg-opacity-15 text-primary fw-bold' : 'badge bg-success bg-opacity-15 text-success fw-bold';
        }
        if (elAbDesglose) {
            elAbDesglose.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-headset text-info me-1"></i>Soporte Técnico:</span>
                    <strong class="text-dark">${desglose.soporte || 0} de Soporte</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-exclamation-triangle text-danger me-1"></i>Incidencias:</span>
                    <strong class="text-danger">${desglose.incidencias || 0} de Incidencias</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-key text-primary me-1"></i>Accesos:</span>
                    <strong class="text-primary">${desglose.accesos || 0} de Accesos</strong>
                </div>
            `;
        }

        // KPI 2: Criticidad Alta
        const elTotCrit = document.getElementById('kpiTotalCriticos');
        const elCritBadge = document.getElementById('kpiCriticosBadge');
        const elCritEst = document.getElementById('kpiCriticosEstatus');
        const elCritDetalleTexto = document.getElementById('kpiCriticosDetalleTexto');
        const totalCriticos = kpis.total_criticos || 0;

        if (elTotCrit) elTotCrit.textContent = totalCriticos;
        if (elCritBadge) elCritBadge.textContent = totalCriticos + ' Críticas';
        if (elCritEst) {
            if (totalCriticos > 0) {
                elCritEst.className = 'badge bg-danger bg-opacity-15 text-danger fw-bold';
                elCritEst.textContent = 'Atención Urgente';
            } else {
                elCritEst.className = 'badge bg-success bg-opacity-15 text-success fw-bold';
                elCritEst.textContent = 'Estable';
            }
        }
        if (elCritDetalleTexto) {
            if (kpis.sistema_critico) {
                elCritDetalleTexto.innerHTML = `<span class="badge bg-danger bg-opacity-15 text-danger px-1 py-0">${totalCriticos} Crítica(s)</span> en <strong>${kpis.sistema_critico}</strong>`;
            } else {
                elCritDetalleTexto.innerHTML = `<span class="badge bg-success bg-opacity-15 text-success px-1 py-0">0 Críticas</span> en plataformas`;
            }
        }

        // KPI 3: Tickets Resueltos y Atendidos
        const elTotRes = document.getElementById('kpiTotalResueltos');
        const elResDetalle = document.getElementById('kpiResueltosDetalle');
        const elResEst = document.getElementById('kpiResueltosEstatus');
        const totalResueltos = kpis.total_resueltos || 0;

        if (elTotRes) elTotRes.textContent = totalResueltos;
        if (elResDetalle) {
            elResDetalle.textContent = `${totalResueltos} tickets cerrados con éxito en la plataforma`;
        }
        if (elResEst) {
            elResEst.textContent = totalResueltos > 0 ? (totalResueltos + ' atendidos') : 'Sin cierres';
            elResEst.className = totalResueltos > 0 ? 'badge bg-success bg-opacity-15 text-success fw-bold' : 'badge bg-secondary bg-opacity-15 text-secondary fw-bold';
        }

        // KPI 4: Mantenimientos Preventivos Realizados
        const elTotMp = document.getElementById('kpiTotalMantenimientos');
        const elMpDetalle = document.getElementById('kpiMantenimientoDetalle');
        const elMpEst = document.getElementById('kpiMantenimientoEstatus');
        const totalMp = kpis.total_mantenimientos || 0;

        if (elTotMp) elTotMp.textContent = totalMp;
        if (elMpDetalle) {
            elMpDetalle.textContent = `${totalMp} fichas técnicas registradas y firmadas`;
        }
        if (elMpEst) {
            elMpEst.textContent = totalMp > 0 ? (totalMp + ' fichas OK') : 'Sin fichas';
            elMpEst.className = totalMp > 0 ? 'badge bg-info bg-opacity-15 text-info fw-bold' : 'badge bg-secondary bg-opacity-15 text-secondary fw-bold';
        }

        // ====================================================================
        // RENDERIZADO: BANNER DE ALERTA TEMPRANA
        // ====================================================================
        const bannerEl = document.getElementById('bannerAlertaDashboard');
        const iconoAlerta = document.getElementById('iconoBannerAlerta');
        const textoAlerta = document.getElementById('textoBannerAlerta');
        const badgeEstado = document.getElementById('badgeEstadoGeneral');
        const btnAccion = document.getElementById('btnAccionAlerta');

        if (bannerEl && textoAlerta) {
            if (alerta.hay_alerta) {
                if (alerta.tipo === 'danger') {
                    bannerEl.className = 'alert alert-danger border-0 shadow-sm rounded-3 py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2 mb-0';
                    if (iconoAlerta) iconoAlerta.className = 'bi bi-exclamation-octagon-fill fs-5 text-danger';
                    textoAlerta.innerHTML = `<strong>${alerta.titulo}</strong> ${alerta.mensaje}`;
                    if (badgeEstado) {
                        badgeEstado.className = 'badge bg-danger text-white small px-2 py-1';
                        badgeEstado.textContent = 'Alerta Crítica';
                    }
                    if (btnAccion && alerta.url_accion) {
                        btnAccion.href = alerta.url_accion;
                        btnAccion.className = 'btn btn-danger btn-sm px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm';
                        btnAccion.innerHTML = `<span>${alerta.texto_boton_accion || 'Ir a atender'}</span><i class="bi bi-arrow-right"></i>`;
                    }
                } else if (alerta.tipo === 'warning') {
                    bannerEl.className = 'alert alert-warning border-0 shadow-sm rounded-3 py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2 mb-0';
                    if (iconoAlerta) iconoAlerta.className = 'bi bi-cpu-fill fs-5 text-warning';
                    textoAlerta.innerHTML = `<strong>${alerta.titulo}</strong> ${alerta.mensaje}`;
                    if (badgeEstado) {
                        badgeEstado.className = 'badge bg-warning text-dark small px-2 py-1';
                        badgeEstado.textContent = 'Alerta Hardware';
                    }
                    if (btnAccion && alerta.url_accion) {
                        btnAccion.href = alerta.url_accion;
                        btnAccion.className = 'btn btn-warning btn-sm px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm text-dark';
                        btnAccion.innerHTML = `<span>${alerta.texto_boton_accion || 'Ver Equipos'}</span><i class="bi bi-arrow-right"></i>`;
                    }
                }
            } else {
                bannerEl.className = 'alert alert-success border-0 shadow-sm rounded-3 py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2 mb-0';
                if (iconoAlerta) iconoAlerta.className = 'bi bi-check-circle-fill fs-5 text-success';
                textoAlerta.innerHTML = `<strong>${alerta.titulo || 'Sistemas Institucionales Estables:'}</strong> ${alerta.mensaje || 'No se registran incidencias críticas ni alertas de hardware pendientes en este momento.'}`;
                if (badgeEstado) {
                    badgeEstado.className = 'badge bg-success bg-opacity-25 text-success small px-2 py-1';
                    badgeEstado.textContent = 'Operativo';
                }
                if (btnAccion) {
                    btnAccion.className = 'btn btn-danger btn-sm px-3 py-1 fw-semibold d-none align-items-center gap-1 shadow-sm';
                }
            }
        }

        // ====================================================================
        // RENDERIZADO: BLOQUE B (DISTRIBUCIÓN MENSUAL POR FORMULARIO EN DONA)
        // ====================================================================
        const conteosDona = distMensual.conteos || { soporte: 0, incidencias: 0, accesos: 0, mantenimiento: 0 };
        const totalMes = distMensual.total_mes || 0;

        // Actualizar textos centrales de la dona
        const elTotalCentro = document.getElementById('donaTotalTicketsCentro');
        const elTextoCentro = document.getElementById('donaTextoMesCentro');
        if (elTotalCentro) elTotalCentro.textContent = totalMes;
        if (elTextoCentro) elTextoCentro.textContent = totalMes === 1 ? 'ticket' : 'tickets';

        // Actualizar pie inferior de la tarjeta: Mayor Demanda
        const elMayorPie = document.getElementById('formularioMayorUsoMes');
        if (elMayorPie) {
            if (totalMes === 0) {
                elMayorPie.textContent = 'Sin actividad registrada';
            } else {
                const may = distMensual.mayor_demanda || {};
                elMayorPie.innerHTML = `<span class="text-primary">${may.formulario || 'Soporte'}</span> (${may.cantidad || 0} tickets)`;
            }
        }

        const canvas = document.getElementById('graficoDonaFormularios') || document.getElementById('graficaDonaFormularios');
        if (canvas && typeof Chart !== 'undefined') {
            if (this._instanciaGraficoDona) {
                try { this._instanciaGraficoDona.destroy(); } catch (e) {}
                this._instanciaGraficoDona = null;
            }

            const ctx = canvas.getContext('2d');
            if (totalMes === 0) {
                this._instanciaGraficoDona = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Sin requerimientos'],
                        datasets: [{
                            data: [1],
                            backgroundColor: ['#e9ecef'],
                            borderWidth: 1,
                            borderColor: '#dee2e6'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    title: function() { return ''; },
                                    label: function() {
                                        return ' Sin requerimientos registrados en este mes';
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                this._instanciaGraficoDona = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: [
                            'Soporte Tecn.',
                            'Incidencias',
                            'Accesos',
                            'Mant.Preventivo'
                        ],
                        datasets: [{
                            data: [
                                conteosDona.soporte || 0,
                                conteosDona.incidencias || 0,
                                conteosDona.accesos || 0,
                                conteosDona.mantenimiento || 0
                            ],
                            backgroundColor: [
                                '#0dcaf0', // Cyan (Soporte)
                                '#dc3545', // Danger Red (Incidencias)
                                '#0d6efd', // Primary Blue (Accesos)
                                '#198754'  // Success Green (Mantenimiento)
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        animation: {
                            duration: 400
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.92)',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 12 },
                                padding: 8,
                                cornerRadius: 6,
                                displayColors: true,
                                callbacks: {
                                    title: function() {
                                        return '';
                                    },
                                    label: function(context) {
                                        const valor = context.raw || 0;
                                        return ` ${context.label}: ${valor}`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        // Top Departamentos
        const contenedorTop = document.getElementById('contenedorTopDepartamentos');
        const elAreaMayor = document.getElementById('areaMayorDemanda');
        const elTotalAreas = document.getElementById('totalAreasAtendidas');

        if (contenedorTop) {
            const ranking = topDeptos.ranking || [];
            if (elTotalAreas) elTotalAreas.textContent = topDeptos.total_areas || 0;

            if (ranking.length === 0) {
                contenedorTop.innerHTML = `
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary opacity-50"></i>
                        <span>No se registran solicitudes por área para este período aún.</span>
                    </div>
                `;
                if (elAreaMayor) elAreaMayor.textContent = 'Ninguna';
            } else {
                if (elAreaMayor) {
                    elAreaMayor.innerHTML = `<strong>${topDeptos.area_mayor || 'Ninguna'}</strong> (${ranking[0].total} tickets)`;
                }

                const coloresBarras = [
                    { barra: 'bg-primary', badge: 'bg-primary text-white' },
                    { barra: 'bg-info', badge: 'bg-info text-info-emphasis' },
                    { barra: 'bg-success', badge: 'bg-success text-white' },
                    { barra: 'bg-warning', badge: 'bg-warning text-dark' },
                    { barra: 'bg-secondary', badge: 'bg-secondary text-white' }
                ];

                let html = '';
                ranking.forEach((dep, index) => {
                    const estilo = coloresBarras[index] || { barra: 'bg-primary', badge: 'bg-primary text-white' };
                    html += `
                        <div class="p-2 rounded-2 bg-light bg-opacity-75 border border-light">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-white text-dark border fw-bold" style="font-size: 0.72rem; min-width: 24px;">#${index + 1}</span>
                                    <span class="fw-semibold text-dark small">${dep.nombre}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">${dep.total} ${dep.total === 1 ? 'ticket' : 'tickets'}</span>
                                    <span class="badge ${estilo.badge} px-2 py-1" style="font-size: 0.72rem;">${dep.porcentaje}%</span>
                                </div>
                            </div>
                            <div class="progress" style="height: 7px; border-radius: 4px; background-color: #e2e8f0;">
                                <div class="progress-bar ${estilo.barra}" role="progressbar" style="width: ${dep.porcentaje}%;" aria-valuenow="${dep.porcentaje}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    `;
                });
                contenedorTop.innerHTML = html;
            }
        }

        // ====================================================================
        // RENDERIZADO: BLOQUE C (MONITOREO ACTIVO DE HARDWARE & TELEMETRÍA)
        // ====================================================================
        const elOptimos = document.getElementById('telemetriaOptimos');
        const elAdvertencias = document.getElementById('telemetriaAdvertencias');
        const elCriticos = document.getElementById('telemetriaCriticos');
        const elAntivirusTxt = document.getElementById('telemetriaAntivirusTexto');
        const contenedorAlertasHw = document.getElementById('contenedorAlertasHardware');

        if (elOptimos) elOptimos.textContent = telemetria.optimos || 0;
        if (elAdvertencias) elAdvertencias.textContent = telemetria.advertencias || 0;
        if (elCriticos) elCriticos.textContent = telemetria.criticos || 0;

        if (elAntivirusTxt) {
            const totalPcs = telemetria.total_pcs || 0;
            const act = telemetria.antivirus_activos || 0;
            const pct = telemetria.porcentaje_antivirus !== undefined ? telemetria.porcentaje_antivirus : 100;
            elAntivirusTxt.textContent = `${pct}% Protegido (${act}/${totalPcs} PCs)`;
        }

        if (contenedorAlertasHw) {
            const alertas = telemetria.alertas || [];
            if (alertas.length === 0) {
                contenedorAlertasHw.innerHTML = `
                    <div class="p-3 rounded-3 bg-light border text-center text-muted small">
                        <i class="bi bi-check-circle-fill text-success fs-5 d-block mb-1"></i>
                        <span>Todos los discos (SMART) y memorias operan dentro de los parámetros seguros.</span>
                    </div>
                `;
            } else {
                let htmlHw = '';
                alertas.forEach(al => {
                    const badgeClase = al.tipo === 'danger' ? 'bg-danger text-white' : 'bg-warning text-dark';
                    htmlHw += `
                        <div class="p-2 rounded-2 bg-light border d-flex align-items-center justify-content-between gap-2 small">
                            <div class="d-flex align-items-center gap-2 text-truncate">
                                <i class="bi bi-exclamation-triangle-fill text-${al.tipo}"></i>
                                <div class="text-truncate">
                                    <strong class="text-dark">${al.hostname}</strong>
                                    <small class="text-muted d-block text-truncate">${al.area} &bull; ${al.motivo}</small>
                                </div>
                            </div>
                            <span class="badge ${badgeClase} text-nowrap" style="font-size: 0.68rem;">Revisión Sugerida</span>
                        </div>
                    `;
                });
                contenedorAlertasHw.innerHTML = htmlHw;
            }
        }
    },

    // --- Módulo: Gestión de Usuarios y Roles ---
    async listarUsuarios() {
        return await this.peticion('/api/usuarios/listar_usuarios.php', {
            method: 'GET'
        });
    },

    async guardarUsuario(datos) {
        return await this.peticion('/api/usuarios/guardar_usuario.php', {
            method: 'POST',
            body: JSON.stringify(datos)
        });
    },

    async cambiarEstadoUsuario(id, activo) {
        return await this.peticion('/api/usuarios/cambiar_estado.php', {
            method: 'POST',
            body: JSON.stringify({ id: id, activo: activo })
        });
    }
};

// Inicialización de salud y oyentes del Menú Lateral y Pantalla de Métricas
document.addEventListener('DOMContentLoaded', () => {
    clienteApi.verificarSaludBackend();

    // Actualización de KPIs en el Dashboard de Métricas y Menú Lateral
    const offcanvasEl = document.getElementById('menuFormulariosLateral');
    if (offcanvasEl) {
        clienteApi.actualizarKpisMenuOffcanvas();
        offcanvasEl.addEventListener('show.bs.offcanvas', () => {
            clienteApi.actualizarKpisMenuOffcanvas();
        });
    }
});

