<?php
/**
 * Endpoint API: Listar Equipos con su Último Diagnóstico, Filtros y Métricas de Salud
 * Compatible con PHP 7.3 y PostgreSQL 13
 * Ruta: /api/diagnostico/listar.php
 */

require_once __DIR__ . '/../../configuracion/conexion_bd.php';
require_once __DIR__ . '/../../configuracion/respuestas_api.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método no permitido. Solo se acepta GET.', 405);
}

$estado_salud = isset($_GET['estado_salud']) ? trim($_GET['estado_salud']) : '';
$buscar       = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$limite       = isset($_GET['limite']) ? (int)$_GET['limite'] : 50;
$offset       = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

if ($limite <= 0 || $limite > 200) {
    $limite = 50;
}
if ($offset < 0) {
    $offset = 0;
}

try {
    $conexion = obtener_conexion_bd();

    $clausulas = array();
    $parametros = array();

    if (!empty($estado_salud) && $estado_salud !== 'todos') {
        $clausulas[] = "estado_salud_general = :estado_salud";
        $parametros[':estado_salud'] = $estado_salud;
    }

    if (!empty($buscar)) {
        $clausulas[] = "(hostname ILIKE :buscar 
                     OR direccion_mac ILIKE :buscar 
                     OR ip_local ILIKE :buscar 
                     OR usuario_windows ILIKE :buscar 
                     OR area ILIKE :buscar 
                     OR encargado ILIKE :buscar 
                     OR codigo_activo ILIKE :buscar 
                     OR marca_modelo ILIKE :buscar)";
        $parametros[':buscar'] = '%' . $buscar . '%';
    }

    $donde = '';
    if (!empty($clausulas)) {
        $donde = 'WHERE ' . implode(' AND ', $clausulas);
    }

    // 1. Conteo total con filtros
    $sql_conteo = "SELECT COUNT(*) FROM vista_ultimo_diagnostico_pc {$donde}";
    $stmt_conteo = $conexion->prepare($sql_conteo);
    $stmt_conteo->execute($parametros);
    $total_equipos = (int)$stmt_conteo->fetchColumn();

    // 2. Consulta de registros
    $sql = "
        SELECT 
            diagnostico_id,
            equipo_id,
            direccion_mac,
            hostname,
            usuario_windows,
            ip_local,
            tipo_evento,
            fecha_diagnostico,
            estado_salud_general,
            temperatura_cpu,
            uso_cpu_porcentaje,
            uso_ram_porcentaje,
            espacio_libre_c_gb,
            salud_disco_smart,
            antivirus_activo,
            amenazas_activas,
            detalle_cpu,
            detalle_ram,
            detalle_almacenamiento,
            alertas_detectadas,
            codigo_activo,
            area,
            encargado,
            marca_modelo
        FROM vista_ultimo_diagnostico_pc
        {$donde}
        ORDER BY fecha_diagnostico DESC
        LIMIT :limite OFFSET :offset
    ";

    $stmt = $conexion->prepare($sql);
    foreach ($parametros as $clave => $valor) {
        $stmt->bindValue($clave, $valor);
    }
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Decodificar campos JSONB si vienen como string desde PDO
    foreach ($equipos as &$eq) {
        $eq['diagnostico_id'] = (int)$eq['diagnostico_id'];
        $eq['equipo_id']      = $eq['equipo_id'] ? (int)$eq['equipo_id'] : null;
        if (isset($eq['detalle_cpu']) && is_string($eq['detalle_cpu'])) {
            $eq['detalle_cpu'] = json_decode($eq['detalle_cpu'], true);
        }
        if (isset($eq['detalle_ram']) && is_string($eq['detalle_ram'])) {
            $eq['detalle_ram'] = json_decode($eq['detalle_ram'], true);
        }
        if (isset($eq['detalle_almacenamiento']) && is_string($eq['detalle_almacenamiento'])) {
            $eq['detalle_almacenamiento'] = json_decode($eq['detalle_almacenamiento'], true);
        }
        if (isset($eq['alertas_detectadas']) && is_string($eq['alertas_detectadas'])) {
            $eq['alertas_detectadas'] = json_decode($eq['alertas_detectadas'], true);
        }
    }

    // 3. Métricas globales de salud de equipos
    $stmt_metricas = $conexion->query("
        SELECT 
            COUNT(*) AS total,
            COUNT(CASE WHEN estado_salud_general = 'optimo' THEN 1 END) AS optimos,
            COUNT(CASE WHEN estado_salud_general = 'bueno' THEN 1 END) AS buenos,
            COUNT(CASE WHEN estado_salud_general = 'advertencia' THEN 1 END) AS advertencias,
            COUNT(CASE WHEN estado_salud_general = 'critico' THEN 1 END) AS criticos
        FROM vista_ultimo_diagnostico_pc
    ");
    $metricas = $stmt_metricas->fetch(PDO::FETCH_ASSOC);

    responder_exito('Lista de equipos obtenida correctamente', array(
        'total'    => $total_equipos,
        'limite'   => $limite,
        'offset'   => $offset,
        'metricas' => $metricas,
        'equipos'  => $equipos
    ));

} catch (Exception $e) {
    responder_error('Error al consultar lista de equipos: ' . $e->getMessage(), 500);
}
