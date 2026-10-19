<?php
/**
 * Vista: Panel de Monitoreo y Diagnóstico de Equipos PC
 * Compatible con PHP 7.3 y PostgreSQL 13
 * Archivo: monitoreo_equipos.php
 */

$titulo_pagina = 'Monitoreo y Diagnóstico de Equipos';
$pagina_activa = 'diagnostico_monitoreo';
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
            <li class="breadcrumb-item text-muted">Auditoría Técnica</li>
            <li class="breadcrumb-item active fw-semibold" aria-current="page">Monitoreo de Equipos PC</li>
        </ol>
    </nav>

    <!-- Banner / Encabezado Principal -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white border-start border-4 border-primary">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill fw-semibold">
                            <i class="bi bi-cpu-fill me-1"></i> Telemetría y Salud de Hardware
                        </span>
                        <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill small" id="contadorActualizacion">
                            <i class="bi bi-clock me-1"></i> Auto-refresco activo
                        </span>
                    </div>
                    <h1 class="h3 fw-bold text-dark mb-1">Panel de Diagnóstico y Monitoreo de PCs</h1>
                    <p class="text-muted small mb-0">
                        Visualización en tiempo real del estado de hardware (CPU, RAM, Discos/SMART), antivirus y aplicaciones de los puestos de trabajo.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2 px-3 py-2 rounded-3" id="btnActualizar">
                        <i class="bi bi-arrow-clockwise fs-6" id="iconoRefresco"></i>
                        <span class="fw-semibold">Actualizar Ahora</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 1. Tarjetas KPI de Resumen Global (Modular) -->
    <?php require_once __DIR__ . '/componentes/kpis_monitoreo.php'; ?>

    <!-- 2. Filtros y Tabla Principal de Monitoreo (Modular) -->
    <?php require_once __DIR__ . '/componentes/tabla_monitoreo.php'; ?>

</main>

<!-- Controlador JavaScript Modular -->
<script src="../../recursos/js/monitoreo_equipos.js"></script>

<?php
require_once __DIR__ . '/../../componentes/pie_pagina.php';
?>
