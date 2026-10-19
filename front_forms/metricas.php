<?php
/**
 * Vista: Pantalla Completa de Métricas y Monitoreo Institucional
 * FASE 4: Centro de Control y Dashboard
 * Compatible con PHP 7.3
 * Archivo: front_forms/metricas.php
 */

$titulo_pagina = 'MÉTRICA - Dashboard de Monitoreo';
$pagina_activa = 'metricas';
$nivel_ruta = '';
$roles_requeridos = array('admin', 'tecnico');

require_once __DIR__ . '/componentes/encabezado.php';
require_once __DIR__ . '/componentes/barra_navegacion.php';

$usuario_actual_sesion = obtener_usuario_sesion();
$rol_actual = obtener_rol_actual();
?>

<main class="container my-4 my-md-5">
    
    <!-- Cabecera de la Pantalla de Métricas con Breadcrumbs y Acciones -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">
                        <i class="bi bi-speedometer2 me-1"></i>Panel Principal
                    </li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <span class="badge bg-primary p-2 rounded-3">
                    <i class="bi bi-speedometer2 fs-4"></i>
                </span>
                <span>Dashboard de Gestión Técnica</span>
            </h1>
            <p class="text-muted small mb-0 mt-1">
                Monitoreo en tiempo real de requerimientos institucionales, salud de hardware y filtración rápida de tickets &bull; Cosmol R.L.
            </p>
        </div>

        <!-- Botones de Acción Superior -->
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill small d-none d-sm-inline-flex align-items-center">
                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Conexión en Vivo
            </span>
            <button type="button" class="btn btn-outline-primary btn-sm px-3 py-2 d-flex align-items-center gap-2 rounded-3 shadow-sm" onclick="actualizarDashboardMetricas()" id="btnRefrescarMetricas" title="Actualizar datos desde la base de datos">
                <i class="bi bi-arrow-clockwise fs-6" id="iconoRefrescarMetricas"></i>
                <span class="fw-semibold">Actualizar Datos</span>
            </button>
        </div>
    </div>

    <!-- 1. BLOQUE A: 4 KPIs de Tensión y Atención Operativa (Modular) -->
    <?php require_once __DIR__ . '/componentes_metricas/bloque_kpis_operativos.php'; ?>

    <!-- 2. BANNER DE ALERTA TEMPRANA EN TIEMPO REAL (Modular) -->
    <?php require_once __DIR__ . '/componentes_metricas/bloque_alerta_temprana.php'; ?>

    <!-- 3. BLOQUE B: Distribución Mensual por Formulario y Demanda por Áreas (Modular) -->
    <?php require_once __DIR__ . '/componentes_metricas/bloque_distribucion_departamentos.php'; ?>

    <!-- 4. BLOQUE C: Monitoreo Activo de Hardware y Telemetría en Vivo de PCs (Modular) -->
    <?php require_once __DIR__ . '/componentes_metricas/bloque_telemetria_hardware.php'; ?>

</main>

<!-- Dependencias de Gráficos y Controlador JavaScript Modular -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="recursos/js/metricas_dashboard.js"></script>

<style>
.tarjeta-kpi-cosmol {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.tarjeta-kpi-cosmol:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
}
.spin-animado {
    animation: rotar 0.8s linear infinite;
}
@keyframes rotar {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<?php
require_once __DIR__ . '/componentes/pie_pagina.php';
?>
