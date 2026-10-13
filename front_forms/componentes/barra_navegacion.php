<?php
/**
 * Componente: Barra de Navegación Superior y Menú Lateral (Offcanvas)
 * FASE 4: Dashboard de Estadísticas y Centro de Comando
 * Compatible con PHP 7.3
 * Archivo: front_forms/componentes/barra_navegacion.php
 */
$ruta_base = isset($nivel_ruta) ? $nivel_ruta : '';
$pagina_activa = isset($pagina_activa) ? $pagina_activa : 'inicio';

if (!function_exists('obtener_usuario_sesion')) {
    require_once __DIR__ . '/control_sesion.php';
}

$usuario_actual_sesion = obtener_usuario_sesion();
$rol_actual = obtener_rol_actual();
$es_tecnico_o_admin = es_personal_tecnico();

// Datos del usuario para la interfaz
$nombre_usuario_mostrar = 'Usuario';
$depto_usuario_mostrar  = 'Institucional';
$cargo_usuario_mostrar  = '';

if ($usuario_actual_sesion) {
    $nombre_usuario_mostrar = !empty($usuario_actual_sesion['nombre_completo']) 
        ? $usuario_actual_sesion['nombre_completo'] 
        : $usuario_actual_sesion['usuario'];
    if (!empty($usuario_actual_sesion['departamento'])) {
        $depto_usuario_mostrar = $usuario_actual_sesion['departamento'];
    }
    if (!empty($usuario_actual_sesion['cargo'])) {
        $cargo_usuario_mostrar = $usuario_actual_sesion['cargo'];
    }
}

// Configuración visual según rol asignado por el Administrador
switch ($rol_actual) {
    case 'admin':
        $badge_rol_clase = 'bg-danger text-white';
        $badge_rol_texto = 'Administrador';
        $badge_rol_icono = 'bi-shield-shaded';
        break;
    case 'tecnico':
        $badge_rol_clase = 'bg-info text-dark';
        $badge_rol_texto = 'Técnico';
        $badge_rol_icono = 'bi-tools';
        break;
    default:
        $badge_rol_clase = 'bg-secondary text-white';
        $badge_rol_texto = 'Solicitante';
        $badge_rol_icono = 'bi-person';
        break;
}

// Configuración Modular de los Formularios Activos del Sistema
$modulos_sistema = array(
    'soporte' => array(
        'numero' => '1',
        'nombre' => 'Soporte Técnico',
        'icono' => 'bi-tools',
        'color' => 'info',
        'url_formulario' => $ruta_base . 'modulos/soporte_tecnico/formulario_soporte.php',
        'url_gestion'    => $ruta_base . 'modulos/soporte_tecnico/gestion_tickets.php',
        'paginas'        => array('soporte_formulario', 'soporte_gestion'),
        'activo'         => true,
        'solo_tecnico'   => false
    ),
    'mantenimiento' => array(
        'numero' => '2',
        'nombre' => 'Mantenimiento Preventivo',
        'icono' => 'bi-shield-check',
        'color' => 'success',
        'url_formulario' => $ruta_base . 'modulos/mantenimiento_preventivo/formulario_mantenimiento.php',
        'url_gestion'    => $ruta_base . 'modulos/mantenimiento_preventivo/gestion_mantenimientos.php',
        'paginas'        => array('mantenimiento_preventivo', 'mantenimiento_gestion'),
        'activo'         => true,
        'solo_tecnico'   => true
    ),
    'incidencias' => array(
        'numero' => '3',
        'nombre' => 'Incidencias de Sistemas',
        'icono' => 'bi-shield-exclamation',
        'color' => 'danger',
        'url_formulario' => $ruta_base . 'modulos/registro_incidencias/formulario_incidencias.php',
        'url_gestion'    => $ruta_base . 'modulos/registro_incidencias/gestion_incidencias.php',
        'paginas'        => array('registro_incidencias', 'incidencias_gestion'),
        'activo'         => true,
        'solo_tecnico'   => false
    ),
    'accesos' => array(
        'numero' => '4',
        'nombre' => 'Asignación de Accesos',
        'icono' => 'bi-shield-lock',
        'color' => 'primary',
        'url_formulario' => $ruta_base . 'modulos/asignacion_accesos/formulario_accesos.php',
        'url_gestion'    => $ruta_base . 'modulos/asignacion_accesos/gestion_accesos.php',
        'paginas'        => array('asignacion_accesos', 'accesos_gestion'),
        'activo'         => true,
        'solo_tecnico'   => false
    ),
    'diagnostico' => array(
        'numero' => 'T',
        'nombre' => 'Telemetría / Diagnóstico PC',
        'icono' => 'bi-cpu',
        'color' => 'warning',
        'url_formulario' => $ruta_base . 'modulos/diagnostico/monitoreo_equipos.php',
        'url_gestion'    => $ruta_base . 'modulos/diagnostico/monitoreo_equipos.php',
        'paginas'        => array('diagnostico_monitoreo', 'diagnostico_detalle'),
        'activo'         => true,
        'solo_tecnico'   => true
    )
);

