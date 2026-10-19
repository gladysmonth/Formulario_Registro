<?php
/**
 * Endpoint: Listar Incidencias de Sistemas con Filtros y Métricas KPI
 * Método: GET
 * Compatible con PHP 7.3
 * Archivo: back_form/api/registro_incidencias/listar_incidencias.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/conexion_bd.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método no permitido. Utilice GET', 405);
}

try {
    $conexion = obtener_conexion_bd();

    // Parámetros de filtrado
    $buscar      = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
    $criticidad  = isset($_GET['criticidad']) ? trim($_GET['criticidad']) : '';
    $estado      = isset($_GET['estado']) ? trim($_GET['estado']) : '';
    $sistema     = isset($_GET['sistema']) ? trim($_GET['sistema']) : '';

    $condiciones = array();
    $params      = array();

    if (!empty($buscar)) {
        $condiciones[] = "(nro_incidencia ILIKE :buscar OR responsable_reporte ILIKE :buscar OR descripcion_tecnica_logs ILIKE :buscar OR accion_realizada ILIKE :buscar)";
        $params[':buscar'] = '%' . $buscar . '%';
    }

    if (!empty($criticidad)) {
        $condiciones[] = "nivel_criticidad = :criticidad";
        $params[':criticidad'] = $criticidad;
    }

    if (!empty($estado)) {
        $condiciones[] = "estado = :estado";
        $params[':estado'] = $estado;
    }

    if (!empty($sistema)) {
        if ($sistema === 'erp_sai') {
            $condiciones[] = "sistema_erp_sai = TRUE";
        } else if ($sistema === 'netcob') {
            $condiciones[] = "sistema_cobranzas_netcob = TRUE";
        } else if ($sistema === 'otros') {
            $condiciones[] = "sistema_otros = TRUE";
        }
    }

    $limite      = isset($_GET['limite']) ? (int)$_GET['limite'] : 50;
    $offset      = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

    if ($limite <= 0 || $limite > 200) {
        $limite = 50;
    }
    if ($offset < 0) {
        $offset = 0;
    }

    $where = !empty($condiciones) ? 'WHERE ' . implode(' AND ', $condiciones) : '';

    // Conteo total para paginación
    $sql_conteo = "SELECT COUNT(*) FROM incidencias_sistemas {$where}";
    $stmt_conteo = $conexion->prepare($sql_conteo);
    $stmt_conteo->execute($params);
    $total_registros = (int)$stmt_conteo->fetchColumn();

    // Consulta de lista paginada
    $sql = "
        SELECT 
            id,
            codigo_documento,
            version_documento,
            nro_incidencia,
            fecha_hora_reporte,
            responsable_reporte,
            sistema_erp_sai,
            sistema_cobranzas_netcob,
            sistema_otros,
            sistema_otros_detalle,
            fallo_base_datos,
            detalle_base_datos,
            fallo_infraestructura_servidor,
            detalle_infraestructura_servidor,
            fallo_enlaces_conectividad,
            detalle_enlaces_conectividad,
            nivel_criticidad,
            estado,
            descripcion_tecnica_logs,
            accion_realizada,
            detalle_tecnico,
            fecha_hora_cierre,
            observaciones_recomendaciones,
            (firma_responsable_reporte IS NOT NULL AND firma_responsable_reporte != '') AS tiene_firma_responsable,
            (firma_sistemas IS NOT NULL AND firma_sistemas != '') AS tiene_firma_sistemas,
            creado_en,
            actualizado_en
        FROM incidencias_sistemas
        {$where}
        ORDER BY id DESC
        LIMIT :limite OFFSET :offset
    ";

    $stmt = $conexion->prepare($sql);
    foreach ($params as $clave_param => $valor_param) {
        $stmt->bindValue($clave_param, $valor_param);
    }
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Métricas globales KPI
    $sql_kpi = "
        SELECT 
            COUNT(*) AS total_incidencias,
            COUNT(CASE WHEN nivel_criticidad = 'critica_nivel_1' THEN 1 END) AS criticas_nivel_1,
            COUNT(CASE WHEN nivel_criticidad = 'alta_nivel_2' THEN 1 END) AS altas_nivel_2,
            COUNT(CASE WHEN estado IN ('abierta', 'en_atencion') THEN 1 END) AS abiertas_atencion,
            COUNT(CASE WHEN estado IN ('resuelta', 'cerrada') THEN 1 END) AS resueltas_cerradas
        FROM incidencias_sistemas
    ";
    $kpi_stmt = $conexion->query($sql_kpi);
    $kpi = $kpi_stmt->fetch(PDO::FETCH_ASSOC);

    responder_exito('Listado de incidencias obtenido correctamente', array(
        'total'           => $total_registros,
        'total_registros' => $total_registros,
        'limite'          => $limite,
        'offset'          => $offset,
        'metricas'        => array(
            'total'              => isset($kpi['total_incidencias']) ? (int)$kpi['total_incidencias'] : 0,
            'criticas_nivel_1'   => isset($kpi['criticas_nivel_1']) ? (int)$kpi['criticas_nivel_1'] : 0,
            'altas_nivel_2'      => isset($kpi['altas_nivel_2']) ? (int)$kpi['altas_nivel_2'] : 0,
            'abiertas_atencion'  => isset($kpi['abiertas_atencion']) ? (int)$kpi['abiertas_atencion'] : 0,
            'resueltas_cerradas' => isset($kpi['resueltas_cerradas']) ? (int)$kpi['resueltas_cerradas'] : 0
        ),
        'registros'       => $registros
    ));

} catch (PDOException $e) {
    responder_error('Error de base de datos al listar incidencias: ' . $e->getMessage(), 500);
} catch (Exception $e) {
    responder_error('Error interno del servidor: ' . $e->getMessage(), 500);
}
