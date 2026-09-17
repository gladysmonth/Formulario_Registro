<?php
/**
 * Subcomponente: Sección 7 - Seguimiento y Recomendaciones (FOR_RIS_001, V-1)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/registro_incidencias/secciones/seccion_7_recomendaciones.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex align-items-center mb-3">
        <span class="numero-seccion me-2">7</span>
        <div>
            <h5 class="fw-bold mb-0 text-dark">Seguimiento y Recomendaciones</h5>
            <small class="text-muted">Medidas preventivas, recomendaciones técnicas a los usuarios o monitoreo posterior</small>
        </div>
    </div>

    <div>
        <label for="observaciones_recomendaciones" class="form-label small fw-semibold">
            Observaciones / Recomendaciones Técnicas
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light text-secondary align-items-start pt-2">
                <i class="bi bi-chat-left-text"></i>
            </span>
            <textarea class="form-control bg-light" id="observaciones_recomendaciones" 
                      name="observaciones_recomendaciones" rows="3" 
                      placeholder="Indique sugerencias preventivas para evitar la recurrencia del fallo o tareas de seguimiento programadas..."></textarea>
        </div>
    </div>
</div>
