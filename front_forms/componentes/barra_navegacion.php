<?php
/**
 * Componente: Barra de Navegación Superior
 * Compatible con PHP 7.3
 * Archivo: barra_navegacion.php
 */
$ruta_base = isset($nivel_ruta) ? $nivel_ruta : '';
$pagina_activa = isset($pagina_activa) ? $pagina_activa : 'inicio';
?>
<?php if ($pagina_activa === 'inicio'): ?>
<!-- Barra de Navegación Simplificada para el Portal Central (Catálogo de Formularios) -->
<nav class="navbar navbar-dark bg-dark shadow-sm sticky-top py-3">
    <div class="container-fluid px-lg-4 d-flex align-items-center">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold fs-4 mb-0 text-white text-decoration-none" href="<?php echo $ruta_base; ?>index.php">
            <span class="badge bg-primary p-2 rounded-3">
                <i class="bi bi-ui-checks-grid fs-4"></i>
            </span>
            <span class="fs-4">Catálogo de Formularios <span class="badge bg-secondary fs-6 fw-normal align-middle">Cosmol R.L.</span></span>
        </a>
    </div>
</nav>
<?php else: ?>
<!-- Barra de Navegación en Módulos Internos: Permite volver al catálogo y alternar entre módulos -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
    <div class="container-fluid px-lg-4">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-semibold" href="<?php echo $ruta_base; ?>index.php">
            <span class="badge bg-primary p-2 rounded-3">
                <i class="bi bi-ui-checks-grid fs-5"></i>
            </span>
            <span>Catálogo de Formularios <span class="badge bg-secondary fs-6 fw-normal">Cosmol</span></span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Alternar navegación">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $ruta_base; ?>index.php">
                        <i class="bi bi-house-door me-1"></i> Catálogo
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo (in_array($pagina_activa, array('soporte_formulario', 'soporte_gestion'))) ? 'active fw-semibold' : ''; ?>" href="#" id="dropSoporte" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-tools me-1 text-info"></i> 1. Soporte Técnico
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="dropSoporte">
                        <li>
                            <a class="dropdown-item <?php echo ($pagina_activa === 'soporte_formulario') ? 'active' : ''; ?>" href="<?php echo $ruta_base; ?>modulos/soporte_tecnico/formulario_soporte.php">
                                <i class="bi bi-plus-circle me-2 text-primary"></i> Llenar Formulario de Soporte
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo ($pagina_activa === 'soporte_gestion') ? 'active' : ''; ?>" href="<?php echo $ruta_base; ?>modulos/soporte_tecnico/gestion_tickets.php">
                                <i class="bi bi-kanban me-2 text-warning"></i> Panel de Gestión de Tickets
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo (in_array($pagina_activa, array('mantenimiento_preventivo', 'mantenimiento_gestion'))) ? 'active fw-semibold' : ''; ?>" href="#" id="dropMantenimiento" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-shield-check me-1 text-success"></i> 2. Mantenimiento Preventivo
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="dropMantenimiento">
                        <li>
                            <a class="dropdown-item <?php echo ($pagina_activa === 'mantenimiento_preventivo') ? 'active' : ''; ?>" href="<?php echo $ruta_base; ?>modulos/mantenimiento_preventivo/formulario_mantenimiento.php">
                                <i class="bi bi-plus-circle me-2 text-primary"></i> Registrar Mantenimiento
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo ($pagina_activa === 'mantenimiento_gestion') ? 'active' : ''; ?>" href="<?php echo $ruta_base; ?>modulos/mantenimiento_preventivo/gestion_mantenimientos.php">
                                <i class="bi bi-card-checklist me-2 text-success"></i> Bandeja de Mantenimientos
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo (in_array($pagina_activa, array('registro_incidencias', 'incidencias_gestion'))) ? 'active fw-semibold' : ''; ?>" href="#" id="dropIncidencias" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-shield-exclamation me-1 text-danger"></i> 3. Incidencias de Sistemas
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="dropIncidencias">
                        <li>
                            <a class="dropdown-item <?php echo ($pagina_activa === 'registro_incidencias') ? 'active' : ''; ?>" href="<?php echo $ruta_base; ?>modulos/registro_incidencias/formulario_incidencias.php">
                                <i class="bi bi-plus-circle me-2 text-danger"></i> Reportar Incidencia
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo ($pagina_activa === 'incidencias_gestion') ? 'active' : ''; ?>" href="<?php echo $ruta_base; ?>modulos/registro_incidencias/gestion_incidencias.php">
                                <i class="bi bi-card-checklist me-2 text-primary"></i> Bandeja de Incidencias
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <span class="nav-link text-muted disabled">
                        <i class="bi bi-lock me-1"></i> 4. Control de Activos <span class="badge bg-secondary text-light">Pronto</span>
                    </span>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3 text-light">
                <span class="badge rounded-pill bg-dark border border-secondary text-light px-3 py-2 small d-flex align-items-center gap-2">
                    <span class="indicador-pulso bg-success"></span>
                    <span id="texto-estado-api">Backend: PHP 7.3</span>
                </span>
            </div>
        </div>
    </div>
</nav>
<?php endif; ?>
