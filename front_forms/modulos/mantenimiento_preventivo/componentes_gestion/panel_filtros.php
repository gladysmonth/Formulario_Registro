<?php
/**
 * Componente: Barra de Filtros y Búsqueda de Mantenimientos
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/mantenimiento_preventivo/componentes_gestion/panel_filtros.php
 */
?>
<!-- Barra de Filtros y Búsqueda -->
<div class="tarjeta-formulario p-3 mb-4">
    <div class="row g-2 align-items-center">
        <div class="col-md-8">
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="inputBuscarMP" 
                       placeholder="Buscar por código (MP-...), técnico, ubicación, hostname o código de activo...">
            </div>
        </div>
        <div class="col-md-4">
            <select class="form-select" id="filtroTipoEquipoMP">
                <option value="">Tipo de Equipo: Todos</option>
                <option value="PC">Solo Computadoras de Escritorio (PC)</option>
                <option value="LAPTOP">Solo Computadoras Portátiles (LAPTOP)</option>
            </select>
        </div>
    </div>
</div>
