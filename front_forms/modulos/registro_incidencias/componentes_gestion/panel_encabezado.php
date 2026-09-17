<?php
/**
 * Subcomponente: Encabezado del Panel de Gestión de Incidencias (FOR_RIS_001, V-1)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/registro_incidencias/componentes_gestion/panel_encabezado.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4 border-start border-4 border-danger">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-semibold mb-2 d-inline-block">
                <i class="bi bi-shield-exclamation me-1"></i> Bandeja Centralizada de Incidencias
            </span>
            <h1 class="h3 fw-bold text-dark mb-1">Control y Seguimiento de Incidencias de Sistemas</h1>
            <p class="text-muted small mb-0">
                COSMOL R.L. &bull; Monitoreo de tickets críticos, asignación de solución técnica, registro de logs y certificación de cierre.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="formulario_incidencias.php" class="btn btn-danger px-3 py-2 fw-semibold shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Nueva Incidencia
            </a>
        </div>
    </div>
</div>