// Identificar módulo activo
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

<?php if ($pagina_activa === 'inicio'): ?>
<!-- Barra de Navegación para el Portal Central (Catálogo de Formularios) -->
<nav class="navbar navbar-dark bg-dark shadow-sm sticky-top py-2 py-md-3">
    <div class="container-fluid px-lg-4 d-flex align-items-center justify-content-between">
        <!-- Izquierda: Botón Menú 3 Rayas (☰) y Marca Principal -->
        <div class="d-flex align-items-center gap-2 gap-md-3">
            <button class="btn btn-outline-light btn-sm d-flex align-items-center gap-2 py-1 px-2 px-md-3 border-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuFormulariosLateral" aria-controls="menuFormulariosLateral" title="Abrir Menú Lateral">
                <i class="bi bi-list fs-5"></i>
                <span class="d-none d-md-inline small fw-semibold">Menú</span>
            </button>
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold fs-4 mb-0 text-white text-decoration-none" href="<?php echo $ruta_base; ?>index.php">
                <span class="badge bg-primary p-2 rounded-3">
                    <i class="bi bi-ui-checks-grid fs-4"></i>
                </span>
                <span class="fs-4 d-none d-sm-inline">Catálogo de Formularios <span class="badge bg-secondary fs-6 fw-normal align-middle">Cosmol R.L.</span></span>
                <span class="fs-5 d-inline d-sm-none">Cosmol R.L.</span>
            </a>
        </div>

        <!-- Derecha: Botón Directo MÉTRICA y Dropdown de Usuario -->
        <div class="d-flex align-items-center gap-2">
            <?php if ($es_tecnico_o_admin): ?>
            <!-- Botón Directo a Ventana MÉTRICA -->
            <button class="btn btn-outline-info btn-sm d-flex align-items-center gap-2 py-1 px-2 px-md-3 border-info text-info fw-bold rounded-pill" type="button" data-bs-toggle="modal" data-bs-target="#modalDashboardMetricas" title="Abrir Ventana de Métricas y Estadísticas">
                <i class="bi bi-speedometer2"></i>
                <span class="d-none d-sm-inline">MÉTRICA</span>
            </button>
            <?php endif; ?>

            <!-- Dropdown de Usuario -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary text-light dropdown-toggle d-flex align-items-center gap-2 py-1 px-3 rounded-pill border-secondary" type="button" id="dropdownUsuarioInicio" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="avatar-usuario-nav bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.85rem;">
                        <i class="bi <?php echo $badge_rol_icono; ?>"></i>
                    </span>
                    <span class="d-none d-sm-inline fw-semibold small"><?php echo htmlspecialchars($nombre_usuario_mostrar); ?></span>
                    <span class="badge <?php echo $badge_rol_clase; ?> ms-1" style="font-size: 0.7rem;"><?php echo $badge_rol_texto; ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="dropdownUsuarioInicio" style="min-width: 240px;">
                    <li class="px-3 py-2 bg-light border-bottom">
                        <div class="fw-bold text-dark small"><?php echo htmlspecialchars($nombre_usuario_mostrar); ?></div>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            <?php echo htmlspecialchars($depto_usuario_mostrar); ?>
                            <?php echo $cargo_usuario_mostrar ? ' &bull; ' . htmlspecialchars($cargo_usuario_mostrar) : ''; ?>
                        </div>
                        <span class="badge <?php echo $badge_rol_clase; ?> mt-1" style="font-size: 0.68rem;"><?php echo $badge_rol_texto; ?></span>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 text-danger py-2" href="<?php echo $ruta_base; ?>logout.php">
                            <i class="bi bi-box-arrow-right"></i>
                            <span class="fw-semibold">Cerrar Sesión</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<?php else: ?>

