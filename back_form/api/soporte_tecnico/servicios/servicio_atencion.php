<?php
/**
 * Submódulo: Registro de Atención y Resolución Técnica de Soporte
 * Compatible con PHP 7.3
 * Archivo: back_form/api/soporte_tecnico/servicios/servicio_atencion.php
 */

/**
 * Registra la atención técnica vinculada al ticket si se suministraron datos técnicos iniciales
 * 
 * @param PDO    $conexion
 * @param int    $ticket_id
 * @param array  $datos
 * @param string|null $firma_sistemas
 * @return bool
 */
function registrar_atencion_tecnica($conexion, $ticket_id, $datos, $firma_sistemas = null) {
    $tecnico_id          = !empty($datos['tecnico_id']) ? (int)$datos['tecnico_id'] : (!empty($datos['tecnico_asignado_id']) ? (int)$datos['tecnico_asignado_id'] : null);
    $asignado_por_id     = !empty($datos['asignado_por_id']) ? (int)$datos['asignado_por_id'] : null;
    $tecnico_asignado    = !empty($datos['tecnico_asignado']) ? trim($datos['tecnico_asignado']) : null;
    $fecha_hora_atencion = !empty($datos['fecha_hora_atencion']) ? $datos['fecha_hora_atencion'] : null;
    $diagnostico         = !empty($datos['diagnostico']) ? trim($datos['diagnostico']) : null;
    $solucion_aplicada   = !empty($datos['solucion_aplicada']) ? trim($datos['solucion_aplicada']) : null;
    $tipo_resolucion     = !empty($datos['tipo_resolucion']) ? trim($datos['tipo_resolucion']) : null;
    $observaciones       = !empty($datos['observaciones_recomendacion']) ? trim($datos['observaciones_recomendacion']) : null;

    $tiene_datos_tecnicos = !empty($tecnico_asignado) || 
                            !empty($tecnico_id) ||
                            !empty($diagnostico) || 
                            !empty($solucion_aplicada) || 
                            !empty($observaciones) || 
                            !empty($tipo_resolucion) || 
                            !empty($firma_sistemas);

    if (!$tiene_datos_tecnicos) {
        return false;
    }

    $sql = "INSERT INTO atenciones_soporte (
                ticket_id,
                tecnico_id,
                asignado_por_id,
                fecha_hora_atencion,
                tecnico_asignado,
                tipo_resolucion,
                diagnostico,
                solucion_aplicada,
                observaciones_recomendacion,
                firma_sistemas
            ) VALUES (
                :ticket_id,
                :tecnico_id,
                :asignado_por_id,
                :fecha_hora_atencion,
                :tecnico_asignado,
                :tipo_resolucion,
                :diagnostico,
                :solucion_aplicada,
                :observaciones_recomendacion,
                :firma_sistemas
            )";

    $stmt = $conexion->prepare($sql);
    return $stmt->execute(array(
        ':ticket_id'                  => $ticket_id,
        ':tecnico_id'                 => $tecnico_id,
        ':asignado_por_id'            => $asignado_por_id,
        ':fecha_hora_atencion'        => !empty($fecha_hora_atencion) ? $fecha_hora_atencion : date('Y-m-d H:i:s'),
        ':tecnico_asignado'           => !empty($tecnico_asignado) ? $tecnico_asignado : 'Área de Soporte Técnico',
        ':tipo_resolucion'            => $tipo_resolucion,
        ':diagnostico'                => $diagnostico,
        ':solucion_aplicada'          => $solucion_aplicada,
        ':observaciones_recomendacion'=> $observaciones,
        ':firma_sistemas'             => $firma_sistemas
    ));
}

/**
 * Guarda o actualiza la atención técnica asociada al ticket durante la gestión
 * 
 * @param PDO         $conexion
 * @param int         $ticket_id
 * @param array       $datos
 * @param string|null $firma_sistemas
 * @return bool
 */
