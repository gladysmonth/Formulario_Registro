<?php
/**
 * Componente: Modal de Consulta y Detalle de Solicitud
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/mis_solicitudes/componentes/modal_detalle_solicitud.php
 */
?>
<div class="modal fade" id="modalDetalleSolicitud" tabindex="-1" aria-labelledby="modalDetalleTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary px-3 py-2 fs-6 font-monospace" id="modalDetalleCodigo">-</span>
                    <span id="modalDetalleBadgeEstado">-</span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted mb-0">Módulo Institucional</label>
                        <p class="fw-semibold text-dark mb-0" id="modalDetalleModulo">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted mb-0">Fecha de Registro</label>
                        <p class="text-dark mb-0" id="modalDetalleFecha">-</p>
                    </div>

                    <div class="col-12 pt-2">
                        <label class="form-label small fw-bold text-muted mb-1">Descripción / Requerimiento Reportado</label>
                        <div class="p-3 bg-light rounded-3 border text-secondary small" id="modalDetalleResumen" style="white-space: pre-wrap;">-</div>
                    </div>

                    <div class="col-md-6 pt-2">
                        <label class="form-label small fw-bold text-muted mb-0">Atención Técnica / Responsable</label>
                        <p class="fw-semibold text-dark mb-0 d-flex align-items-center gap-1" id="modalDetalleTecnico">
                            <i class="bi bi-person-check text-primary"></i> <span>-</span>
                        </p>
                    </div>
                    <div class="col-md-6 pt-2">
                        <label class="form-label small fw-bold text-muted mb-0">Prioridad / Criticidad</label>
                        <p class="mb-0 text-capitalize" id="modalDetallePrioridad">-</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 px-4">
                <button type="button" class="btn btn-secondary btn-sm px-4 fw-semibold" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

