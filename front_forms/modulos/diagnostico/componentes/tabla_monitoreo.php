<?php
/**
 * Componente: Barra de Filtros y Tabla Principal de Equipos
 * Archivo: front_forms/modulos/diagnostico/componentes/tabla_monitoreo.php
 */
?>
<!-- Barra de Filtros y Búsqueda -->
<div class="card border-0 shadow-sm rounded-3 bg-white mb-3">
    <div class="card-body p-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-6 col-lg-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0" id="inputBuscar" placeholder="Buscar por Hostname, MAC, Usuario o Marca...">
                </div>
            </div>
            <div class="col-md-6 col-lg-7 d-flex justify-content-md-end gap-1 flex-wrap" id="filtrosEstado">
                <button class="btn btn-sm btn-primary active-filtro btn-filtro" data-filtro="todos">Todos</button>
                <button class="btn btn-sm btn-outline-success btn-filtro" data-filtro="optimo">Óptimos</button>
                <button class="btn btn-sm btn-outline-warning btn-filtro" data-filtro="advertencia">Advertencias</button>
                <button class="btn btn-sm btn-outline-danger btn-filtro" data-filtro="critico">Críticos</button>
            </div>
        </div>
    </div>
</div>

<!-- Tabla Principal de Monitoreo -->
<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tablaEquipos">
            <thead class="table-light border-bottom text-secondary small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                <tr>
                    <th class="ps-4">Equipo / Responsable</th>
                    <th>Red / MAC</th>
                    <th>CPU (Carga)</th>
                    <th>Memoria RAM</th>
                    <th>Almacenamiento</th>
                    <th>Antivirus</th>
                    <th>Último Reporte</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody id="cuerpoTablaEquipos">
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                        <div>Cargando telemetría de equipos...</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
