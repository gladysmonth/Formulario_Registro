<?php
/**
 * Componente: Tabla de Solicitudes del Usuario Solicitante
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/mis_solicitudes/componentes/tabla_mis_solicitudes.php
 */
?>
<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
    <div class="card-header bg-white border-bottom p-3 p-md-4">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-lg-5">
                <h5 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-clock-history text-primary me-2"></i>Historial de Mis Requerimientos
                </h5>
                <p class="text-muted small mb-0">Seguimiento en vivo del estado de atención técnica</p>
            </div>
            <div class="col-12 col-lg-7">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end align-items-center">
                    <div class="input-group input-group-sm flex-grow-1" style="min-width: 140px; max-width: 220px;">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="filtroBusquedaSolicitud" class="form-control border-start-0 bg-light" placeholder="Buscar ticket o código...">
                    </div>
                    <select id="filtroModuloSolicitud" class="form-select form-select-sm bg-light flex-grow-1" style="min-width: 130px; max-width: 180px;">
                        <option value="">Todos los formularios</option>
                        <option value="soporte">Soporte Técnico</option>
                        <option value="incidencia">Incidencias Sistemas</option>
                        <option value="accesos">Asignación Accesos</option>
                    </select>
                    <select id="filtroEstadoSolicitud" class="form-select form-select-sm bg-light flex-grow-1" style="min-width: 110px; max-width: 140px;">
                        <option value="">Todos estados</option>
                        <option value="pendiente">En Espera</option>
                        <option value="en_proceso">En Atención</option>
                        <option value="resuelto">Atendidas</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tablaMisSolicitudes">
            <thead class="table-light small text-uppercase text-secondary">
                <tr>
                    <th class="ps-4" style="width: 140px;">Nro. Registro</th>
                    <th>Formulario</th>
                    <th>Fecha Envío</th>
                    <th>Resumen del Problema / Petición</th>
                    <th class="text-center">Estado Actual</th>
                    <th>Asignado A</th>
                    <th class="text-end pe-4" style="width: 100px;">Detalle</th>
                </tr>
            </thead>
            <tbody id="cuerpoTablaMisSolicitudes" class="border-top-0 small">
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        Consultando tus solicitudes...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

