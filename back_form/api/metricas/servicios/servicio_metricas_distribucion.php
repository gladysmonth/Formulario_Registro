<?php
/**
 * Servicio: Cálculo de Distribución Mensual y Top Departamentos (Bloque B)
 * Compatible con PHP 7.3
 * Archivo: back_form/api/metricas/servicios/servicio_metricas_distribucion.php
 */

/**
 * Calcula los totales y porcentajes del mes solicitado para la gráfica dona y el ranking departamental
 * @param PDO $conexion
 * @param array $periodo Array con inicio_mes_date, fin_mes_date, inicio_mes_ts, fin_mes_ts
 * @return array
 */
function calcular_distribucion_mensual($conexion, $periodo) {
    $inicio_mes_date = $periodo['inicio_mes_date'];
    $fin_mes_date    = $periodo['fin_mes_date'];
    $inicio_mes_ts   = $periodo['inicio_mes_ts'];
    $fin_mes_ts      = $periodo['fin_mes_ts'];

    // 1. Conteo Soporte en el mes
    $stmt_m_sop = $conexion->prepare("SELECT COUNT(*) FROM tickets_soporte 
        WHERE (fecha_solicitud >= :ini_d AND fecha_solicitud <= :fin_d) 
           OR (creado_en >= :ini_ts AND creado_en <= :fin_ts)");
    $stmt_m_sop->execute(array(
        ':ini_d'  => $inicio_mes_date,
        ':fin_d'  => $fin_mes_date,
        ':ini_ts' => $inicio_mes_ts,
        ':fin_ts' => $fin_mes_ts
    ));
    $conteo_mes_sop = (int)$stmt_m_sop->fetchColumn();

    // 2. Conteo Incidencias en el mes
    $stmt_m_inc = $conexion->prepare("SELECT COUNT(*) FROM incidencias_sistemas 
        WHERE (fecha_hora_reporte >= :ini_ts AND fecha_hora_reporte <= :fin_ts) 
           OR (creado_en >= :ini_ts AND creado_en <= :fin_ts)");
    $stmt_m_inc->execute(array(
        ':ini_ts' => $inicio_mes_ts,
        ':fin_ts' => $fin_mes_ts
    ));
    $conteo_mes_inc = (int)$stmt_m_inc->fetchColumn();

    // 3. Conteo Accesos en el mes
    $stmt_m_acc = $conexion->prepare("SELECT COUNT(*) FROM solicitudes_accesos 
        WHERE (fecha_solicitud >= :ini_ts AND fecha_solicitud <= :fin_ts) 
           OR (creado_en >= :ini_ts AND creado_en <= :fin_ts)");
    $stmt_m_acc->execute(array(
        ':ini_ts' => $inicio_mes_ts,
        ':fin_ts' => $fin_mes_ts
    ));
    $conteo_mes_acc = (int)$stmt_m_acc->fetchColumn();

    // 4. Conteo Mantenimiento en el mes
    $stmt_m_mp = $conexion->prepare("SELECT COUNT(*) FROM mantenimientos_preventivos 
        WHERE (fecha_mantenimiento >= :ini_d AND fecha_mantenimiento <= :fin_d) 
           OR (creado_en >= :ini_ts AND creado_en <= :fin_ts)");
    $stmt_m_mp->execute(array(
        ':ini_d'  => $inicio_mes_date,
        ':fin_d'  => $fin_mes_date,
        ':ini_ts' => $inicio_mes_ts,
        ':fin_ts' => $fin_mes_ts
    ));
    $conteo_mes_mp = (int)$stmt_m_mp->fetchColumn();

    $total_mes = $conteo_mes_sop + $conteo_mes_inc + $conteo_mes_acc + $conteo_mes_mp;

    $pct_sop = $total_mes > 0 ? (int)round(($conteo_mes_sop / $total_mes) * 100) : 0;
    $pct_inc = $total_mes > 0 ? (int)round(($conteo_mes_inc / $total_mes) * 100) : 0;
    $pct_acc = $total_mes > 0 ? (int)round(($conteo_mes_acc / $total_mes) * 100) : 0;
    $pct_mp  = $total_mes > 0 ? max(0, 100 - ($pct_sop + $pct_inc + $pct_acc)) : 0;

    // Formulario de mayor demanda en el mes
    $lista_demanda = array(
        array('nombre' => 'Soporte Tecn.',   'cant' => $conteo_mes_sop),
        array('nombre' => 'Incidencias',     'cant' => $conteo_mes_inc),
        array('nombre' => 'Accesos',         'cant' => $conteo_mes_acc),
        array('nombre' => 'Mant.Preventivo', 'cant' => $conteo_mes_mp)
    );
    usort($lista_demanda, function($a, $b) {
        return $b['cant'] - $a['cant'];
    });

    $mayor_demanda_nombre = ($total_mes > 0) ? $lista_demanda[0]['nombre'] : 'Ninguno';
    $mayor_demanda_cant   = ($total_mes > 0) ? $lista_demanda[0]['cant'] : 0;

    // Top 5 Departamentos / Áreas consolidadas
    $sql_top_deptos = "WITH areas_consolidadas AS (
        SELECT departamento_area AS area FROM tickets_soporte WHERE departamento_area IS NOT NULL AND trim(departamento_area) != ''
        UNION ALL
        SELECT area_departamento AS area FROM solicitudes_accesos WHERE area_departamento IS NOT NULL AND trim(area_departamento) != ''
        UNION ALL
        SELECT CASE 
            WHEN sistema_erp_sai = TRUE THEN 'ERP - SAI (Facturación)' 
            WHEN sistema_cobranzas_netcob = TRUE THEN 'Cobranzas - NETCOB' 
            WHEN sistema_otros_detalle IS NOT NULL AND trim(sistema_otros_detalle) != '' THEN sistema_otros_detalle 
            ELSE 'Infraestructura TI' 
        END AS area 
        FROM incidencias_sistemas
        UNION ALL
        SELECT ubicacion_equipo AS area FROM mantenimientos_preventivos WHERE ubicacion_equipo IS NOT NULL AND trim(ubicacion_equipo) != ''
    )
    SELECT initcap(trim(area)) AS nombre, COUNT(*) AS total
    FROM areas_consolidadas
    WHERE trim(area) != ''
    GROUP BY initcap(trim(area))
    ORDER BY total DESC
    LIMIT 5";
    $top_deptos_raw = $conexion->query($sql_top_deptos)->fetchAll();

    $sql_total_areas = "WITH areas_consolidadas AS (
        SELECT departamento_area AS area FROM tickets_soporte WHERE departamento_area IS NOT NULL AND trim(departamento_area) != ''
        UNION ALL
        SELECT area_departamento AS area FROM solicitudes_accesos WHERE area_departamento IS NOT NULL AND trim(area_departamento) != ''
        UNION ALL
        SELECT CASE 
            WHEN sistema_erp_sai = TRUE THEN 'ERP - SAI (Facturación)' 
            WHEN sistema_cobranzas_netcob = TRUE THEN 'Cobranzas - NETCOB' 
            WHEN sistema_otros_detalle IS NOT NULL AND trim(sistema_otros_detalle) != '' THEN sistema_otros_detalle 
            ELSE 'Infraestructura TI' 
        END AS area 
        FROM incidencias_sistemas
        UNION ALL
        SELECT ubicacion_equipo AS area FROM mantenimientos_preventivos WHERE ubicacion_equipo IS NOT NULL AND trim(ubicacion_equipo) != ''
    )
    SELECT COUNT(DISTINCT initcap(trim(area))) FROM areas_consolidadas WHERE trim(area) != ''";
    $total_areas_distintas = (int)$conexion->query($sql_total_areas)->fetchColumn();

    $suma_top5 = 0;
    foreach ($top_deptos_raw as $d) {
        $suma_top5 += (int)$d['total'];
    }

    $top_deptos = array();
    foreach ($top_deptos_raw as $d) {
        $cant = (int)$d['total'];
        $pct  = $suma_top5 > 0 ? (int)round(($cant / $suma_top5) * 100) : 0;
        $top_deptos[] = array(
            'nombre'     => $d['nombre'],
            'total'      => $cant,
            'porcentaje' => $pct
        );
    }

    $area_mayor_demanda = !empty($top_deptos) ? $top_deptos[0]['nombre'] : 'Ninguna';

    return array(
        'distribucion_mensual' => array(
            'total_mes'     => $total_mes,
            'conteos'       => array(
                'soporte'       => $conteo_mes_sop,
                'incidencias'   => $conteo_mes_inc,
                'accesos'       => $conteo_mes_acc,
                'mantenimiento' => $conteo_mes_mp
            ),
            'porcentajes'   => array(
                'soporte'       => $pct_sop,
                'incidencias'   => $pct_inc,
                'accesos'       => $pct_acc,
                'mantenimiento' => $pct_mp
            ),
            'mayor_demanda' => array(
                'formulario' => $mayor_demanda_nombre,
                'cantidad'   => $mayor_demanda_cant
            )
        ),
        'top_departamentos' => array(
            'total_areas' => $total_areas_distintas,
            'area_mayor'  => $area_mayor_demanda,
            'ranking'     => $top_deptos
        )
    );
}
