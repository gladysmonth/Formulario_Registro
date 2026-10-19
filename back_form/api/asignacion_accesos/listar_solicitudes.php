<?php
/**
 * Endpoint: Listar Solicitudes de Asignación de Accesos con Filtros y Métricas KPI
 * Compatible con PHP 7.3
 * Archivo: back_form/api/asignacion_accesos/listar_solicitudes.php
 */

require_once __DIR__ . '/../../configuracion/conexion_bd.php';
require_once __DIR__ . '/../../configuracion/respuestas_api.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método HTTP no permitido. Debe utilizar GET.', 405);
}

$conexion = obtener_conexion_bd();
if (!$conexion) {
    responder_error('No se pudo conectar a la base de datos.', 500);
}

try {
    // 1. Cálculo de Métricas KPI
    $sql_metricas = "
        SELECT 
            COUNT(*) AS total,
            COUNT(CASE WHEN estado = 'pendiente' THEN 1 END) AS pendientes,
            COUNT(CASE WHEN estado = 'en_proceso' THEN 1 END) AS en_proceso,
            COUNT(CASE WHEN estado = 'atendido' THEN 1 END) AS atendidas,
            COUNT(CASE WHEN estado = 'rechazado' THEN 1 END) AS rechazadas
        FROM solicitudes_accesos
    ";
    $stmt_m = $conexion->query($sql_metricas);
    $metricas = $stmt_m->fetch(PDO::FETCH_ASSOC);

    // 2. Construcción de Filtros Dinámicos
    $condiciones = array();
    $parametros = array();

    // Filtro por Estado
    if (!empty($_GET['estado'])) {
        $estado = trim($_GET['estado']);
        $condiciones[] = "estado = :estado";
        $parametros[':estado'] = $estado;
    }

    // Filtro por Sistema
    if (!empty($_GET['sistema'])) {
        $sis = trim($_GET['sistema']);
        if ($sis === 'sai') {
            $condiciones[] = "sistema_erp_sai = TRUE";
        } elseif ($sis === 'netcob') {
            $condiciones[] = "sistema_cobranzas_netcob = TRUE";
        } elseif ($sis === 'otros') {
            $condiciones[] = "sistema_otros = TRUE";
        }
    }

    // Filtro por Búsqueda de Texto (soporta ?buscar o ?busqueda)
    $texto_busqueda = !empty($_GET['busqueda']) ? trim($_GET['busqueda']) : (!empty($_GET['buscar']) ? trim($_GET['buscar']) : '');
    if (!empty($texto_busqueda)) {
        $busqueda = '%' . $texto_busqueda . '%';
        $condiciones[] = "(
            nro_solicitud ILIKE :busqueda OR 
            nombre_solicitante ILIKE :busqueda OR 
            area_departamento ILIKE :busqueda OR 
            nombre_usuario_detalles ILIKE :busqueda
        )";
        $parametros[':busqueda'] = $busqueda;
    }

    $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

    if ($limite <= 0 || $limite > 200) {
        $limite = 50;
    }
    if ($offset < 0) {
        $offset = 0;
    }

    $where = !empty($condiciones) ? 'WHERE ' . implode(' AND ', $condiciones) : '';

    // Conteo total para paginación
    $sql_conteo = "SELECT COUNT(*) FROM solicitudes_accesos {$where}";
    $stmt_conteo = $conexion->prepare($sql_conteo);
    $stmt_conteo->execute($parametros);
    $total_registros = (int)$stmt_conteo->fetchColumn();

    // 3. Consulta de Registros Paginada
    $sql_registros = "
        SELECT 
            id,
            nro_solicitud,
            fecha_solicitud,
            nombre_solicitante,
            cargo_solicitante,
            area_departamento,
            sistema_erp_sai,
            sistema_cobranzas_netcob,
            sistema_otros,
            sistema_otros_detalle,
            es_usuario_nuevo,
            nombre_usuario_detalles,
            estado,
            atendido_por,
            fecha_hora_atencion,
            (firma_solicitante IS NOT NULL) AS tiene_firma_solicitante,
            (firma_autoriza IS NOT NULL) AS tiene_firma_autoriza,
            (firma_sistemas IS NOT NULL) AS tiene_firma_sistemas,
            creado_en
        FROM solicitudes_accesos
        $where
        ORDER BY fecha_solicitud DESC, id DESC
        LIMIT :limite OFFSET :offset
    ";

    $stmt_reg = $conexion->prepare($sql_registros);
    foreach ($parametros as $clave_param => $valor_param) {
        $stmt_reg->bindValue($clave_param, $valor_param);
    }
    $stmt_reg->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt_reg->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt_reg->execute();
    $registros = $stmt_reg->fetchAll(PDO::FETCH_ASSOC);

    responder_exito('Listado de solicitudes obtenido correctamente', array(
        'total'           => $total_registros,
        'total_registros' => $total_registros,
        'limite'          => $limite,
        'offset'          => $offset,
        'metricas'        => array(
            'total'       => isset($metricas['total']) ? (int)$metricas['total'] : 0,
            'pendientes'  => isset($metricas['pendientes']) ? (int)$metricas['pendientes'] : 0,
            'en_proceso'  => isset($metricas['en_proceso']) ? (int)$metricas['en_proceso'] : 0,
            'atendidas'   => isset($metricas['atendidas']) ? (int)$metricas['atendidas'] : 0,
            'rechazadas'  => isset($metricas['rechazadas']) ? (int)$metricas['rechazadas'] : 0
        ),
        'registros'       => $registros
    ));

} catch (PDOException $e) {
    error_log('Error PDO en listar_solicitudes.php: ' . $e->getMessage());
    responder_error('Error al consultar las solicitudes de accesos: ' . $e->getMessage(), 500);
}
