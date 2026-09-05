<?php
/**
 * Subcomponente: Sección 1 - Datos Generales de Mantenimiento Preventivo
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/mantenimiento_preventivo/secciones/seccion_1_datos_generales.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex align-items-center mb-3">
        <span class="numero-seccion me-2">1</span>
        <div>
            <h5 class="fw-bold mb-0 text-dark">Datos Generales del Mantenimiento</h5>
            <small class="text-muted">Identificación del técnico responsable y lugar del servicio</small>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-5">
            <label for="tecnico_responsable" class="form-label small fw-semibold">
                Técnico Responsable <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary"><i class="bi bi-person-badge"></i></span>
                <input type="text" class="form-control" id="tecnico_responsable" name="tecnico_responsable" 
                       placeholder="Nombre del técnico ejecutor" required>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <label for="fecha_mantenimiento" class="form-label small fw-semibold">
                Fecha del Servicio <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary"><i class="bi bi-calendar3"></i></span>
                <input type="date" class="form-control" id="fecha_mantenimiento" name="fecha_mantenimiento" 
                       value="<?php echo date('Y-m-d'); ?>" required>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <label for="ubicacion_equipo" class="form-label small fw-semibold">
                Ubicación del Equipo <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary"><i class="bi bi-geo-alt"></i></span>
                <input type="text" class="form-control" id="ubicacion_equipo" name="ubicacion_equipo" 
                       placeholder="Ej: Contabilidad - Piso 2" required>
            </div>
        </div>
    </div>
</div>
