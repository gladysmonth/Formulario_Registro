<?php
/**
 * Subcomponente: Sección 5 - Descripción Técnica y Logs de Error (FOR_RIS_001, V-1)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/registro_incidencias/secciones/seccion_5_descripcion_logs.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center">
            <span class="numero-seccion me-2">5</span>
            <div>
                <h5 class="fw-bold mb-0 text-dark">Descripción Técnica y Logs de Error</h5>
                <small class="text-muted">Detalle minucioso de la falla, trazas de excepción, consultas SQL o mensajes del sistema</small>
            </div>
        </div>
        <span class="badge bg-light text-secondary border font-monospace">Texto amplio</span>
    </div>

    <div>
        <label for="descripcion_tecnica_logs" class="form-label small fw-semibold">
            Descripción Técnica del Fallo / Registro de Logs <span class="text-danger">*</span>
        </label>
        <div class="input-group mb-2">
            <span class="input-group-text bg-light text-secondary align-items-start pt-2">
                <i class="bi bi-terminal"></i>
            </span>
            <textarea class="form-control font-monospace small bg-light" id="descripcion_tecnica_logs" 
                      name="descripcion_tecnica_logs" rows="6" 
                      placeholder="Pegue aquí el texto completo del error, código de excepción, stacktrace, captura de consola o descripción detallada del comportamiento anómalo..." required></textarea>
        </div>
        <div class="form-text small text-muted">
            <i class="bi bi-info-circle me-1"></i> Tip: Puede copiar y pegar directamente mensajes de error de Postgres, Apache, logs de Windows o trazas de ejecución de las aplicaciones.
        </div>
    </div>
</div>
