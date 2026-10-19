/**
 * Lógica Cliente: Dashboard de Monitoreo y Métricas Institucionales
 * Archivo: front_forms/recursos/js/metricas_dashboard.js
 */

/**
 * Orquestador Dinámico del Dashboard de Gestión Técnica
 */
async function actualizarDashboardMetricas() {
    const btn = document.getElementById('btnRefrescarMetricas');
    const icono = document.getElementById('iconoRefrescarMetricas');
    const textoHora = document.getElementById('textoUltimaActualizacion');

    if (icono) icono.classList.add('spin-animado');
    if (btn) btn.disabled = true;

    try {
        if (typeof clienteApi !== 'undefined' && typeof clienteApi.cargarDashboardGestionTecnica === 'function') {
            await clienteApi.cargarDashboardGestionTecnica();
        }
        if (textoHora) {
            const ahora = new Date();
            textoHora.textContent = 'Actualizado a las ' + ahora.toLocaleTimeString();
        }
    } catch (e) {
        console.error('Error al actualizar dashboard:', e);
    } finally {
        if (icono) icono.classList.remove('spin-animado');
        if (btn) btn.disabled = false;
    }
}

/**
 * Permite cambiar el mes desde el selector desplegable
 */
async function cambiarPeriodoMetricas() {
    const select = document.getElementById('selectMesMetricas');
    if (!select || typeof clienteApi === 'undefined') return;

    const mesSeleccionado = parseInt(select.value, 10);
    const ahora = new Date();
    const mesActual = ahora.getMonth() + 1;
    const diffMeses = mesSeleccionado - mesActual;

    clienteApi._offsetMesDashboard = diffMeses;
    await actualizarDashboardMetricas();
}

document.addEventListener('DOMContentLoaded', function() {
    // Sincronizar selector con mes actual
    const select = document.getElementById('selectMesMetricas');
    if (select) {
        select.value = (new Date()).getMonth() + 1;
    }
    actualizarDashboardMetricas();
});
