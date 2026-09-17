<?php
/**
 * Subcomponente: Modal de Ficha Técnica Oficial e Impresión (FOR_RIS_001, V-1)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/registro_incidencias/componentes_gestion/modal_detalle.php
 */
?>
<div class="modal fade" id="modalDetalleIncidencia" tabindex="-1" aria-labelledby="tituloModalIncidencia" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            
            <!-- Encabezado del Modal (No imprimible) -->
            <div class="modal-header bg-danger text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-shield-exclamation fs-4"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="tituloModalIncidencia">
                            Ficha de Incidencia: <span id="modalCodigoIncidencia" class="font-monospace">INC-0000-0000</span>
                        </h5>
                        <small class="opacity-75">COSMOL R.L. &bull; Departamento de Sistemas</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-white text-danger fw-bold px-3 py-2 fs-6 rounded-pill" id="modalBadgeEstado">
                        Abierta
                    </span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <!-- Cuerpo del Modal: Ficha Técnica Oficial Imprimible -->
            <div class="modal-body p-4 bg-light" id="areaImprimibleIncidencia">
                
                <!-- Encabezado Institucional de Impresión (Visible en papel/PDF) -->
                <div class="encabezado-impresion-mp d-none mb-3 pb-2 border-bottom border-2 border-danger">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h6 class="fw-bold text-dark mb-0">COOPERATIVA DE SERVICIOS PÚBLICOS "MONTERO" R.L. - COSMOL R.L.</h6>
                            <h5 class="fw-bold text-danger mb-0">REGISTRO DE INCIDENCIAS DE SISTEMAS</h5>
                            <small class="text-muted">DEPARTAMENTO DE SISTEMAS &bull; GESTIÓN Y CONTROL DE EVENTOS TECNOLÓGICOS</small>
                        </div>
                        <div class="col-4 text-end">
                            <div class="badge bg-danger-subtle text-danger border border-danger p-2 fs-6">
                                CONTROL DE INCIDENCIAS
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 1. DATOS GENERALES Y CRITICIDAD -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white py-2 fw-bold text-dark border-bottom small text-uppercase">
                        <i class="bi bi-info-circle text-primary me-1"></i> 1. Datos Generales y Nivel de Criticidad
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3 col-sm-6">
                                <small class="text-muted d-block">Nro. de Incidencia:</small>
                                <span class="fw-bold font-monospace text-danger fs-6" id="modalNroIncidencia">-</span>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <small class="text-muted d-block">Fecha y Hora Reporte:</small>
                                <span class="fw-semibold text-dark" id="modalFechaHoraReporte">-</span>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <small class="text-muted d-block">Responsable del Reporte:</small>
                                <span class="fw-semibold text-dark" id="modalResponsableReporte">-</span>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <small class="text-muted d-block">Criticidad Institucional:</small>
                                <span id="modalCriticidadBadge">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. PLATAFORMA / SISTEMA AFECTADO -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white py-2 fw-bold text-dark border-bottom small text-uppercase">
                        <i class="bi bi-hdd-network text-primary me-1"></i> 2. Plataforma / Sistema Afectado
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-2 flex-wrap" id="modalSistemasBadges">
                            <!-- Badges dinámicos de sistemas -->
                        </div>
                        <div id="modalOtrosSistemasDetalle" class="mt-2 text-muted small d-none">
                            <strong>Detalle de Otros Sistemas:</strong> <span id="modalTextoOtrosSistemas"></span>
                        </div>
                    </div>
                </div>

                <!-- 3. NATURALEZA TÉCNICA DEL FALLO -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white py-2 fw-bold text-dark border-bottom small text-uppercase">
                        <i class="bi bi-tools text-primary me-1"></i> 3. Naturaleza Técnica del Fallo
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 35%;">Componente</th>
                                        <th style="width: 15%; text-align: center;">Estado</th>
                                        <th style="width: 50%;">Descripción / Tipo de Error</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold"><i class="bi bi-database text-primary me-1"></i> Base de Datos</td>
                                        <td class="text-center" id="modalBadgeFalloBD">-</td>
                                        <td id="modalDetalleFalloBD" class="text-muted">Sin fallo reportado</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold"><i class="bi bi-server text-info me-1"></i> Infraestructura / Servidor</td>
                                        <td class="text-center" id="modalBadgeFalloServidor">-</td>
                                        <td id="modalDetalleFalloServidor" class="text-muted">Sin fallo reportado</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold"><i class="bi bi-router text-warning me-1"></i> Enlaces / Conectividad</td>
                                        <td class="text-center" id="modalBadgeFalloRed">-</td>
                                        <td id="modalDetalleFalloRed" class="text-muted">Sin fallo reportado</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 4. DESCRIPCIÓN TÉCNICA Y LOGS DE ERROR -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white py-2 fw-bold text-dark border-bottom small text-uppercase">
                        <i class="bi bi-terminal text-primary me-1"></i> 4. Descripción Técnica y Logs de Error
                    </div>
                    <div class="card-body bg-light">
                        <pre class="mb-0 p-3 bg-white rounded border font-monospace text-dark small text-wrap" 
                             style="max-height: 250px; overflow-y: auto;" id="modalDescripcionLogs">-</pre>
                    </div>
                </div>

                <!-- 5. SOLUCIÓN APLICADA Y TIEMPOS -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white py-2 fw-bold text-dark border-bottom small text-uppercase d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-check2-circle text-success me-1"></i> 5. Solución Aplicada y Tiempos</span>
                        <span id="modalBadgeSolucion">-</span>
                    </div>
                    <div class="card-body">
                        <!-- Vista de Solución Existente -->
                        <div id="modalVistaSolucionExistente">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Acción Realizada:</small>
                                    <p class="mb-0 fw-semibold text-dark" id="modalAccionRealizada">Pendiente de atención técnica.</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Detalle Técnico (Scripts/Comandos):</small>
                                    <p class="mb-0 font-monospace small text-secondary" id="modalDetalleTecnico">Sin detalle registrado.</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Fecha y Hora de Cierre:</small>
                                    <span class="fw-semibold text-dark" id="modalFechaHoraCierre">No cerrada</span>
                                </div>
                            </div>
                        </div>

                        <!-- Formulario para Registrar/Editar Solución (si está abierta o en atención) -->
                        <div id="modalFormularioAtencion" class="d-none mt-3 pt-3 border-top">
                            <h6 class="fw-bold text-primary mb-2"><i class="bi bi-wrench me-1"></i> Registrar Resolución Técnica:</h6>
                            <input type="hidden" id="modalInputIdIncidencia">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Acción Realizada <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="modalInputAccionRealizada" rows="3" 
                                              placeholder="Procedimiento realizado para solucionar la falla..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Detalle Técnico (Comandos, cambios)</label>
                                    <textarea class="form-control font-monospace small" id="modalInputDetalleTecnico" rows="3" 
                                              placeholder="Consultas SQL, reinicios, cambios de parámetros..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Cambiar Estado:</label>
                                    <select class="form-select" id="modalSelectEstadoSolucion">
                                        <option value="en_atencion">En Atención</option>
                                        <option value="resuelta" selected>Resuelta (Solución Aplicada)</option>
                                        <option value="cerrada">Cerrada (Verificada)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Fecha/Hora de Cierre:</label>
                                    <input type="datetime-local" class="form-control" id="modalInputFechaCierre" 
                                           value="<?php echo date('Y-m-d\TH:i'); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold">Observaciones / Recomendaciones:</label>
                                    <textarea class="form-control" id="modalInputObservaciones" rows="2" 
                                              placeholder="Medidas preventivas sugeridas..."></textarea>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="button" class="btn btn-success px-4" id="btnGuardarSolucionModal">
                                    <i class="bi bi-check-circle me-1"></i> Guardar Solución y Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 6. SEGUIMIENTO Y RECOMENDACIONES -->
                <div class="card mb-3 border-0 shadow-sm" id="cardRecomendacionesVista">
                    <div class="card-header bg-white py-2 fw-bold text-dark border-bottom small text-uppercase">
                        <i class="bi bi-chat-left-text text-primary me-1"></i> 6. Seguimiento y Recomendaciones
                    </div>
                    <div class="card-body">
                        <p class="mb-0 text-muted" id="modalObservacionesTexto">Sin recomendaciones adicionales registradas.</p>
                    </div>
                </div>

                <!-- 7. FIRMAS DIGITALES -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-2 fw-bold text-dark border-bottom small text-uppercase">
                        <i class="bi bi-pen text-primary me-1"></i> 7. Firmas Digitales de Conformidad
                    </div>
                    <div class="card-body">
                        <div class="row g-4 text-center">
                            <!-- Firma Responsable -->
                            <div class="col-6">
                                <div class="border rounded p-2 bg-white">
                                    <div style="height: 110px; display: flex; align-items: center; justify-content: center;">
                                        <img id="modalImgFirmaResponsable" src="" alt="Firma Responsable" 
                                             class="img-fluid" style="max-height: 100px; display: none;">
                                        <span id="modalFirmaResponsableVacia" class="text-muted small">Sin firma registrada</span>
                                    </div>
                                    <div class="border-top pt-1 mt-1 small">
                                        <strong id="modalPieResponsable">Responsable del Reporte</strong>
                                        <div class="text-muted" style="font-size: 0.75rem;">Usuario Notificador</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Firma Sistemas -->
                            <div class="col-6">
                                <div class="border rounded p-2 bg-white">
                                    <div style="height: 110px; display: flex; align-items: center; justify-content: center;">
                                        <img id="modalImgFirmaSistemas" src="" alt="Firma Sistemas" 
                                             class="img-fluid" style="max-height: 100px; display: none;">
                                        <span id="modalFirmaSistemasVacia" class="text-muted small">Pendiente de certificación</span>
                                    </div>
                                    <div class="border-top pt-1 mt-1 small">
                                        <strong>Departamento de Sistemas</strong>
                                        <div class="text-muted" style="font-size: 0.75rem;">Ingeniero / Soporte Técnico</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Pie del Modal (Acciones) -->
            <div class="modal-footer bg-white py-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i> Cerrar
                </button>
                <button type="button" class="btn btn-outline-primary" id="btnToggleFormularioAtencion">
                    <i class="bi bi-tools me-1"></i> Atender / Modificar Solución
                </button>
                <button type="button" class="btn btn-danger px-4" id="btnImprimirFichaRI">
                    <i class="bi bi-printer me-1"></i> Imprimir Ficha Oficial
                </button>
            </div>

        </div>
    </div>
</div>
