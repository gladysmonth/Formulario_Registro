<?php
/**
 * Servicio: Telemetría y Salud de Hardware de Equipos (Bloque C)
 * Compatible con PHP 7.3
 * Archivo: back_form/api/metricas/servicios/servicio_metricas_telemetria.php
 */

/**
 * Obtiene el resumen de salud de los equipos y las alertas de hardware activas
 * @param PDO $conexion
 * @return array
 */
function obtener_telemetria_hardware($conexion) {
    // 1. Resumen consolidado desde la vista de último diagnóstico
    $sql_telemetria = "SELECT 
        COUNT(*) AS total,
        COUNT(*) FILTER (WHERE lower(estado_salud_general) IN ('optimo', 'bueno')) AS optimos,
        COUNT(*) FILTER (WHERE lower(estado_salud_general) = 'advertencia') AS advertencias,
        COUNT(*) FILTER (WHERE lower(estado_salud_general) = 'critico') AS criticos,
        COUNT(*) FILTER (WHERE antivirus_activo = TRUE) AS antivirus_activos
    FROM vista_ultimo_diagnostico_pc";
    
    $telemetria_kpi = $conexion->query($sql_telemetria)->fetch();

    $total_pcs     = isset($telemetria_kpi['total']) ? (int)$telemetria_kpi['total'] : 0;
    $pcs_optimas   = isset($telemetria_kpi['optimos']) ? (int)$telemetria_kpi['optimos'] : 0;
    $pcs_adv       = isset($telemetria_kpi['advertencias']) ? (int)$telemetria_kpi['advertencias'] : 0;
    $pcs_criticas  = isset($telemetria_kpi['criticos']) ? (int)$telemetria_kpi['criticos'] : 0;
    $antivirus_act = isset($telemetria_kpi['antivirus_activos']) ? (int)$telemetria_kpi['antivirus_activos'] : 0;
    $pct_antivirus = $total_pcs > 0 ? (int)round(($antivirus_act / $total_pcs) * 100) : 100;

    // 2. Alertas puntuales de hardware (fallos de SMART, disco C < 15GB, estado crítico, RAM alta)
    $sql_alertas_hw = "SELECT 
        hostname, 
        COALESCE(area, usuario_windows, 'Oficina') AS area, 
        estado_salud_general, 
        salud_disco_smart, 
        espacio_libre_c_gb, 
        uso_ram_porcentaje
    FROM vista_ultimo_diagnostico_pc
    WHERE lower(estado_salud_general) IN ('critico', 'advertencia')
       OR lower(coalesce(salud_disco_smart, '')) LIKE '%falla%'
       OR (espacio_libre_c_gb IS NOT NULL AND espacio_libre_c_gb < 15)
    ORDER BY 
        CASE WHEN lower(estado_salud_general) = 'critico' THEN 1 ELSE 2 END, 
        diagnostico_id DESC
    LIMIT 3";
    
    $alertas_hw_raw = $conexion->query($sql_alertas_hw)->fetchAll();

    $alertas_hardware = array();
    foreach ($alertas_hw_raw as $al) {
        $salud_smart = isset($al['salud_disco_smart']) ? $al['salud_disco_smart'] : '';
        $smart_falla = (stripos($salud_smart, 'falla') !== false);
        $disco_poco  = ($al['espacio_libre_c_gb'] !== null && (float)$al['espacio_libre_c_gb'] < 15);
        $estado_gen  = isset($al['estado_salud_general']) ? strtolower($al['estado_salud_general']) : '';
        $es_crit     = ($estado_gen === 'critico');

        $motivo = 'Alerta preventiva de memoria';
        $tipo   = 'warning';

        if ($smart_falla) {
            $motivo = 'Fallo SMART en Disco';
            $tipo   = 'danger';
        } elseif ($disco_poco) {
            $motivo = 'Disco C crítico (' . $al['espacio_libre_c_gb'] . 'GB libres)';
            $tipo   = 'danger';
        } elseif ($es_crit) {
            $motivo = 'Alerta crítica de hardware';
            $tipo   = 'danger';
        } elseif (!empty($al['uso_ram_porcentaje'])) {
            $motivo = 'Memoria elevada (' . $al['uso_ram_porcentaje'] . '% RAM)';
        }

        $alertas_hardware[] = array(
            'hostname' => !empty($al['hostname']) ? $al['hostname'] : 'PC',
            'area'     => !empty($al['area']) ? $al['area'] : 'Oficina',
            'motivo'   => $motivo,
            'tipo'     => $tipo
        );
    }

    return array(
        'total_pcs'            => $total_pcs,
        'optimos'              => $pcs_optimas,
        'advertencias'         => $pcs_adv,
        'criticos'             => $pcs_criticas,
        'antivirus_activos'    => $antivirus_act,
        'porcentaje_antivirus' => $pct_antivirus,
        'alertas'              => $alertas_hardware
    );
}
