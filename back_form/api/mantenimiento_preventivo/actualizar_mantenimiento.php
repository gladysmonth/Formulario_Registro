<?php
/**
 * Endpoint: Actualizar Registro de Mantenimiento Preventivo (Observaciones y Firmas)
 * Método: POST o PUT
 * Compatible con PHP 7.3
 * Archivo: back_form/api/mantenimiento_preventivo/actualizar_mantenimiento.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/conexion_bd.php';
require_once __DIR__ . '/../../configuracion/seguridad_api.php';
require_once __DIR__ . '/servicios/servicio_firmas_mp.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    responder_error('Método no permitido. Utilice POST o PUT', 405);
}

$datos = obtener_cuerpo_json();

$id = !empty($datos['id']) ? (int)$datos['id'] : null;

if (empty($id)) {
    responder_error('El identificador (id) del registro de mantenimiento es obligatorio', 400);
}

try {
    $conexion = obtener_conexion_bd();
    $conexion->beginTransaction();

    // Comprobar existencia del registro
    $stmt_existe = $conexion->prepare("SELECT id, codigo_mantenimiento, firma_sistemas, firma_responsable_equipo FROM mantenimientos_preventivos WHERE id = :id");
    $stmt_existe->execute(array(':id' => $id));
    $mp_actual = $stmt_existe->fetch();

    if (!$mp_actual) {
        $conexion->rollBack();
        responder_error('El registro de mantenimiento preventivo no existe', 404);
    }

    $actualizaciones = array();
    $parametros = array(':id' => $id);

    // Campos de texto permitidos para actualizar
    if (isset($datos['tecnico_responsable']) && trim($datos['tecnico_responsable']) !== '') {
        $actualizaciones[] = "tecnico_responsable = :tecnico_responsable";
        $parametros[':tecnico_responsable'] = trim($datos['tecnico_responsable']);
    }

    if (isset($datos['ubicacion_equipo']) && trim($datos['ubicacion_equipo']) !== '') {
        $actualizaciones[] = "ubicacion_equipo = :ubicacion_equipo";
        $parametros[':ubicacion_equipo'] = trim($datos['ubicacion_equipo']);
    }

    if (isset($datos['observaciones_incidencias'])) {
        $actualizaciones[] = "observaciones_incidencias = :observaciones_incidencias";
        $parametros[':observaciones_incidencias'] = trim($datos['observaciones_incidencias']);
    }

    if (isset($datos['limpieza_otros'])) {
        $actualizaciones[] = "limpieza_otros = :limpieza_otros";
        $parametros[':limpieza_otros'] = trim($datos['limpieza_otros']);
    }

    if (isset($datos['mantenimiento_interno_otros'])) {
        $actualizaciones[] = "mantenimiento_interno_otros = :mantenimiento_interno_otros";
        $parametros[':mantenimiento_interno_otros'] = trim($datos['mantenimiento_interno_otros']);
    }

    // Firmas digitales
    if (!empty($datos['firma_sistemas'])) {
        $actualizaciones[] = "firma_sistemas = :firma_sistemas";
        $parametros[':firma_sistemas'] = $datos['firma_sistemas'];
    }

    if (!empty($datos['firma_responsable_equipo'])) {
        $actualizaciones[] = "firma_responsable_equipo = :firma_responsable_equipo";
        $parametros[':firma_responsable_equipo'] = $datos['firma_responsable_equipo'];
    }

    if (empty($actualizaciones)) {
        $conexion->rollBack();
        responder_error('No se enviaron datos para actualizar', 400);
    }

    $actualizaciones[] = "actualizado_en = CURRENT_TIMESTAMP";

    $sql = "UPDATE mantenimientos_preventivos SET " . implode(', ', $actualizaciones) . " WHERE id = :id";
    $stmt_up = $conexion->prepare($sql);
    $stmt_up->execute($parametros);

    // Auditoría
    $usuario_actor = obtener_usuario_actor_api($conexion, $datos);
    $actor_id = $usuario_actor ? $usuario_actor['id'] : null;
    $actor_nombre = $usuario_actor ? $usuario_actor['nombre_completo'] : (!empty($datos['tecnico_responsable']) ? $datos['tecnico_responsable'] : 'Técnico de Mantenimiento');

    registrar_auditoria_operacion(
        $conexion,
        'mantenimiento_preventivo',
        $id,
        $mp_actual['codigo_mantenimiento'],
        'actualizacion_mantenimiento',
        null,
        null,
        $actor_id,
        $actor_nombre,
        array(
            'actualizado_por' => $actor_nombre,
            'firma_sistemas_enviada' => !empty($datos['firma_sistemas'])
        )
    );

    $conexion->commit();

    responder_exito('Registro de mantenimiento preventivo actualizado correctamente', array(
        'id' => $id,
        'codigo_mantenimiento' => $mp_actual['codigo_mantenimiento']
    ));

} catch (Exception $e) {
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }
    responder_error('Error al actualizar el registro de mantenimiento: ' . $e->getMessage(), 500);
}
