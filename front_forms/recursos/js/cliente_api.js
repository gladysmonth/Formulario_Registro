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
            // 1. Obtener métricas consolidadas de tickets
            const respTickets = await this.peticion('/api/soporte_tecnico/listar_tickets.php?limite=1');
            if (respTickets.exito && respTickets.datos && respTickets.datos.metricas) {
                const m = respTickets.datos.metricas;
                resultado.tickets.total = parseInt(m.total, 10) || 0;
                resultado.tickets.pendientes = parseInt(m.pendientes, 10) || 0;
                resultado.tickets.en_proceso = parseInt(m.en_proceso, 10) || 0;
                resultado.tickets.resueltos = parseInt(m.resueltos, 10) || 0;
                resultado.tickets.fuerza_mayor = parseInt(m.fuerza_mayor, 10) || 0;
            }
        } catch (e) {
            console.warn('[Dashboard] No se pudieron obtener métricas de tickets:', e);
        }

        try {
            // 2. Obtener recuento de PCs con alertas técnicas de hardware
            const respPcs = await this.peticion('/api/diagnostico/listar.php');
            if (respPcs.exito && respPcs.datos) {
                if (respPcs.datos.metricas) {
                    const m = respPcs.datos.metricas;
                    const criticos = parseInt(m.criticos, 10) || 0;
                    const advertencias = parseInt(m.advertencias, 10) || 0;
                    resultado.pcsAlerta = criticos + advertencias;
                } else if (Array.isArray(respPcs.datos.equipos)) {
                    resultado.pcsAlerta = respPcs.datos.equipos.filter(pc => {
                        const esAlerta = (pc.estado_salud_general === 'critico' || pc.estado_salud_general === 'advertencia');
                        const tieneAlertas = (Array.isArray(pc.alertas_detectadas) && pc.alertas_detectadas.length > 0);
                        return esAlerta || tieneAlertas;
                    }).length;
                }
            }
        } catch (e) {
            // Si el usuario no tiene permisos o no está disponible, mantener en 0
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
    _cacheDashboardRegistros: null,
    _instanciaGraficoDona: null,

    /**
     * Calcula el rango de fechas para el mes según el offset:
     * 0 = mes actual, -1 = mes anterior, +1 = mes siguiente
     */
    obtenerRangoMes(offsetMeses = 0) {
        const ahora = new Date();
        const anio = ahora.getFullYear();
        const mesIndex = ahora.getMonth() + (offsetMeses || 0);

        const inicio = new Date(anio, mesIndex, 1, 0, 0, 0, 0);
        const fin = new Date(anio, mesIndex + 1, 0, 23, 59, 59, 999);

        return { inicio, fin };
    },

    /**
     * Permite avanzar o retroceder de mes en la Dona de Distribución Mensual
     * 0 = volver al mes actual, -1 = mes anterior, +1 = mes siguiente
     */
    cambiarMesDashboard(direccion) {
        if (direccion === 0) {
            this._offsetMesDashboard = 0;
        } else {
            this._offsetMesDashboard += direccion;
        }
        this.renderizarDonaMensual();
    },

    /**
     * Renderiza la Dona Mensual de los 4 Formularios con navegación por meses,
     * tooltips interactivos con porcentajes y cantidades, y contador central.
     */
    renderizarDonaMensual() {
        if (!this._cacheDashboardRegistros) return;

        const rango = this.obtenerRangoMes(this._offsetMesDashboard);
        const nombresMeses = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        const nombreMes = nombresMeses[rango.inicio.getMonth()];
        const anio = rango.inicio.getFullYear();

        let textoMes = `${nombreMes} ${anio}`;
        if (this._offsetMesDashboard === 0) {
            textoMes += ' (Actual)';
        }

        const elTextoMes = document.getElementById('textoMesSeleccionado');
        const elBtnVolver = document.getElementById('btnVolverMesActual');
        if (elTextoMes) {
            elTextoMes.innerHTML = `<i class="bi bi-calendar3 text-primary me-1"></i>${textoMes}`;
        }
        if (elBtnVolver) {
            elBtnVolver.classList.toggle('d-none', this._offsetMesDashboard === 0);
        }

        // Conteo por formulario dentro del mes seleccionado
        let conteo = { sop: 0, inc: 0, acc: 0, mp: 0 };

        function estaEnRango(fechaStr) {
            if (!fechaStr) return false;
            const f = new Date(fechaStr.indexOf(' ') !== -1 ? fechaStr.replace(' ', 'T') : fechaStr);
            if (isNaN(f.getTime())) return false;
            return (f >= rango.inicio && f <= rango.fin);
        }

        const reg = this._cacheDashboardRegistros;
        (reg.soporte || []).forEach(t => {
            if (estaEnRango(t.fecha_solicitud || t.creado_en)) conteo.sop++;
        });
        (reg.incidencias || []).forEach(i => {
            if (estaEnRango(i.fecha_hora_reporte || i.creado_en)) conteo.inc++;
        });
        (reg.accesos || []).forEach(a => {
            if (estaEnRango(a.fecha_solicitud || a.creado_en)) conteo.acc++;
        });
        (reg.mantenimiento || []).forEach(mp => {
            if (estaEnRango(mp.fecha_mantenimiento || mp.creado_en)) conteo.mp++;
        });

        const totalMes = conteo.sop + conteo.inc + conteo.acc + conteo.mp;

        // Cálculo de porcentajes para tooltips y leyendas
        const pctSop = totalMes > 0 ? Math.round((conteo.sop / totalMes) * 100) : 0;
        const pctInc = totalMes > 0 ? Math.round((conteo.inc / totalMes) * 100) : 0;
        const pctAcc = totalMes > 0 ? Math.round((conteo.acc / totalMes) * 100) : 0;
        const pctMp  = totalMes > 0 ? Math.max(0, 100 - (pctSop + pctInc + pctAcc)) : 0;

        // Actualizar textos centrales de la dona
        const elTotalCentro = document.getElementById('donaTotalTicketsCentro');
        const elTextoCentro = document.getElementById('donaTextoMesCentro');
        if (elTotalCentro) elTotalCentro.textContent = totalMes;
        if (elTextoCentro) elTextoCentro.textContent = totalMes === 1 ? 'ticket' : 'tickets';

        // Actualizar pie inferior de la tarjeta: solo Mayor Demanda
        const elMayorPie = document.getElementById('formularioMayorUsoMes');
        if (elMayorPie) {
            if (totalMes === 0) {
                elMayorPie.textContent = 'Sin actividad registrada';
            } else {
                const lista = [
                    { nombre: 'Soporte Tecn.', cant: conteo.sop },
                    { nombre: 'Incidencias', cant: conteo.inc },
                    { nombre: 'Accesos', cant: conteo.acc },
                    { nombre: 'Mant.Preventivo', cant: conteo.mp }
                ].sort((a, b) => b.cant - a.cant);

                elMayorPie.innerHTML = `<span class="text-primary">${lista[0].nombre}</span> (${lista[0].cant} tickets)`;
            }
        }

        // Renderizado del Gráfico de Dona con Chart.js
        const canvas = document.getElementById('graficoDonaFormularios');
        if (!canvas) return;

        if (typeof Chart !== 'undefined') {
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
                            data: [conteo.sop, conteo.inc, conteo.acc, conteo.mp],
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
    },

    /**
     * Carga y orquesta en vivo todos los indicadores del Dashboard de Gestión Técnica (Bloques A, B y C)
     */
    async cargarDashboardGestionTecnica() {
        const [resSoporte, resIncidencias, resAccesos, resMantenimiento, resDiag] = await Promise.allSettled([
            this.peticion('/api/soporte_tecnico/listar_tickets.php?limite=50'),
            this.peticion('/api/registro_incidencias/listar_incidencias.php?limite=50'),
            this.peticion('/api/asignacion_accesos/listar_solicitudes.php?limite=50'),
            this.peticion('/api/mantenimiento_preventivo/listar_mantenimientos.php?limite=50'),
            this.peticion('/api/diagnostico/listar.php?limite=50')
        ]);

        let totales = { soporte: 0, incidencias: 0, accesos: 0, mantenimiento: 0 };
        let pendientesPorModulo = { sop: 0, inc: 0, acc: 0 };
        let conteoDepartamentos = {};

        let totalAbiertos = 0;
        let totalCriticos = 0;
        let totalResueltos = 0;

        let hayAlertaCritica = false;
        let primerIncidenciaCritica = null;

        function registrarEstado(estado, esCritico, claveModulo) {
            const est = (estado || '').toLowerCase();
            const esCerrado = (est === 'resuelto' || est === 'atendido' || est === 'completado' || est === 'cerrada');

            if (!esCerrado) {
                totalAbiertos++;
                if (esCritico) totalCriticos++;
                if (claveModulo && pendientesPorModulo[claveModulo] !== undefined) {
                    pendientesPorModulo[claveModulo]++;
                }
            } else {
                totalResueltos++;
            }
        }

        function registrarDemandaArea(areaTexto) {
            if (!areaTexto || typeof areaTexto !== 'string') return;
            let limpia = areaTexto.trim();
            if (!limpia || limpia.length < 2) return;
            limpia = limpia.charAt(0).toUpperCase() + limpia.slice(1);
            conteoDepartamentos[limpia] = (conteoDepartamentos[limpia] || 0) + 1;
        }

        // Cache de registros para navegación de semanas en vivo
        const ticketsSop = (resSoporte.status === 'fulfilled' && resSoporte.value && resSoporte.value.exito && Array.isArray(resSoporte.value.datos.tickets)) ? resSoporte.value.datos.tickets : [];
        const regInc = (resIncidencias.status === 'fulfilled' && resIncidencias.value && resIncidencias.value.exito && Array.isArray(resIncidencias.value.datos.registros)) ? resIncidencias.value.datos.registros : [];
        const regAcc = (resAccesos.status === 'fulfilled' && resAccesos.value && resAccesos.value.exito && Array.isArray(resAccesos.value.datos.registros)) ? resAccesos.value.datos.registros : [];
        const regMp = (resMantenimiento.status === 'fulfilled' && resMantenimiento.value && resMantenimiento.value.exito && Array.isArray(resMantenimiento.value.datos.registros)) ? resMantenimiento.value.datos.registros : [];

        this._cacheDashboardRegistros = {
            soporte: ticketsSop,
            incidencias: regInc,
            accesos: regAcc,
            mantenimiento: regMp
        };

        // 1. Procesar Soporte Técnico
        if (resSoporte.status === 'fulfilled' && resSoporte.value && resSoporte.value.exito) {
            const d = resSoporte.value.datos;
            const m = d.metricas || {};
            totales.soporte = parseInt(m.total, 10) || 0;

            if (Array.isArray(d.tickets)) {
                d.tickets.forEach(t => {
                    const esUrgente = (t.prioridad === 'urgente' || t.prioridad === 'alta');
                    registrarEstado(t.estado, esUrgente, 'sop');
                    registrarDemandaArea(t.departamento_area || 'Área General');
                });
            }
        }

        // 2. Procesar Incidencias de Sistemas
        if (resIncidencias.status === 'fulfilled' && resIncidencias.value && resIncidencias.value.exito) {
            const d = resIncidencias.value.datos;
            const m = d.metricas || {};
            totales.incidencias = parseInt(m.total, 10) || 0;

            if (Array.isArray(d.registros)) {
                d.registros.forEach(i => {
                    const critStr = (i.nivel_criticidad || '').toLowerCase();
                    const esCrit = (critStr === 'critica_nivel_1' || critStr === 'alta_nivel_2');
                    const est = (i.estado || '').toLowerCase();
                    const noCerrada = (est !== 'resuelta' && est !== 'cerrada');

                    if (esCrit && noCerrada && !primerIncidenciaCritica) {
                        hayAlertaCritica = true;
                        primerIncidenciaCritica = {
                            codigo: i.nro_incidencia || 'INC-CRÍTICA',
                            sistema: i.sistema_erp_sai ? 'ERP - SAI (Facturación)' : (i.sistema_cobranzas_netcob ? 'Cobranzas - NETCOB' : (i.sistema_otros_detalle || 'Plataforma Crítica'))
                        };
                    }

                    registrarEstado(i.estado, esCrit, 'inc');

                    const areaInc = i.sistema_erp_sai ? 'ERP - SAI (Facturación)' : (i.sistema_cobranzas_netcob ? 'Cobranzas - NETCOB' : (i.sistema_otros_detalle || i.responsable_reporte || 'Infraestructura TI'));
                    registrarDemandaArea(areaInc);
                });
            }
        }

        // 3. Procesar Asignación de Accesos
        if (resAccesos.status === 'fulfilled' && resAccesos.value && resAccesos.value.exito) {
            const d = resAccesos.value.datos;
            const m = d.metricas || {};
            totales.accesos = parseInt(m.total, 10) || 0;

            if (Array.isArray(d.registros)) {
                d.registros.forEach(a => {
                    registrarEstado(a.estado, false, 'acc');
                    registrarDemandaArea(a.area_departamento || 'Institucional');
                });
            }
        }

        // 4. Procesar Mantenimiento Preventivo
        if (resMantenimiento.status === 'fulfilled' && resMantenimiento.value && resMantenimiento.value.exito) {
            const d = resMantenimiento.value.datos;
            const m = d.metricas || {};
            totales.mantenimiento = parseInt(m.total || m.total_mantenimientos, 10) || 0;

            if (Array.isArray(d.registros)) {
                d.registros.forEach(mp => {
                    registrarEstado('completado', false, 'mp');
                    registrarDemandaArea(mp.ubicacion_equipo || 'Oficinas / Equipos');
                });
            }
        }

        // 5. Procesar Telemetría PC (Bloque C)
        let optimosPc = 0;
        let advertenciasPc = 0;
        let criticosPc = 0;
        let totalPcs = 0;
        let antivirusActivos = 0;
        let alertasHardware = [];

        if (resDiag.status === 'fulfilled' && resDiag.value && resDiag.value.exito) {
            const d = resDiag.value.datos;
            const m = d.metricas || {};
            optimosPc = (parseInt(m.optimos, 10) || 0) + (parseInt(m.buenos, 10) || 0);
            advertenciasPc = parseInt(m.advertencias, 10) || 0;
            criticosPc = parseInt(m.criticos, 10) || 0;
            totalPcs = parseInt(m.total, 10) || 0;

            if (criticosPc > 0 && !hayAlertaCritica) {
                hayAlertaCritica = true;
            }

            if (Array.isArray(d.equipos)) {
                d.equipos.forEach(eq => {
                    if (eq.antivirus_activo === true || eq.antivirus_activo === 't' || eq.antivirus_activo === 1) {
                        antivirusActivos++;
                    }

                    const esCrit = (eq.estado_salud_general === 'critico');
                    const esAdv = (eq.estado_salud_general === 'advertencia');
                    const smartFalla = (eq.salud_disco_smart && eq.salud_disco_smart.toLowerCase().indexOf('falla') !== -1);
                    const pocoDisco = (eq.espacio_libre_c_gb !== null && parseFloat(eq.espacio_libre_c_gb) < 15);

                    if (esCrit || smartFalla || pocoDisco) {
                        alertasHardware.push({
                            hostname: eq.hostname || 'PC sin nombre',
                            area: eq.area || eq.usuario_windows || 'Oficina',
                            motivo: smartFalla ? 'Fallo SMART en Disco' : (pocoDisco ? `Disco C crítico (${eq.espacio_libre_c_gb}GB libres)` : 'Alerta crítica de HW'),
                            tipo: 'danger'
                        });
                    } else if (esAdv && alertasHardware.length < 3) {
                        alertasHardware.push({
                            hostname: eq.hostname || 'PC',
                            area: eq.area || 'Oficina',
                            motivo: `Memoria elevada (${eq.uso_ram_porcentaje || 85}% RAM)`,
                            tipo: 'warning'
                        });
                    }
                });
            }
        }

        // ====================================================================
        // RENDERIZADO: BLOQUE A (KPIS EJECUTIVOS)
        // ====================================================================
        // KPI 1: Carga de Trabajo y Desglose de Pendientes por Formulario
        const elTotAb = document.getElementById('kpiTotalAbiertos');
        const elAbEst = document.getElementById('kpiAbiertosEstatus');
        const elAbDesglose = document.getElementById('kpiAbiertosDesglose');

        if (elTotAb) elTotAb.textContent = totalAbiertos;
        if (elAbEst) {
            elAbEst.textContent = totalAbiertos > 0 ? (totalAbiertos + ' pendientes') : 'Al día';
            elAbEst.className = totalAbiertos > 0 ? 'badge bg-primary bg-opacity-15 text-primary fw-bold' : 'badge bg-success bg-opacity-15 text-success fw-bold';
        }
        if (elAbDesglose) {
            elAbDesglose.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-headset text-info me-1"></i>Soporte Técnico:</span>
                    <strong class="text-dark">${pendientesPorModulo.sop} de Soporte</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-exclamation-triangle text-danger me-1"></i>Incidencias:</span>
                    <strong class="text-danger">${pendientesPorModulo.inc} de Incidencias</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-key text-primary me-1"></i>Accesos:</span>
                    <strong class="text-primary">${pendientesPorModulo.acc} de Accesos</strong>
                </div>
            `;
        }

        // KPI 2: Criticidad Alta
        const elTotCrit = document.getElementById('kpiTotalCriticos');
        const elCritBadge = document.getElementById('kpiCriticosBadge');
        const elCritEst = document.getElementById('kpiCriticosEstatus');
        const elCritDetalleTexto = document.getElementById('kpiCriticosDetalleTexto');

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
            if (primerIncidenciaCritica) {
                elCritDetalleTexto.innerHTML = `<span class="badge bg-danger bg-opacity-15 text-danger px-1 py-0">${totalCriticos} Crítica(s)</span> en <strong>${primerIncidenciaCritica.sistema}</strong>`;
            } else {
                elCritDetalleTexto.innerHTML = `<span class="badge bg-success bg-opacity-15 text-success px-1 py-0">0 Críticas</span> en plataformas`;
            }
        }

        // KPI 3: Tickets Resueltos y Atendidos
        const elTotRes = document.getElementById('kpiTotalResueltos');
        const elResDetalle = document.getElementById('kpiResueltosDetalle');
        const elResEst = document.getElementById('kpiResueltosEstatus');
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
        if (elTotMp) elTotMp.textContent = totales.mantenimiento;
        if (elMpDetalle) {
            elMpDetalle.textContent = `${totales.mantenimiento} fichas técnicas registradas y firmadas`;
        }
        if (elMpEst) {
            elMpEst.textContent = totales.mantenimiento > 0 ? (totales.mantenimiento + ' fichas OK') : 'Sin fichas';
            elMpEst.className = totales.mantenimiento > 0 ? 'badge bg-info bg-opacity-15 text-info fw-bold' : 'badge bg-secondary bg-opacity-15 text-secondary fw-bold';
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
            if (hayAlertaCritica && primerIncidenciaCritica) {
                bannerEl.className = 'alert alert-danger border-0 shadow-sm rounded-3 py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2 mb-0';
                if (iconoAlerta) iconoAlerta.className = 'bi bi-exclamation-octagon-fill fs-5 text-danger';
                textoAlerta.innerHTML = `<strong>Atención Inmediata en Formulario de Incidencias:</strong> Incidencia Crítica <strong>[${primerIncidenciaCritica.codigo}]</strong> en <strong>${primerIncidenciaCritica.sistema}</strong> requiere atención técnica.`;
                if (badgeEstado) {
                    badgeEstado.className = 'badge bg-danger text-white small px-2 py-1';
                    badgeEstado.textContent = 'Alerta Crítica';
                }
                if (btnAccion) {
                    btnAccion.href = 'modulos/registro_incidencias/gestion_incidencias.php';
                    btnAccion.className = 'btn btn-danger btn-sm px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm';
                    btnAccion.innerHTML = '<span>Ir a atender</span><i class="bi bi-arrow-right"></i>';
                }
            } else if (hayAlertaCritica && criticosPc > 0) {
                bannerEl.className = 'alert alert-warning border-0 shadow-sm rounded-3 py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2 mb-0';
                if (iconoAlerta) iconoAlerta.className = 'bi bi-cpu-fill fs-5 text-warning';
                textoAlerta.innerHTML = `<strong>Atención de Hardware:</strong> Se detectaron <strong>${criticosPc}</strong> equipos con fallas críticas de SMART o temperatura en la red.`;
                if (badgeEstado) {
                    badgeEstado.className = 'badge bg-warning text-dark small px-2 py-1';
                    badgeEstado.textContent = 'Alerta Hardware';
                }
                if (btnAccion) {
                    btnAccion.href = 'modulos/diagnostico/monitoreo_equipos.php';
                    btnAccion.className = 'btn btn-warning btn-sm px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm text-dark';
                    btnAccion.innerHTML = '<span>Ver Equipos</span><i class="bi bi-arrow-right"></i>';
                }
            } else {
                bannerEl.className = 'alert alert-success border-0 shadow-sm rounded-3 py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2 mb-0';
                if (iconoAlerta) iconoAlerta.className = 'bi bi-check-circle-fill fs-5 text-success';
                textoAlerta.innerHTML = '<strong>Sistemas Institucionales Estables:</strong> No se registran incidencias críticas ni alertas de hardware pendientes en este momento.';
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
        this.renderizarDonaMensual();

        // Top Departamentos
        const contenedorTop = document.getElementById('contenedorTopDepartamentos');
        const elAreaMayor = document.getElementById('areaMayorDemanda');
        const elTotalAreas = document.getElementById('totalAreasAtendidas');

        if (contenedorTop) {
            const listaDeptos = Object.keys(conteoDepartamentos).map(k => ({
                nombre: k,
                total: conteoDepartamentos[k]
            })).sort((a, b) => b.total - a.total);

            if (elTotalAreas) elTotalAreas.textContent = listaDeptos.length;

            if (listaDeptos.length === 0) {
                contenedorTop.innerHTML = `
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary opacity-50"></i>
                        <span>No se registran solicitudes por área para este período aún.</span>
                    </div>
                `;
                if (elAreaMayor) elAreaMayor.textContent = 'Ninguna';
            } else {
                const totalDemandaAreas = listaDeptos.reduce((acc, curr) => acc + curr.total, 0);
                const top5 = listaDeptos.slice(0, 5);

                if (elAreaMayor) {
                    elAreaMayor.innerHTML = `<strong>${top5[0].nombre}</strong> (${top5[0].total} tickets)`;
                }

                const coloresBarras = [
                    { barra: 'bg-primary', badge: 'bg-primary text-white' },
                    { barra: 'bg-info', badge: 'bg-info text-info-emphasis' },
                    { barra: 'bg-success', badge: 'bg-success text-white' },
                    { barra: 'bg-warning', badge: 'bg-warning text-dark' },
                    { barra: 'bg-secondary', badge: 'bg-secondary text-white' }
                ];

                let html = '';
                top5.forEach((dep, index) => {
                    const pct = totalDemandaAreas > 0 ? Math.round((dep.total / totalDemandaAreas) * 100) : 0;
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
                                    <span class="badge ${estilo.badge} px-2 py-1" style="font-size: 0.72rem;">${pct}%</span>
                                </div>
                            </div>
                            <div class="progress" style="height: 7px; border-radius: 4px; background-color: #e2e8f0;">
                                <div class="progress-bar ${estilo.barra}" role="progressbar" style="width: ${pct}%;" aria-valuenow="${pct}" aria-valuemin="0" aria-valuemax="100"></div>
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

        if (elOptimos) elOptimos.textContent = optimosPc;
        if (elAdvertencias) elAdvertencias.textContent = advertenciasPc;
        if (elCriticos) elCriticos.textContent = criticosPc;

        if (elAntivirusTxt) {
            const pctAntivirus = totalPcs > 0 ? Math.round((antivirusActivos / totalPcs) * 100) : 100;
            elAntivirusTxt.textContent = `${pctAntivirus}% Protegido (${antivirusActivos}/${totalPcs} PCs)`;
        }

        if (contenedorAlertasHw) {
            if (alertasHardware.length === 0) {
                contenedorAlertasHw.innerHTML = `
                    <div class="p-3 rounded-3 bg-light border text-center text-muted small">
                        <i class="bi bi-check-circle-fill text-success fs-5 d-block mb-1"></i>
                        <span>Todos los discos (SMART) y memorias operan dentro de los parámetros seguros.</span>
                    </div>
                `;
            } else {
                let htmlHw = '';
                alertasHardware.slice(0, 3).forEach(al => {
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

