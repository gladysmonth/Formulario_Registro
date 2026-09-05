<?php
/**
 * Subcomponente: Firmas Digitales de Conformidad de Mantenimiento Preventivo
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/mantenimiento_preventivo/secciones/seccion_firmas.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex align-items-center mb-3">
        <span class="numero-seccion me-2"><i class="bi bi-pen"></i></span>
        <div>
            <h5 class="fw-bold mb-0 text-dark">Firmas Digitales de Conformidad</h5>
            <small class="text-muted">Certificación de entrega y recepción satisfactoria del mantenimiento preventivo</small>
        </div>
    </div>

    <div class="row g-4">
        <!-- CUADRO 1: RESPONSABLE DEL EQUIPO -->
        <div class="col-md-6">
            <div class="tarjeta-firma h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-secondary-subtle text-dark border">
                        <i class="bi bi-person-check me-1"></i> RESPONSABLE DEL EQUIPO
                    </span>
                    <span class="badge bg-danger-subtle text-danger border" id="badgeFirmaResponsable">
                        Pendiente
                    </span>
                </div>
                <div class="contenedor-lienzo">
                    <canvas id="canvasFirmaResponsable" class="lienzo-firma"></canvas>
                    <div class="linea-guia-firma">Firme aquí (Responsable del Equipo)</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">Firma del usuario asignado al equipo</small>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" id="btnLimpiarFirmaResponsable" style="font-size: 0.8rem;">
                        <i class="bi bi-eraser me-1"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>

        <!-- CUADRO 2: DPTO. DE SISTEMAS -->
        <div class="col-md-6">
            <div class="tarjeta-firma h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-secondary-subtle text-dark border">
                        <i class="bi bi-shield-check me-1"></i> DPTO. DE SISTEMAS
                    </span>
                    <span class="badge bg-danger-subtle text-danger border" id="badgeFirmaSistemasMP">
                        Pendiente
                    </span>
                </div>
                <div class="contenedor-lienzo">
                    <canvas id="canvasFirmaSistemasMP" class="lienzo-firma"></canvas>
                    <div class="linea-guia-firma">Firme aquí (Dpto. de Sistemas)</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">Firma del técnico certificador</small>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" id="btnLimpiarFirmaSistemasMP" style="font-size: 0.8rem;">
                        <i class="bi bi-eraser me-1"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
