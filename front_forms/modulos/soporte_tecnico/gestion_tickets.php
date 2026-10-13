<?php
/**
 * Vista: Panel de Gestión y Seguimiento de Tickets de Soporte Técnico (Orquestador Maestro)
 * Compatible con PHP 7.3
 * Archivo: gestion_tickets.php
 */

$titulo_pagina = 'Panel de Gestión - Soporte Técnico';
$pagina_activa = 'soporte_gestion';
$nivel_ruta = '../../';
$roles_requeridos = array('admin', 'tecnico');

require_once __DIR__ . '/../../componentes/encabezado.php';
require_once __DIR__ . '/../../componentes/barra_navegacion.php';
?>

<main class="container my-4 my-lg-5">
    <!-- Migas de Pan (Breadcrumbs) -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="../../index.php" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item text-muted">Soporte Técnico</li>
            <li class="breadcrumb-item active" aria-current="page">Bandeja de Gestión de Tickets</li>
        </ol>
    </nav>

    <!-- 1. Encabezado del Panel -->
    <?php require_once __DIR__ . '/componentes_gestion/panel_encabezado.php'; ?>

    <!-- 2. Tarjetas de Métricas Rápidas (KPIs) -->
    <?php require_once __DIR__ . '/componentes_gestion/panel_metricas.php'; ?>

    <!-- 3. Barra de Filtros y Búsqueda -->
    <?php require_once __DIR__ . '/componentes_gestion/panel_filtros.php'; ?>

    <!-- 4. Tabla de Registros -->
    <?php require_once __DIR__ . '/componentes_gestion/panel_tabla.php'; ?>
</main>

<!-- 5. Modal de Atención y Cierre Técnico (Secciones 4, 5, 6 y Firma) -->
<?php require_once __DIR__ . '/componentes_gestion/modal_atencion.php'; ?>

<?php
$scripts_adicionales = array(
    'recursos/js/gestion_tickets.js'
);
require_once __DIR__ . '/../../componentes/pie_pagina.php';
?>