<!-- Barra Superior para Módulos de Formularios -->
<nav class="navbar navbar-dark bg-dark shadow-sm sticky-top py-2 py-md-3">
    <div class="container-fluid px-3 px-lg-4 d-flex align-items-center justify-content-between">
        
        <!-- Izquierda: Botón Menú 3 Rayas (☰) y Botón Catálogo para volver -->
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-light btn-sm d-flex align-items-center gap-2 py-1 px-2 px-md-3 border-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuFormulariosLateral" aria-controls="menuFormulariosLateral" title="Abrir Menú Lateral">
                <i class="bi bi-list fs-5"></i>
                <span class="d-none d-md-inline small fw-semibold">Menú</span>
            </button>
            <a href="<?php echo $ruta_base; ?>index.php" class="btn btn-outline-light btn-sm d-flex align-items-center gap-2 py-1 px-2 px-md-3 border-secondary" title="Volver al Catálogo de Formularios">
                <i class="bi bi-arrow-left"></i>
                <span class="fw-semibold d-none d-sm-inline">Catálogo</span>
            </a>
        </div>

        <!-- Centro: Título Oficial Único Solicitado -->
        <div class="text-center px-2">
            <span class="navbar-brand mb-0 h1 fw-bold text-white fs-5 fs-md-4">
                Formulario de Registro - <span class="badge bg-secondary fs-6 fw-normal align-middle">Cosmol R.L.</span>
            </span>
        </div>

        <!-- Derecha: Botón Directo MÉTRICA y Perfil de Usuario -->
        <div class="d-flex align-items-center gap-2">
            <?php if ($es_tecnico_o_admin): ?>
            <!-- Botón Directo a Ventana MÉTRICA -->
            <button class="btn btn-outline-info btn-sm d-flex align-items-center gap-2 py-1 px-2 px-md-3 border-info text-info fw-bold rounded-pill" type="button" data-bs-toggle="modal" data-bs-target="#modalDashboardMetricas" title="Abrir Ventana de Métricas y Estadísticas">
                <i class="bi bi-speedometer2"></i>
                <span class="d-none d-sm-inline">MÉTRICA</span>
            </button>
            <?php endif; ?>

            <!-- Dropdown Compacto de Usuario -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary text-light dropdown-toggle d-flex align-items-center gap-2 py-1 px-2 px-md-3 rounded-pill border-secondary" type="button" id="dropdownUsuarioNav" data-bs-toggle="dropdown" aria-expanded="false" title="<?php echo htmlspecialchars($nombre_usuario_mostrar); ?>">
                    <span class="avatar-usuario-nav bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px; font-size: 0.8rem;">
                        <i class="bi <?php echo $badge_rol_icono; ?>"></i>
                    </span>
                    <span class="d-none d-md-inline fw-semibold small"><?php echo htmlspecialchars($nombre_usuario_mostrar); ?></span>
                    <span class="badge <?php echo $badge_rol_clase; ?> ms-1 d-none d-sm-inline" style="font-size: 0.68rem;"><?php echo $badge_rol_texto; ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="dropdownUsuarioNav" style="min-width: 240px;">
                    <li class="px-3 py-2 bg-light border-bottom">
                        <div class="fw-bold text-dark small"><?php echo htmlspecialchars($nombre_usuario_mostrar); ?></div>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            <?php echo htmlspecialchars($depto_usuario_mostrar); ?>
                            <?php echo $cargo_usuario_mostrar ? ' &bull; ' . htmlspecialchars($cargo_usuario_mostrar) : ''; ?>
                        </div>
                        <span class="badge <?php echo $badge_rol_clase; ?> mt-1" style="font-size: 0.68rem;"><?php echo $badge_rol_texto; ?></span>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 text-danger py-2" href="<?php echo $ruta_base; ?>logout.php">
                            <i class="bi bi-box-arrow-right"></i>
                            <span class="fw-semibold">Cerrar Sesión</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</nav>
<?php endif; ?>

<!-- ====================================================================
     PANEL LATERAL OFFCANVAS IZQUIERDO (CENTRO DE NAVEGACIÓN Y ACCESOS)
     ==================================================================== -->
