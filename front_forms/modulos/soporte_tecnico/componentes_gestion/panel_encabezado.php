<?php
/**
 * Componente: Encabezado del Panel de Gestión de Tickets
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/soporte_tecnico/componentes_gestion/panel_encabezado.php
 */
?>
<!-- Encabezado del Panel -->
<div class="tarjeta-formulario p-4 mb-4 border-start border-4 border-warning">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <span class="badge bg-warning-subtle text-warning-emphasis border px-3 py-2 rounded-pill fw-semibold mb-2">
                <i class="bi bi-kanban me-1"></i> Área de Soporte Técnico
            </span>
            <h1 class="h3 fw-bold text-dark mb-1">Bandeja de Tickets y Atención</h1>
            <p class="text-muted small mb-0">
                Administre, clasifique prioridades, asigne técnicos y registre soluciones aplicadas a las solicitudes recibidas.
            </p>
        </div>
        <div class="d-flex gap-2">
            <button id="btnRefrescar" class="btn btn-outline-secondary btn-sm px-3 py-2">
                <i class="bi bi-arrow-clockwise me-1"></i> Refrescar
            </button>
            <a href="formulario_soporte.php" class="btn btn-primary btn-sm px-3 py-2">
                <i class="bi bi-plus-circle me-1"></i> Nuevo Ticket
            </a>
        </div>
    </div>
</div>
