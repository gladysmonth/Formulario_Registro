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
<?php
// Configuración Modular de los Formularios del Sistema (PHP 7.3 compatible)
$modulos_sistema = array(
    'soporte' => array(
        'numero' => '1',
        'nombre' => 'Soporte Técnico',
        'icono' => 'bi-tools',
        'color' => 'info',
        'url_formulario' => $ruta_base . 'modulos/soporte_tecnico/formulario_soporte.php',
        'url_gestion'    => $ruta_base . 'modulos/soporte_tecnico/gestion_tickets.php',
        'paginas'        => array('soporte_formulario', 'soporte_gestion'),
        'activo'         => true
    ),
    'mantenimiento' => array(
        'numero' => '2',
        'nombre' => 'Mantenimiento Preventivo',
        'icono' => 'bi-shield-check',
        'color' => 'success',
        'url_formulario' => $ruta_base . 'modulos/mantenimiento_preventivo/formulario_mantenimiento.php',
        'url_gestion'    => $ruta_base . 'modulos/mantenimiento_preventivo/gestion_mantenimientos.php',
        'paginas'        => array('mantenimiento_preventivo', 'mantenimiento_gestion'),
        'activo'         => true
    ),
    'incidencias' => array(
        'numero' => '3',
        'nombre' => 'Incidencias de Sistemas',
        'icono' => 'bi-shield-exclamation',
        'color' => 'danger',
        'url_formulario' => $ruta_base . 'modulos/registro_incidencias/formulario_incidencias.php',
        'url_gestion'    => $ruta_base . 'modulos/registro_incidencias/gestion_incidencias.php',
        'paginas'        => array('registro_incidencias', 'incidencias_gestion'),
        'activo'         => true
    ),
    'accesos' => array(
        'numero' => '4',
        'nombre' => 'Asignación de Accesos',
        'icono' => 'bi-shield-lock',
        'color' => 'primary',
        'url_formulario' => $ruta_base . 'modulos/asignacion_accesos/formulario_accesos.php',
        'url_gestion'    => $ruta_base . 'modulos/asignacion_accesos/gestion_accesos.php',
        'paginas'        => array('asignacion_accesos', 'accesos_gestion'),
        'activo'         => true
    ),
    'compras' => array(
        'numero' => '5',
        'nombre' => 'Compras y Suministros',
        'icono' => 'bi-cart-check',
        'color' => 'secondary',
        'url_formulario' => '#',
        'url_gestion'    => '#',
        'paginas'        => array(),
        'activo'         => false
    )
);

// Identificar módulo activo y tipo de vista
$clave_modulo_activo = null;
$modulo_activo_info  = null;
$es_vista_gestion    = false;

foreach ($modulos_sistema as $clave => $mod) {
    if (in_array($pagina_activa, $mod['paginas'])) {
        $clave_modulo_activo = $clave;
        $modulo_activo_info  = $mod;
        $es_vista_gestion    = (strpos($pagina_activa, 'gestion') !== false);
        break;
    }
}
?>
<!-- Barra Superior Limpia y Fija (Título Oficial + Botón Catálogo + Botón de 3 Rayas) -->
<nav class="navbar navbar-dark bg-dark shadow-sm sticky-top py-2 py-md-3">
    <div class="container-fluid px-3 px-lg-4 d-flex align-items-center justify-content-between">
        
        <!-- Izquierda: Botón Catálogo para volver -->
        <div class="d-flex align-items-center">
            <a href="<?php echo $ruta_base; ?>index.php" class="btn btn-outline-light btn-sm d-flex align-items-center gap-2 py-1 px-3 border-secondary" title="Volver al Catálogo de Formularios">
                <i class="bi bi-arrow-left"></i>
                <span class="fw-semibold">Catálogo</span>
            </a>
        </div>

        <!-- Centro: Título Oficial Único Solicitado -->
        <div class="text-center px-2">
            <span class="navbar-brand mb-0 h1 fw-bold text-white fs-5 fs-md-4">
                Formulario de Registro - <span class="badge bg-secondary fs-6 fw-normal align-middle">Cosmol R.L.</span>
            </span>
        </div>

        <!-- Derecha: Botón de 3 Rayas (☰) para desplegar todos los formularios -->
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-light btn-sm d-flex align-items-center gap-2 py-1 px-2 px-md-3 border-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuFormulariosLateral" aria-controls="menuFormulariosLateral" title="Ver otros formularios">
                <i class="bi bi-list fs-5"></i>
                <span class="d-none d-md-inline small fw-semibold">Formularios</span>
            </button>
        </div>

    </div>
</nav>

<!-- Panel Lateral Desplegable (Offcanvas) de 3 Rayas con los 5 Formularios -->
<div class="offcanvas offcanvas-end offcanvas-modulos shadow-lg" tabindex="-1" id="menuFormulariosLateral" aria-labelledby="tituloOffcanvasModulos">
    <div class="offcanvas-header border-bottom border-secondary border-opacity-25 py-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary p-2 rounded-3">
                <i class="bi bi-ui-checks-grid fs-5"></i>
            </span>
            <div>
                <h5 class="offcanvas-title fw-bold text-white mb-0" id="tituloOffcanvasModulos">
                    Formularios Cosmol
                </h5>
                <small class="text-secondary" style="font-size: 0.75rem;">Selecciona un formulario para navegar</small>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    
    <div class="offcanvas-body p-3 d-flex flex-column justify-content-between">
        <div class="d-flex flex-column gap-2">
            <div class="small text-uppercase text-secondary fw-semibold px-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                Módulos del Sistema:
            </div>

            <?php foreach ($modulos_sistema as $clave => $mod): ?>
                <?php $es_activo = ($clave === $clave_modulo_activo); ?>
                <div class="tarjeta-modulo-offcanvas <?php echo $es_activo ? 'modulo-activo' : ''; ?>">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi <?php echo $mod['icono']; ?> text-<?php echo $mod['color']; ?> fs-5"></i>
                            <span class="fw-semibold text-white small">
                                <?php echo htmlspecialchars($mod['numero'] . '. ' . $mod['nombre']); ?>
                            </span>
                        </div>
                        <?php if ($es_activo): ?>
                            <span class="badge bg-primary text-white" style="font-size: 0.65rem;">Actual</span>
                        <?php elseif (!$mod['activo']): ?>
                            <span class="badge bg-secondary text-light" style="font-size: 0.65rem;">Pronto</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($mod['activo']): ?>
                        <div class="d-flex gap-2">
                            <a href="<?php echo $mod['url_formulario']; ?>" class="btn btn-sm btn-outline-light flex-grow-1 py-1" style="font-size: 0.75rem;">
                                <i class="bi bi-pencil-square me-1 text-primary"></i> Llenar Formulario
                            </a>
                            <a href="<?php echo $mod['url_gestion']; ?>" class="btn btn-sm btn-outline-secondary text-light flex-grow-1 py-1" style="font-size: 0.75rem;">
                                <i class="bi bi-card-checklist me-1 text-info"></i> Bandeja
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="small text-secondary fst-italic py-1" style="font-size: 0.75rem;">
                            En fase de desarrollo
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pie del Panel Lateral: Enlace al Catálogo Central -->
        <div class="border-top border-secondary border-opacity-25 pt-3 mt-3">
            <a href="<?php echo $ruta_base; ?>index.php" class="btn btn-outline-primary w-100 btn-sm py-2 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-house-door"></i>
                <span class="fw-semibold">Ir al Catálogo de Formularios</span>
            </a>
            <div class="text-center text-secondary small mt-2" style="font-size: 0.72rem;">
                Cosmol R.L. &bull; Departamento de Sistemas
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
