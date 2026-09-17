<?php
/**
 * Subcomponente: Sección 6 - Solución Aplicada y Tiempos (FOR_RIS_001, V-1)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/registro_incidencias/secciones/seccion_6_solucion_tiempos.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
        <div class="d-flex align-items-center">
            <span class="numero-seccion me-2">6</span>
            <div>
                <h5 class="fw-bold mb-0 text-dark">Solución Aplicada y Tiempos</h5>
                <small class="text-muted">Acciones correctivas implementadas, procedimientos técnicos y cierre del incidente</small>
            </div>
        </div>
        <div class="form-check form-switch m-0">
            <input class="form-check-input" type="checkbox" role="switch" id="switch_solucion_inmediata">
            <label class="form-check-label small fw-semibold cursor-pointer" for="switch_solucion_inmediata">
                Registrar Solución / Cerrar ahora
            </label>
        </div>
    </div>

    <!-- Contenedor de campos de solución -->
    <div id="contenedor_campos_solucion" class="p-3 bg-light rounded-3 border">
        <div class="row g-3">
            <!-- Acción Realizada -->
            <div class="col-md-6">
                <label for="accion_realizada" class="form-label small fw-semibold">
                    Acción Realizada <span class="text-muted">(Descripción funcional)</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-secondary align-items-start pt-2">
                        <i class="bi bi-check2-circle"></i>
                    </span>
                    <textarea class="form-control" id="accion_realizada" name="accion_realizada" rows="3" 
                              placeholder="Indique qué se ejecutó para solventar la falla (ej: reanudación de colas, liberación de tablas, reinicio de servicios)..."></textarea>
                </div>
            </div>

            <!-- Detalle Técnico -->
            <div class="col-md-6">
                <label for="detalle_tecnico" class="form-label small fw-semibold">
                    Detalle Técnico <span class="text-muted">(Comandos, parches, scripts)</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-secondary align-items-start pt-2">
                        <i class="bi bi-code-slash"></i>
                    </span>
                    <textarea class="form-control font-monospace small" id="detalle_tecnico" name="detalle_tecnico" rows="3" 
                              placeholder="Scripts SQL ejecutados, puertos abiertos, variables modificadas, etc..."></textarea>
                </div>
            </div>

            <!-- Fecha/Hora de Cierre y Estado -->
            <div class="col-md-6 col-sm-6">
                <label for="fecha_hora_cierre" class="form-label small fw-semibold">
                    Fecha y Hora de Cierre
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-secondary"><i class="bi bi-clock-history"></i></span>
                    <input type="datetime-local" class="form-control" id="fecha_hora_cierre" name="fecha_hora_cierre">
                </div>
            </div>

            <div class="col-md-6 col-sm-6">
                <label for="estado" class="form-label small fw-semibold">
                    Estado Operativo de la Incidencia
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-secondary"><i class="bi bi-flag"></i></span>
                    <select class="form-select fw-semibold" id="estado" name="estado">
                        <option value="abierta" selected>Abierta (En espera de atención)</option>
                        <option value="en_atencion">En Atención (Técnicos investigando)</option>
                        <option value="resuelta">Resuelta (Solución aplicada)</option>
                        <option value="cerrada">Cerrada (Verificada y certificada)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
