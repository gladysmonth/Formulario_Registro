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

    // Comprobar existencia previa
    $stmt_existe = $conexion->prepare("SELECT id, estado FROM tickets_soporte WHERE id = :id");
    $stmt_existe->execute(array(':id' => $id));
    $ticket_actual = $stmt_existe->fetch();

    if (!$ticket_actual) {
        responder_error('El ticket especificado no existe', 404);
    }

    $actualizaciones = array();
    $parametros = array(':id' => $id);

    // 4. Prioridad
    if (isset($datos['prioridad'])) {
        $prioridades_validas = array('urgente', 'alta', 'media', 'baja');
        if (in_array(strtolower($datos['prioridad']), $prioridades_validas)) {
            $actualizaciones[] = "prioridad = :prioridad";
            $parametros[':prioridad'] = strtolower($datos['prioridad']);
        }
    }

    // 5. Datos del Técnico
    if (isset($datos['fecha_hora_atencion'])) {
        $actualizaciones[] = "fecha_hora_atencion = :fecha_hora_atencion";
        $parametros[':fecha_hora_atencion'] = !empty($datos['fecha_hora_atencion']) ? $datos['fecha_hora_atencion'] : null;
    }

    if (isset($datos['tecnico_asignado'])) {
        $actualizaciones[] = "tecnico_asignado = :tecnico_asignado";
        $parametros[':tecnico_asignado'] = !empty(trim($datos['tecnico_asignado'])) ? trim($datos['tecnico_asignado']) : null;
    }

    if (isset($datos['diagnostico'])) {
        $actualizaciones[] = "diagnostico = :diagnostico";
        $parametros[':diagnostico'] = !empty(trim($datos['diagnostico'])) ? trim($datos['diagnostico']) : null;
    }

    if (isset($datos['solucion_aplicada'])) {
        $actualizaciones[] = "solucion_aplicada = :solucion_aplicada";
        $parametros[':solucion_aplicada'] = !empty(trim($datos['solucion_aplicada'])) ? trim($datos['solucion_aplicada']) : null;
    }

    if (isset($datos['tipo_resolucion'])) {
        $actualizaciones[] = "tipo_resolucion = :tipo_resolucion";
        $parametros[':tipo_resolucion'] = !empty(trim($datos['tipo_resolucion'])) ? trim($datos['tipo_resolucion']) : null;
    }

    // 6. Observaciones
    if (isset($datos['observaciones_recomendacion'])) {
        $actualizaciones[] = "observaciones_recomendacion = :observaciones_recomendacion";
        $parametros[':observaciones_recomendacion'] = !empty(trim($datos['observaciones_recomendacion'])) ? trim($datos['observaciones_recomendacion']) : null;
    }

    // Estado del ticket
    if (isset($datos['estado'])) {
        $estados_validos = array('pendiente', 'en_proceso', 'resuelto', 'cancelado');
        if (in_array(strtolower($datos['estado']), $estados_validos)) {
            $actualizaciones[] = "estado = :estado";
            $parametros[':estado'] = strtolower($datos['estado']);
        }
    }

    if (empty($actualizaciones)) {
        responder_error('No se enviaron campos válidos para actualizar', 400);
    }

    $sql = "UPDATE tickets_soporte SET " . implode(', ', $actualizaciones) . " WHERE id = :id RETURNING id, codigo_ticket, estado, actualizado_en";
    $stmt = $conexion->prepare($sql);
    $stmt->execute($parametros);
    $resultado = $stmt->fetch();

    responder_exito('Ticket actualizado exitosamente', $resultado);

} catch (Exception $e) {
    responder_error('Error al actualizar el ticket: ' . $e->getMessage(), 500);
}
