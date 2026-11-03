<?php
/**
 * Vista Principal: Seguimiento y Consulta de Mis Solicitudes
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/mis_solicitudes/mis_solicitudes.php
 */

$titulo_pagina = 'Mis Solicitudes y Seguimiento';
$pagina_activa = 'mis_solicitudes';
$nivel_ruta = '../../';

require_once __DIR__ . '/../../componentes/encabezado.php';
require_once __DIR__ . '/../../componentes/barra_navegacion.php';
?>

<main class="container-fluid px-2 px-sm-3 px-md-4 py-3 py-md-4">
    <!-- Encabezado y Navegación de Migas de Pan -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?php echo $nivel_ruta; ?>index.php" class="text-decoration-none">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mis Solicitudes</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-primary"></i>
                <span>Mis Solicitudes y Seguimiento</span>
            </h1>
        </div>
        <div>
            <a href="<?php echo $nivel_ruta; ?>index.php" class="btn btn-primary btn-sm d-flex align-items-center gap-1 fw-semibold">
                <i class="bi bi-pencil-square"></i>
                <span>Llenar Nuevo Formulario</span>
            </a>
        </div>
    </div>

    <!-- Bloque 1: Resumen KPI de Solicitudes -->
    <?php require_once __DIR__ . '/componentes/kpis_mis_solicitudes.php'; ?>

    <!-- Bloque 2: Tabla de Solicitudes con Filtros y Buscador -->
    <?php require_once __DIR__ . '/componentes/tabla_mis_solicitudes.php'; ?>

    <!-- Bloque 3: Modal de Detalle y Estado -->
    <?php require_once __DIR__ . '/componentes/modal_detalle_solicitud.php'; ?>
</main>

<?php 
$scripts_adicionales = array('recursos/js/mis_solicitudes.js');
require_once __DIR__ . '/../../componentes/pie_pagina.php'; 
?>

