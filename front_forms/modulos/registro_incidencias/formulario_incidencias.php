<?php
/**
 * Vista: Formulario de Registro de Incidencias de Sistemas (FOR_RIS_001, V-1)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/registro_incidencias/formulario_incidencias.php
 */

$titulo_pagina = 'Registro de Incidencias de Sistemas';
$pagina_activa = 'registro_incidencias';
$nivel_ruta = '../../';

require_once __DIR__ . '/../../componentes/encabezado.php';
require_once __DIR__ . '/../../componentes/barra_navegacion.php';
?>

<main class="container my-4 my-lg-5">
    <!-- Migas de Pan (Breadcrumbs) -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="../../index.php" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item text-muted">Incidencias de Sistemas</li>
            <li class="breadcrumb-item active" aria-current="page">Nuevo Reporte</li>
        </ol>
    </nav>

    <!-- Encabezado del Formulario -->
    <div class="tarjeta-formulario p-4 mb-4 border-start border-4 border-danger">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-semibold mb-2 d-inline-block">
                    <i class="bi bi-exclamation-octagon-fill me-1"></i> Control de Incidencias
                </span>
                <h1 class="h3 fw-bold text-dark mb-1">Registro de Incidencias de Sistemas</h1>
                <p class="text-muted small mb-0">
                    COSMOL R.L. &bull; Reporte técnico de anomalías en plataformas institucionales (ERP SAI, NETCOB), servidores, conectividad y base de datos.
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="gestion_incidencias.php" class="btn btn-outline-secondary btn-sm px-3 py-2">
                    <i class="bi bi-card-checklist me-1"></i> Bandeja de Incidencias
                </a>
            </div>
        </div>
    </div>

    <!-- Alerta dinámica de validación -->
    <div id="alertaValidacionRI" class="alert alert-danger d-none shadow-sm" role="alert"></div>

    <!-- Formulario Principal Modular -->
    <form id="formRegistroIncidencia" novalidate>
        <!-- 1. Datos Generales de la Incidencia -->
        <?php require_once __DIR__ . '/secciones/seccion_1_datos_generales.php'; ?>

        <!-- 2. Plataforma / Sistema Afectado -->
        <?php require_once __DIR__ . '/secciones/seccion_2_plataforma_sistema.php'; ?>

        <!-- 3. Naturaleza Técnica del Fallo (Marcar y rellenar) -->
        <?php require_once __DIR__ . '/secciones/seccion_3_naturaleza_fallo.php'; ?>

        <!-- 4. Nivel de Criticidad Institucional -->
        <?php require_once __DIR__ . '/secciones/seccion_4_nivel_criticidad.php'; ?>

        <!-- 5. Descripción Técnica y Logs de Error -->
        <?php require_once __DIR__ . '/secciones/seccion_5_descripcion_logs.php'; ?>

        <!-- 6. Solución Aplicada y Tiempos -->
        <?php require_once __DIR__ . '/secciones/seccion_6_solucion_tiempos.php'; ?>

        <!-- 7. Seguimiento y Recomendaciones -->
        <?php require_once __DIR__ . '/secciones/seccion_7_recomendaciones.php'; ?>

        <!-- 8. Firmas Digitales Institucionales -->
        <?php require_once __DIR__ . '/secciones/seccion_firmas.php'; ?>

        <!-- Botones de Acción -->
        <div class="tarjeta-formulario p-4 text-end">
            <button type="reset" class="btn btn-outline-secondary me-2 px-4 py-2" id="btnRestablecerRI">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Restablecer Formulario
            </button>
            <button type="submit" class="btn btn-danger px-4 py-2 fw-semibold" id="btnGuardarRI">
                <i class="bi bi-save me-1"></i> Guardar Reporte de Incidencia
            </button>
        </div>
    </form>
</main>

<?php
$scripts_adicionales = array(
    'recursos/js/formulario_incidencias.js'
);
require_once __DIR__ . '/../../componentes/pie_pagina.php';
?>
