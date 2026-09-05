<?php
/**
 * Subcomponente: Sección 4 - Mantenimiento Interno (Software / Configuración)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/mantenimiento_preventivo/secciones/seccion_4_mantenimiento_interno.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex align-items-center mb-3">
        <span class="numero-seccion me-2">4</span>
        <div>
            <h5 class="fw-bold mb-0 text-dark">Mantenimiento Interno (Software / Configuración)</h5>
            <small class="text-muted">Marque las labores de optimización lógica, seguridad y actualización efectuadas</small>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4 col-sm-6">
            <div class="form-check p-3 bg-light rounded-3 border h-100">
                <input class="form-check-input ms-0 me-2" type="checkbox" id="actualizacion_so" name="actualizacion_so">
                <label class="form-check-label fw-semibold small text-dark" for="actualizacion_so">
                    <i class="bi bi-arrow-repeat text-success me-1"></i> Actualización del sistema operativo
                </label>
                <div class="text-muted small mt-1 ps-4">Descarga e instalación de parches críticos de seguridad.</div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="form-check p-3 bg-light rounded-3 border h-100">
                <input class="form-check-input ms-0 me-2" type="checkbox" id="eliminacion_temporales" name="eliminacion_temporales">
                <label class="form-check-label fw-semibold small text-dark" for="eliminacion_temporales">
                    <i class="bi bi-trash3 text-success me-1"></i> Eliminación de temporales y caché
                </label>
                <div class="text-muted small mt-1 ps-4">Liberación de espacio en disco (%TEMP%, prefetch y papelera).</div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="form-check p-3 bg-light rounded-3 border h-100">
                <input class="form-check-input ms-0 me-2" type="checkbox" id="desfragmentacion_optimizacion" name="desfragmentacion_optimizacion">
                <label class="form-check-label fw-semibold small text-dark" for="desfragmentacion_optimizacion">
                    <i class="bi bi-hdd-stack text-success me-1"></i> Desfragmentación (HDD) / TRIM (SSD)
                </label>
                <div class="text-muted small mt-1 ps-4">Optimización de sectores y velocidad de lectura de unidades.</div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="form-check p-3 bg-light rounded-3 border h-100">
                <input class="form-check-input ms-0 me-2" type="checkbox" id="escaneo_antivirus" name="escaneo_antivirus">
                <label class="form-check-label fw-semibold small text-dark" for="escaneo_antivirus">
                    <i class="bi bi-shield-check text-success me-1"></i> Escaneo antivirus / anti-malware
                </label>
                <div class="text-muted small mt-1 ps-4">Análisis completo y actualización de firmas de seguridad.</div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="form-check p-3 bg-light rounded-3 border h-100">
                <input class="form-check-input ms-0 me-2" type="checkbox" id="verificacion_drivers" name="verificacion_drivers">
                <label class="form-check-label fw-semibold small text-dark" for="verificacion_drivers">
                    <i class="bi bi-cpu text-success me-1"></i> Verificación de drivers
                </label>
                <div class="text-muted small mt-1 ps-4">Controladores de video, red, chipset y periféricos.</div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="form-check p-3 bg-light rounded-3 border h-100">
                <input class="form-check-input ms-0 me-2" type="checkbox" id="copia_seguridad" name="copia_seguridad">
                <label class="form-check-label fw-semibold small text-dark" for="copia_seguridad">
                    <i class="bi bi-cloud-arrow-up text-success me-1"></i> Copia de seguridad de datos críticos
                </label>
                <div class="text-muted small mt-1 ps-4">Respaldo preventivo de archivos institucionales del usuario.</div>
            </div>
        </div>

        <div class="col-12">
            <label for="mantenimiento_interno_otros" class="form-label small fw-semibold text-secondary">
                <i class="bi bi-plus-circle me-1"></i> Otras tareas de software / configuración (Opcional):
            </label>
            <input type="text" class="form-control form-control-sm" id="mantenimiento_interno_otros" name="mantenimiento_interno_otros" 
                   placeholder="Especifique otras acciones (ej: depuración de programas de inicio, licenciamiento...)">
        </div>
    </div>
</div>
