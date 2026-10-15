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
        'numero_personal' => '1',
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
        'numero_personal' => '',
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
        'numero_personal' => '2',
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
        'numero_personal' => '3',
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
        'numero_personal' => '',
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
        <!-- Espacio izquierdo equilibrado para centrar el título -->
        <div style="min-width: 90px;" class="d-none d-sm-block"></div>

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
        
        <!-- Espacio izquierdo equilibrado para centrar el título -->
        <div style="min-width: 90px;" class="d-none d-sm-block"></div>

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
        <div class="d-flex justify-content-end align-items-center" style="min-width: 90px;">
            <a href="<?php echo $ruta_base; ?>logout.php" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2 py-1 px-2 px-md-3 fw-semibold border-secondary" title="Cerrar sesión institucional">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-sm-inline">Salir</span>
            </a>
        </div>

    </div>
</nav>
<?php endif; ?>

<?php
$inicial_usuario = !empty($nombre_usuario_mostrar) ? strtoupper(substr(trim($nombre_usuario_mostrar), 0, 1)) : 'U';
?>
<!-- ====================================================================
     BARRA LATERAL COSMOL PERSISTENTE (CON DIBUJITOS Y COLAPSABLE)
     ==================================================================== -->
<aside class="barra-lateral-cosmol" id="barraLateralCosmol" aria-label="Menú Principal">
    <!-- Parte Superior: Header, Botón Dashboard/Métrica y Formularios -->
    <div class="d-flex flex-column">
        <!-- 1. Cabecera del Menú: MENÚ y Botón < -->
        <div class="sidebar-header-cosmol" id="sidebarHeaderToggle" style="cursor: pointer;" title="Alternar menú">
            <span class="sidebar-label-menu" id="sidebarLabelMenu">MENÚ</span>
            <button type="button" class="btn-toggle-chevron-sidebar" id="btnToggleChevronSidebar" aria-label="Alternar menú">
                <i class="bi bi-chevron-right" id="iconoChevronSidebar"></i>
            </button>
        </div>

        <!-- 2. Botón Unificado "MÉTRICA / DASHBOARD" (Un solo cuadrado sin contenedores anidados) -->
        <div class="seccion-boton-dashboard">
            <?php if ($es_tecnico_o_admin): ?>
            <a href="<?php echo $ruta_base; ?>metricas.php" class="btn-dashboard-sidebar <?php echo ($pagina_activa === 'metricas') ? 'activo' : ''; ?>" title="MÉTRICA &bull; Dashboard de Gestión Técnica">
                <i class="bi bi-speedometer2 fs-5"></i>
                <span class="sidebar-item-texto">Dashboard</span>
            </a>
            <?php else: ?>
            <a href="<?php echo $ruta_base; ?>index.php" class="btn-dashboard-sidebar <?php echo ($pagina_activa === 'inicio') ? 'activo' : ''; ?>" title="Formulario Detallado &bull; Portal Central">
                <i class="bi bi-grid-3x3-gap-fill fs-5"></i>
                <span class="sidebar-item-texto">Formulario Detallado</span>
            </a>
            <?php endif; ?>
        </div>

        <!-- 3. Título de Categoría: FORMULARIOS -->
        <div class="sidebar-categoria-header">
            FORMULARIOS
        </div>

        <!-- 4. Lista de Formularios con sus dibujitos clickeables -->
        <nav class="sidebar-lista-items">
            <?php foreach ($modulos_sistema as $clave => $mod): ?>
                <?php 
                // Si el módulo es técnico y el usuario es solicitante, ocultarlo
                if (!empty($mod['solo_tecnico']) && !$es_tecnico_o_admin) {
                    continue;
                }
                $es_activo = ($clave === $clave_modulo_activo);
                $numero_mostrar = ($es_tecnico_o_admin || empty($mod['numero_personal'])) ? $mod['numero'] : $mod['numero_personal'];
                ?>
                <a href="<?php echo $mod['url_formulario']; ?>" class="sidebar-item-link <?php echo $es_activo ? 'activo' : ''; ?>" title="<?php echo htmlspecialchars($numero_mostrar . '. ' . $mod['nombre']); ?>">
                    <span class="sidebar-item-icono text-<?php echo $mod['color']; ?>">
                        <i class="bi <?php echo $mod['icono']; ?>"></i>
                    </span>
                    <span class="fw-semibold text-white text-truncate link-nombre-formulario">
                        <?php echo htmlspecialchars($numero_mostrar . '. ' . $mod['nombre']); ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Parte Inferior: Perfil de Usuario con Avatar Circular -->
    <div class="sidebar-pie-usuario">
        <div class="sidebar-usuario-contenedor" title="<?php echo htmlspecialchars($nombre_usuario_mostrar . ' (' . $badge_rol_texto . ')'); ?>">
            <span class="avatar-sidebar-circulo">
                <?php echo htmlspecialchars($inicial_usuario); ?>
            </span>
            <div class="sidebar-info-usuario">
                <div class="sidebar-usuario-nombre"><?php echo htmlspecialchars($nombre_usuario_mostrar); ?></div>
                <div class="sidebar-usuario-rol"><?php echo htmlspecialchars($badge_rol_texto); ?></div>
            </div>
        </div>
    </div>
</aside>

<!-- Telón de Fondo para Pantallas Móviles -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- Script de Control del Menú Lateral Colapsable -->
<script>
(function() {
    function initSidebarCosmol() {
        var sidebar = document.getElementById('barraLateralCosmol');
        var backdrop = document.getElementById('sidebarBackdrop');
        var btnChevron = document.getElementById('btnToggleChevronSidebar');
        var headerToggle = document.getElementById('sidebarHeaderToggle');
        var iconoChevron = document.getElementById('iconoChevronSidebar');

        if (!sidebar) return;

        function alternarSidebar() {
            var estaExpandida = sidebar.classList.toggle('expandida');
            document.body.classList.toggle('sidebar-expandida', estaExpandida);
            
            if (iconoChevron) {
                if (estaExpandida) {
                    iconoChevron.className = 'bi bi-chevron-left';
                } else {
                    iconoChevron.className = 'bi bi-chevron-right';
                }
            }
        }

        function cerrarSidebar() {
            sidebar.classList.remove('expandida');
            document.body.classList.remove('sidebar-expandida');
            if (iconoChevron) {
                iconoChevron.className = 'bi bi-chevron-right';
            }
        }

        if (btnChevron) {
            btnChevron.addEventListener('click', function(e) {
                e.stopPropagation();
                alternarSidebar();
            });
        }
        if (headerToggle) {
            headerToggle.addEventListener('click', function(e) {
                if (e.target !== btnChevron && !btnChevron.contains(e.target)) {
                    alternarSidebar();
                }
            });
        }
        if (backdrop) backdrop.addEventListener('click', cerrarSidebar);

        // Al hacer clic en un enlace del menú en móvil o tablet, cerrar automáticamente si estaba expandido
        var linksSidebar = sidebar.querySelectorAll('a');
        linksSidebar.forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    cerrarSidebar();
                }
            });
        });

        // En pantallas grandes y móviles, iniciar con icono bi-chevron-right si está colapsado
        if (iconoChevron && !sidebar.classList.contains('expandida')) {
            iconoChevron.className = 'bi bi-chevron-right';
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebarCosmol);
    } else {
        initSidebarCosmol();
    }
})();
</script>

