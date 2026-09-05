<?php
/**
 * Subcomponente: Sección 6 - Observaciones e Incidencias
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/mantenimiento_preventivo/secciones/seccion_6_observaciones.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex align-items-center mb-3">
        <span class="numero-seccion me-2">6</span>
        <div>
            <h5 class="fw-bold mb-0 text-dark">Observaciones / Incidencias</h5>
            <small class="text-muted">Anotaciones de estado previo, recomendaciones al usuario o hallazgos relevantes</small>
        </div>
    </div>

    <div>
        <label for="observaciones_incidencias" class="form-label small fw-semibold">
            Detalle de Observaciones y Recomendaciones
        </label>
        <textarea class="form-control" id="observaciones_incidencias" name="observaciones_incidencias" rows="4" 
                  placeholder="Ingrese cualquier observación técnica, advertencia o recomendación preventiva para el usuario..."></textarea>
    </div>
</div>
