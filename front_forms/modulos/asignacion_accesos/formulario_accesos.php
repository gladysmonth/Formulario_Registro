<?php
/**
 * Vista: Formulario de Creación y/o Asignación de Accesos de Usuarios de Sistemas
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/asignacion_accesos/formulario_accesos.php
 */

$titulo_pagina = 'Creación y Asignación de Accesos';
$pagina_activa = 'asignacion_accesos';
$nivel_ruta = '../../';

require_once __DIR__ . '/../../componentes/encabezado.php';
require_once __DIR__ . '/../../componentes/barra_navegacion.php';
?>

<main class="container my-4 my-lg-5">
    <!-- Migas de Pan (Breadcrumbs) -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="../../index.php" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item text-muted">Asignación de Accesos</li>
            <li class="breadcrumb-item active" aria-current="page">Nueva Solicitud</li>
        </ol>
    </nav>

    <!-- Encabezado del Formulario -->
    <div class="tarjeta-formulario p-4 mb-4 border-start border-4 border-primary">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold mb-2 d-inline-block">
                    <i class="bi bi-shield-lock-fill me-1"></i> Control de Accesos Institucionales
                </span>
                <h1 class="h3 fw-bold text-dark mb-1">Creación y Asignación de Accesos</h1>
                <p class="text-muted small mb-0">
                    COSMOL R.L. &bull; Solicitud formal de creación de usuarios, perfiles y permisos en plataformas ERP SAI, NETCOB y sistemas corporativos.
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="gestion_accesos.php" class="btn btn-outline-secondary btn-sm px-3 py-2">
                    <i class="bi bi-card-checklist me-1"></i> Bandeja de Solicitudes
                </a>
            </div>
        </div>
    </div>

    <!-- Alerta dinámica de validación -->
    <div id="alertaValidacionAcc" class="alert alert-danger d-none shadow-sm" role="alert"></div>

    <!-- Formulario Principal Modular -->
    <form id="formRegistroAccesos" novalidate>
        <!-- 1. Datos Generales -->
        <?php require_once __DIR__ . '/secciones/seccion_1_datos_generales.php'; ?>

        <!-- 2. Plataforma / Sistema -->
        <?php require_once __DIR__ . '/secciones/seccion_2_plataforma_sistema.php'; ?>

        <!-- 3. Datos del Usuario -->
        <?php require_once __DIR__ . '/secciones/seccion_3_datos_usuario.php'; ?>

        <!-- 4. Requerimientos de Accesos -->
        <?php require_once __DIR__ . '/secciones/seccion_4_requerimientos.php'; ?>

        <!-- 5. Departamento de Sistemas (Atención y Comentarios) -->
        <?php require_once __DIR__ . '/secciones/seccion_5_atencion_sistemas.php'; ?>

        <!-- 6. Firmas y Validaciones Institucionales (Triple Firma) -->
        <?php require_once __DIR__ . '/secciones/seccion_firmas.php'; ?>

        <!-- Botones de Acción -->
        <div class="tarjeta-formulario p-4 text-end d-flex flex-column flex-sm-row justify-content-end gap-2">
            <button type="reset" class="btn btn-light border px-4 py-2 text-secondary" id="btnLimpiarFormAccesos">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar Campos
            </button>
            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm" id="btnEnviarSolicitudAccesos">
                <i class="bi bi-send-fill me-1"></i> Registrar Solicitud de Acceso
            </button>
        </div>
    </form>
</main>

<script src="<?php echo $nivel_ruta; ?>recursos/js/formulario_accesos.js"></script>

<?php require_once __DIR__ . '/../../componentes/pie_pagina.php'; ?>
