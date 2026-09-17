<?php
/**
 * Subcomponente: Sección 1 - Datos Generales de la Incidencia (FOR_RIS_001, V-1)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/registro_incidencias/secciones/seccion_1_datos_generales.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex align-items-center mb-3">
        <span class="numero-seccion me-2">1</span>
        <div>
            <h5 class="fw-bold mb-0 text-dark">Datos Generales de la Incidencia</h5>
            <small class="text-muted">Información básica del evento y persona que emite el reporte</small>
        </div>
    </div>

    <div class="row g-3">
        <!-- Nro. de Incidencia -->
        <div class="col-md-4 col-sm-6">
            <label for="nro_incidencia" class="form-label small fw-semibold">
                Nro. de Incidencia <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-white text-secondary"><i class="bi bi-hash"></i></span>
                <input type="text" class="form-control font-monospace fw-bold text-primary bg-white" 
                       id="nro_incidencia" name="nro_incidencia" 
                       placeholder="Ej: INC-<?php echo date('Y'); ?>-0001 o Nro. manual" required>
            </div>
            <div class="form-text small text-muted">Ingrese el número o identificador de la incidencia.</div>
        </div>

        <!-- Fecha y hora del Reporte -->
        <div class="col-md-4 col-sm-6">
            <label for="fecha_hora_reporte" class="form-label small fw-semibold">
                Fecha y Hora del Reporte <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary"><i class="bi bi-calendar-event"></i></span>
                <input type="datetime-local" class="form-control" id="fecha_hora_reporte" name="fecha_hora_reporte" 
                       value="<?php echo date('Y-m-d\TH:i'); ?>" required>
            </div>
        </div>

        <!-- Responsable del Reporte -->
        <div class="col-md-4">
            <label for="responsable_reporte" class="form-label small fw-semibold">
                Responsable del Reporte <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary"><i class="bi bi-person-fill"></i></span>
                <input type="text" class="form-control" id="responsable_reporte" name="responsable_reporte" 
                       placeholder="Nombre de quien reporta la falla" required>
            </div>
        </div>
    </div>
</div>
