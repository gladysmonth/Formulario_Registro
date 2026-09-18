<?php
/**
 * Subcomponente: Sección 5 - Departamento de Sistemas y Comentarios (Asignación de Accesos)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/asignacion_accesos/secciones/seccion_5_atencion_sistemas.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4 border-start border-4 border-info">
    <div class="d-flex align-items-center mb-3">
        <span class="numero-seccion me-2">5</span>
        <div>
            <h5 class="fw-bold mb-0 text-dark">Departamento de Sistemas (Atención y Comentarios)</h5>
            <small class="text-muted">Espacio técnico para asignación de responsable, fecha de atención y observaciones técnicas</small>
        </div>
    </div>

    <div class="row g-3">
        <!-- Atendido por -->
        <div class="col-md-5 col-sm-6">
            <label for="atendido_por" class="form-label small fw-semibold">
                Atendido por (Técnico Responsable)
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary"><i class="bi bi-person-gear"></i></span>
                <input type="text" class="form-control" id="atendido_por" name="atendido_por" 
                       placeholder="Nombre del técnico o ingeniero de Sistemas">
            </div>
            <div class="form-text small text-muted">Puede completarse en la creación o durante la atención en bandeja.</div>
        </div>

        <!-- Fecha y Hora de Atención -->
        <div class="col-md-4 col-sm-6">
            <label for="fecha_hora_atencion" class="form-label small fw-semibold">
                Fecha y Hora de Atención
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary"><i class="bi bi-clock-history"></i></span>
                <input type="datetime-local" class="form-control" id="fecha_hora_atencion" name="fecha_hora_atencion">
            </div>
        </div>

        <!-- Estado de la Solicitud -->
        <div class="col-md-3 col-sm-12">
            <label for="estado" class="form-label small fw-semibold">
                Estado Inicial
            </label>
            <select class="form-select" id="estado" name="estado">
                <option value="pendiente" selected>Pendiente</option>
                <option value="en_proceso">En Proceso</option>
                <option value="atendido">Atendido / Habilitado</option>
            </select>
        </div>

        <!-- Comentarios del Área Técnica -->
        <div class="col-12">
            <label for="comentarios_sistemas" class="form-label small fw-semibold">
                Comentarios / Observaciones Técnicas o Restricciones
            </label>
            <textarea class="form-control" id="comentarios_sistemas" name="comentarios_sistemas" 
                      rows="3" placeholder="Observaciones técnicas, políticas de contraseña aplicadas, vigencia temporal del acceso o restricciones de seguridad..."></textarea>
        </div>
    </div>
</div>
