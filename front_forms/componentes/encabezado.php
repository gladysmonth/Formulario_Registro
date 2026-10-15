<?php
/**
 * Componente: Encabezado HTML y Carga de Estilos
 * Compatible con PHP 7.3
 * Archivo: encabezado.php
 */
if (!isset($titulo_pagina)) {
    $titulo_pagina = 'Portal de Formularios de Registro';
}

require_once __DIR__ . '/control_sesion.php';

// Ruta base relativa al root de front_forms
$ruta_base = isset($nivel_ruta) ? $nivel_ruta : '';

// Si la vista no define $omitir_control_sesion = true, exigir login obligatorio o rol específico
if (!isset($omitir_control_sesion) || !$omitir_control_sesion) {
    if (isset($roles_requeridos) && is_array($roles_requeridos)) {
        requerir_rol($roles_requeridos, $ruta_base);
    } else {
        requerir_autenticacion($ruta_base);
    }
}

$usuario_actual_sesion = obtener_usuario_sesion();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema Modular de Formularios de Registro Organizacional">
    <title><?php echo htmlspecialchars($titulo_pagina); ?> | Cosmol</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Estilos Personalizados del Sistema con control de caché -->
    <?php 
    $ruta_css_fisica = __DIR__ . '/../recursos/css/estilos_personalizados.css';
    $version_css = file_exists($ruta_css_fisica) ? filemtime($ruta_css_fisica) : time();
    ?>
    <link rel="stylesheet" href="<?php echo $ruta_base; ?>recursos/css/estilos_personalizados.css?v=<?php echo $version_css; ?>">
    <!-- Inyección dinámica de configuración de API Backend -->
    <?php
    $host_cliente = isset($_SERVER['HTTP_HOST']) ? explode(':', $_SERVER['HTTP_HOST'])[0] : '';
    if (!empty($host_cliente)) {
        $url_backend_final = 'http://' . $host_cliente . ':83';
    } else {
        $url_backend_final = getenv('URL_BACKEND') ?: 'http://192.168.11.70:83';
    }
    ?>
    <script>
        window.URL_BACKEND = <?php echo json_encode($url_backend_final); ?>;
        window.USUARIO_SESION = <?php echo json_encode($usuario_actual_sesion); ?>;
    </script>
</head>
<body class="bg-light d-flex flex-column min-vh-100 con-sidebar-cosmol">
