<?php
/**
 * Componente: Encabezado del Panel de Mantenimientos Preventivos
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/mantenimiento_preventivo/componentes_gestion/panel_encabezado.php
 */
?>
<!-- Encabezado del Panel -->
<div class="tarjeta-formulario p-4 mb-4 border-start border-4 border-primary">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold mb-2">
                <i class="bi bi-shield-check me-1"></i> Control de Mantenimiento Preventivo
            </span>
            <h1 class="h3 fw-bold text-dark mb-1">Bandeja de Mantenimientos (PC / Laptop)</h1>
            <p class="text-muted small mb-0">
                Consulte y supervise el historial de revisiones técnicas físicas y lógicas realizadas al parque de computadoras.
            </p>
        </div>
        <div class="d-flex gap-2">
            <button id="btnRefrescarMP" class="btn btn-outline-secondary btn-sm px-3 py-2">
                <i class="bi bi-arrow-clockwise me-1"></i> Refrescar
            </button>
            <a href="formulario_mantenimiento.php" class="btn btn-primary btn-sm px-3 py-2">
                <i class="bi bi-plus-circle me-1"></i> Nuevo Mantenimiento
            </a>
        </div>
    </div>
</div>
