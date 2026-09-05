<?php
/**
 * Vista: Formulario de Registro de Mantenimiento Preventivo (Equipos: PC / Laptop)
 * Compatible con PHP 7.3
 * Archivo: formulario_mantenimiento.php
 */

$titulo_pagina = 'Registro de Mantenimiento Preventivo - Equipos PC / Laptop';
$pagina_activa = 'mantenimiento_preventivo';
$nivel_ruta = '../../';

require_once __DIR__ . '/../../componentes/encabezado.php';
require_once __DIR__ . '/../../componentes/barra_navegacion.php';
?>

<main class="container my-4 my-lg-5">
    <!-- Migas de Pan (Breadcrumbs) -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="../../index.php" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item text-muted">Mantenimiento Preventivo</li>
            <li class="breadcrumb-item active" aria-current="page">Nuevo Registro</li>
        </ol>
    </nav>

    <!-- Encabezado del Formulario -->
    <div class="tarjeta-formulario p-4 mb-4 border-start border-4 border-primary">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold mb-2">
                    <i class="bi bi-shield-check me-1"></i> Formulario de Control Técnico
                </span>
                <h1 class="h3 fw-bold text-dark mb-1">Mantenimiento Preventivo (PC / Laptop)</h1>
                <p class="text-muted small mb-0">
                    Registre las actividades de limpieza física, optimización lógica y pruebas de rendimiento con las firmas de conformidad correspondientes.
                </p>
            </div>
            <div>
                <a href="gestion_mantenimientos.php" class="btn btn-outline-secondary btn-sm px-3 py-2">
                    <i class="bi bi-card-checklist me-1"></i> Ver Registros
                </a>
            </div>
        </div>
    </div>

    <!-- Alerta dinámica de validación -->
    <div id="alertaValidacionMP" class="alert alert-danger d-none shadow-sm" role="alert"></div>

    <!-- Formulario Principal Modular -->
    <form id="formMantenimientoPreventivo" novalidate>
        <!-- 1. Datos Generales -->
        <?php require_once __DIR__ . '/secciones/seccion_1_datos_generales.php'; ?>

        <!-- 2. Información del Equipo -->
        <?php require_once __DIR__ . '/secciones/seccion_2_informacion_equipo.php'; ?>

        <!-- 3. Mantenimiento Externo (Limpieza Física) -->
        <?php require_once __DIR__ . '/secciones/seccion_3_mantenimiento_externo.php'; ?>

        <!-- 4. Mantenimiento Interno (Software / Configuración) -->
        <?php require_once __DIR__ . '/secciones/seccion_4_mantenimiento_interno.php'; ?>

        <!-- 5. Verificación de Funcionamiento -->
        <?php require_once __DIR__ . '/secciones/seccion_5_verificacion_funcionamiento.php'; ?>

        <!-- 6. Observaciones / Incidencias -->
        <?php require_once __DIR__ . '/secciones/seccion_6_observaciones.php'; ?>

        <!-- 7. Firmas Digitales (Responsable del Equipo y Dpto. de Sistemas) -->
        <?php require_once __DIR__ . '/secciones/seccion_firmas.php'; ?>

        <!-- Botones de Acción -->
        <div class="tarjeta-formulario p-4 text-end">
            <button type="reset" class="btn btn-outline-secondary me-2 px-4 py-2" id="btnCancelarMP">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Restablecer
            </button>
            <button type="submit" class="btn btn-primary px-4 py-2" id="btnGuardarMP">
                <i class="bi bi-save me-1"></i> Guardar Registro de Mantenimiento
            </button>
        </div>
    </form>
</main>

<?php
$scripts_adicionales = array(
    'recursos/js/formulario_mantenimiento.js'
);
require_once __DIR__ . '/../../componentes/pie_pagina.php';
?>