function guardar_o_actualizar_atencion($conexion, $ticket_id, $datos, $firma_sistemas = null) {
    $tecnico_id          = !empty($datos['tecnico_asignado_id']) ? (int)$datos['tecnico_asignado_id'] : (!empty($datos['tecnico_id']) ? (int)$datos['tecnico_id'] : null);
    $asignado_por_id     = !empty($datos['asignado_por_id']) ? (int)$datos['asignado_por_id'] : (!empty($datos['usuario_id']) ? (int)$datos['usuario_id'] : null);
    $tecnico_asignado    = !empty($datos['tecnico_asignado']) ? trim($datos['tecnico_asignado']) : null;
    $fecha_hora_atencion = !empty($datos['fecha_hora_atencion']) ? $datos['fecha_hora_atencion'] : date('Y-m-d H:i:s');
    $diagnostico         = (!empty($datos['diagnostico']) && trim($datos['diagnostico']) !== '') ? trim($datos['diagnostico']) : null;
    $solucion_aplicada   = (!empty($datos['solucion_aplicada']) && trim($datos['solucion_aplicada']) !== '') ? trim($datos['solucion_aplicada']) : null;
    $tipo_resolucion     = (!empty($datos['tipo_resolucion']) && trim($datos['tipo_resolucion']) !== '') ? trim($datos['tipo_resolucion']) : null;
    $observaciones       = (!empty($datos['observaciones_recomendacion']) && trim($datos['observaciones_recomendacion']) !== '') ? trim($datos['observaciones_recomendacion']) : null;
    $firma               = !empty($firma_sistemas) ? $firma_sistemas : (!empty($datos['firma_sistemas']) ? $datos['firma_sistemas'] : null);

    // Comprobar si ya existe atención previa
    $stmt_existe = $conexion->prepare("SELECT id FROM atenciones_soporte WHERE ticket_id = :ticket_id ORDER BY id DESC LIMIT 1");
    $stmt_existe->execute(array(':ticket_id' => $ticket_id));
    $atencion_existente = $stmt_existe->fetch();

    if ($atencion_existente) {
        $sql = "UPDATE atenciones_soporte SET 
                    tecnico_id = COALESCE(:tecnico_id, tecnico_id),
                    asignado_por_id = COALESCE(:asignado_por_id, asignado_por_id),
                    fecha_hora_atencion = :fecha_hora_atencion,
                    tecnico_asignado = :tecnico_asignado,
                    tipo_resolucion = :tipo_resolucion,
                    diagnostico = :diagnostico,
                    solucion_aplicada = :solucion_aplicada,
                    observaciones_recomendacion = :observaciones_recomendacion,
                    firma_sistemas = COALESCE(:firma_sistemas, firma_sistemas)
                WHERE id = :atencion_id";
        $stmt = $conexion->prepare($sql);
        return $stmt->execute(array(
            ':atencion_id'                => $atencion_existente['id'],
            ':tecnico_id'                 => $tecnico_id,
            ':asignado_por_id'            => $asignado_por_id,
            ':fecha_hora_atencion'        => $fecha_hora_atencion,
            ':tecnico_asignado'           => !empty($tecnico_asignado) ? $tecnico_asignado : 'Área de Soporte Técnico',
            ':tipo_resolucion'            => $tipo_resolucion,
            ':diagnostico'                => $diagnostico,
            ':solucion_aplicada'          => $solucion_aplicada,
            ':observaciones_recomendacion'=> $observaciones,
            ':firma_sistemas'             => $firma
        ));
    } else {
        $sql = "INSERT INTO atenciones_soporte (
                    ticket_id,
                    tecnico_id,
                    asignado_por_id,
                    fecha_hora_atencion,
                    tecnico_asignado,
                    tipo_resolucion,
                    diagnostico,
                    solucion_aplicada,
                    observaciones_recomendacion,
                    firma_sistemas
                ) VALUES (
                    :ticket_id,
                    :tecnico_id,
                    :asignado_por_id,
                    :fecha_hora_atencion,
                    :tecnico_asignado,
                    :tipo_resolucion,
                    :diagnostico,
                    :solucion_aplicada,
                    :observaciones_recomendacion,
                    :firma_sistemas
                )";
        $stmt = $conexion->prepare($sql);
        return $stmt->execute(array(
            ':ticket_id'                  => $ticket_id,
            ':tecnico_id'                 => $tecnico_id,
            ':asignado_por_id'            => $asignado_por_id,
            ':fecha_hora_atencion'        => $fecha_hora_atencion,
            ':tecnico_asignado'           => !empty($tecnico_asignado) ? $tecnico_asignado : 'Área de Soporte Técnico',
            ':tipo_resolucion'            => $tipo_resolucion,
            ':diagnostico'                => $diagnostico,
            ':solucion_aplicada'          => $solucion_aplicada,
            ':observaciones_recomendacion'=> $observaciones,
            ':firma_sistemas'             => $firma
        ));
    }
}

