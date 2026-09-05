<?php
/**
 * Subcomponente: Sección 3 - Mantenimiento Externo (Limpieza Física)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/mantenimiento_preventivo/secciones/seccion_3_mantenimiento_externo.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex align-items-center mb-3">
        <span class="numero-seccion me-2">3</span>
        <div>
            <h5 class="fw-bold mb-0 text-dark">Mantenimiento Externo (Limpieza Física)</h5>
            <small class="text-muted">Marque las actividades de limpieza física y verificación de hardware ejecutadas</small>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="form-check p-3 bg-light rounded-3 border h-100">
                <input class="form-check-input ms-0 me-2" type="checkbox" id="limpieza_carcasa_componentes" name="limpieza_carcasa_componentes">
                <label class="form-check-label fw-semibold small text-dark" for="limpieza_carcasa_componentes">
                    <i class="bi bi-fan text-primary me-1"></i> Limpieza de carcasa, ventiladores y componentes
                </label>
                <div class="text-muted small mt-1 ps-4">Soplado, remoción de polvo y verificación de flujo de aire.</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-check p-3 bg-light rounded-3 border h-100">
                <input class="form-check-input ms-0 me-2" type="checkbox" id="limpieza_pantalla_teclado" name="limpieza_pantalla_teclado">
                <label class="form-check-label fw-semibold small text-dark" for="limpieza_pantalla_teclado">
                    <i class="bi bi-display text-primary me-1"></i> Limpieza de pantalla, teclado y touchpad
                </label>
                <div class="text-muted small mt-1 ps-4">Desinfección superficial con alcohol isopropílico / limpiador de pantallas.</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-check p-3 bg-light rounded-3 border h-100">
                <input class="form-check-input ms-0 me-2" type="checkbox" id="verificacion_conectores" name="verificacion_conectores">
                <label class="form-check-label fw-semibold small text-dark" for="verificacion_conectores">
                    <i class="bi bi-usb-symbol text-primary me-1"></i> Verificación de conectores (USB, HDMI, etc.)
                </label>
                <div class="text-muted small mt-1 ps-4">Inspección física de puertos de datos, video y alimentación.</div>
            </div>
        </div>

        <div class="col-12">
            <label for="limpieza_otros" class="form-label small fw-semibold text-secondary">
                <i class="bi bi-plus-circle me-1"></i> Otros aspectos de limpieza física (Opcional):
            </label>
            <input type="text" class="form-control form-control-sm" id="limpieza_otros" name="limpieza_otros" 
                   placeholder="Detalle cualquier otra labor física realizada (ej: reemplazo de pasta térmica, sujeción de cables...)">
        </div>
    </div>
</div>