<div class="offcanvas offcanvas-start offcanvas-modulos shadow-lg" tabindex="-1" id="menuFormulariosLateral" aria-labelledby="tituloOffcanvasModulos">
    <!-- Cabecera del Panel Lateral Izquierdo -->
    <div class="offcanvas-header border-bottom border-secondary border-opacity-25 py-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary p-2 rounded-3">
                <i class="bi bi-ui-checks-grid fs-5"></i>
            </span>
            <div>
                <h5 class="offcanvas-title fw-bold text-white mb-0" id="tituloOffcanvasModulos">
                    Menú del Sistema
                </h5>
                <small class="text-secondary" style="font-size: 0.75rem;">Cosmol R.L. &bull; Navegación</small>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    
    <div class="offcanvas-body p-3 d-flex flex-column justify-content-between">
        <div class="d-flex flex-column gap-3">
            
            <!-- Tarjeta de Perfil del Usuario Activo -->
            <div class="tarjeta-usuario-offcanvas">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar-usuario-nav bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 34px; height: 34px; font-size: 0.95rem;">
                            <i class="bi <?php echo $badge_rol_icono; ?>"></i>
                        </span>
                        <div>
                            <div class="fw-bold text-white small lh-1"><?php echo htmlspecialchars($nombre_usuario_mostrar); ?></div>
                            <small class="text-secondary" style="font-size: 0.72rem;"><?php echo htmlspecialchars($depto_usuario_mostrar); ?></small>
                        </div>
                    </div>
                    <span class="badge <?php echo $badge_rol_clase; ?>" style="font-size: 0.68rem;"><?php echo $badge_rol_texto; ?></span>
                </div>
                <div class="pt-2 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                    <span class="text-secondary small" style="font-size: 0.72rem;">
                        <i class="bi bi-circle-fill text-success me-1" style="font-size: 0.45rem;"></i> Sesión Activa
                    </span>
                    <a href="<?php echo $ruta_base; ?>logout.php" class="btn btn-outline-danger btn-sm py-0 px-2" style="font-size: 0.75rem;">
                        <i class="bi bi-box-arrow-right me-1"></i> Salir
                    </a>
                </div>
            </div>

            <?php if ($es_tecnico_o_admin): ?>
            <!-- ========================================================
                 BOTÓN DESTACADO PARA ABRIR LA VENTANA MODAL "MÉTRICA"
                 ======================================================== -->
            <div class="tarjeta-boton-metrica-menu">
                <div class="small text-uppercase text-info fw-bold mb-2 px-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                    <i class="bi bi-speedometer2 me-1"></i> Centro de Estadísticas
                </div>
                <button type="button" class="btn btn-primary w-100 py-2 px-3 d-flex align-items-center justify-content-between rounded-3 shadow-sm fw-bold border-0 btn-abrir-metrica" data-bs-toggle="modal" data-bs-target="#modalDashboardMetricas" data-bs-dismiss="offcanvas" title="Abrir ventana de Dashboard y Métricas">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-speedometer2 fs-5 text-warning"></i>
                        <span class="fs-6 letter-spacing-1">MÉTRICA</span>
                    </span>
                    <span class="badge bg-white bg-opacity-25 text-white fw-normal" style="font-size: 0.72rem;">
                        Abrir Ventana <i class="bi bi-box-arrow-up-right ms-1"></i>
                    </span>
                </button>
            </div>
            <?php endif; ?>

            <!-- ========================================================
                 ACCESOS A FORMULARIOS INSTITUCIONALES (Compactos de 1 clic)
                 ======================================================== -->
            <div>
                <div class="small text-uppercase text-secondary fw-bold mb-2 px-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                    <i class="bi bi-ui-checks-grid me-1 text-primary"></i> Formularios Institucionales
                </div>
                
                <div class="lista-formularios-compacta">
                    <?php foreach ($modulos_sistema as $clave => $mod): ?>
                        <?php 
                        // Si el módulo es técnico y el usuario es solicitante, ocultarlo
                        if (!empty($mod['solo_tecnico']) && !$es_tecnico_o_admin) {
                            continue;
                        }
                        $es_activo = ($clave === $clave_modulo_activo);
                        ?>
                        <div class="item-formulario-compacto <?php echo $es_activo ? 'activo' : ''; ?>">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-dark border border-secondary text-<?php echo $mod['color']; ?> p-1 rounded" style="font-size: 0.75rem;">
                                    <i class="bi <?php echo $mod['icono']; ?>"></i>
                                </span>
                                <div>
                                    <div class="fw-semibold text-white" style="font-size: 0.78rem;">
                                        <?php echo htmlspecialchars($mod['numero'] . '. ' . $mod['nombre']); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <!-- Botón 1 clic para Llenar Formulario -->
                                <a href="<?php echo $mod['url_formulario']; ?>" class="btn btn-outline-light btn-sm py-0 px-2" style="font-size: 0.7rem;" title="Llenar Formulario">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <!-- Botón 1 clic para Bandeja (solo personal técnico) -->
                                <?php if ($es_tecnico_o_admin && $clave !== 'diagnostico'): ?>
                                    <a href="<?php echo $mod['url_gestion']; ?>" class="btn btn-outline-secondary text-light btn-sm py-0 px-2" style="font-size: 0.7rem;" title="Bandeja de Gestión">
                                        <i class="bi bi-card-checklist text-info"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <!-- Pie del Panel Lateral: Enlace al Catálogo Detallado -->
        <div class="border-top border-secondary border-opacity-25 pt-3 mt-3">
            <a href="<?php echo $ruta_base; ?>index.php" class="btn btn-outline-primary w-100 btn-sm py-2 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-grid-3x3-gap-fill"></i>
                <span class="fw-semibold">Ver Catálogo Detallado de Formularios</span>
            </a>
            <div class="text-center text-secondary small mt-2" style="font-size: 0.72rem;">
                Cosmol R.L. &bull; Departamento de Sistemas
            </div>
        </div>
    </div>
