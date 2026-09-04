<?php
/**
 * Sección: Firmas Digitales de Conformidad
 * Cuadros de Firma: SOLICITANTE y DPTO. DE SISTEMAS
 * Formulario de Soporte Técnico
 * Compatible con PHP 7.3
 */
?>
<div class="col-12">
    <div class="tarjeta-formulario p-4">
        <div class="encabezado-seccion d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center">
                <span class="numero-seccion"><i class="bi bi-pen"></i></span>
                <div>
                    <h2 class="h5 fw-bold mb-0">Firmas Digitales de Conformidad</h2>
                    <small class="text-muted">Firme directamente en el recuadro blanco usando el mouse o pantalla táctil</small>
                </div>
            </div>
            <div>
                <span class="badge bg-primary-subtle text-primary border px-3 py-2 rounded-pill small">
                    <i class="bi bi-shield-lock me-1"></i> Firma Digital Registrada
                </span>
            </div>
        </div>

        <div class="row g-4 pt-2">
            <!-- CUADRO 1: FIRMA DEL SOLICITANTE -->
            <div class="col-md-6">
                <div class="tarjeta-firma p-3 rounded-3 border bg-light h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold small text-dark">
                                <i class="bi bi-person-fill text-primary me-1"></i> Firma del Solicitante <span class="text-danger">*</span>
                            </span>
                            <span id="badgeFirmaSolicitante" class="badge bg-danger-subtle text-danger border">Pendiente</span>
                        </div>
                        
                        <!-- Lienzo de Firma Solicitante -->
                        <div class="contenedor-lienzo position-relative bg-white border rounded-3 shadow-sm">
                            <canvas id="canvasFirmaSolicitante" class="lienzo-firma w-100" height="160"></canvas>
                            <div class="linea-guia-firma"></div>
                            <small class="texto-guia-firma text-muted">Firme aquí</small>
                        </div>
                        <input type="hidden" id="firma_solicitante" name="firma_solicitante">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                        <span class="fw-bold text-secondary text-uppercase tracking-wider small">SOLICITANTE</span>
                        <button type="button" id="btnLimpiarFirmaSolicitante" class="btn btn-outline-secondary btn-sm py-1 px-3">
                            <i class="bi bi-eraser me-1"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>

            <!-- CUADRO 2: FIRMA DEL DPTO. DE SISTEMAS -->
            <div class="col-md-6">
                <div class="tarjeta-firma p-3 rounded-3 border bg-light h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold small text-dark">
                                <i class="bi bi-laptop text-info me-1"></i> Firma Dpto. de Sistemas
                            </span>
                            <span id="badgeFirmaSistemas" class="badge bg-secondary-subtle text-secondary border">Opcional al registrar</span>
                        </div>

                        <!-- Lienzo de Firma Sistemas -->
                        <div class="contenedor-lienzo position-relative bg-white border rounded-3 shadow-sm">
                            <canvas id="canvasFirmaSistemas" class="lienzo-firma w-100" height="160"></canvas>
                            <div class="linea-guia-firma"></div>
                            <small class="texto-guia-firma text-muted">Firme aquí</small>
                        </div>
                        <input type="hidden" id="firma_sistemas" name="firma_sistemas">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                        <span class="fw-bold text-secondary text-uppercase tracking-wider small">DPTO. DE SISTEMAS</span>
                        <button type="button" id="btnLimpiarFirmaSistemas" class="btn btn-outline-secondary btn-sm py-1 px-3">
                            <i class="bi bi-eraser me-1"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
