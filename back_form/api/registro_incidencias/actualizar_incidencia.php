<?php
/**
 * Endpoint: Actualizar Solución y Cierre de Incidencia de Sistemas
 * Método: POST
 * Compatible con PHP 7.3
 * Archivo: back_form/api/registro_incidencias/actualizar_incidencia.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/conexion_bd.php';
require_once __DIR__ . '/servicios/servicio_firmas_ri.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_error('Método no permitido. Utilice POST', 405);
}

$datos = obtener_cuerpo_json();

$id = isset($datos['id']) ? intval($datos['id']) : 0;
if ($id <= 0) {
    responder_error('ID de incidencia requerido', 400);
}

try {
    $conexion = obtener_conexion_bd();

    // Comprobar existencia
    $stmt_check = $conexion->prepare("SELECT * FROM incidencias_sistemas WHERE id = :id LIMIT 1");
    $stmt_check->execute(array(':id' => $id));
    $actual = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$actual) {
        responder_error('La incidencia no existe', 404);
    }

    $estado = isset($datos['estado']) ? trim($datos['estado']) : $actual['estado'];
    $estados_validos = array('abierta', 'en_atencion', 'resuelta', 'cerrada');
    if (!in_array($estado, $estados_validos, true)) {
        $estado = $actual['estado'];
    }

    $accion_realizada = isset($datos['accion_realizada']) ? trim($datos['accion_realizada']) : $actual['accion_realizada'];
    $detalle_tecnico  = isset($datos['detalle_tecnico']) ? trim($datos['detalle_tecnico']) : $actual['detalle_tecnico'];
    $fecha_hora_cierre = !empty($datos['fecha_hora_cierre']) 
        ? str_replace('T', ' ', $datos['fecha_hora_cierre']) 
        : $actual['fecha_hora_cierre'];

    $observaciones = isset($datos['observaciones_recomendaciones']) 
        ? trim($datos['observaciones_recomendaciones']) 
        : $actual['observaciones_recomendaciones'];

    // Firma de Sistemas
    $firma_sistemas = !empty($datos['firma_sistemas']) ? $datos['firma_sistemas'] : $actual['firma_sistemas'];

    // Validar cierre
    if (($estado === 'resuelta' || $estado === 'cerrada')) {
        if (empty($accion_realizada)) {
            responder_error('La Acción Realizada es obligatoria para marcar la incidencia como resuelta o cerrada', 422);
        }
        if (empty($fecha_hora_cierre)) {
            $fecha_hora_cierre = date('Y-m-d H:i:s');
        }
    }

    $sql = "
        UPDATE incidencias_sistemas SET
            estado = :estado,
            accion_realizada = :accion_realizada,
            detalle_tecnico = :detalle_tecnico,
            fecha_hora_cierre = :fecha_hora_cierre,
            observaciones_recomendaciones = :observaciones,
            firma_sistemas = :firma_sistemas
        WHERE id = :id
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute(array(
        ':estado'           => $estado,
        ':accion_realizada' => $accion_realizada,
        ':detalle_tecnico'  => $detalle_tecnico,
        ':fecha_hora_cierre'=> $fecha_hora_cierre,
        ':observaciones'    => $observaciones,
        ':firma_sistemas'   => $firma_sistemas,
        ':id'               => $id
    ));

    responder_exito('Incidencia actualizada correctamente', array(
        'id'     => $id,
        'estado' => $estado
    ));

} catch (PDOException $e) {
    responder_error('Error de base de datos al actualizar: ' . $e->getMessage(), 500);
} catch (Exception $e) {
    responder_error('Error interno del servidor: ' . $e->getMessage(), 500);
}
