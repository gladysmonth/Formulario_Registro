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
?>

<main class="container my-5">
    <!-- Hero / Banner de Bienvenida -->
    <div class="p-5 mb-4 bg-white rounded-4 shadow-sm border">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold mb-2">
                    <i class="bi bi-layers-fill me-1"></i> Plataforma Modular de Registro
                </span>
                <h1 class="display-6 fw-bold text-dark mt-2 mb-3">Formularios de Registro Institucional</h1>
                <p class="lead text-muted fs-6 mb-4">
                    Bienvenido al sistema unificado de gestión de solicitudes y registros. Selecciona el formulario correspondiente para iniciar tu requerimiento o acceder a la bandeja de gestión y seguimiento.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="modulos/soporte_tecnico/formulario_soporte.php" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">
                        <i class="bi bi-tools me-1"></i> Formulario de Soporte Técnico
                    </a>
                    <a href="modulos/soporte_tecnico/gestion_tickets.php" class="btn btn-outline-secondary px-4 py-2 rounded-3">
                        <i class="bi bi-kanban me-1"></i> Panel de Tickets
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-center d-none d-lg-block">
                <div class="p-4 bg-light rounded-circle d-inline-flex shadow-sm border">
                    <i class="bi bi-clipboard-check text-primary" style="font-size: 5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Catálogo de los 4 Formularios del Sistema -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 fw-bold mb-0">Módulos del Sistema (4 Formularios)</h2>
        <span class="badge bg-secondary-subtle text-dark border">1 Activo &bull; 3 en Planificación</span>
    </div>

    <div class="row g-4">
        <!-- FORMULARIO 1: ACTIVO -->
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
                            <div class="col-6"><i class="bi bi-check2 text-primary me-1"></i> 6 Secciones completas</div>
                            <div class="col-6"><i class="bi bi-check2 text-primary me-1"></i> Generación de Ticket</div>
                            <div class="col-6"><i class="bi bi-check2 text-primary me-1"></i> Clasificación Prioridad</div>
                            <div class="col-6"><i class="bi bi-check2 text-primary me-1"></i> Resolución Técnica</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-auto pt-2">
                        <a href="modulos/soporte_tecnico/formulario_soporte.php" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-pencil-square me-1"></i> Llenar Solicitud
                        </a>
                        <a href="modulos/soporte_tecnico/gestion_tickets.php" class="btn btn-outline-primary" title="Panel de Gestión">
                            <i class="bi bi-kanban"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORMULARIO 2: PRÓXIMO -->
        <div class="col-md-6 col-lg-6">
            <div class="card h-100 tarjeta-modulo opacity-75 bg-white border">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="p-3 bg-secondary-subtle text-secondary rounded-3">
                            <i class="bi bi-calendar-check fs-2"></i>
                        </div>
                        <span class="badge bg-secondary px-3 py-2 rounded-pill">
                            <i class="bi bi-hourglass-split me-1"></i> En Desarrollo
                        </span>
                    </div>

                    <h3 class="h5 fw-bold text-dark mb-2">2. Permisos y Vacaciones</h3>
                    <p class="text-muted small flex-grow-1">
                        Gestión y autorización de solicitudes de vacaciones, permisos remunerados, licencias médicas y justificaciones de ausencia laboral para el departamento de Recursos Humanos.
                    </p>

                    <div class="bg-light p-3 rounded-3 mb-3 small border text-muted">
                        <i class="bi bi-lock me-1"></i> Módulo planificado en la siguiente fase de desarrollo.
                    </div>

                    <button class="btn btn-secondary disabled mt-auto" disabled>
                        <i class="bi bi-clock me-1"></i> Próximamente
                    </button>
                </div>
            </div>
        </div>

        <!-- FORMULARIO 3: PRÓXIMO -->
        <div class="col-md-6 col-lg-6">
            <div class="card h-100 tarjeta-modulo opacity-75 bg-white border">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="p-3 bg-secondary-subtle text-secondary rounded-3">
                            <i class="bi bi-box-seam fs-2"></i>
                        </div>
                        <span class="badge bg-secondary px-3 py-2 rounded-pill">
                            <i class="bi bi-hourglass-split me-1"></i> En Desarrollo
                        </span>
                    </div>

                    <h3 class="h5 fw-bold text-dark mb-2">3. Control de Activos e Inventario</h3>
                    <p class="text-muted small flex-grow-1">
                        Asignación, devolución y custodia de equipos informáticos, dispositivos móviles, licencias de software y herramientas corporativas asignadas al personal.
                    </p>

                    <div class="bg-light p-3 rounded-3 mb-3 small border text-muted">
                        <i class="bi bi-lock me-1"></i> Módulo planificado en la siguiente fase de desarrollo.
                    </div>

                    <button class="btn btn-secondary disabled mt-auto" disabled>
                        <i class="bi bi-clock me-1"></i> Próximamente
                    </button>
                </div>
            </div>
        </div>

        <!-- FORMULARIO 4: PRÓXIMO -->
        <div class="col-md-6 col-lg-6">
            <div class="card h-100 tarjeta-modulo opacity-75 bg-white border">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="p-3 bg-secondary-subtle text-secondary rounded-3">
                            <i class="bi bi-cart3 fs-2"></i>
                        </div>
                        <span class="badge bg-secondary px-3 py-2 rounded-pill">
                            <i class="bi bi-hourglass-split me-1"></i> En Desarrollo
                        </span>
                    </div>

                    <h3 class="h5 fw-bold text-dark mb-2">4. Compras y Suministros</h3>
                    <p class="text-muted small flex-grow-1">
                        Requerimiento formal de materiales de oficina, consumibles, repuestos o adquisición de nuevos suministros para aprobación por el área administrativa.
                    </p>

                    <div class="bg-light p-3 rounded-3 mb-3 small border text-muted">
                        <i class="bi bi-lock me-1"></i> Módulo planificado en la siguiente fase de desarrollo.
                    </div>

                    <button class="btn btn-secondary disabled mt-auto" disabled>
                        <i class="bi bi-clock me-1"></i> Próximamente
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
$scripts_adicionales = array();
require_once __DIR__ . '/componentes/pie_pagina.php';
?>
