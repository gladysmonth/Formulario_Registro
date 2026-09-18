<?php
/**
 * Endpoint: Actualizar Atención Técnica y Estado de Solicitud de Accesos
 * Compatible con PHP 7.3
 * Archivo: back_form/api/asignacion_accesos/actualizar_solicitud.php
 */

require_once __DIR__ . '/../../configuracion/conexion_bd.php';
require_once __DIR__ . '/../../configuracion/respuestas_api.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_error('Método HTTP no permitido. Debe utilizar POST.', 405);
}

$cuerpo = file_get_contents('php://input');
$datos = json_decode($cuerpo, true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($datos)) {
    responder_error('El cuerpo de la petición no contiene un formato JSON válido.', 400);
}

$id = !empty($datos['id']) ? intval($datos['id']) : 0;
if ($id <= 0) {
    responder_error('El ID de la solicitud es obligatorio para realizar la actualización.', 422);
}

$estado = !empty($datos['estado']) ? trim($datos['estado']) : 'atendido';
$estados_permitidos = array('pendiente', 'en_proceso', 'atendido', 'rechazado');
if (!in_array($estado, $estados_permitidos)) {
    responder_error('El estado especificado no es válido.', 422);
}

$atendido_por = !empty($datos['atendido_por']) ? htmlspecialchars(trim($datos['atendido_por']), ENT_QUOTES, 'UTF-8') : null;
$fecha_hora_atencion = !empty($datos['fecha_hora_atencion']) ? trim($datos['fecha_hora_atencion']) : date('Y-m-d H:i:s');
$comentarios = !empty($datos['comentarios_sistemas']) ? htmlspecialchars(trim($datos['comentarios_sistemas']), ENT_QUOTES, 'UTF-8') : null;
$firma_sistemas = !empty($datos['firma_sistemas']) ? $datos['firma_sistemas'] : null;

$conexion = obtener_conexion_bd();
if (!$conexion) {
    responder_error('No se pudo conectar a la base de datos.', 500);
}

try {
    // 1. Verificar existencia de la solicitud
    $stmt_check = $conexion->prepare("SELECT id, firma_sistemas FROM solicitudes_accesos WHERE id = :id");
    $stmt_check->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt_check->execute();
    $actual = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$actual) {
        responder_error('La solicitud especificada no existe.', 404);
    }

    // Si no se envía una nueva firma de sistemas, se conserva la firma existente
    $firma_final_sistemas = !empty($firma_sistemas) ? $firma_sistemas : $actual['firma_sistemas'];

    // 2. Ejecutar actualización
    $sql_update = "
        UPDATE solicitudes_accesos
        SET 
            estado = :estado,
            atendido_por = :atendido_por,
            fecha_hora_atencion = :fecha_hora_atencion,
            comentarios_sistemas = :comentarios_sistemas,
            firma_sistemas = :firma_sistemas
        WHERE id = :id
    ";

    $stmt = $conexion->prepare($sql_update);
    $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
    $stmt->bindValue(':atendido_por', $atendido_por, $atendido_por ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':fecha_hora_atencion', $fecha_hora_atencion, PDO::PARAM_STR);
    $stmt->bindValue(':comentarios_sistemas', $comentarios, $comentarios ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':firma_sistemas', $firma_final_sistemas, $firma_final_sistemas ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    $stmt->execute();

    responder_exito('Atención de solicitud actualizada correctamente', array(
        'id'                  => $id,
        'estado'              => $estado,
        'atendido_por'        => $atendido_por,
        'fecha_hora_atencion' => $fecha_hora_atencion
    ));

} catch (PDOException $e) {
    error_log('Error PDO en actualizar_solicitud.php: ' . $e->getMessage());
    responder_error('Error al actualizar la solicitud: ' . $e->getMessage(), 500);
}
