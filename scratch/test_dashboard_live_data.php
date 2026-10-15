<?php
// Script de prueba para validar datos en vivo del Dashboard de Gestión Técnica
require_once __DIR__ . '/../back_form/configuracion/conexion_bd.php';

$conexion = obtener_conexion_bd();

$resumen = array();

// 1. Conteo Tickets Soporte
$stmt = $conexion->query("SELECT COUNT(*) FROM tickets_soporte");
$resumen['total_soporte'] = (int)$stmt->fetchColumn();

// 2. Conteo Incidencias
$stmt = $conexion->query("SELECT COUNT(*) FROM incidencias_sistemas");
$resumen['total_incidencias'] = (int)$stmt->fetchColumn();

// 3. Conteo Accesos
$stmt = $conexion->query("SELECT COUNT(*) FROM solicitudes_accesos");
$resumen['total_accesos'] = (int)$stmt->fetchColumn();

// 4. Conteo Mantenimiento
$stmt = $conexion->query("SELECT COUNT(*) FROM mantenimientos_preventivos");
$resumen['total_mantenimiento'] = (int)$stmt->fetchColumn();

// 5. Total Operativo
$resumen['total_operativo'] = $resumen['total_soporte'] + $resumen['total_incidencias'] + $resumen['total_accesos'] + $resumen['total_mantenimiento'];

// 6. Departamentos
$stmt = $conexion->query("
    SELECT depto, COUNT(*) as cant FROM (
        SELECT departamento_area as depto FROM tickets_soporte
        UNION ALL
        SELECT area_departamento as depto FROM solicitudes_accesos
        UNION ALL
        SELECT ubicacion_equipo as depto FROM mantenimientos_preventivos
    ) t GROUP BY depto ORDER BY cant DESC LIMIT 5
");
$resumen['top_departamentos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 7. Telemetria
$stmt = $conexion->query("
    SELECT estado_salud_general, count(*) as cant 
    FROM vista_ultimo_diagnostico_pc 
    GROUP BY estado_salud_general
");
$resumen['salud_telemetria'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($resumen, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
