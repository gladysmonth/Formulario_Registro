<?php
/**
 * Endpoint: Obtener Detalle de un Ticket de Soporte Técnico
 * Método: GET
 * Compatible con PHP 7.3
 * Archivo: obtener_ticket.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/conexion_bd.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método no permitido. Utilice GET', 405);
}

$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;
$codigo = isset($_GET['codigo']) ? trim($_GET['codigo']) : null;

if (empty($id) && empty($codigo)) {
    responder_error('Debe proporcionar el parámetro id o codigo', 400);
}

try {
    $conexion = obtener_conexion_bd();

    $sql_base = "SELECT 
                    t.id,
                    t.codigo_ticket,
                    t.nombre_solicitante,
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
                    t.firma_solicitante,
                    COALESCE(a.firma_sistemas, t.firma_sistemas) AS firma_sistemas,
                    t.creado_en,
                    t.actualizado_en,
                    a.id AS atencion_id,
                    a.fecha_hora_atencion,
                    a.tecnico_asignado,
                    a.tipo_resolucion,
                    a.diagnostico,
                    a.solucion_aplicada,
                    a.observaciones_recomendacion
                FROM tickets_soporte t
                LEFT JOIN atenciones_soporte a ON a.ticket_id = t.id";

    if (!empty($id)) {
        $sql = "{$sql_base} WHERE t.id = :id ORDER BY a.id DESC LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(array(':id' => $id));
    } else {
        $sql = "{$sql_base} WHERE t.codigo_ticket = :codigo ORDER BY a.id DESC LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(array(':codigo' => $codigo));
    }

    $ticket = $stmt->fetch();

    if (!$ticket) {
        responder_error('Ticket no encontrado', 404);
    }

    $ticket['soporte_hardware'] = (bool)$ticket['soporte_hardware'];
    $ticket['soporte_software'] = (bool)$ticket['soporte_software'];

    responder_exito('Detalle de ticket recuperado', $ticket);

} catch (Exception $e) {
    responder_error('Error al obtener el ticket: ' . $e->getMessage(), 500);
}
