<?php
/**
 * Endpoint: Actualizar Ticket de Soporte Técnico (Gestión, Derivación y Cierre)
 * Método: POST o PUT
 * Compatible con PHP 7.3
 * Archivo: actualizar_ticket.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/conexion_bd.php';
require_once __DIR__ . '/../../configuracion/seguridad_api.php';
require_once __DIR__ . '/servicios/servicio_atencion.php';

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

    // 1. Comprobar existencia previa del ticket
    $stmt_existe = $conexion->prepare("SELECT id, codigo_ticket, estado, prioridad, firma_sistemas, tecnico_asignado_id FROM tickets_soporte WHERE id = :id");
    $stmt_existe->execute(array(':id' => $id));
    $ticket_actual = $stmt_existe->fetch();

    if (!$ticket_actual) {
        $conexion->rollBack();
        responder_error('El ticket especificado no existe', 404);
    }

    // 2. Validar firma al resolver
    $nuevo_estado = isset($datos['estado']) ? strtolower(trim($datos['estado'])) : null;
    $firma_sistemas_enviada = !empty($datos['firma_sistemas']) ? $datos['firma_sistemas'] : null;

    if ($nuevo_estado === 'resuelto') {
        if (empty($firma_sistemas_enviada) && empty($ticket_actual['firma_sistemas'])) {
            $conexion->rollBack();
            responder_error('La firma digital del Dpto. de Sistemas es obligatoria para resolver y cerrar el ticket', 422);
        }
    }

    // 3. Extraer derivación de técnicos
    $tecnico_asignado_id = !empty($datos['tecnico_asignado_id']) ? (int)$datos['tecnico_asignado_id'] : (!empty($datos['tecnico_id']) ? (int)$datos['tecnico_id'] : null);
    $nombre_tecnico_derivado = (!empty($datos['tecnico_asignado']) && trim($datos['tecnico_asignado']) !== '') ? trim($datos['tecnico_asignado']) : null;

    $actualizaciones_ticket = array();
    $parametros_ticket = array(':id' => $id);

    if ($tecnico_asignado_id !== null) {
        $actualizaciones_ticket[] = "tecnico_asignado_id = :tecnico_asignado_id";
        $parametros_ticket[':tecnico_asignado_id'] = $tecnico_asignado_id;

        if (empty($nombre_tecnico_derivado)) {
            $stmt_tec_info = $conexion->prepare("SELECT nombre_completo FROM usuarios WHERE id = :tid");
            $stmt_tec_info->execute(array(':tid' => $tecnico_asignado_id));
            $nombre_tec_bd = $stmt_tec_info->fetchColumn();
            if ($nombre_tec_bd) {
                $nombre_tecnico_derivado = $nombre_tec_bd;
            }
        }

        if ($nuevo_estado === null && $ticket_actual['estado'] === 'pendiente') {
            $nuevo_estado = 'en_proceso';
        }
    }

    if (isset($datos['diagnostico_id'])) {
        $diag_id_val = !empty($datos['diagnostico_id']) ? (int)$datos['diagnostico_id'] : null;
        $actualizaciones_ticket[] = "diagnostico_id = :diagnostico_id";
        $parametros_ticket[':diagnostico_id'] = $diag_id_val;
    }

    if (isset($datos['prioridad'])) {
        $prioridades_validas = array('urgente', 'alta', 'media', 'baja');
        if (in_array(strtolower($datos['prioridad']), $prioridades_validas)) {
            $actualizaciones_ticket[] = "prioridad = :prioridad";
            $parametros_ticket[':prioridad'] = strtolower($datos['prioridad']);
        }
    }

    if ($nuevo_estado !== null) {
        $estados_validos = array('pendiente', 'en_proceso', 'resuelto', 'cancelado', 'fuerza_mayor');
        if (in_array($nuevo_estado, $estados_validos)) {
            $actualizaciones_ticket[] = "estado = :estado";
            $parametros_ticket[':estado'] = $nuevo_estado;
        }
    }

    if (isset($datos['motivo_imposibilidad'])) {
        $actualizaciones_ticket[] = "motivo_imposibilidad = :motivo_imposibilidad";
        $parametros_ticket[':motivo_imposibilidad'] = trim($datos['motivo_imposibilidad']);
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

    // 4. Submódulo de Atención: Guardar o actualizar la ficha técnica
    $hay_datos_atencion = isset($datos['fecha_hora_atencion']) || 
                           !empty($nombre_tecnico_derivado) || 
                           $tecnico_asignado_id !== null ||
                           isset($datos['diagnostico']) || 
                           isset($datos['solucion_aplicada']) || 
                           isset($datos['tipo_resolucion']) || 
                           isset($datos['observaciones_recomendacion']) ||
                           !empty($datos['firma_sistemas']);

    if ($hay_datos_atencion) {
        $datos_para_atencion = $datos;
        if (!empty($nombre_tecnico_derivado)) {
            $datos_para_atencion['tecnico_asignado'] = $nombre_tecnico_derivado;
        }
        guardar_o_actualizar_atencion($conexion, $id, $datos_para_atencion, $firma_sistemas_enviada);
    }

    if (empty($actualizaciones_ticket) && !$hay_datos_atencion) {
        $conexion->rollBack();
        responder_error('No se enviaron campos válidos para actualizar', 400);
    }

    // 5. Obtener estado final
    $stmt_final = $conexion->prepare("
        SELECT id, codigo_ticket, estado, prioridad, tecnico_asignado_id, diagnostico_id, motivo_imposibilidad, actualizado_en 
        FROM tickets_soporte 
        WHERE id = :id
    ");
    $stmt_final->execute(array(':id' => $id));
    $resultado = $stmt_final->fetch();

    // 6. Auditoría institucional de la operación
    $usuario_actor = obtener_usuario_actor_api($conexion, $datos);
    $actor_id = $usuario_actor ? $usuario_actor['id'] : null;
    $actor_nombre = $usuario_actor ? $usuario_actor['nombre_completo'] : ($nombre_tecnico_derivado ? $nombre_tecnico_derivado : 'Área de Soporte');

    registrar_auditoria_operacion(
        $conexion,
        'soporte_tecnico',
        $id,
        $ticket_actual['codigo_ticket'],
        'actualizacion_ticket',
        $ticket_actual['estado'],
        $nuevo_estado !== null ? $nuevo_estado : $ticket_actual['estado'],
        $actor_id,
        $actor_nombre,
        array(
            'prioridad'        => isset($datos['prioridad']) ? $datos['prioridad'] : $ticket_actual['prioridad'],
            'tecnico_asignado' => $nombre_tecnico_derivado,
            'tipo_resolucion'  => isset($datos['tipo_resolucion']) ? $datos['tipo_resolucion'] : null
        )
    );

    $conexion->commit();

    responder_exito('Ticket actualizado exitosamente', $resultado);

} catch (Exception $e) {
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }
    responder_error('Error al actualizar el ticket: ' . $e->getMessage(), 500);
}
