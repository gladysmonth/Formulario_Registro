<?php
/**
 * Portal Central de Formularios de Registro
 * Compatible con PHP 7.3
 * Archivo: index.php
 */

$titulo_pagina = 'Catálogo de Formularios de Registro';
$pagina_activa = 'inicio';
$nivel_ruta = '';

require_once __DIR__ . '/componentes/encabezado.php';
require_once __DIR__ . '/componentes/barra_navegacion.php';

$usuario_actual_sesion = obtener_usuario_sesion();
$rol_actual = obtener_rol_actual();
$es_tecnico_o_admin = es_personal_tecnico();
$mensaje_error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<main class="container my-5">
    <?php if ($mensaje_error === 'acceso_denegado'): ?>
    <div class="alert alert-warning alert-dismissible fade show shadow-sm border-warning mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-shield-lock-fill fs-4 me-3 text-warning"></i>
            <div>
                <h6 class="alert-heading fw-bold mb-1">Acceso Restringido</h6>
                <p class="mb-0 small">Esta sección está reservada exclusivamente para el personal técnico y administradores del Departamento de Sistemas.</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php endif; ?>

    <!-- Hero / Banner de Bienvenida -->
    <div class="p-5 mb-4 bg-white rounded-4 shadow-sm border">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold mb-2">
                    <i class="bi bi-layers-fill me-1"></i> Plataforma Modular de Registro
                </span>
                <h1 class="display-6 fw-bold text-dark mt-2 mb-3">
                    <?php if ($usuario_actual_sesion): ?>
                        ¡Hola, <?php echo htmlspecialchars(!empty($usuario_actual_sesion['nombre_completo']) ? $usuario_actual_sesion['nombre_completo'] : $usuario_actual_sesion['usuario']); ?>!
                    <?php else: ?>
                        Formularios de Registro Institucional
                    <?php endif; ?>
                </h1>
                <p class="lead text-muted fs-6 mb-0">
                    Bienvenido al sistema unificado de gestión de solicitudes y registros. Selecciona el formulario correspondiente para iniciar tu requerimiento<?php echo $es_tecnico_o_admin ? ' o acceder a las bandejas técnicas de atención.' : '.'; ?>
                </p>
            </div>
            <div class="col-lg-4 text-center d-none d-lg-block">
                <div class="p-4 bg-light rounded-circle d-inline-flex shadow-sm border">
                    <i class="bi bi-clipboard-check text-primary" style="font-size: 5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Catálogo de Formularios del Sistema -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 fw-bold mb-0">Módulos del Sistema</h2>
        <span class="badge bg-secondary-subtle text-dark border">
            <?php echo $es_tecnico_o_admin ? '4 Formularios + 1 Auditoría PC' : '4 Formularios Activos'; ?>
        </span>
    </div>

    <div class="row g-4">
        <!-- FORMULARIO 1: ACTIVO (SOPORTE TÉCNICO) -->
        <div class="col-md-6 col-lg-6">
            <div class="card h-100 tarjeta-modulo border-primary shadow-sm bg-white">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="p-3 bg-primary-subtle text-primary rounded-3">
                            <i class="bi bi-headset fs-2"></i>
                        </div>
                        <span class="badge bg-success px-3 py-2 rounded-pill">
                            <i class="bi bi-check-circle-fill me-1"></i> Activo y Disponible
                        </span>
                    </div>

                    <h3 class="h5 fw-bold text-dark mb-2">1. Formulario de Soporte Técnico</h3>
                    <p class="text-muted small flex-grow-1">
                        Reporte de fallas de hardware, problemas de software, detalle del equipo afectado, definición de prioridad operativa y registro de diagnósticos y soluciones por el personal de soporte.
                    </p>

                    <div class="bg-light p-3 rounded-3 mb-3 small border">
                        <div class="row g-2 text-secondary">
                            <div class="col-6"><i class="bi bi-check2 text-primary me-1"></i> Datos del Solicitante</div>
                            <div class="col-6"><i class="bi bi-check2 text-primary me-1"></i> Catálogo de Equipos</div>
                            <div class="col-6"><i class="bi bi-check2 text-primary me-1"></i> Diagnóstico Técnico</div>
                            <div class="col-6"><i class="bi bi-check2 text-primary me-1"></i> Doble Firma Digital</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-auto pt-2">
                        <a href="modulos/soporte_tecnico/formulario_soporte.php" class="btn btn-primary flex-grow-1 fw-semibold">
                            <i class="bi bi-pencil-square me-1"></i> Llenar Formulario
                        </a>
                        <?php if ($es_tecnico_o_admin): ?>
                        <a href="modulos/soporte_tecnico/gestion_tickets.php" class="btn btn-outline-primary" title="Bandeja de Gestión">
                            <i class="bi bi-kanban"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORMULARIO 2: ACTIVO (MANTENIMIENTO PREVENTIVO PC / LAPTOP) -->
        <div class="col-md-6 col-lg-6">
            <div class="card h-100 tarjeta-modulo border-success shadow-sm bg-white">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="p-3 bg-success-subtle text-success rounded-3">
                            <i class="bi bi-tools fs-2"></i>
                        </div>
                        <span class="badge bg-success px-3 py-2 rounded-pill">
                            <i class="bi bi-check-circle-fill me-1"></i> Activo y Disponible
                        </span>
                    </div>

                    <h3 class="h5 fw-bold text-dark mb-2">2. Mantenimiento Preventivo (Equipos)</h3>
                    <p class="text-muted small flex-grow-1">
                        Ficha técnica de inspección, limpieza física interna/externa, verificación operativa de hardware, software y periféricos de equipos PC y Laptops institucionales.
                    </p>

                    <div class="bg-light p-3 rounded-3 mb-3 small border">
                        <div class="row g-2 text-secondary">
                            <div class="col-6"><i class="bi bi-check2 text-success me-1"></i> Ficha Técnica PC/Laptop</div>
                            <div class="col-6"><i class="bi bi-check2 text-success me-1"></i> Limpieza Física y Lógica</div>
                            <div class="col-6"><i class="bi bi-check2 text-success me-1"></i> Test de Rendimiento</div>
                            <div class="col-6"><i class="bi bi-check2 text-success me-1"></i> Doble Firma Digital</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-auto pt-2">
                        <a href="modulos/mantenimiento_preventivo/formulario_mantenimiento.php" class="btn btn-success text-white flex-grow-1 fw-semibold">
                            <i class="bi bi-pencil-square me-1"></i> Registrar Mantenimiento
                        </a>
                        <?php if ($es_tecnico_o_admin): ?>
                        <a href="modulos/mantenimiento_preventivo/gestion_mantenimientos.php" class="btn btn-outline-success" title="Bandeja de Mantenimientos">
                            <i class="bi bi-card-checklist"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORMULARIO 3: ACTIVO (REGISTRO DE INCIDENCIAS DE SISTEMAS) -->
        <div class="col-md-6 col-lg-6">
            <div class="card h-100 tarjeta-modulo border-danger shadow-sm bg-white">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="p-3 bg-danger-subtle text-danger rounded-3">
                            <i class="bi bi-shield-exclamation fs-2"></i>
                        </div>
                        <span class="badge bg-success px-3 py-2 rounded-pill">
                            <i class="bi bi-check-circle-fill me-1"></i> Activo y Disponible
                        </span>
                    </div>

                    <h3 class="h5 fw-bold text-dark mb-2">3. Registro de Incidencias de Sistemas</h3>
                    <p class="text-muted small flex-grow-1">
                        Reporte de fallas técnicas en plataformas (ERP SAI, NETCOB), servidores, base de datos y enlaces con clasificación por criticidad, logs de error, solución aplicada y firmas digitales.
                    </p>

                    <div class="bg-light p-3 rounded-3 mb-3 small border">
                        <div class="row g-2 text-secondary">
                            <div class="col-6"><i class="bi bi-check2 text-danger me-1"></i> Control de Fallas</div>
                            <div class="col-6"><i class="bi bi-check2 text-danger me-1"></i> Marcar y rellenar fallo</div>
                            <div class="col-6"><i class="bi bi-check2 text-danger me-1"></i> Criticidad en 3 Niveles</div>
                            <div class="col-6"><i class="bi bi-check2 text-danger me-1"></i> Ficha Técnica Oficial</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-auto pt-2">
                        <a href="modulos/registro_incidencias/formulario_incidencias.php" class="btn btn-danger flex-grow-1 fw-semibold">
                            <i class="bi bi-pencil-square me-1"></i> Reportar Incidencia
                        </a>
                        <?php if ($es_tecnico_o_admin): ?>
                        <a href="modulos/registro_incidencias/gestion_incidencias.php" class="btn btn-outline-danger" title="Bandeja de Incidencias">
                            <i class="bi bi-card-checklist"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORMULARIO 4: ACTIVO (CREACIÓN Y ASIGNACIÓN DE ACCESOS) -->
        <div class="col-md-6 col-lg-6">
            <div class="card h-100 tarjeta-modulo border-primary shadow-sm bg-white">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="p-3 bg-primary-subtle text-primary rounded-3">
                            <i class="bi bi-shield-lock fs-2"></i>
                        </div>
                        <span class="badge bg-success px-3 py-2 rounded-pill">
                            <i class="bi bi-check-circle-fill me-1"></i> Activo y Disponible
                        </span>
                    </div>

                    <h3 class="h5 fw-bold text-dark mb-2">4. Asignación de Accesos</h3>
                    <p class="text-muted small flex-grow-1">
                        Creación y asignación formal de usuarios, perfiles y permisos en plataformas ERP SAI, NETCOB y sistemas corporativos con validación jerárquica y triple firma digital.
                    </p>

                    <div class="bg-light p-3 rounded-3 mb-3 small border">
                        <div class="row g-2 text-secondary">
                            <div class="col-6"><i class="bi bi-check2 text-primary me-1"></i> ERP SAI y NETCOB</div>
                            <div class="col-6"><i class="bi bi-check2 text-primary me-1"></i> Cuentas Nuevas / Existentes</div>
                            <div class="col-6"><i class="bi bi-check2 text-primary me-1"></i> Perfiles Extendidos</div>
                            <div class="col-6"><i class="bi bi-check2 text-primary me-1"></i> Triple Firma Digital</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-auto pt-2">
                        <a href="modulos/asignacion_accesos/formulario_accesos.php" class="btn btn-primary flex-grow-1 fw-semibold">
                            <i class="bi bi-pencil-square me-1"></i> Solicitar Acceso
                        </a>
                        <?php if ($es_tecnico_o_admin): ?>
                        <a href="modulos/asignacion_accesos/gestion_accesos.php" class="btn btn-outline-primary" title="Bandeja de Accesos">
                            <i class="bi bi-card-checklist"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($es_tecnico_o_admin): ?>
        <!-- MÓDULO EXCLUSIVO TÉCNICO: TELEMETRÍA Y DIAGNÓSTICO DE PCS -->
        <div class="col-md-6 col-lg-6">
            <div class="card h-100 tarjeta-modulo border-warning shadow-sm bg-white">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="p-3 bg-warning-subtle text-dark rounded-3">
                            <i class="bi bi-cpu fs-2"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold">
                            <i class="bi bi-shield-fill-check me-1"></i> Exclusivo Técnicos
                        </span>
                    </div>

                    <h3 class="h5 fw-bold text-dark mb-2">Telemetría y Diagnóstico de PCs</h3>
                    <p class="text-muted small flex-grow-1">
                        Monitoreo en vivo de equipos auditados vía agente PowerShell. Inspección de procesador, memoria RAM, salud SMART de discos, alertas térmicas y Defender.
                    </p>

                    <div class="bg-light p-3 rounded-3 mb-3 small border">
                        <div class="row g-2 text-secondary">
                            <div class="col-6"><i class="bi bi-check2 text-warning me-1"></i> Telemetría en Vivo</div>
                            <div class="col-6"><i class="bi bi-check2 text-warning me-1"></i> Estado SMART Discos</div>
                            <div class="col-6"><i class="bi bi-check2 text-warning me-1"></i> Alertas de Hardware</div>
                            <div class="col-6"><i class="bi bi-check2 text-warning me-1"></i> Historial Forense</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-auto pt-2">
                        <a href="modulos/diagnostico/monitoreo_equipos.php" class="btn btn-warning text-dark flex-grow-1 fw-bold">
                            <i class="bi bi-speedometer2 me-1"></i> Panel de Monitoreo PC
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php
$scripts_adicionales = array();
require_once __DIR__ . '/componentes/pie_pagina.php';
?>
