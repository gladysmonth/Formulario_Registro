<?php
/**
 * Verificación de Estado del Backend (Healthcheck y Catálogo de APIs)
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

responder_exito('Servicio Backend de Formularios y Auditoría Activo', array(
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
        'autenticacion' => array(
            'login' => 'POST /api/auth/login.php',
            'perfil' => 'GET /api/auth/perfil.php?id={id}'
        ),
        'soporte_tecnico' => array(
            'crear_ticket' => 'POST /api/soporte_tecnico/crear_ticket.php',
            'listar_tickets' => 'GET /api/soporte_tecnico/listar_tickets.php',
            'obtener_ticket' => 'GET /api/soporte_tecnico/obtener_ticket.php?id={id}',
            'actualizar_ticket' => 'POST /api/soporte_tecnico/actualizar_ticket.php',
            'buscar_equipos' => 'GET /api/soporte_tecnico/buscar_equipos.php?q={termino}'
        ),
        'mantenimiento_preventivo' => array(
            'crear_mantenimiento' => 'POST /api/mantenimiento_preventivo/crear_mantenimiento.php',
            'listar_mantenimientos' => 'GET /api/mantenimiento_preventivo/listar_mantenimientos.php',
            'obtener_mantenimiento' => 'GET /api/mantenimiento_preventivo/obtener_mantenimiento.php?id={id}'
        ),
        'registro_incidencias' => array(
            'crear_incidencia' => 'POST /api/registro_incidencias/crear_incidencia.php',
            'listar_incidencias' => 'GET /api/registro_incidencias/listar_incidencias.php',
            'obtener_incidencia' => 'GET /api/registro_incidencias/obtener_incidencia.php?id={id}',
            'actualizar_incidencia' => 'POST /api/registro_incidencias/actualizar_incidencia.php'
        ),
        'asignacion_accesos' => array(
            'crear_solicitud' => 'POST /api/asignacion_accesos/crear_solicitud.php',
            'listar_solicitudes' => 'GET /api/asignacion_accesos/listar_solicitudes.php',
            'obtener_solicitud' => 'GET /api/asignacion_accesos/obtener_solicitud.php?id={id}',
            'actualizar_solicitud' => 'POST /api/asignacion_accesos/actualizar_solicitud.php'
        ),
        'diagnostico_pc' => array(
            'recibir_telemetria' => 'POST /api/diagnostico/recibir.php',
            'listar_equipos' => 'GET /api/diagnostico/listar.php',
            'detalle_equipo' => 'GET /api/diagnostico/detalle.php?mac={mac}'
        )
    ),
    'fecha_hora_servidor' => date('Y-m-d H:i:s')
));