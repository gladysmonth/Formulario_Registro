<?php
/**
 * Servicio: Cálculo de KPIs Globales de Gestión Técnica (Bloque A)
 * Compatible con PHP 7.3
 * Archivo: back_form/api/metricas/servicios/servicio_metricas_kpi.php
 */

/**
 * Calcula los totales globales acumulados de tickets, incidencias, accesos y mantenimientos
 * @param PDO $conexion
 * @return array
 */
function calcular_kpis_globales($conexion) {
    // 1. Soporte Técnico
    $sql_soporte = "SELECT 
        COUNT(*) AS total,
        COUNT(*) FILTER (WHERE lower(estado) NOT IN ('resuelto', 'cerrado')) AS pendientes,
        COUNT(*) FILTER (WHERE lower(estado) IN ('resuelto', 'cerrado')) AS resueltos,
        COUNT(*) FILTER (WHERE lower(prioridad) IN ('urgente', 'alta') AND lower(estado) NOT IN ('resuelto', 'cerrado')) AS criticos
    FROM tickets_soporte";
    $kpi_soporte = $conexion->query($sql_soporte)->fetch();

    // 2. Incidencias de Sistemas
    $sql_incidencias = "SELECT 
        COUNT(*) AS total,
        COUNT(*) FILTER (WHERE lower(estado) NOT IN ('resuelta', 'cerrada')) AS pendientes,
        COUNT(*) FILTER (WHERE lower(estado) IN ('resuelta', 'cerrada')) AS resueltas,
        COUNT(*) FILTER (WHERE lower(nivel_criticidad) IN ('critica_nivel_1', 'alta_nivel_2') AND lower(estado) NOT IN ('resuelta', 'cerrada')) AS criticas
    FROM incidencias_sistemas";
    $kpi_incidencias = $conexion->query($sql_incidencias)->fetch();

    // 3. Asignación de Accesos
    $sql_accesos = "SELECT 
        COUNT(*) AS total,
        COUNT(*) FILTER (WHERE lower(estado) NOT IN ('atendido', 'rechazado')) AS pendientes,
        COUNT(*) FILTER (WHERE lower(estado) = 'atendido') AS atendidas
    FROM solicitudes_accesos";
    $kpi_accesos = $conexion->query($sql_accesos)->fetch();

    // 4. Mantenimiento Preventivo
    $sql_mp = "SELECT COUNT(*) AS total FROM mantenimientos_preventivos";
    $kpi_mp = $conexion->query($sql_mp)->fetch();

    // Normalizar valores a enteros en PHP 7.3
    $pendientes_sop = isset($kpi_soporte['pendientes']) ? (int)$kpi_soporte['pendientes'] : 0;
    $pendientes_inc = isset($kpi_incidencias['pendientes']) ? (int)$kpi_incidencias['pendientes'] : 0;
    $pendientes_acc = isset($kpi_accesos['pendientes']) ? (int)$kpi_accesos['pendientes'] : 0;
    $total_abiertos = $pendientes_sop + $pendientes_inc + $pendientes_acc;

    $criticos_sop   = isset($kpi_soporte['criticos']) ? (int)$kpi_soporte['criticos'] : 0;
    $criticos_inc   = isset($kpi_incidencias['criticas']) ? (int)$kpi_incidencias['criticas'] : 0;
    $total_criticos = $criticos_sop + $criticos_inc;

    $resueltos_sop   = isset($kpi_soporte['resueltos']) ? (int)$kpi_soporte['resueltos'] : 0;
    $resueltos_inc   = isset($kpi_incidencias['resueltas']) ? (int)$kpi_incidencias['resueltas'] : 0;
    $resueltos_acc   = isset($kpi_accesos['atendidas']) ? (int)$kpi_accesos['atendidas'] : 0;
    $total_resueltos = $resueltos_sop + $resueltos_inc + $resueltos_acc;

    $total_mp = isset($kpi_mp['total']) ? (int)$kpi_mp['total'] : 0;

    return array(
        'total_abiertos'       => $total_abiertos,
        'desglose_pendientes'  => array(
            'soporte'     => $pendientes_sop,
            'incidencias' => $pendientes_inc,
            'accesos'     => $pendientes_acc
        ),
        'total_criticos'       => $total_criticos,
        'total_resueltos'      => $total_resueltos,
        'total_mantenimientos' => $total_mp
    );
}
