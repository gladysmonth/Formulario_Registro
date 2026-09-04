<?php
/**
 * Sección 4: Prioridad
 * Formulario de Soporte Técnico
 * Compatible con PHP 7.3
 */
?>
<div class="col-12">
    <div class="tarjeta-formulario p-4">
        <div class="encabezado-seccion d-flex align-items-center">
            <span class="numero-seccion numero-seccion-tecnico">4</span>
            <div>
                <h2 class="h5 fw-bold mb-0">Prioridad</h2>
                <small class="text-muted">A definir por el área de soporte según impacto operativo</small>
            </div>
        </div>

        <div class="row g-3">
            <!-- Urgente -->
            <div class="col-md-3 col-sm-6">
                <input type="radio" class="btn-check" name="prioridad" id="prio_urgente" value="urgente" autocomplete="off">
                <label class="btn btn-outline-danger w-100 text-start p-3 h-100 rounded-3" for="prio_urgente">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Urgente</div>
                    <div class="small opacity-75">Afecta operaciones críticas del negocio.</div>
                </label>
            </div>

            <!-- Alta -->
            <div class="col-md-3 col-sm-6">
                <input type="radio" class="btn-check" name="prioridad" id="prio_alta" value="alta" autocomplete="off">
                <label class="btn btn-outline-warning text-dark w-100 text-start p-3 h-100 rounded-3" for="prio_alta">
                    <div class="fw-bold mb-1"><i class="bi bi-arrow-up-circle-fill me-1"></i> Alta</div>
                    <div class="small opacity-75">Afecta productividad de forma significativa.</div>
                </label>
            </div>

            <!-- Media -->
            <div class="col-md-3 col-sm-6">
                <input type="radio" class="btn-check" name="prioridad" id="prio_media" value="media" checked autocomplete="off">
                <label class="btn btn-outline-primary w-100 text-start p-3 h-100 rounded-3" for="prio_media">
                    <div class="fw-bold mb-1"><i class="bi bi-dash-circle-fill me-1"></i> Media</div>
                    <div class="small opacity-75">Molestia operativa pero no detiene el trabajo.</div>
                </label>
            </div>

            <!-- Baja -->
            <div class="col-md-3 col-sm-6">
                <input type="radio" class="btn-check" name="prioridad" id="prio_baja" value="baja" autocomplete="off">
                <label class="btn btn-outline-success w-100 text-start p-3 h-100 rounded-3" for="prio_baja">
                    <div class="fw-bold mb-1"><i class="bi bi-arrow-down-circle-fill me-1"></i> Baja</div>
                    <div class="small opacity-75">Requerimiento rutinario o de baja criticidad.</div>
                </label>
            </div>
        </div>
    </div>
</div>
