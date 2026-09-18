<?php
/**
 * Endpoint: Obtener Detalle Completo de una Solicitud de Accesos
 * Compatible con PHP 7.3
 * Archivo: back_form/api/asignacion_accesos/obtener_solicitud.php
 */

require_once __DIR__ . '/../../configuracion/conexion_bd.php';
require_once __DIR__ . '/../../configuracion/respuestas_api.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método HTTP no permitido. Debe utilizar GET.', 405);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$codigo = isset($_GET['codigo']) ? trim($_GET['codigo']) : '';

if ($id <= 0 && empty($codigo)) {
    responder_error('Debe proporcionar un ID o código de solicitud válido.', 400);
}

$conexion = obtener_conexion_bd();
if (!$conexion) {
    responder_error('No se pudo conectar a la base de datos.', 500);
}

try {
    if ($id > 0) {
        $stmt = $conexion->prepare("SELECT * FROM solicitudes_accesos WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    } else {
        $stmt = $conexion->prepare("SELECT * FROM solicitudes_accesos WHERE nro_solicitud = :codigo LIMIT 1");
        $stmt->bindValue(':codigo', $codigo, PDO::PARAM_STR);
    }

    $stmt->execute();
    $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$solicitud) {
        responder_error('No se encontró la solicitud de accesos especificada.', 404);
    }

    responder_exito('Detalle de la solicitud obtenido correctamente', $solicitud);

} catch (PDOException $e) {
    error_log('Error PDO en obtener_solicitud.php: ' . $e->getMessage());
    responder_error('Error al recuperar la información de la solicitud: ' . $e->getMessage(), 500);
}
