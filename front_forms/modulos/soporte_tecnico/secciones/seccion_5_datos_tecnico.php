<?php
/**
 * Sección 5: Datos del Técnico
 * Formulario de Soporte Técnico
 * Compatible con PHP 7.3
 */
?>
<div class="col-12">
    <div class="tarjeta-formulario p-4 bloque-tecnico">
        <div class="encabezado-seccion d-flex align-items-center">
            <span class="numero-seccion numero-seccion-tecnico">5</span>
            <div>
                <h2 class="h5 fw-bold mb-0">Datos del Técnico</h2>
                <small class="text-muted">Para completar por el área de soporte técnico</small>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label for="fecha_hora_atencion" class="form-label small fw-semibold">Fecha y Hora de Atención</label>
                <input type="datetime-local" class="form-control" id="fecha_hora_atencion" name="fecha_hora_atencion">
            </div>

            <div class="col-md-4">
                <label for="tecnico_asignado" class="form-label small fw-semibold">Técnico Asignado</label>
                <input type="text" class="form-control" id="tecnico_asignado" name="tecnico_asignado" placeholder="Ej: Ing. Marco Morales">
            </div>

            <div class="col-md-4">
                <label for="tipo_resolucion" class="form-label small fw-semibold">Tipo de Resolución</label>
                <select class="form-select" id="tipo_resolucion" name="tipo_resolucion">
                    <option value="">Seleccione tipo...</option>
                    <option value="Mantenimiento Correctivo">Mantenimiento Correctivo</option>
                    <option value="Mantenimiento Preventivo">Mantenimiento Preventivo</option>
                    <option value="Configuración / Soporte de Software">Configuración / Soporte de Software</option>
                    <option value="Reemplazo de Hardware / Periférico">Reemplazo de Hardware / Periférico</option>
                    <option value="Capacitación / Orientación a Usuario">Capacitación / Orientación a Usuario</option>
                    <option value="Derivado a Garantía o Proveedor Externo">Derivado a Garantía o Proveedor Externo</option>
                </select>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="diagnostico" class="form-label small fw-semibold">Diagnóstico Técnico</label>
                <textarea class="form-control" id="diagnostico" name="diagnostico" rows="3" placeholder="Diagnóstico de la causa raíz de la falla..."></textarea>
            </div>

            <div class="col-md-6">
                <label for="solucion_aplicada" class="form-label small fw-semibold">Solución Aplicada</label>
                <textarea class="form-control" id="solucion_aplicada" name="solucion_aplicada" rows="3" placeholder="Detalle las acciones realizadas para resolver el incidente..."></textarea>
            </div>
        </div>
    </div>
</div>
