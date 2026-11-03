<?php
/**
 * Vista Principal: Gestión de Usuarios y Roles (Exclusivo Administrador)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/usuarios/gestion_usuarios.php
 */

$titulo_pagina = 'Gestión de Usuarios y Roles';
$pagina_activa = 'gestion_usuarios';
$nivel_ruta = '../../';
$roles_requeridos = array('admin');

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
                    <li class="breadcrumb-item"><a href="<?php echo $nivel_ruta; ?>metricas.php" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Gestión de Usuarios</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock-fill text-primary"></i>
                <span>Gestión de Usuarios y Roles</span>
            </h1>
        </div>
        <div>
            <a href="<?php echo $nivel_ruta; ?>metricas.php" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i>
                <span>Volver al Dashboard</span>
            </a>
        </div>
    </div>

    <!-- Bloque 1: Tarjetas KPI -->
    <?php require_once __DIR__ . '/componentes/kpis_usuarios.php'; ?>

    <!-- Bloque 2: Tabla de Usuarios con Filtros y Buscador -->
    <?php require_once __DIR__ . '/componentes/tabla_usuarios.php'; ?>

    <!-- Bloque 3: Modal Emergente para Crear/Editar Usuario -->
    <?php require_once __DIR__ . '/componentes/modal_usuario.php'; ?>
</main>

<?php 
$scripts_adicionales = array('recursos/js/gestion_usuarios.js');
require_once __DIR__ . '/../../componentes/pie_pagina.php'; 
?>

