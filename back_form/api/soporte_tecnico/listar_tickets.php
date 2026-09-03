<?php
/**
 * Endpoint: Listar Tickets de Soporte Técnico con Filtros
 * Método: GET
 * Compatible con PHP 7.3
 * Archivo: listar_tickets.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/conexion_bd.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método no permitido. Utilice GET', 405);
}

$estado    = isset($_GET['estado']) ? trim($_GET['estado']) : '';
$prioridad = isset($_GET['prioridad']) ? trim($_GET['prioridad']) : '';
$buscar    = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$limite    = isset($_GET['limite']) ? (int)$_GET['limite'] : 50;
$offset    = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

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

    if (!empty($estado) && $estado !== 'todos') {
        $clausulas[] = "estado = :estado";
        $parametros[':estado'] = $estado;
    }

    if (!empty($prioridad) && $prioridad !== 'todos') {
        $clausulas[] = "prioridad = :prioridad";
        $parametros[':prioridad'] = $prioridad;
    }

    if (!empty($buscar)) {
        $clausulas[] = "(codigo_ticket ILIKE :buscar OR nombre_solicitante ILIKE :buscar OR departamento_area ILIKE :buscar OR descripcion_problema ILIKE :buscar)";
        $parametros[':buscar'] = '%' . $buscar . '%';
    }

    $donde = '';
    if (!empty($clausulas)) {
        $donde = 'WHERE ' . implode(' AND ', $clausulas);
    }

    // Consulta de conteo total
    $sql_conteo = "SELECT COUNT(*) FROM tickets_soporte {$donde}";
    $stmt_conteo = $conexion->prepare($sql_conteo);
    $stmt_conteo->execute($parametros);
    $total_registros = (int)$stmt_conteo->fetchColumn();

    // Consulta de registros ordenados por fecha de creación descendente
    $sql = "SELECT 
                id,
                codigo_ticket,
                nombre_solicitante,
                fecha_solicitud,
                departamento_area,
                soporte_hardware,
                soporte_software,
                descripcion_problema,
                numero_serie,
                marca_modelo,
                sistema_operativo,
                prioridad,
                estado,
                fecha_hora_atencion,
                tecnico_asignado,
                diagnostico,
                solucion_aplicada,
                tipo_resolucion,
                observaciones_recomendacion,
                creado_en,
                actualizado_en
            FROM tickets_soporte
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

    $tickets = $stmt->fetchAll();

    // Convertir booleanos de PostgreSQL para respuesta consistente
    foreach ($tickets as &$ticket) {
        $ticket['soporte_hardware'] = (bool)$ticket['soporte_hardware'];
        $ticket['soporte_software'] = (bool)$ticket['soporte_software'];
    }

    // Métricas rápidas para el dashboard
    $stmt_metricas = $conexion->query("SELECT 
        COUNT(*) AS total,
        COUNT(CASE WHEN estado = 'pendiente' THEN 1 END) AS pendientes,
        COUNT(CASE WHEN estado = 'en_proceso' THEN 1 END) AS en_proceso,
        COUNT(CASE WHEN estado = 'resuelto' THEN 1 END) AS resueltos,
        COUNT(CASE WHEN prioridad = 'urgente' THEN 1 END) AS urgentes
    FROM tickets_soporte");
    $metricas = $stmt_metricas->fetch();

    responder_exito('Tickets recuperados correctamente', array(
        'total' => $total_registros,
        'limite' => $limite,
        'offset' => $offset,
        'metricas' => $metricas,
        'tickets' => $tickets
    ));

} catch (Exception $e) {
    responder_error('Error al consultar los tickets: ' . $e->getMessage(), 500);
}
