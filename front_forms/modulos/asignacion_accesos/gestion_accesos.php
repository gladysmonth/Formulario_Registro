<?php
/**
 * Vista: Panel de Control y Bandeja de Solicitudes de Accesos
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/asignacion_accesos/gestion_accesos.php
 */

$titulo_pagina = 'Bandeja de Solicitudes de Accesos';
$pagina_activa = 'accesos_gestion';
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
            <li class="breadcrumb-item active" aria-current="page">Bandeja de Solicitudes</li>
        </ol>
    </nav>

    <!-- 1. Encabezado del Panel -->
    <?php require_once __DIR__ . '/componentes_gestion/panel_encabezado.php'; ?>

    <!-- 2. Tarjetas KPI de Métricas Rápidas -->
    <?php require_once __DIR__ . '/componentes_gestion/panel_metricas.php'; ?>

    <!-- 3. Filtros Dinámicos y Búsqueda -->
    <?php require_once __DIR__ . '/componentes_gestion/panel_filtros.php'; ?>

    <!-- 4. Tabla Responsiva de Solicitudes -->
    <?php require_once __DIR__ . '/componentes_gestion/panel_tabla.php'; ?>

    <!-- 5. Modal de Ficha Técnica Imprimible y Atención Técnica -->
    <?php require_once __DIR__ . '/componentes_gestion/modal_detalle.php'; ?>
</main>

<script src="<?php echo $nivel_ruta; ?>recursos/js/gestion_accesos.js"></script>

<?php require_once __DIR__ . '/../../componentes/pie_pagina.php'; ?>
