<?php
/**
 * Componente: Barra de Búsqueda y Filtros del Panel
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/soporte_tecnico/componentes_gestion/panel_filtros.php
 */
?>
<!-- Barra de Filtros y Búsqueda -->
<div class="tarjeta-formulario p-3 mb-4">
    <div class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="inputBuscar" placeholder="Buscar por código, solicitante, área o descripción...">
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <select class="form-select" id="filtroEstado">
                <option value="">Estado: Todos</option>
                <option value="pendiente">Solo Pendientes</option>
                <option value="en_proceso">Solo En Proceso</option>
                <option value="resuelto">Solo Resueltos</option>
                <option value="cancelado">Solo Cancelados</option>
            </select>
        </div>
        <div class="col-md-4 col-sm-6">
            <select class="form-select" id="filtroPrioridad">
                <option value="">Prioridad: Todas</option>
                <option value="urgente">Urgente</option>
                <option value="alta">Alta</option>
                <option value="media">Media</option>
                <option value="baja">Baja</option>
            </select>
        </div>
    </div>
</div>
