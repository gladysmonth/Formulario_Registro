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
    <div class="container-fluid px-3 px-lg-4 d-flex align-items-center justify-content-between">
        <!-- Izquierda: Botón Menú 3 Rayas (☰) -->
        <div class="d-flex align-items-center" style="min-width: 90px;">
            <button class="btn btn-outline-light btn-sm d-flex align-items-center gap-2 py-1 px-2 px-md-3 border-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuFormulariosLateral" aria-controls="menuFormulariosLateral" title="Abrir Menú Lateral">
                <i class="bi bi-list fs-5"></i>
                <span class="d-none d-md-inline small fw-semibold">Menú</span>
            </button>
        </div>

        <!-- Centro: CATALOGO DETALLADO DE FORMULARIO -->
        <div class="text-center flex-grow-1 px-2">
            <span class="navbar-brand mb-0 h1 fw-bold text-white fs-5 fs-md-4 text-uppercase tracking-wider">
                CATALOGO DETALLADO DE FORMULARIO
            </span>
        </div>

        <!-- Derecha: Botón Salir para todas las vistas -->
        <div class="d-flex justify-content-end align-items-center" style="min-width: 90px;">
            <a href="<?php echo $ruta_base; ?>logout.php" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2 py-1 px-2 px-md-3 fw-semibold border-secondary" title="Cerrar sesión institucional">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-sm-inline">Salir</span>
            </a>
        </div>
    </div>
</nav>

<?php else: ?>

<!-- Barra Superior para Módulos de Formularios y Pantallas Secundarias -->
<nav class="navbar navbar-dark bg-dark shadow-sm sticky-top py-2 py-md-3">
    <div class="container-fluid px-3 px-lg-4 d-flex align-items-center justify-content-between">
        
        <!-- Izquierda: Botón Menú 3 Rayas (☰) y Botón Catálogo para volver -->
        <div class="d-flex align-items-center gap-2" style="min-width: 140px;">
            <button class="btn btn-outline-light btn-sm d-flex align-items-center gap-2 py-1 px-2 px-md-3 border-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuFormulariosLateral" aria-controls="menuFormulariosLateral" title="Abrir Menú Lateral">
                <i class="bi bi-list fs-5"></i>
                <span class="d-none d-md-inline small fw-semibold">Menú</span>
            </button>
            <a href="<?php echo $ruta_base; ?>index.php" class="btn btn-outline-light btn-sm d-flex align-items-center gap-2 py-1 px-2 px-md-3 border-secondary" title="Volver al Catálogo de Formularios">
                <i class="bi bi-arrow-left"></i>
                <span class="fw-semibold d-none d-sm-inline">Catálogo</span>
            </a>
        </div>

        <!-- Centro: Título Oficial de la Vista -->
        <div class="text-center flex-grow-1 px-2 text-truncate">
            <span class="navbar-brand mb-0 h1 fw-bold text-white fs-5 fs-md-4 text-truncate">
                <?php if ($pagina_activa === 'metricas'): ?>
                    Panel de Métrica y Gestión - <span class="badge bg-secondary fs-6 fw-normal align-middle">Cosmol R.L.</span>
                <?php else: ?>
                    Formulario de Registro - <span class="badge bg-secondary fs-6 fw-normal align-middle">Cosmol R.L.</span>
                <?php endif; ?>
            </span>
        </div>

        <!-- Derecha: Botón Salir para todas las vistas -->
        <div class="d-flex justify-content-end align-items-center" style="min-width: 140px;">
            <a href="<?php echo $ruta_base; ?>logout.php" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2 py-1 px-2 px-md-3 fw-semibold border-secondary" title="Cerrar sesión institucional">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-sm-inline">Salir</span>
            </a>
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

            <?php if ($es_tecnico_o_admin): ?>
            <!-- ========================================================
                 BOTÓN DESTACADO PARA IR A LA PANTALLA "MÉTRICA"
                 ======================================================== -->
            <div class="tarjeta-boton-metrica-menu">
                <div class="small text-uppercase text-info fw-bold mb-2 px-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                    <i class="bi bi-speedometer2 me-1"></i> Centro de Estadísticas
                </div>
                <a href="<?php echo $ruta_base; ?>metricas.php" class="btn btn-primary w-100 py-2 px-3 d-flex align-items-center justify-content-between rounded-3 shadow-sm fw-bold border-0 btn-abrir-metrica <?php echo ($pagina_activa === 'metricas') ? 'active' : ''; ?>" title="Ir a la pantalla de Métricas">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-speedometer2 fs-5 text-warning"></i>
                        <span class="fs-6 letter-spacing-1">MÉTRICA</span>
                    </span>
                    <span class="badge bg-white bg-opacity-25 text-white fw-normal" style="font-size: 0.72rem;">
                        Ver Pantalla <i class="bi bi-arrow-right-short ms-1"></i>
                    </span>
                </a>
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
                            <!-- Enlace directo en el nombre e icono para entrar a rellenar el formulario -->
                            <a href="<?php echo $mod['url_formulario']; ?>" class="d-flex align-items-center gap-2 text-decoration-none flex-grow-1 text-truncate" title="Rellenar <?php echo htmlspecialchars($mod['nombre']); ?>">
                                <span class="badge bg-dark border border-secondary text-<?php echo $mod['color']; ?> p-1 rounded" style="font-size: 0.75rem;">
                                    <i class="bi <?php echo $mod['icono']; ?>"></i>
                                </span>
                                <span class="fw-semibold text-white text-truncate link-nombre-formulario" style="font-size: 0.82rem;">
                                    <?php echo htmlspecialchars($mod['numero'] . '. ' . $mod['nombre']); ?>
                                </span>
                            </a>
                            <!-- Acceso secundario a bandeja técnica (solo admin/técnicos) -->
                            <?php if ($es_tecnico_o_admin && $clave !== 'diagnostico'): ?>
                                <a href="<?php echo $mod['url_gestion']; ?>" class="btn btn-outline-secondary text-light btn-sm py-0 px-2 ms-2" style="font-size: 0.7rem;" title="Bandeja de Gestión">
                                    <i class="bi bi-card-checklist text-info"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <!-- Pie del Panel Lateral: Perfil del Usuario Activo (Ubicado abajo) y Enlace al Catálogo -->
        <div class="border-top border-secondary border-opacity-25 pt-3 mt-3">
            
            <!-- Tarjeta de Perfil del Usuario Activo (Reubicada abajo y sin botón salir) -->
            <div class="tarjeta-usuario-offcanvas p-2 rounded-3 bg-secondary bg-opacity-10 border border-secondary border-opacity-25 mb-3">
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
                    <?php if (!empty($cargo_usuario_mostrar)): ?>
                    <span class="text-secondary small" style="font-size: 0.7rem;">
                        <?php echo htmlspecialchars($cargo_usuario_mostrar); ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>

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

