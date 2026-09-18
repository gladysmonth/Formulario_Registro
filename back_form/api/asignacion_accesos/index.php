<?php
/**
 * Estado y Metadatos del Módulo de Asignación de Accesos de Usuarios de Sistemas
 * Compatible con PHP 7.3
 * Archivo: back_form/api/asignacion_accesos/index.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';

configurar_cors();

responder_exito('API de Asignación de Accesos de Usuarios de Sistemas activa', array(
    'modulo'    => 'Creación y Asignación de Accesos de Usuarios de Sistemas',
    'endpoints' => array(
        'POST /crear_solicitud.php'      => 'Registrar nueva solicitud con perfiles y firmas',
        'GET /listar_solicitudes.php'    => 'Listar solicitudes con filtros y métricas KPI',
        'GET /obtener_solicitud.php'     => 'Obtener ficha técnica completa por ID o código',
        'POST /actualizar_solicitud.php' => 'Actualizar atención técnica, comentarios y estado'
    )
));
