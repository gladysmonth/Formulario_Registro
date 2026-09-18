<?php
/**
 * Subcomponente: Sección 4 - Requerimientos de Accesos (Asignación de Accesos)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/asignacion_accesos/secciones/seccion_4_requerimientos.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex align-items-center mb-3">
        <span class="numero-seccion me-2">4</span>
        <div>
            <h5 class="fw-bold mb-0 text-dark">Requerimientos de Accesos</h5>
            <small class="text-muted">Detalle exhaustivo de perfiles, permisos específicos, módulos o restricciones solicitadas</small>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <label for="requerimientos_accesos" class="form-label small fw-semibold">
                Detalle de Perfiles y Módulos Requeridos <span class="text-danger">*</span>
            </label>
            <textarea class="form-control font-monospace" id="requerimientos_accesos" name="requerimientos_accesos" 
                      rows="7" placeholder="Describa ampliamente los accesos requeridos:
- Módulos a consultar o registrar (ej: Facturación, Cobranzas, Reportes Contables)
- Tipo de permisos (Solo lectura, modificación, anulación, supervisor)
- Sucursal, agencia o centro operativo donde operará
- Restricciones horarias o de terminal si aplican..." required></textarea>
            <div class="form-text small text-muted">
                Proporcione el detalle claro para que el Departamento de Sistemas configure los privilegios correspondientes en las bases de datos y servidores.
            </div>
        </div>
    </div>
</div>
