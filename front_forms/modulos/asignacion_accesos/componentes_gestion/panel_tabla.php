<?php
/**
 * Subcomponente: Tabla de Registros de Solicitudes de Accesos
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/asignacion_accesos/componentes_gestion/panel_tabla.php
 */
?>
<div class="tarjeta-formulario p-0 overflow-hidden mb-4 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tablaSolicitudesAccesos">
            <thead class="table-dark text-nowrap small text-uppercase">
                <tr>
                    <th scope="col" class="ps-3 py-3">Código</th>
                    <th scope="col" class="py-3">Fecha</th>
                    <th scope="col" class="py-3">Solicitante</th>
                    <th scope="col" class="py-3">Área / Cargo</th>
                    <th scope="col" class="py-3">Plataforma</th>
                    <th scope="col" class="py-3">Usuario / Cuenta</th>
                    <th scope="col" class="py-3 text-center">Estado</th>
                    <th scope="col" class="py-3 text-center">Firmas</th>
                    <th scope="col" class="pe-3 py-3 text-end">Acciones</th>
                </tr>
            </thead>
            <tbody id="cuerpoTablaAccesos" class="small">
                <!-- Se poblará dinámicamente mediante JavaScript -->
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        Cargando solicitudes de accesos...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Estado Vacío -->
    <div id="estadoVacioAccesos" class="d-none text-center py-5 px-3">
        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
        <h6 class="fw-bold text-dark mt-3 mb-1">No se encontraron solicitudes</h6>
        <p class="text-muted small mb-3">No hay registros que coincidan con los filtros seleccionados.</p>
        <a href="formulario_accesos.php" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-plus-circle me-1"></i> Nueva Solicitud
        </a>
    </div>
</div>
