<?php
/**
 * Estado y Metadatos del Módulo de Registro de Incidencias de Sistemas
 * Compatible con PHP 7.3
 * Archivo: back_form/api/registro_incidencias/index.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';

configurar_cors();

responder_exito('API de Registro de Incidencias de Sistemas activa', array(
    'modulo'      => 'Registro de Incidencias de Sistemas',
    'endpoints'   => array(
        'POST /crear_incidencia.php'      => 'Registrar nueva incidencia con firmas digitales',
        'GET /listar_incidencias.php'     => 'Listar incidencias con filtros y métricas KPI',
        'GET /obtener_incidencia.php'     => 'Obtener ficha técnica completa por ID o código',
        'POST /actualizar_incidencia.php' => 'Actualizar resolución, cierre y firma técnica'
    )
));
