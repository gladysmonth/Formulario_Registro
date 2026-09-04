<?php
/**
 * Vista: Formulario de Registro de Soporte Técnico (6 Secciones)
 * Compatible con PHP 7.3
 * Archivo: formulario_soporte.php
 */

$titulo_pagina = 'Formulario de Registro - Soporte Técnico';
$pagina_activa = 'soporte_formulario';
$nivel_ruta = '../../';

require_once __DIR__ . '/../../componentes/encabezado.php';
require_once __DIR__ . '/../../componentes/barra_navegacion.php';
?>

<main class="container my-4 my-lg-5">
    <!-- Migas de Pan (Breadcrumbs) -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="../../index.php" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item text-muted">Soporte Técnico</li>
            <li class="breadcrumb-item active" aria-current="page">Nuevo Formulario de Registro</li>
        </ol>
    </nav>

    <!-- Encabezado del Formulario -->
    <div class="tarjeta-formulario p-4 mb-4 border-start border-4 border-primary">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-primary-subtle text-primary border px-3 py-2 rounded-pill fw-semibold mb-2">
                    <i class="bi bi-shield-check me-1"></i> Formulario N° 1
                </span>
                <h1 class="h3 fw-bold text-dark mb-1">Formulario de Registro: Soporte Técnico</h1>
                <p class="text-muted small mb-0">
                    Complete los datos del requerimiento o incidente técnico. Los campos marcados con <span class="text-danger">*</span> son obligatorios.
                </p>
            </div>
            <div>
                <a href="gestion_tickets.php" class="btn btn-outline-primary btn-sm px-3 py-2">
                    <i class="bi bi-kanban me-1"></i> Ver Panel de Tickets
                </a>
            </div>
        </div>
    </div>

    <!-- Alerta de Validación Dinámica -->
    <div id="alertaValidacion" class="alert alert-danger d-none shadow-sm" role="alert"></div>

    <!-- FORMULARIO PRINCIPAL -->
    <form id="formularioSoporteTecnico" novalidate>
        <div class="row g-4">
            
            <!-- SECCIÓN 1: DATOS GENERALES -->
            <?php include __DIR__ . '/secciones/seccion_1_datos_generales.php'; ?>

            <!-- SECCIÓN 2: TIPO DE SOPORTE REQUERIDO -->
            <?php include __DIR__ . '/secciones/seccion_2_tipo_soporte.php'; ?>

            <!-- SECCIÓN 3: DETALLE DEL PROBLEMA / REQUERIMIENTO -->
            <?php include __DIR__ . '/secciones/seccion_3_detalle_problema.php'; ?>

            <!-- INTERRUPTOR PARA ACTIVAR SECCIONES 4, 5 Y 6 (SOPORTE TÉCNICO) -->
            <div class="col-12">
                <div class="p-3 bg-white border rounded-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 bg-info-subtle text-info rounded-circle">
                            <i class="bi bi-person-badge fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">¿Llenar campos de Soporte Técnico inmediatamente?</div>
                            <small class="text-muted">Si eres técnico o cuentas con los datos de diagnóstico/solución, activa este interruptor para llenar las secciones 4, 5 y 6.</small>
                        </div>
                    </div>
                    <div class="form-check form-switch fs-5">
                        <input class="form-check-input" type="checkbox" id="switchSeccionTecnica" role="switch">
                    </div>
                </div>
            </div>

            <!-- CONTENEDOR COLAPSABLE: SECCIONES 4, 5 Y 6 -->
            <div id="seccionTecnicaContenedor" class="col-12 d-none">
                <div class="row g-4">
                    <!-- SECCIÓN 4: PRIORIDAD -->
                    <?php include __DIR__ . '/secciones/seccion_4_prioridad.php'; ?>

                    <!-- SECCIÓN 5: DATOS DEL TÉCNICO -->
                    <?php include __DIR__ . '/secciones/seccion_5_datos_tecnico.php'; ?>

                    <!-- SECCIÓN 6: OBSERVACIONES / RECOMENDACIÓN -->
                    <?php include __DIR__ . '/secciones/seccion_6_observaciones.php'; ?>
                </div>
            </div>

            <!-- SECCIÓN: FIRMAS DIGITALES (SOLICITANTE Y DPTO. DE SISTEMAS) -->
            <?php include __DIR__ . '/secciones/seccion_firmas.php'; ?>

            <!-- BOTONES DE ACCIÓN -->
            <div class="col-12">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 p-3 bg-white border rounded-3">
                    <a href="../../index.php" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-arrow-left me-1"></i> Volver al Catálogo
                    </a>

                    <div class="d-flex gap-2 w-100 w-sm-auto justify-content-end">
                        <button type="reset" class="btn btn-light border px-3">
                            <i class="bi bi-eraser me-1"></i> Limpiar
                        </button>
                        <button type="submit" id="btnGuardarTicket" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                            <i class="bi bi-send-check me-2"></i> Registrar Ticket de Soporte
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</main>

<?php
$scripts_adicionales = array(
    'recursos/js/formulario_soporte.js'
);
require_once __DIR__ . '/../../componentes/pie_pagina.php';
?>
