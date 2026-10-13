<?php
/**
 * Endpoint: Listar Tickets de Soporte Técnico con Filtros RBAC y Telemetría
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

$estado                 = isset($_GET['estado']) ? trim($_GET['estado']) : '';
$prioridad              = isset($_GET['prioridad']) ? trim($_GET['prioridad']) : '';
$buscar                 = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$usuario_solicitante_id = isset($_GET['usuario_solicitante_id']) ? (int)$_GET['usuario_solicitante_id'] : (isset($_GET['usuario_id']) ? (int)$_GET['usuario_id'] : 0);
$tecnico_asignado_id    = isset($_GET['tecnico_asignado_id']) ? (int)$_GET['tecnico_asignado_id'] : (isset($_GET['tecnico_id']) ? (int)$_GET['tecnico_id'] : 0);
$limite                 = isset($_GET['limite']) ? (int)$_GET['limite'] : 50;
$offset                 = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

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
        $clausulas[] = "t.estado = :estado";
        $parametros[':estado'] = $estado;
    }

    if (!empty($prioridad) && $prioridad !== 'todos') {
        $clausulas[] = "t.prioridad = :prioridad";
        $parametros[':prioridad'] = $prioridad;
    }

    if ($usuario_solicitante_id > 0) {
        $clausulas[] = "t.usuario_solicitante_id = :usuario_solicitante_id";
        $parametros[':usuario_solicitante_id'] = $usuario_solicitante_id;
    }

    if ($tecnico_asignado_id > 0) {
        $clausulas[] = "(t.tecnico_asignado_id = :tecnico_asignado_id OR a.tecnico_id = :tecnico_asignado_id)";
        $parametros[':tecnico_asignado_id'] = $tecnico_asignado_id;
    }

    if (!empty($buscar)) {
        $clausulas[] = "(t.codigo_ticket ILIKE :buscar 
                     OR t.nombre_solicitante ILIKE :buscar 
                     OR t.departamento_area ILIKE :buscar 
                     OR t.descripcion_problema ILIKE :buscar 
                     OR a.tecnico_asignado ILIKE :buscar 
                     OR t.direccion_mac ILIKE :buscar 
                     OR t.codigo_activo ILIKE :buscar)";
        $parametros[':buscar'] = '%' . $buscar . '%';
    }

    $donde = '';
    if (!empty($clausulas)) {
        $donde = 'WHERE ' . implode(' AND ', $clausulas);
    }

    // Consulta de conteo total
    $sql_conteo = "SELECT COUNT(DISTINCT t.id) 
                   FROM tickets_soporte t 
                   LEFT JOIN atenciones_soporte a ON a.ticket_id = t.id 
                   {$donde}";
    $stmt_conteo = $conexion->prepare($sql_conteo);
    $stmt_conteo->execute($parametros);
    $total_registros = (int)$stmt_conteo->fetchColumn();

    // Consulta de registros con joins a atenciones, usuarios y telemetría
    $sql = "SELECT 
                t.id,
                t.codigo_ticket,
                t.nombre_solicitante,
                t.usuario_solicitante_id,
                t.fecha_solicitud,
                t.departamento_area,
                t.soporte_hardware,
                t.soporte_software,
                t.descripcion_problema,
                t.equipo_id,
                t.codigo_activo,
                t.numero_serie,
                t.tipo_equipo,
                t.marca_modelo,
                t.sistema_operativo,
                t.area,
                t.encargado,
                COALESCE(t.area, t.area_encargado) AS area_encargado,
                t.centro_costo,
                t.prioridad,
                t.estado,
                t.motivo_imposibilidad,
                t.diagnostico_id,
                t.direccion_mac,
                (t.diagnostico_id IS NOT NULL) AS tiene_diagnostico,
                hd.estado_salud_general AS estado_salud_pc,
                hd.temperatura_cpu AS temperatura_cpu_pc,
                hd.salud_disco_smart AS salud_disco_smart_pc,
                t.tecnico_asignado_id,
                ut.nombre_completo AS nombre_tecnico_asignado,
                COALESCE(a.tecnico_asignado, ut.nombre_completo) AS tecnico_asignado,
                t.firma_solicitante,
                COALESCE(a.firma_sistemas, t.firma_sistemas) AS firma_sistemas,
                t.creado_en,
                t.actualizado_en,
                a.fecha_hora_atencion,
                a.diagnostico,
                a.solucion_aplicada,
                a.tipo_resolucion,
                a.observaciones_recomendacion
            FROM tickets_soporte t
            LEFT JOIN (
                SELECT DISTINCT ON (ticket_id) *
                FROM atenciones_soporte
                ORDER BY ticket_id, id DESC
            ) a ON a.ticket_id = t.id
            LEFT JOIN usuarios ut ON ut.id = COALESCE(a.tecnico_id, t.tecnico_asignado_id)
            LEFT JOIN historial_diagnosticos hd ON hd.id = t.diagnostico_id
            {$donde}
            ORDER BY t.creado_en DESC
            LIMIT :limite OFFSET :offset";

    $stmt = $conexion->prepare($sql);
    foreach ($parametros as $clave => $valor) {
        $stmt->bindValue($clave, $valor);
    }
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convertir tipos de datos de PostgreSQL para respuesta consistente
    foreach ($tickets as &$ticket) {
        $ticket['id']                     = (int)$ticket['id'];
        $ticket['soporte_hardware']       = (bool)$ticket['soporte_hardware'];
        $ticket['soporte_software']       = (bool)$ticket['soporte_software'];
        $ticket['tiene_diagnostico']      = (bool)$ticket['tiene_diagnostico'];
        $ticket['usuario_solicitante_id'] = $ticket['usuario_solicitante_id'] ? (int)$ticket['usuario_solicitante_id'] : null;
        $ticket['tecnico_asignado_id']    = $ticket['tecnico_asignado_id'] ? (int)$ticket['tecnico_asignado_id'] : null;
        $ticket['diagnostico_id']         = $ticket['diagnostico_id'] ? (int)$ticket['diagnostico_id'] : null;
    }

    // Métricas globales del dashboard
    $stmt_metricas = $conexion->query("SELECT 
        COUNT(*) AS total,
        COUNT(CASE WHEN estado = 'pendiente' THEN 1 END) AS pendientes,
        COUNT(CASE WHEN estado = 'en_proceso' THEN 1 END) AS en_proceso,
        COUNT(CASE WHEN estado = 'resuelto' THEN 1 END) AS resueltos,
        COUNT(CASE WHEN estado = 'fuerza_mayor' THEN 1 END) AS fuerza_mayor,
        COUNT(CASE WHEN prioridad = 'urgente' THEN 1 END) AS urgentes,
        COUNT(CASE WHEN diagnostico_id IS NOT NULL THEN 1 END) AS con_telemetria
    FROM tickets_soporte");
    $metricas = $stmt_metricas->fetch(PDO::FETCH_ASSOC);

    responder_exito('Tickets recuperados correctamente', array(
        'total'    => $total_registros,
        'limite'   => $limite,
        'offset'   => $offset,
        'metricas' => $metricas,
        'tickets'  => $tickets
    ));

} catch (Exception $e) {
    responder_error('Error al consultar los tickets: ' . $e->getMessage(), 500);
}
