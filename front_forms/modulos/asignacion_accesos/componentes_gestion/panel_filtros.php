<?php
/**
 * Subcomponente: Filtros y Búsqueda de Solicitudes de Accesos
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/asignacion_accesos/componentes_gestion/panel_filtros.php
 */
?>
<div class="tarjeta-formulario p-3 mb-4">
    <div class="row g-3 align-items-center">
        <!-- Buscador general -->
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white text-secondary"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="filtroBuscarAccesos" 
                       placeholder="Buscar por código, solicitante, username o área...">
            </div>
        </div>

        <!-- Filtro por Estado -->
        <div class="col-md-3 col-sm-6">
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary"><i class="bi bi-funnel"></i></span>
                <select class="form-select" id="filtroEstadoAccesos">
                    <option value="">Todos los Estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="en_proceso">En Proceso</option>
                    <option value="atendido">Atendido / Habilitado</option>
                    <option value="rechazado">Rechazado</option>
                </select>
            </div>
        </div>

        <!-- Filtro por Sistema -->
        <div class="col-md-3 col-sm-6">
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary"><i class="bi bi-pc-display"></i></span>
                <select class="form-select" id="filtroSistemaAccesos">
                    <option value="">Todas las Plataformas</option>
                    <option value="sai">SISTEMA ERP - SAI</option>
                    <option value="netcob">SISTEMA COBRANZAS - NETCOB</option>
                    <option value="otros">Otros Sistemas</option>
                </select>
            </div>
        </div>

        <!-- Botón Limpiar Filtros -->
        <div class="col-md-1 text-md-end text-center">
            <button type="button" class="btn btn-outline-secondary w-100" id="btnLimpiarFiltrosAccesos" title="Restablecer filtros">
                <i class="bi bi-arrow-counterclockwise"></i>
            </button>
        </div>
    </div>
</div>
