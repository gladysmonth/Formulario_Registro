<?php
/**
 * Endpoint: Listar Registros de Mantenimiento Preventivo con Filtros
 * Método: GET
 * Compatible con PHP 7.3
 * Archivo: listar_mantenimientos.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/conexion_bd.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método no permitido. Utilice GET', 405);
}

$tipo_equipo = isset($_GET['tipo_equipo']) ? strtoupper(trim($_GET['tipo_equipo'])) : '';
$buscar      = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$limite      = isset($_GET['limite']) ? (int)$_GET['limite'] : 50;
$offset      = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

if ($limite <= 0 || $limite > 200) $limite = 50;
if ($offset < 0) $offset = 0;

try {
    $conexion = obtener_conexion_bd();

    $clausulas = array();
    $parametros = array();

    if (!empty($tipo_equipo) && in_array($tipo_equipo, array('PC', 'LAPTOP'))) {
        $clausulas[] = "tipo_equipo = :tipo_equipo";
        $parametros[':tipo_equipo'] = $tipo_equipo;
    }

    if (!empty($buscar)) {
        $clausulas[] = "(codigo_mantenimiento ILIKE :buscar OR tecnico_responsable ILIKE :buscar OR ubicacion_equipo ILIKE :buscar OR nombre_equipo ILIKE :buscar OR codigo_activo ILIKE :buscar OR marca_modelo ILIKE :buscar)";
        $parametros[':buscar'] = '%' . $buscar . '%';
    }

    $donde = '';
    if (!empty($clausulas)) {
        $donde = 'WHERE ' . implode(' AND ', $clausulas);
    }

    // Conteo total con filtros
    $sql_conteo = "SELECT COUNT(*) FROM mantenimientos_preventivos {$donde}";
    $stmt_conteo = $conexion->prepare($sql_conteo);
    $stmt_conteo->execute($parametros);
    $total_registros = (int)$stmt_conteo->fetchColumn();

    // Consulta de registros
    $sql = "SELECT 
                id,
                codigo_mantenimiento,
                tecnico_responsable,
                fecha_mantenimiento,
                ubicacion_equipo,
                tipo_equipo,
                nombre_equipo,
                codigo_activo,
                marca_modelo,
                sistema_operativo,
                tipo_red,
                creado_en
            FROM mantenimientos_preventivos
            {$donde}
            ORDER BY creado_en DESC
            LIMIT :limite OFFSET :offset";

    $stmt = $conexion->prepare($sql);
    foreach ($parametros as $clave => $valor) {
        $stmt->bindValue($clave, $valor);
    }
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $registros = $stmt->fetchAll();

    // Métricas del dashboard de Mantenimiento Preventivo
    $stmt_metricas = $conexion->query("SELECT 
        COUNT(*) AS total,
        COUNT(CASE WHEN tipo_equipo = 'PC' THEN 1 END) AS total_pc,
        COUNT(CASE WHEN tipo_equipo = 'LAPTOP' THEN 1 END) AS total_laptop,
        COUNT(CASE WHEN date_trunc('month', fecha_mantenimiento) = date_trunc('month', CURRENT_DATE) THEN 1 END) AS este_mes
    FROM mantenimientos_preventivos");
    $metricas = $stmt_metricas->fetch();

    responder_exito('Registros de mantenimiento recuperados', array(
        'total'     => $total_registros,
        'limite'    => $limite,
        'offset'    => $offset,
        'metricas'  => $metricas,
        'registros' => $registros
    ));

} catch (Exception $e) {
    responder_error('Error al consultar mantenimientos: ' . $e->getMessage(), 500);
}
