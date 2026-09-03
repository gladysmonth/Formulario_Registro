<?php
/**
 * Verificación de Estado del Backend (Healthcheck)
 * Compatible con PHP 7.3
 * Archivo: index.php
 */

require_once __DIR__ . '/configuracion/respuestas_api.php';
require_once __DIR__ . '/configuracion/conexion_bd.php';

configurar_cors();

$estado_bd = 'desconectado';
$mensaje_bd = '';

try {
    $conexion = obtener_conexion_bd();
    $sentencia = $conexion->query('SELECT version()');
    $version_bd = $sentencia->fetchColumn();
    $estado_bd = 'conectado';
    $mensaje_bd = $version_bd;
} catch (Exception $e) {
    $estado_bd = 'error_conexion';
    $mensaje_bd = $e->getMessage();
}

responder_exito('Servicio Backend de Formularios de Registro Activo', array(
    'servicio' => 'back_form',
    'version_php' => phpversion(),
    'extensiones_cargadas' => array(
        'pdo' => extension_loaded('pdo'),
        'pdo_pgsql' => extension_loaded('pdo_pgsql'),
        'pgsql' => extension_loaded('pgsql')
    ),
    'base_de_datos' => array(
        'estado' => $estado_bd,
        'detalle' => $mensaje_bd
    ),
    'modulos_disponibles' => array(
        'soporte_tecnico' => array(
            'crear_ticket' => 'POST /api/soporte_tecnico/crear_ticket.php',
            'listar_tickets' => 'GET /api/soporte_tecnico/listar_tickets.php',
            'obtener_ticket' => 'GET /api/soporte_tecnico/obtener_ticket.php?id={id}',
            'actualizar_ticket' => 'POST /api/soporte_tecnico/actualizar_ticket.php'
        )
    ),
    'fecha_hora_servidor' => date('Y-m-d H:i:s')
));
