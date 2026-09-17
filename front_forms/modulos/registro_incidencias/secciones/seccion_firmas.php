<?php
/**
 * Subcomponente: Sección 8 - Firmas Digitales (FOR_RIS_001, V-1)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/registro_incidencias/secciones/seccion_firmas.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex align-items-center mb-3">
        <span class="numero-seccion me-2"><i class="bi bi-pen"></i></span>
        <div>
            <h5 class="fw-bold mb-0 text-dark">Firmas Digitales Institucionales</h5>
            <small class="text-muted">Conformidad del reporte inicial y certificación del Departamento de Sistemas</small>
        </div>
    </div>

    <div class="row g-4">
        <!-- CUADRO 1: RESPONSABLE DEL REPORTE -->
        <div class="col-md-6">
            <div class="tarjeta-firma h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-secondary-subtle text-dark border">
                        <i class="bi bi-person-check me-1"></i> RESPONSABLE DEL REPORTE
                    </span>
                    <span class="badge bg-danger-subtle text-danger border" id="badgeFirmaResponsableRI">
                        Pendiente
                    </span>
                </div>
                <div class="contenedor-lienzo">
                    <canvas id="canvasFirmaResponsableRI" class="lienzo-firma"></canvas>
                    <div class="linea-guia-firma">Firme aquí (Responsable del Reporte)</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">Firma de quien notifica el fallo</small>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" id="btnLimpiarFirmaResponsableRI" style="font-size: 0.8rem;">
                        <i class="bi bi-eraser me-1"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>

        <!-- CUADRO 2: DEPARTAMENTO DE SISTEMAS -->
        <div class="col-md-6">
            <div class="tarjeta-firma h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-secondary-subtle text-dark border">
                        <i class="bi bi-shield-check me-1"></i> DEPARTAMENTO DE SISTEMAS
                    </span>
                    <span class="badge bg-secondary-subtle text-secondary border" id="badgeFirmaSistemasRI">
                        Opcional / Cierre
                    </span>
                </div>
                <div class="contenedor-lienzo">
                    <canvas id="canvasFirmaSistemasRI" class="lienzo-firma"></canvas>
                    <div class="linea-guia-firma">Firme aquí (Ingeniero / Dpto. Sistemas)</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">Certificación de atención y cierre</small>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" id="btnLimpiarFirmaSistemasRI" style="font-size: 0.8rem;">
                        <i class="bi bi-eraser me-1"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
