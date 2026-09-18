<?php
/**
 * Subcomponente: Sección 6 - Firmas y Validaciones Institucionales (Asignación de Accesos)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/asignacion_accesos/secciones/seccion_firmas.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex align-items-center mb-3">
        <span class="numero-seccion me-2"><i class="bi bi-pen"></i></span>
        <div>
            <h5 class="fw-bold mb-0 text-dark">Firmas y Validaciones Institucionales</h5>
            <small class="text-muted">Conformidad del solicitante, autorización de jefatura y certificación del Departamento de Sistemas</small>
        </div>
    </div>

    <div class="row g-4">
        <!-- 1. SOLICITANTE -->
        <div class="col-lg-4 col-md-6">
            <div class="tarjeta-firma h-100 d-flex flex-column justify-content-between">
                <div class="contenedor-lienzo flex-grow-1">
                    <canvas id="canvasFirmaSolicitanteAcc" class="lienzo-firma"></canvas>
                    <div class="linea-guia-firma">Firme aquí (Solicitante)</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">Conformidad del interesado</small>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" id="btnLimpiarFirmaSolicitanteAcc" style="font-size: 0.8rem;">
                        <i class="bi bi-eraser me-1"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>

        <!-- 2. AUTORIZA (JEFATURA) -->
        <div class="col-lg-4 col-md-6">
            <div class="tarjeta-firma h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                            <i class="bi bi-shield-check me-1"></i> AUTORIZA (JEFATURA)
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary border" id="badgeFirmaAutorizaAcc">
                            Firmado
                        </span>
                    </div>
                    <div class="mb-2">
                        <input type="text" class="form-control form-control-sm" id="nombre_autoriza" name="nombre_autoriza" 
                               placeholder="Nombre y cargo de quien autoriza">
                    </div>
                </div>
                <div class="contenedor-lienzo flex-grow-1">
                    <canvas id="canvasFirmaAutorizaAcc" class="lienzo-firma"></canvas>
                    <div class="linea-guia-firma">Firme aquí</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">Aprobación jerárquica</small>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" id="btnLimpiarFirmaAutorizaAcc" style="font-size: 0.8rem;">
                        <i class="bi bi-eraser me-1"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>

        <!-- 3. DPTO. DE SISTEMAS -->
        <div class="col-lg-4 col-md-12">
            <div class="tarjeta-firma h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                            <i class="bi bi-cpu me-1"></i> DPTO. DE SISTEMAS
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary border" id="badgeFirmaSistemasAcc">
                            Firmado
                        </span>
                    </div>
                </div>
                <div class="contenedor-lienzo flex-grow-1">
                    <canvas id="canvasFirmaSistemasAcc" class="lienzo-firma"></canvas>
                    <div class="linea-guia-firma">Firme aquí (Dpto. Sistemas)</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">Validación y habilitación técnica</small>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" id="btnLimpiarFirmaSistemasAcc" style="font-size: 0.8rem;">
                        <i class="bi bi-eraser me-1"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
