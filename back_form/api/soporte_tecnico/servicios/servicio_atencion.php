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
    $tecnico_asignado    = !empty($datos['tecnico_asignado']) ? trim($datos['tecnico_asignado']) : null;
    $fecha_hora_atencion = !empty($datos['fecha_hora_atencion']) ? $datos['fecha_hora_atencion'] : null;
    $diagnostico         = !empty($datos['diagnostico']) ? trim($datos['diagnostico']) : null;
    $solucion_aplicada   = !empty($datos['solucion_aplicada']) ? trim($datos['solucion_aplicada']) : null;
    $tipo_resolucion     = !empty($datos['tipo_resolucion']) ? trim($datos['tipo_resolucion']) : null;
    $observaciones       = !empty($datos['observaciones_recomendacion']) ? trim($datos['observaciones_recomendacion']) : null;

    $tiene_datos_tecnicos = !empty($tecnico_asignado) || 
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

    $stmt = $conexion->prepare($sql);
    return $stmt->execute(array(
        ':ticket_id'                  => $ticket_id,
        ':fecha_hora_atencion'        => !empty($fecha_hora_atencion) ? $fecha_hora_atencion : date('Y-m-d H:i:s'),
        ':tecnico_asignado'           => !empty($tecnico_asignado) ? $tecnico_asignado : 'Área de Soporte Técnico',
        ':tipo_resolucion'            => $tipo_resolucion,
        ':diagnostico'                => $diagnostico,
        ':solucion_aplicada'          => $solucion_aplicada,
        ':observaciones_recomendacion'=> $observaciones,
        ':firma_sistemas'             => $firma_sistemas
    ));
}
