<?php
/**
 * Controlador de Métricas y Estadísticas de Gestión Técnica
 * Método: GET
 * Compatible con PHP 7.3
 * Archivo: back_form/api/metricas/dashboard.php
 * 
 * Orquesta los servicios modulares de cálculo de KPIs, distribución mensual,
 * telemetría de hardware y alertas tempranas.
 */

require_once __DIR__ . '/../../configuracion/conexion_bd.php';
require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/seguridad_api.php';

// Cargar submódulos de cálculo especializados
require_once __DIR__ . '/servicios/servicio_metricas_kpi.php';
require_once __DIR__ . '/servicios/servicio_metricas_distribucion.php';
require_once __DIR__ . '/servicios/servicio_metricas_telemetria.php';
require_once __DIR__ . '/servicios/servicio_metricas_alertas.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método no permitido. Solo se permite GET.', 405);
}

try {
    $conexion = obtener_conexion_bd();

    // 1. Determinación de Parámetros de Período (Mes y Año)
    $mes_actual  = (int)date('n');
    $anio_actual = (int)date('Y');

    $mes  = isset($_GET['mes'])  ? (int)$_GET['mes']  : $mes_actual;
    $anio = isset($_GET['anio']) ? (int)$_GET['anio'] : $anio_actual;

    if ($mes < 1 || $mes > 12) {
        $mes = $mes_actual;
    }
    if ($anio < 2020 || $anio > 2035) {
        $anio = $anio_actual;
    }

    $nombres_meses = array(
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    );

    $mes_pad = str_pad($mes, 2, '0', STR_PAD_LEFT);
    $ultimo_dia_mes = (int)date('t', strtotime("{$anio}-{$mes_pad}-01"));

    $periodo = array(
        'inicio_mes_date' => "{$anio}-{$mes_pad}-01",
        'fin_mes_date'    => "{$anio}-{$mes_pad}-{$ultimo_dia_mes}",
        'inicio_mes_ts'   => "{$anio}-{$mes_pad}-01 00:00:00",
        'fin_mes_ts'      => "{$anio}-{$mes_pad}-{$ultimo_dia_mes} 23:59:59"
    );

    // 2. Ejecutar Servicios Modulares
    $kpis_globales       = calcular_kpis_globales($conexion);
    $dist_y_deptos       = calcular_distribucion_mensual($conexion, $periodo);
    $telemetria_hw       = obtener_telemetria_hardware($conexion);
    $eval_alerta         = evaluar_alerta_temprana($conexion, $telemetria_hw['criticos']);

    // Incorporar el nombre del sistema crítico identificado a los KPIs
    $kpis_globales['sistema_critico'] = $eval_alerta['sistema_critico'];

    // 3. Respuesta Consolidada
    $resultado = array(
        'periodo' => array(
            'mes'        => $mes,
            'anio'       => $anio,
            'nombre_mes' => isset($nombres_meses[$mes]) ? $nombres_meses[$mes] : 'Mes',
            'es_actual'  => ($mes === $mes_actual && $anio === $anio_actual),
            'rango'      => array(
                'inicio' => $periodo['inicio_mes_ts'],
                'fin'    => $periodo['fin_mes_ts']
            )
        ),
        'kpis_globales'        => $kpis_globales,
        'distribucion_mensual' => $dist_y_deptos['distribucion_mensual'],
        'top_departamentos'    => $dist_y_deptos['top_departamentos'],
        'telemetria_hardware'  => $telemetria_hw,
        'alerta_temprana'      => $eval_alerta['alerta_temprana']
    );

    responder_exito('Métricas consolidadas de gestión técnica obtenidas correctamente', $resultado);

} catch (Exception $e) {
    responder_error('Error al calcular métricas de gestión: ' . $e->getMessage(), 500);
}