</div>

<?php if ($es_tecnico_o_admin): ?>
<!-- ====================================================================
     VENTANA MODAL: DASHBOARD DE MÉTRICAS Y FILTRACIÓN RÁPIDA DE TICKETS
     ==================================================================== -->
<div class="modal fade" id="modalDashboardMetricas" tabindex="-1" aria-labelledby="tituloModalMetricas" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg modal-dashboard-metricas-estilo">
            
            <!-- Cabecera de la Ventana Modal de Métricas -->
            <div class="modal-header border-bottom border-secondary border-opacity-25 py-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary p-2 rounded-3">
                        <i class="bi bi-speedometer2 fs-5"></i>
                    </span>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="tituloModalMetricas">
                            MÉTRICA &bull; Dashboard de Gestión Técnica
                        </h5>
                        <small class="text-secondary" style="font-size: 0.75rem;">Monitoreo en Tiempo Real y Filtrado Rápido &bull; Cosmol R.L.</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary text-light btn-sm py-1 px-2" onclick="clienteApi.actualizarKpisMenuOffcanvas()" title="Actualizar datos en vivo">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        <span class="d-none d-sm-inline small">Refrescar</span>
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
            </div>

            <!-- Cuerpo de la Ventana Modal -->
            <div class="modal-body p-4">
                
                <!-- 1. Cuadrícula de KPIs -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                        <span class="small text-uppercase text-info fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            <i class="bi bi-graph-up me-1"></i> Indicadores Clave en Vivo (KPIs)
                        </span>
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50" style="font-size: 0.7rem;">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.45rem;"></i> Conexión Activa
                        </span>
                    </div>

                    <div class="row g-3">
                        <!-- 1. Tickets Pendientes (Badge ámbar) -->
                        <div class="col-6 col-md-3">
                            <a href="<?php echo $ruta_base; ?>modulos/soporte_tecnico/gestion_tickets.php?estado=pendiente" class="tarjeta-kpi-modal kpi-pendiente" title="Ver Tickets Abiertos/Pendientes">
                                <div class="d-flex justify-content-between align-items-start">
                                    <span class="kpi-valor" id="kpiTicketsPendientes">-</span>
                                    <i class="bi bi-hourglass-split text-warning fs-5"></i>
                                </div>
                                <div class="kpi-etiqueta">Abiertos / Pendientes</div>
                            </a>
                        </div>

                        <!-- 2. Tickets En Proceso (Badge celeste) -->
                        <div class="col-6 col-md-3">
                            <a href="<?php echo $ruta_base; ?>modulos/soporte_tecnico/gestion_tickets.php?estado=en_proceso" class="tarjeta-kpi-modal kpi-en-proceso" title="Ver Tickets En Proceso">
                                <div class="d-flex justify-content-between align-items-start">
                                    <span class="kpi-valor" id="kpiTicketsEnProceso">-</span>
                                    <i class="bi bi-gear-wide-connected text-info fs-5"></i>
                                </div>
                                <div class="kpi-etiqueta">En Proceso</div>
                            </a>
                        </div>

                        <!-- 3. Tickets Resueltos (Badge verde) -->
                        <div class="col-6 col-md-3">
                            <a href="<?php echo $ruta_base; ?>modulos/soporte_tecnico/gestion_tickets.php?estado=resuelto" class="tarjeta-kpi-modal kpi-resuelto" title="Ver Tickets Resueltos">
                                <div class="d-flex justify-content-between align-items-start">
                                    <span class="kpi-valor" id="kpiTicketsResueltos">-</span>
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                </div>
                                <div class="kpi-etiqueta">Resueltos</div>
                            </a>
                        </div>

                        <!-- 4. Fuerza Mayor (Badge púrpura) -->
                        <div class="col-6 col-md-3">
                            <a href="<?php echo $ruta_base; ?>modulos/soporte_tecnico/gestion_tickets.php?estado=fuerza_mayor" class="tarjeta-kpi-modal kpi-fuerza-mayor" title="Ver Casos de Fuerza Mayor">
                                <div class="d-flex justify-content-between align-items-start">
                                    <span class="kpi-valor" id="kpiTicketsFuerzaMayor">-</span>
                                    <i class="bi bi-shield-slash fs-5" style="color: #c084fc;"></i>
                                </div>
                                <div class="kpi-etiqueta">Fuerza Mayor</div>
                            </a>
                        </div>

                        <!-- 5. PCs en Alerta de Hardware (Auditoría SMART/CPU) -->
                        <div class="col-12">
                            <a href="<?php echo $ruta_base; ?>modulos/diagnostico/monitoreo_equipos.php" class="tarjeta-kpi-modal kpi-alerta-pc" title="Auditoría de PCs con Alertas de Hardware">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="p-2 bg-danger bg-opacity-25 rounded-3 border border-danger border-opacity-50">
                                            <i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-baseline gap-2">
                                                <span class="kpi-valor" id="kpiPcsAlerta">-</span>
                                                <span class="badge bg-danger text-white" style="font-size: 0.7rem;">Alerta SMART / CPU / Defender</span>
                                            </div>
                                            <div class="kpi-etiqueta text-light">Equipos con alertas técnicas detectadas por Agente PowerShell</div>
                                        </div>
                                    </div>
                                    <span class="btn btn-outline-danger btn-sm d-none d-sm-inline-flex align-items-center gap-1">
                                        <span>Auditar PCs</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 2. Bandeja de Tickets con Filtración Rápida -->
                <div class="mb-2">
                    <div class="small text-uppercase text-secondary fw-bold mb-2 px-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <i class="bi bi-funnel-fill me-1 text-primary"></i> Bandeja y Filtración Rápida de Tickets
                    </div>
                    
                    <div class="d-flex flex-column gap-2">
                        <!-- Enlace Principal: Ver Tickets ABIERTOS -->
                        <a href="<?php echo $ruta_base; ?>modulos/soporte_tecnico/gestion_tickets.php?estado=pendiente" class="btn-filtro-abiertos-principal d-flex align-items-center justify-content-between py-2 px-3">
                            <span class="d-flex align-items-center gap-2 fs-6">
                                <i class="bi bi-inbox-fill fs-5"></i>
                                <span>Ver Tickets ABIERTOS (Prioritarios)</span>
                            </span>
                            <span class="badge bg-white text-primary fw-bold px-2 py-1">Ir a Bandeja &rarr;</span>
                        </a>

                        <!-- Fila de filtros secundarios -->
                        <div class="row g-2 mt-1">
                            <div class="col-4">
                                <a href="<?php echo $ruta_base; ?>modulos/soporte_tecnico/gestion_tickets.php?estado=en_proceso" class="btn-filtro-secundario py-2 w-100" title="Tickets en proceso de atención">
                                    <i class="bi bi-clock-history text-info fs-6"></i>
                                    <span class="fw-semibold">En Proceso</span>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="<?php echo $ruta_base; ?>modulos/soporte_tecnico/gestion_tickets.php?estado=resuelto" class="btn-filtro-secundario py-2 w-100" title="Tickets resueltos y cerrados">
                                    <i class="bi bi-check2-circle text-success fs-6"></i>
                                    <span class="fw-semibold">Resueltos</span>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="<?php echo $ruta_base; ?>modulos/soporte_tecnico/gestion_tickets.php?estado=fuerza_mayor" class="btn-filtro-secundario py-2 w-100" title="Casos de fuerza mayor o contingencia">
                                    <i class="bi bi-shield-slash text-warning fs-6"></i>
                                    <span class="fw-semibold">Contingencia</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Pie de la Ventana Modal -->
            <div class="modal-footer border-top border-secondary border-opacity-25 py-2 px-4 d-flex justify-content-between align-items-center">
                <span class="text-secondary small" style="font-size: 0.75rem;">
                    <i class="bi bi-shield-check text-success me-1"></i> Panel exclusivo de Administración y Soporte Técnico
                </span>
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div>
<?php endif; ?>
