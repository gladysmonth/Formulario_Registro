<?php
/**
 * Subcomponente: Sección 1 - Datos Generales (Asignación de Accesos)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/asignacion_accesos/secciones/seccion_1_datos_generales.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex align-items-center mb-3">
        <span class="numero-seccion me-2">1</span>
        <div>
            <h5 class="fw-bold mb-0 text-dark">Datos Generales</h5>
            <small class="text-muted">Información institucional del solicitante del acceso</small>
        </div>
    </div>

    <!-- Fecha automática del sistema -->
    <input type="hidden" id="fecha_solicitud" name="fecha_solicitud" value="<?php echo date('Y-m-d\TH:i'); ?>">

    <div class="row g-3">
        <!-- 1. Nombre Completo -->
        <div class="col-12">
            <label for="nombre_solicitante" class="form-label small fw-semibold">
                Nombre Completo <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary"><i class="bi bi-person-fill"></i></span>
                <input type="text" class="form-control" id="nombre_solicitante" name="nombre_solicitante" 
                       placeholder="Nombre y apellidos del solicitante" required>
            </div>
        </div>

        <!-- 2. Cargo -->
        <div class="col-md-6 col-sm-12">
            <label for="cargo_solicitante" class="form-label small fw-semibold">
                Cargo <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary"><i class="bi bi-briefcase-fill"></i></span>
                <input type="text" class="form-control" id="cargo_solicitante" name="cargo_solicitante" 
                       placeholder="Cargo institucional o puesto laboral" required>
            </div>
        </div>

        <!-- 3. Área / Departamento -->
        <div class="col-md-6 col-sm-12">
            <label for="area_departamento" class="form-label small fw-semibold">
                Área / Departamento <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary"><i class="bi bi-building"></i></span>
                <input type="text" class="form-control" id="area_departamento" name="area_departamento" 
                       placeholder="Ej: Cobranzas, Contabilidad, Comercial, RRHH" required>
            </div>
        </div>
    </div>
</div>
