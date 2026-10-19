<?php
/**
 * Vista: Radiografía Técnica Detallada de Equipo (Estilo HWMonitor + Forense)
 * Compatible con PHP 7.3 y PostgreSQL 13
 * Archivo: detalle_equipo.php
 */

$titulo_pagina = 'Ficha Técnica de Equipo - Diagnóstico';
$pagina_activa = 'diagnostico_detalle';
$nivel_ruta = '../../';
$roles_requeridos = array('admin', 'tecnico');

require_once __DIR__ . '/../../componentes/encabezado.php';
require_once __DIR__ . '/../../componentes/barra_navegacion.php';
?>

<main class="container-fluid px-3 px-lg-4 my-4">
    <!-- Migas de Pan (Breadcrumbs) -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="../../index.php" class="text-decoration-none"><i class="bi bi-house-door me-1"></i>Inicio</a></li>
            <li class="breadcrumb-item"><a href="monitoreo_equipos.php" class="text-decoration-none">Monitoreo de Equipos</a></li>
            <li class="breadcrumb-item active fw-semibold" aria-current="page" id="breadcrumbHostname">Cargando Ficha...</li>
        </ol>
    </nav>

    <!-- Barra Superior con Botón de Regreso y Acciones -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="monitoreo_equipos.php" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2 px-3 py-1 rounded-3">
            <i class="bi bi-arrow-left"></i>
            <span class="fw-semibold">Volver a la Lista de Equipos</span>
        </a>
        <div class="d-flex gap-2">
            <button class="btn btn-light border btn-sm d-inline-flex align-items-center gap-2 px-3 py-1 rounded-3" onclick="window.print()">
                <i class="bi bi-printer"></i>
                <span>Imprimir Reporte</span>
            </button>
            <button class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-2 px-3 py-1 rounded-3" id="btnRecargar">
                <i class="bi bi-arrow-clockwise" id="iconoRecarga"></i>
                <span>Refrescar</span>
            </button>
        </div>
    </div>

    <!-- Spinner de Carga Inicial -->
    <div id="cargandoDetalle" class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white mb-4">
        <div class="spinner-border text-primary mx-auto mb-3" role="status"></div>
        <h5 class="text-dark fw-bold mb-1">Cargando telemetría del equipo...</h5>
        <p class="text-muted small mb-0">Consultando hardware, SMART y auditoría forense en PostgreSQL.</p>
    </div>

    <!-- Contenedor Principal de la Ficha (Oculto mientras carga) -->
    <div id="contenidoDetalle" style="display: none;">

        <!-- 1. Cabecera y Alertas del Equipo -->
        <?php require_once __DIR__ . '/componentes/cabecera_equipo.php'; ?>

        <!-- 2. Pestañas de Navegación de la Ficha -->
        <ul class="nav nav-pills gap-2 mb-4 p-2 bg-white rounded-3 shadow-sm border" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active px-4 py-2 rounded-3 d-flex align-items-center gap-2 fw-semibold" id="tab-hardware-tab" data-bs-toggle="pill" data-bs-target="#tab-hardware" type="button" role="tab">
                    <i class="bi bi-speedometer2"></i> Hardware & Sensores
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-4 py-2 rounded-3 d-flex align-items-center gap-2 fw-semibold" id="tab-seguridad-tab" data-bs-toggle="pill" data-bs-target="#tab-seguridad" type="button" role="tab">
                    <i class="bi bi-shield-check"></i> Software & Auditoría Forense
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-4 py-2 rounded-3 d-flex align-items-center gap-2 fw-semibold" id="tab-historial-tab" data-bs-toggle="pill" data-bs-target="#tab-historial" type="button" role="tab">
                    <i class="bi bi-clock-history"></i> Historial de Diagnósticos
                </button>
            </li>
        </ul>

        <!-- 3. Contenido de las Pestañas (Modulares) -->
        <div class="tab-content" id="pills-tabContent">
            <?php 
            require_once __DIR__ . '/componentes/tab_hardware.php';
            require_once __DIR__ . '/componentes/tab_seguridad.php';
            require_once __DIR__ . '/componentes/tab_historial.php';
            ?>
        </div>

    </div>
</main>

<!-- Controlador JavaScript Modular -->
<script src="../../recursos/js/detalle_equipo.js"></script>

<?php
require_once __DIR__ . '/../../componentes/pie_pagina.php';
?>
