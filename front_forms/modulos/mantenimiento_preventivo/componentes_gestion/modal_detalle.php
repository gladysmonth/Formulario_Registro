<?php
/**
 * Componente: Modal de Visualización de Ficha Técnica de Mantenimiento Preventivo
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/mantenimiento_preventivo/componentes_gestion/modal_detalle.php
 */
?>
<!-- MODAL DE DETALLE DE FICHA TÉCNICA -->
<div class="modal fade" id="modalDetalleMP" tabindex="-1" aria-labelledby="modalTituloMP" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="modalTituloMP">
                    <i class="bi bi-file-earmark-medical text-primary me-2"></i> Ficha de Mantenimiento: <span id="modalCodigoMP" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body p-4" id="imprimibleFichaMP">
                <!-- Cabecera Institucional para Impresión Oficial (solo visible al imprimir) -->
                <div class="cabecera-impresion-ficha d-none mb-2 pb-2 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold fs-6 text-uppercase text-dark">COSMOL R.L. &bull; DEPARTAMENTO DE SISTEMAS</div>
                            <div class="text-secondary small fw-semibold">FICHA TÉCNICA DE MANTENIMIENTO PREVENTIVO (PC / LAPTOP)</div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light text-dark border fs-6 px-3 py-1 fw-bold" id="mdlCodigoPrint"></span>
                        </div>
                    </div>
                </div>

                <!-- 1. Datos Generales -->
                <div class="bg-light p-3 rounded-3 border mb-3">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">
                        <i class="bi bi-person-badge text-primary me-1"></i> 1. Datos Generales
                    </h6>
                    <div class="row g-2 small">
                        <div class="col-md-5"><strong>Técnico Responsable:</strong> <span id="mdlTecnico"></span></div>
                        <div class="col-md-3"><strong>Fecha:</strong> <span id="mdlFecha"></span></div>
                        <div class="col-md-4"><strong>Ubicación:</strong> <span id="mdlUbicacion"></span></div>
                    </div>
                </div>

                <!-- 2. Información del Equipo -->
                <div class="bg-light p-3 rounded-3 border mb-3">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">
                        <i class="bi bi-pc-display text-primary me-1"></i> 2. Información del Equipo
                    </h6>
                    <div class="row g-2 small">
                        <div class="col-md-4"><strong>Tipo:</strong> <span id="mdlTipoEquipo"></span></div>
                        <div class="col-md-4"><strong>Nombre Equipo:</strong> <span id="mdlNombreEquipo"></span></div>
                        <div class="col-md-4"><strong>Cód. Activo:</strong> <span id="mdlCodActivo"></span></div>
                        <div class="col-md-4"><strong>Marca/Modelo:</strong> <span id="mdlMarcaModelo"></span></div>
                        <div class="col-md-4"><strong>Sistema Operativo:</strong> <span id="mdlSO"></span></div>
                        <div class="col-md-4"><strong>Red:</strong> <span id="mdlTipoRed"></span></div>
                        <div class="col-md-4"><strong>Procesador (CPU):</strong> <span id="mdlCPU"></span></div>
                        <div class="col-md-4"><strong>Memoria RAM:</strong> <span id="mdlRAM"></span></div>
                        <div class="col-md-4"><strong>Almacenamiento:</strong> <span id="mdlDisco"></span></div>
                        <div class="col-md-4"><strong>Dirección IP:</strong> <span id="mdlIP"></span></div>
                    </div>
                </div>

                <!-- 3. Mantenimiento Externo -->
                <div class="bg-light p-3 rounded-3 border mb-3">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">
                        <i class="bi bi-fan text-primary me-1"></i> 3. Mantenimiento Externo (Limpieza Física)
                    </h6>
                    <ul class="list-unstyled small mb-1" id="mdlCheckExterno"></ul>
                    <div id="mdlOtrosExternoCont" class="small text-muted fst-italic mt-1 d-none">
                        <strong>Otros:</strong> <span id="mdlOtrosExterno"></span>
                    </div>
                </div>

                <!-- 4. Mantenimiento Interno -->
                <div class="bg-light p-3 rounded-3 border mb-3">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">
                        <i class="bi bi-arrow-repeat text-success me-1"></i> 4. Mantenimiento Interno (Software / Configuración)
                    </h6>
                    <ul class="list-unstyled small mb-1" id="mdlCheckInterno"></ul>
                    <div id="mdlOtrosInternoCont" class="small text-muted fst-italic mt-1 d-none">
                        <strong>Otros:</strong> <span id="mdlOtrosInterno"></span>
                    </div>
                </div>

                <!-- 5. Verificación de Funcionamiento -->
                <div class="bg-light p-3 rounded-3 border mb-3">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">
                        <i class="bi bi-speedometer2 text-warning me-1"></i> 5. Verificación de Funcionamiento
                    </h6>
                    <ul class="list-unstyled small mb-0" id="mdlCheckVerificacion"></ul>
                </div>

                <!-- 6. Observaciones -->
                <div class="bg-light p-3 rounded-3 border mb-3">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">
                        <i class="bi bi-chat-left-text text-secondary me-1"></i> 6. Observaciones / Incidencias
                    </h6>
                    <p class="small text-secondary mb-0 bg-white p-2 rounded border" id="mdlObservaciones">
                        <em>Sin observaciones registradas.</em>
                    </p>
                </div>

                <!-- 7. Firmas de Conformidad -->
                <div class="bg-light p-3 rounded-3 border bloque-firmas-ficha">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="bi bi-pen text-dark me-1"></i> 7. Firmas Digitales de Conformidad
                    </h6>
                    <div class="row g-3 text-center">
                        <div class="col-md-6">
                            <div class="p-2 bg-white rounded border">
                                <span class="badge bg-secondary-subtle text-dark border mb-2 d-block">
                                    RESPONSABLE DEL EQUIPO
                                </span>
                                <img id="mdlFirmaResponsable" src="" alt="Firma Responsable" class="img-fluid" style="max-height: 90px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 bg-white rounded border">
                                <span class="badge bg-secondary-subtle text-dark border mb-2 d-block">
                                    DPTO. DE SISTEMAS
                                </span>
                                <img id="mdlFirmaSistemas" src="" alt="Firma Sistemas" class="img-fluid" style="max-height: 90px;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 8. Panel de Edición y Actualización de Ficha (Solo para Técnicos/Admin) -->
                <div id="panelEdicionMP" class="d-none border-top pt-3 mt-4">
                    <div class="alert alert-primary py-2 d-flex align-items-center gap-2 small mb-3">
                        <i class="bi bi-pencil-square fs-5 text-primary"></i>
                        <div>
                            <strong>Modo de Actualización Técnica:</strong> Modifique los datos generales, observaciones o actualice la firma de Sistemas para esta ficha.
                        </div>
                    </div>

                    <form id="formActualizarMP" novalidate>
                        <input type="hidden" id="editIdMP" value="">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="editTecnicoMP" class="form-label small fw-semibold text-secondary mb-1">
                                    <i class="bi bi-person me-1"></i> Técnico Responsable:
                                </label>
                                <input type="text" class="form-control form-control-sm" id="editTecnicoMP" required>
                            </div>
                            <div class="col-md-6">
                                <label for="editUbicacionMP" class="form-label small fw-semibold text-secondary mb-1">
                                    <i class="bi bi-geo-alt me-1"></i> Ubicación del Equipo:
                                </label>
                                <input type="text" class="form-control form-control-sm" id="editUbicacionMP" required>
                            </div>
                            <div class="col-12">
                                <label for="editObservacionesMP" class="form-label small fw-semibold text-secondary mb-1">
                                    <i class="bi bi-chat-left-text me-1"></i> Observaciones / Incidencias Detectadas:
                                </label>
                                <textarea class="form-control form-control-sm" id="editObservacionesMP" rows="3" placeholder="Detalle observaciones preventivas, trabajos adicionales realizados..."></textarea>
                            </div>
                        </div>

                        <!-- Firma Digital de Sistemas en Edición -->
                        <div class="bg-light p-3 rounded-3 border mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                <label class="form-label small fw-bold text-dark mb-0">
                                    <i class="bi bi-pen me-1 text-primary"></i> Firma Digital del Dpto. de Sistemas:
                                </label>
                                <div class="d-flex align-items-center gap-2">
                                    <span id="badgeFirmaEditSistemasMP" class="badge bg-secondary-subtle text-secondary border">Firma Actual Conservada</span>
                                    <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" id="btnLimpiarFirmaEditSistemasMP" style="font-size: 0.75rem;">
                                        <i class="bi bi-eraser me-1"></i> Nueva Firma
                                    </button>
                                </div>
                            </div>
                            <div class="border rounded bg-white p-1 text-center">
                                <canvas id="canvasFirmaEditSistemasMP" width="450" height="110" class="w-100" style="touch-action: none; cursor: crosshair; max-height: 110px;"></canvas>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                * Si no dibuja un nuevo trazo, se mantendrá la firma previa registrada en el sistema.
                            </small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnCancelarEdicionMP">
                                <i class="bi bi-x-circle me-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-success btn-sm px-3 shadow-sm" id="btnGuardarEdicionMP">
                                <i class="bi bi-check-circle me-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal-footer bg-light justify-content-between">
                <button type="button" class="btn btn-outline-primary" id="btnToggleEditarMP">
                    <i class="bi bi-pencil-square me-1"></i> <span id="textoBtnEditarMP">Editar / Actualizar Ficha</span>
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" id="btnImprimirFichaMP">
                        <i class="bi bi-printer me-1"></i> Imprimir Ficha
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
