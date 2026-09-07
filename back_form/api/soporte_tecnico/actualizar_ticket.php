<?php
/**
 * Endpoint: Actualizar Ticket de Soporte Técnico (Gestión y Cierre)
 * Método: POST (compatible con formularios y fetch)
 * Compatible con PHP 7.3
 * Archivo: actualizar_ticket.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/conexion_bd.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    responder_error('Método no permitido. Utilice POST o PUT', 405);
}

$datos = obtener_cuerpo_json();

$id = !empty($datos['id']) ? (int)$datos['id'] : null;

if (empty($id)) {
    responder_error('El identificador (id) del ticket es obligatorio para actualizar', 400);
}

try {
    $conexion = obtener_conexion_bd();
    $conexion->beginTransaction();

    // Comprobar existencia previa del ticket
    $stmt_existe = $conexion->prepare("SELECT id, codigo_ticket, estado, prioridad, firma_sistemas FROM tickets_soporte WHERE id = :id");
    $stmt_existe->execute(array(':id' => $id));
    $ticket_actual = $stmt_existe->fetch();

    if (!$ticket_actual) {
        $conexion->rollBack();
        responder_error('El ticket especificado no existe', 404);
    }

    // 1. Validar obligatoriedad de firma de sistemas al resolver
    $nuevo_estado = isset($datos['estado']) ? strtolower($datos['estado']) : null;
    $firma_sistemas_enviada = !empty($datos['firma_sistemas']) ? $datos['firma_sistemas'] : null;

    if ($nuevo_estado === 'resuelto') {
        if (empty($firma_sistemas_enviada) && empty($ticket_actual['firma_sistemas'])) {
            $conexion->rollBack();
            responder_error('La firma digital del Dpto. de Sistemas es obligatoria para resolver y cerrar el ticket', 422);
        }
    }

    // 2. Actualizaciones en tickets_soporte (prioridad y estado)
    $actualizaciones_ticket = array();
    $parametros_ticket = array(':id' => $id);

    if (isset($datos['prioridad'])) {
        $prioridades_validas = array('urgente', 'alta', 'media', 'baja');
        if (in_array(strtolower($datos['prioridad']), $prioridades_validas)) {
            $actualizaciones_ticket[] = "prioridad = :prioridad";
            $parametros_ticket[':prioridad'] = strtolower($datos['prioridad']);
        }
    }

    if ($nuevo_estado !== null) {
        $estados_validos = array('pendiente', 'en_proceso', 'resuelto', 'cancelado');
        if (in_array($nuevo_estado, $estados_validos)) {
            $actualizaciones_ticket[] = "estado = :estado";
            $parametros_ticket[':estado'] = $nuevo_estado;
        }
    }

    if (!empty($firma_sistemas_enviada)) {
        $actualizaciones_ticket[] = "firma_sistemas = :firma_sistemas";
        $parametros_ticket[':firma_sistemas'] = $firma_sistemas_enviada;
    }

    if (!empty($actualizaciones_ticket)) {
        $sql_ticket = "UPDATE tickets_soporte SET " . implode(', ', $actualizaciones_ticket) . " WHERE id = :id";
        $stmt_up_ticket = $conexion->prepare($sql_ticket);
        $stmt_up_ticket->execute($parametros_ticket);
    }

    // 2. Comprobar y actualizar / registrar en atenciones_soporte
    $hay_datos_atencion = isset($datos['fecha_hora_atencion']) || 
                           isset($datos['tecnico_asignado']) || 
                           isset($datos['diagnostico']) || 
                           isset($datos['solucion_aplicada']) || 
                           isset($datos['tipo_resolucion']) || 
                           isset($datos['observaciones_recomendacion']) ||
                           !empty($datos['firma_sistemas']);

    if ($hay_datos_atencion) {
        $stmt_atencion_existe = $conexion->prepare("SELECT id FROM atenciones_soporte WHERE ticket_id = :ticket_id ORDER BY id DESC LIMIT 1");
        $stmt_atencion_existe->execute(array(':ticket_id' => $id));
        $atencion_existente = $stmt_atencion_existe->fetch();

        $fecha_hora_atencion = !empty($datos['fecha_hora_atencion']) ? $datos['fecha_hora_atencion'] : date('Y-m-d H:i:s');
        $tecnico_asignado    = !empty(trim($datos['tecnico_asignado'] ?? '')) ? trim($datos['tecnico_asignado']) : 'Área de Soporte Técnico';
        $diagnostico         = !empty(trim($datos['diagnostico'] ?? '')) ? trim($datos['diagnostico']) : null;
        $solucion_aplicada   = !empty(trim($datos['solucion_aplicada'] ?? '')) ? trim($datos['solucion_aplicada']) : null;
        $tipo_resolucion     = !empty(trim($datos['tipo_resolucion'] ?? '')) ? trim($datos['tipo_resolucion']) : null;
        $observaciones       = !empty(trim($datos['observaciones_recomendacion'] ?? '')) ? trim($datos['observaciones_recomendacion']) : null;
        $firma_sistemas      = !empty($datos['firma_sistemas']) ? $datos['firma_sistemas'] : null;

        if ($atencion_existente) {
            $sql_up_atencion = "UPDATE atenciones_soporte SET 
                                    fecha_hora_atencion = :fecha_hora_atencion,
                                    tecnico_asignado = :tecnico_asignado,
                                    tipo_resolucion = :tipo_resolucion,
                                    diagnostico = :diagnostico,
                                    solucion_aplicada = :solucion_aplicada,
                                    observaciones_recomendacion = :observaciones_recomendacion,
                                    firma_sistemas = COALESCE(:firma_sistemas, firma_sistemas)
                                WHERE id = :atencion_id";
            $stmt_up_at = $conexion->prepare($sql_up_atencion);
            $stmt_up_at->execute(array(
                ':atencion_id'                => $atencion_existente['id'],
                ':fecha_hora_atencion'        => $fecha_hora_atencion,
                ':tecnico_asignado'           => $tecnico_asignado,
                ':tipo_resolucion'            => $tipo_resolucion,
                ':diagnostico'                => $diagnostico,
                ':solucion_aplicada'          => $solucion_aplicada,
                ':observaciones_recomendacion'=> $observaciones,
                ':firma_sistemas'             => $firma_sistemas
            ));
        } else {
            $sql_in_atencion = "INSERT INTO atenciones_soporte (
                                    ticket_id,
                                    fecha_hora_atencion,
                                    tecnico_asignado,
                                    tipo_resolucion,
                                    diagnostico,
                                    solucion_aplicada,
                                    observaciones_recomendacion,
                                    firma_sistemas
                                ) VALUES (
                                    :ticket_id,
                                    :fecha_hora_atencion,
                                    :tecnico_asignado,
                                    :tipo_resolucion,
                                    :diagnostico,
                                    :solucion_aplicada,
                                    :observaciones_recomendacion,
                                    :firma_sistemas
                                )";
            $stmt_in_at = $conexion->prepare($sql_in_atencion);
            $stmt_in_at->execute(array(
                ':ticket_id'                  => $id,
                ':fecha_hora_atencion'        => $fecha_hora_atencion,
                ':tecnico_asignado'           => $tecnico_asignado,
                ':tipo_resolucion'            => $tipo_resolucion,
                ':diagnostico'                => $diagnostico,
                ':solucion_aplicada'          => $solucion_aplicada,
                ':observaciones_recomendacion'=> $observaciones,
                ':firma_sistemas'             => $firma_sistemas
            ));
        }
    }

    if (empty($actualizaciones_ticket) && !$hay_datos_atencion) {
        $conexion->rollBack();
        responder_error('No se enviaron campos válidos para actualizar', 400);
    }

    // Obtener estado final
    $stmt_final = $conexion->prepare("SELECT id, codigo_ticket, estado, prioridad, actualizado_en FROM tickets_soporte WHERE id = :id");
    $stmt_final->execute(array(':id' => $id));
    $resultado = $stmt_final->fetch();

    $conexion->commit();

    responder_exito('Ticket actualizado exitosamente', $resultado);

} catch (Exception $e) {
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }
    responder_error('Error al actualizar el ticket: ' . $e->getMessage(), 500);
}
