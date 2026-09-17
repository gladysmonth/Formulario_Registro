<?php
/**
 * Subcomponente: Tabla de Registros de Incidencias (FOR_RIS_001, V-1)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/registro_incidencias/componentes_gestion/panel_tabla.php
 */
?>
<div class="tarjeta-formulario p-0 overflow-hidden mb-4 border">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tablaIncidencias">
            <thead class="table-light">
                <tr class="small text-secondary text-uppercase">
                    <th class="ps-3 py-3" style="width: 14%;">Nro. Incidencia</th>
                    <th style="width: 14%;">Fecha / Hora</th>
                    <th style="width: 16%;">Responsable</th>
                    <th style="width: 15%;">Plataforma</th>
                    <th style="width: 13%;">Criticidad</th>
                    <th style="width: 12%;">Estado</th>
                    <th class="text-end pe-3" style="width: 16%;">Acciones</th>
                </tr>
            </thead>
            <tbody id="cuerpoTablaIncidencias">
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm text-danger me-2" role="status"></div>
                        Cargando registros de incidencias...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Paginación / Resumen de Registros -->
    <div class="p-3 bg-light border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
        <small class="text-muted" id="textoResumenTablaRI">Mostrando 0 incidencias</small>
        <div id="paginacionRI"></div>
    </div>
</div>
