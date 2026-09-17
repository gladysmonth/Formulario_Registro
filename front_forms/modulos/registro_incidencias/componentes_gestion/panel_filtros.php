<?php
/**
 * Subcomponente: Filtros y Búsqueda de Incidencias (FOR_RIS_001, V-1)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/registro_incidencias/componentes_gestion/panel_filtros.php
 */
?>
<div class="tarjeta-formulario p-3 mb-4">
    <div class="row g-2 align-items-center">
        <!-- Buscador de texto -->
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-white text-secondary"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="filtroBuscarRI" 
                       placeholder="Buscar por Nro, responsable o descripción...">
            </div>
        </div>

        <!-- Filtro por Criticidad -->
        <div class="col-md-3 col-sm-6">
            <select class="form-select" id="filtroCriticidadRI">
                <option value="">Todas las Criticidades</option>
                <option value="critica_nivel_1">Nivel 1: Crítica (Facturación/Cajas)</option>
                <option value="alta_nivel_2">Nivel 2: Alta (Módulo Importante)</option>
                <option value="media_baja_nivel_3">Nivel 3: Media / Baja</option>
            </select>
        </div>

        <!-- Filtro por Estado -->
        <div class="col-md-2 col-sm-6">
            <select class="form-select" id="filtroEstadoRI">
                <option value="">Todos los Estados</option>
                <option value="abierta">Abiertas</option>
                <option value="en_atencion">En Atención</option>
                <option value="resuelta">Resueltas</option>
                <option value="cerrada">Cerradas</option>
            </select>
        </div>

        <!-- Filtro por Sistema -->
        <div class="col-md-2 col-sm-6">
            <select class="form-select" id="filtroSistemaRI">
                <option value="">Cualquier Sistema</option>
                <option value="erp_sai">ERP - SAI</option>
                <option value="netcob">Cobranzas - NETCOB</option>
                <option value="otros">Otros Sistemas</option>
            </select>
        </div>

        <!-- Botón Limpiar -->
        <div class="col-md-1 col-sm-6 text-end">
            <button type="button" class="btn btn-outline-secondary w-100" id="btnLimpiarFiltrosRI" title="Limpiar Filtros">
                <i class="bi bi-arrow-counterclockwise"></i>
            </button>
        </div>
    </div>
</div>
