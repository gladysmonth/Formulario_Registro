<?php
/**
 * Componente: Encabezado HTML y Carga de Estilos
 * Compatible con PHP 7.3
 * Archivo: encabezado.php
 */
if (!isset($titulo_pagina)) {
    $titulo_pagina = 'Portal de Formularios de Registro';
}

// Ruta base relativa al root de front_forms
$ruta_base = isset($nivel_ruta) ? $nivel_ruta : '';
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

    <!-- Estilos Personalizados del Sistema -->
    <link rel="stylesheet" href="<?php echo $ruta_base; ?>recursos/css/estilos_personalizados.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
