/**
 * Cliente Centralizado de Comunicación con la API Backend
 * Archivo: cliente_api.js
 */

const CONFIGURACION_API = {
    // Si se accede desde navegador, el backend responde en el puerto 83 mapeado por Docker
    urlBase: window.URL_BACKEND || 'http://localhost:83'
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
    }
};

// Verificar estado de conexión automáticamente al cargar
document.addEventListener('DOMContentLoaded', () => {
    clienteApi.verificarSaludBackend();
});
