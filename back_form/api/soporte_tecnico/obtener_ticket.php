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

    if (!empty($id)) {
        $sql = "SELECT * FROM tickets_soporte WHERE id = :id LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(array(':id' => $id));
    } else {
        $sql = "SELECT * FROM tickets_soporte WHERE codigo_ticket = :codigo LIMIT 1";
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
