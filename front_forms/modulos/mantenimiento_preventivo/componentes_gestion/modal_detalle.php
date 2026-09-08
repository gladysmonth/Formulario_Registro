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
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-primary" id="btnImprimirFichaMP">
                    <i class="bi bi-printer me-1"></i> Imprimir Ficha
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
