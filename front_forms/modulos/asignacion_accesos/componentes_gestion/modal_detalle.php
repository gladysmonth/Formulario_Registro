<?php
/**
 * Subcomponente: Modal de Ficha Técnica Oficial e Impresión (Asignación de Accesos)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/asignacion_accesos/componentes_gestion/modal_detalle.php
 */
?>
<div class="modal fade" id="modalDetalleAccesos" tabindex="-1" aria-labelledby="tituloModalAccesos" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            
            <!-- Encabezado del Modal (No imprimible) -->
            <div class="modal-header bg-primary text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-shield-lock-fill fs-4"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="tituloModalAccesos">
                            Solicitud de Accesos: <span id="modalCodigoAccesos" class="font-monospace">ACC-0000-0000</span>
                        </h5>
                        <small class="opacity-75">COSMOL R.L. &bull; Departamento de Sistemas</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-white text-primary fw-bold px-3 py-2 fs-6 rounded-pill" id="modalBadgeEstado">
                        Pendiente
                    </span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <!-- Cuerpo del Modal: Ficha Técnica Oficial Imprimible -->
            <div class="modal-body p-4 bg-light" id="areaImprimibleAccesos">
                
                <!-- Encabezado Institucional de Impresión (Visible en papel/PDF) -->
                <div class="encabezado-impresion-mp d-none mb-3 pb-2 border-bottom border-2 border-primary">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h6 class="fw-bold text-dark mb-0">COOPERATIVA DE SERVICIOS PÚBLICOS "MONTERO" R.L. - COSMOL R.L.</h6>
                            <h5 class="fw-bold text-primary mb-0">CREACIÓN Y ASIGNACIÓN DE ACCESOS DE USUARIOS DE SISTEMAS</h5>
                            <small class="text-muted">DEPARTAMENTO DE SISTEMAS &bull; GESTIÓN DE PRIVILEGIOS Y CUENTAS INSTITUCIONALES</small>
                        </div>
                        <div class="col-4 text-end">
                            <div class="badge bg-primary-subtle text-primary border border-primary p-2 fs-6">
                                CONTROL DE ACCESOS
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 1. DATOS GENERALES -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white py-2 fw-bold text-dark border-bottom small text-uppercase">
                        <i class="bi bi-info-circle text-primary me-1"></i> 1. Datos Generales del Solicitante
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3 col-sm-6">
                                <small class="text-muted d-block">Nro. de Solicitud:</small>
                                <span class="fw-bold font-monospace text-primary fs-6" id="modalNroSolicitud">-</span>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <small class="text-muted d-block">Fecha de Solicitud:</small>
                                <span class="fw-semibold text-dark" id="modalFechaSolicitud">-</span>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <small class="text-muted d-block">Nombre del Solicitante:</small>
                                <span class="fw-bold text-dark" id="modalNombreSolicitante">-</span>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <small class="text-muted d-block">Cargo Institucional:</small>
                                <span class="fw-semibold text-dark" id="modalCargoSolicitante">-</span>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <small class="text-muted d-block">Área / Departamento:</small>
                                <span class="fw-semibold text-dark" id="modalAreaDepartamento">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. PLATAFORMA / SISTEMA Y CONDICIÓN DE USUARIO -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-white py-2 fw-bold text-dark border-bottom small text-uppercase">
                                <i class="bi bi-pc-display text-primary me-1"></i> 2. Plataforma / Sistema Afectado
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush small" id="modalListaPlataformas">
                                    <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                        <span>SISTEMA ERP - SAI</span>
                                        <span id="badgeModalSai" class="badge bg-light text-secondary border">-</span>
                                    </li>
                                    <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                        <span>SISTEMA DE COBRANZAS - NETCOB</span>
                                        <span id="badgeModalNetcob" class="badge bg-light text-secondary border">-</span>
                                    </li>
                                    <li class="list-group-item px-0 py-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>OTROS SISTEMAS</span>
                                            <span id="badgeModalOtros" class="badge bg-light text-secondary border">-</span>
                                        </div>
                                        <div id="modalDetalleOtros" class="text-muted small mt-1 ps-2 fst-italic"></div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-white py-2 fw-bold text-dark border-bottom small text-uppercase">
                                <i class="bi bi-person-badge text-primary me-1"></i> 3. Datos del Usuario / Cuenta
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <small class="text-muted d-block">Condición de la Cuenta:</small>
                                    <span id="modalCondicionUsuario" class="badge bg-info-subtle text-info border px-2 py-1 fs-6 mt-1">-</span>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Username / Especificación de Cuenta:</small>
                                    <span id="modalUsernameDetalles" class="font-monospace fw-bold text-dark fs-6">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. REQUERIMIENTOS DE ACCESOS -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white py-2 fw-bold text-dark border-bottom small text-uppercase">
                        <i class="bi bi-file-earmark-text text-primary me-1"></i> 4. Requerimientos de Accesos (Perfiles, Permisos y Módulos)
                    </div>
                    <div class="card-body">
                        <pre class="mb-0 bg-white p-3 rounded border font-monospace text-dark small" 
                             id="modalRequerimientosAccesos" style="white-space: pre-wrap; font-family: inherit;">-</pre>
                    </div>
                </div>

                <!-- 5. DEPARTAMENTO DE SISTEMAS (Atención Técnica) -->
                <div class="card mb-3 border-0 shadow-sm border-start border-4 border-info">
                    <div class="card-header bg-white py-2 fw-bold text-dark border-bottom small text-uppercase d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-gear-fill text-info me-1"></i> 5. Atención y Comentarios de Sistemas</span>
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 no-print" id="btnEditarAtencionModal">
                            <i class="bi bi-pencil-square me-1"></i> Atender / Modificar
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Vista de Solo Lectura -->
                        <div id="vistaLecturaAtencion">
                            <div class="row g-3">
                                <div class="col-md-6 col-sm-6">
                                    <small class="text-muted d-block">Técnico Responsable:</small>
                                    <span class="fw-bold text-dark" id="modalAtendidoPor">-</span>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <small class="text-muted d-block">Fecha y Hora de Atención:</small>
                                    <span class="fw-semibold text-dark" id="modalFechaAtencion">-</span>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">Comentarios / Observaciones Técnicas:</small>
                                    <p class="mb-0 text-secondary small fst-italic" id="modalComentariosSistemas">-</p>
                                </div>
                            </div>
                        </div>

                        <!-- Formulario de Edición Rápida (Oculto por defecto) -->
                        <form id="formAtencionModalAccesos" class="d-none mt-2 p-3 bg-light rounded border">
                            <input type="hidden" id="editAccesosId">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Atendido por (Técnico):</label>
                                    <input type="text" class="form-control form-control-sm" id="editAtendidoPor" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Fecha y Hora de Atención:</label>
                                    <input type="datetime-local" class="form-control form-control-sm" id="editFechaAtencion" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Estado de la Solicitud:</label>
                                    <select class="form-select form-select-sm" id="editEstadoAccesos">
                                        <option value="pendiente">Pendiente</option>
                                        <option value="en_proceso">En Proceso</option>
                                        <option value="atendido">Atendido / Habilitado</option>
                                        <option value="rechazado">Rechazado</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold">Comentarios u Observaciones Técnicas:</label>
                                    <textarea class="form-control form-control-sm" id="editComentariosSistemas" rows="2" 
                                              placeholder="Observaciones de configuración, credenciales o restricciones aplicadas"></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="button" class="btn btn-sm btn-light border me-1" id="btnCancelarEdicionModal">Cancelar</button>
                                    <button type="submit" class="btn btn-sm btn-success fw-semibold" id="btnGuardarEdicionModal">
                                        <i class="bi bi-check2-circle me-1"></i> Guardar Atención
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- 6. FIRMAS INSTITUCIONALES (Triple Firma) -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white py-2 fw-bold text-dark border-bottom small text-uppercase">
                        <i class="bi bi-pen text-primary me-1"></i> 6. Firmas y Validaciones Institucionales
                    </div>
                    <div class="card-body">
                        <div class="row g-3 text-center">
                            <!-- Firma Solicitante -->
                            <div class="col-md-4">
                                <div class="border rounded p-2 bg-white h-100 d-flex flex-column justify-content-between">
                                    <div class="contenedor-firma-vista" style="min-height: 90px;">
                                        <img id="modalImgFirmaSolicitante" class="img-fluid" style="max-height: 85px; display: none;" alt="Firma Solicitante">
                                        <span id="modalSinFirmaSolicitante" class="text-muted small fst-italic d-block pt-4">Sin firma</span>
                                    </div>
                                    <div class="border-top pt-2 mt-2">
                                        <strong class="d-block small text-dark" id="modalNombrePieSolicitante">SOLICITANTE</strong>
                                        <small class="text-muted" style="font-size: 0.75rem;">Conformidad del Requerimiento</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Firma Autoriza -->
                            <div class="col-md-4">
                                <div class="border rounded p-2 bg-white h-100 d-flex flex-column justify-content-between">
                                    <div class="contenedor-firma-vista" style="min-height: 90px;">
                                        <img id="modalImgFirmaAutoriza" class="img-fluid" style="max-height: 85px; display: none;" alt="Firma Autoriza">
                                        <span id="modalSinFirmaAutoriza" class="text-muted small fst-italic d-block pt-4">Sin firma</span>
                                    </div>
                                    <div class="border-top pt-2 mt-2">
                                        <strong class="d-block small text-dark" id="modalNombrePieAutoriza">AUTORIZA (JEFATURA)</strong>
                                        <small class="text-muted" style="font-size: 0.75rem;">Aprobación Jerárquica</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Firma Sistemas -->
                            <div class="col-md-4">
                                <div class="border rounded p-2 bg-white h-100 d-flex flex-column justify-content-between">
                                    <div class="contenedor-firma-vista" style="min-height: 90px;">
                                        <img id="modalImgFirmaSistemas" class="img-fluid" style="max-height: 85px; display: none;" alt="Firma Sistemas">
                                        <span id="modalSinFirmaSistemas" class="text-muted small fst-italic d-block pt-4">Sin firma</span>
                                    </div>
                                    <div class="border-top pt-2 mt-2">
                                        <strong class="d-block small text-dark">DPTO. DE SISTEMAS</strong>
                                        <small class="text-muted" style="font-size: 0.75rem;">Certificación Técnica</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Pie del Modal: Acciones e Impresión -->
            <div class="modal-footer bg-white border-top d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i> Cerrar
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-dark btn-sm px-3" id="btnImprimirFichaAccesos">
                        <i class="bi bi-printer me-1"></i> Imprimir Ficha Oficial
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
