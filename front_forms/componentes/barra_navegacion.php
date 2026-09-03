<?php
/**
 * Componente: Barra de Navegación Superior
 * Compatible con PHP 7.3
 * Archivo: barra_navegacion.php
 */
$ruta_base = isset($nivel_ruta) ? $nivel_ruta : '';
$pagina_activa = isset($pagina_activa) ? $pagina_activa : 'inicio';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
    <div class="container-fluid px-lg-4">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-semibold" href="<?php echo $ruta_base; ?>index.php">
            <span class="badge bg-primary p-2 rounded-3">
                <i class="bi bi-ui-checks-grid fs-5"></i>
            </span>
            <span>Portal de Registros <span class="badge bg-secondary fs-6 fw-normal">Cosmol</span></span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Alternar navegación">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($pagina_activa === 'inicio') ? 'active fw-semibold' : ''; ?>" href="<?php echo $ruta_base; ?>index.php">
                        <i class="bi bi-house-door me-1"></i> Catálogo de Formularios
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
                <li class="nav-item">
                    <span class="nav-link text-muted disabled">
                        <i class="bi bi-lock me-1"></i> 2. Permisos y Vacaciones <span class="badge bg-secondary text-light">Pronto</span>
                    </span>
                </li>
                <li class="nav-item">
                    <span class="nav-link text-muted disabled">
                        <i class="bi bi-lock me-1"></i> 3. Activos e Inventario <span class="badge bg-secondary text-light">Pronto</span>
                    </span>
                </li>
                <li class="nav-item">
                    <span class="nav-link text-muted disabled">
                        <i class="bi bi-lock me-1"></i> 4. Compras y Suministros <span class="badge bg-secondary text-light">Pronto</span>
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
