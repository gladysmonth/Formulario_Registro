<?php
/**
 * Componente: Modal de Atención y Cierre Técnico (Secciones 4, 5, 6 y Firma Técnica)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/soporte_tecnico/componentes_gestion/modal_atencion.php
 */
?>
<!-- MODAL DE ATENCIÓN Y CIERRE TÉCNICO -->
<div class="modal fade" id="modalAtencionTicket" tabindex="-1" aria-labelledby="modalTitulo" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <!-- FORMULARIO PRINCIPAL DEL MODAL (Es el modal-content para respetar el scroll de Bootstrap 5) -->
        <form id="formAtencionTicket" class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="modalTitulo">
                    <i class="bi bi-tools text-warning me-2"></i> Atención de Ticket: <span id="modalTicketCodigo" class="text-warning"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            
            <input type="hidden" id="atencion_ticket_id">

            <div class="modal-body p-4">
                <!-- Resumen del requerimiento original del solicitante -->
                <div class="bg-light p-3 rounded-3 border mb-4">
                    <div class="row g-2 small">
                        <div class="col-md-6">
                            <strong>Solicitante:</strong> <span id="modalTicketSolicitante" class="text-dark fw-semibold"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Departamento / Área:</strong> <span id="modalTicketArea" class="text-dark fw-semibold"></span>
                        </div>
                        <div class="col-12 mt-2">
                            <strong>Equipo Afectado:</strong>
                            <div id="modalTicketEquipoDetalles" class="mt-1 p-2 bg-white rounded border text-secondary">
                                <!-- Dinámico vía JS con los 7 campos de inventario -->
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <strong>Descripción del Problema:</strong>
                            <p id="modalTicketDescripcion" class="mb-0 text-secondary bg-white p-2 rounded border mt-1"></p>
                        </div>
                        <div class="col-12 mt-2">
                            <strong>Firma del Solicitante:</strong>
                            <div class="mt-1">
                                <img id="modalFirmaSolicitante" src="" alt="Firma del Solicitante" class="img-fluid border rounded bg-white p-2 shadow-sm" style="max-height: 90px; display: none;">
                                <span id="modalSinFirmaSolicitante" class="text-muted fst-italic small d-none"><i class="bi bi-info-circle me-1"></i>No registrada</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 4: PRIORIDAD Y ESTADO -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="numero-seccion numero-seccion-tecnico">4</span>
                        <h6 class="fw-bold mb-0">Prioridad y Estado Operativo</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="atencion_prioridad" class="form-label small fw-semibold">Prioridad Definida por Soporte</label>
                            <select class="form-select" id="atencion_prioridad" required>
                                <option value="urgente">Urgente (afecta operaciones críticas)</option>
                                <option value="alta">Alta (afecta productividad significativa)</option>
                                <option value="media">Media (molestia operativa pero no detiene trabajo)</option>
                                <option value="baja">Baja (requerimiento menor)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="atencion_estado" class="form-label small fw-semibold">Estado Actual</label>
                            <select class="form-select" id="atencion_estado" required>
                                <option value="pendiente">Pendiente</option>
                                <option value="en_proceso">En Proceso</option>
                                <option value="resuelto">Resuelto</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 5: DATOS DEL TÉCNICO -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="numero-seccion numero-seccion-tecnico">5</span>
                        <h6 class="fw-bold mb-0">Datos del Técnico y Resolución</h6>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="atencion_fecha_hora" class="form-label small fw-semibold">Fecha y Hora de Atención</label>
                            <input type="datetime-local" class="form-control" id="atencion_fecha_hora">
                        </div>
                        <div class="col-md-6">
                            <label for="atencion_tecnico" class="form-label small fw-semibold">Técnico Asignado</label>
                            <input type="text" class="form-control" id="atencion_tecnico" placeholder="Nombre del técnico responsable">
                        </div>
                        <div class="col-12">
                            <label for="atencion_tipo_resolucion" class="form-label small fw-semibold">Tipo de Resolución</label>
                            <select class="form-select" id="atencion_tipo_resolucion">
                                <option value="">Seleccione tipo...</option>
                                <option value="Mantenimiento Correctivo">Mantenimiento Correctivo</option>
                                <option value="Mantenimiento Preventivo">Mantenimiento Preventivo</option>
                                <option value="Configuración / Soporte de Software">Configuración / Soporte de Software</option>
                                <option value="Reemplazo de Hardware / Periférico">Reemplazo de Hardware / Periférico</option>
                                <option value="Capacitación / Orientación a Usuario">Capacitación / Orientación a Usuario</option>
                                <option value="Derivado a Garantía o Proveedor Externo">Derivado a Garantía o Proveedor Externo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="atencion_diagnostico" class="form-label small fw-semibold">Diagnóstico</label>
                            <textarea class="form-control" id="atencion_diagnostico" rows="3" placeholder="Causa del problema identificado..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="atencion_solucion" class="form-label small fw-semibold">Solución Aplicada</label>
                            <textarea class="form-control" id="atencion_solucion" rows="3" placeholder="Acciones realizadas para solucionar..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 6: OBSERVACIONES / RECOMENDACIONES -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="numero-seccion numero-seccion-tecnico">6</span>
                        <h6 class="fw-bold mb-0">Observaciones y Recomendaciones</h6>
                    </div>

                    <div>
                        <textarea class="form-control" id="atencion_observaciones" rows="3" placeholder="Observaciones adicionales, sugerencias preventivas..."></textarea>
                    </div>
                </div>

                <!-- SECCIÓN FIRMA: DPTO. DE SISTEMAS -->
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex align-items-center mb-2">
                        <span class="numero-seccion numero-seccion-tecnico"><i class="bi bi-pen"></i></span>
                        <h6 class="fw-bold mb-0">Firma de Conformidad Técnica (Dpto. de Sistemas)</h6>
                    </div>
                    <p class="text-muted small mb-2">
                        Firma digital del personal del Departamento de Sistemas para validar la atención o cierre del ticket.
                    </p>
                    
                    <!-- Si ya existía firma previa -->
                    <div id="contenedorFirmaSistemasPrevia" class="mb-3 p-2 bg-light rounded border d-none">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="small fw-semibold text-secondary"><i class="bi bi-check-circle-fill text-success me-1"></i>Firma previamente registrada:</span>
                            <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2" id="btnCambiarFirmaSistemas" style="font-size: 0.75rem;">
                                <i class="bi bi-arrow-repeat me-1"></i>Cambiar Firma
                            </button>
                        </div>
                        <div class="text-center">
                            <img id="imgFirmaSistemasPrevia" src="" alt="Firma Sistemas Registrada" class="img-fluid border rounded bg-white p-1" style="max-height: 80px;">
                        </div>
                    </div>

                    <!-- Lienzo de dibujo de firma -->
                    <div id="contenedorLienzoFirmaSistemas" class="tarjeta-firma">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-secondary-subtle text-dark border">
                                <i class="bi bi-shield-check me-1"></i>DPTO. DE SISTEMAS
                            </span>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" id="btnLimpiarFirmaAtencion" style="font-size: 0.75rem;">
                                <i class="bi bi-eraser me-1"></i>Limpiar
                            </button>
                        </div>
                        <div class="contenedor-lienzo">
                            <canvas id="canvasFirmaAtencion" class="lienzo-firma" style="height: 130px;"></canvas>
                            <div class="linea-guia-firma">Firme aquí (Dpto. de Sistemas)</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" id="btnGuardarAtencion" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
