<?php
/**
 * Servicio: Evaluación de Alerta Temprana en Tiempo Real (Bloque D)
 * Compatible con PHP 7.3
 * Archivo: back_form/api/metricas/servicios/servicio_metricas_alertas.php
 */

/**
 * Evalúa incidencias críticas no resueltas o fallos de hardware para activar alertas institucionales
 * @param PDO $conexion
 * @param int $pcs_criticas Cantidad de equipos con estado de salud crítico
 * @return array
 */
function evaluar_alerta_temprana($conexion, $pcs_criticas = 0) {
    // 1. Buscar la incidencia crítica más reciente no resuelta
    $sql_primera_critica = "SELECT 
        nro_incidencia,
        sistema_erp_sai,
        sistema_cobranzas_netcob,
        sistema_otros_detalle
    FROM incidencias_sistemas
    WHERE lower(nivel_criticidad) IN ('critica_nivel_1', 'alta_nivel_2') 
      AND lower(estado) NOT IN ('resuelta', 'cerrada')
    ORDER BY 
        CASE WHEN lower(nivel_criticidad) = 'critica_nivel_1' THEN 1 ELSE 2 END,
        fecha_hora_reporte DESC
    LIMIT 1";
    
    $primera_critica = $conexion->query($sql_primera_critica)->fetch();

    $hay_alerta          = false;
    $tipo_alerta         = 'operativo';
    $titulo_alerta       = 'Sistemas Institucionales Estables';
    $mensaje_alerta      = 'No se registran incidencias críticas ni alertas de hardware pendientes en este momento.';
    $url_accion          = null;
    $texto_boton_accion  = null;
    $sistema_critico_nom = null;

    if ($primera_critica) {
        $hay_alerta = true;
        $tipo_alerta = 'danger';
        $sis_nombre = 'Plataforma Crítica';

        if (!empty($primera_critica['sistema_erp_sai'])) {
            $sis_nombre = 'ERP - SAI (Facturación)';
        } elseif (!empty($primera_critica['sistema_cobranzas_netcob'])) {
            $sis_nombre = 'Cobranzas - NETCOB';
        } elseif (!empty($primera_critica['sistema_otros_detalle'])) {
            $sis_nombre = $primera_critica['sistema_otros_detalle'];
        }

        $sistema_critico_nom = $sis_nombre;
        $codigo_inc = !empty($primera_critica['nro_incidencia']) ? $primera_critica['nro_incidencia'] : 'INC-CRÍTICA';
        
        $titulo_alerta      = 'Atención Inmediata en Formulario de Incidencias:';
        $mensaje_alerta     = "Incidencia Crítica <strong>[{$codigo_inc}]</strong> en <strong>{$sis_nombre}</strong> requiere atención técnica.";
        $url_accion         = 'modulos/registro_incidencias/gestion_incidencias.php';
        $texto_boton_accion = 'Ir a atender';

    } elseif ($pcs_criticas > 0) {
        $hay_alerta         = true;
        $tipo_alerta        = 'warning';
        $titulo_alerta      = 'Atención de Hardware:';
        $mensaje_alerta     = "Se detectaron <strong>{$pcs_criticas}</strong> equipos con fallas críticas de SMART o temperatura en la red.";
        $url_accion         = 'modulos/diagnostico/monitoreo_equipos.php';
        $texto_boton_accion = 'Ver Equipos';
    }

    return array(
        'alerta_temprana' => array(
            'hay_alerta'         => $hay_alerta,
            'tipo'               => $tipo_alerta,
            'titulo'             => $titulo_alerta,
            'mensaje'            => $mensaje_alerta,
            'url_accion'         => $url_accion,
            'texto_boton_accion' => $texto_boton_accion
        ),
        'sistema_critico' => $sistema_critico_nom
    );
}
