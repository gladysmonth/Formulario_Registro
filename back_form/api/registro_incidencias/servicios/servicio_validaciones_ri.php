<?php
/**
 * Submódulo: Validación de Datos para Registro de Incidencias de Sistemas
 * Compatible con PHP 7.3
 * Archivo: back_form/api/registro_incidencias/servicios/servicio_validaciones_ri.php
 */

/**
 * Valida los datos recibidos para crear o actualizar una incidencia
 *
 * @param array $datos
 * @param bool $es_creacion
 * @return array ['valido' => bool, 'errores' => array, 'datos_procesados' => array]
 */
function validar_datos_incidencia($datos, $es_creacion = true) {
    $errores = array();

    // 1. Datos Generales
    $responsable_reporte = isset($datos['responsable_reporte']) ? trim($datos['responsable_reporte']) : '';
    if (empty($responsable_reporte)) {
        $errores[] = 'El campo Responsable del Reporte es obligatorio.';
    }

    $fecha_hora_reporte = !empty($datos['fecha_hora_reporte']) ? $datos['fecha_hora_reporte'] : date('Y-m-d H:i:s');
    // Normalizar formato si viene de datetime-local (HTML5) con 'T'
    $fecha_hora_reporte = str_replace('T', ' ', $fecha_hora_reporte);

    // 2. Plataforma / Sistema
    $sistema_erp_sai         = !empty($datos['sistema_erp_sai']) ? true : false;
    $sistema_cobranzas_netcob = !empty($datos['sistema_cobranzas_netcob']) ? true : false;
    $sistema_otros           = !empty($datos['sistema_otros']) ? true : false;
    $sistema_otros_detalle   = isset($datos['sistema_otros_detalle']) ? trim($datos['sistema_otros_detalle']) : null;

    if ($sistema_otros && empty($sistema_otros_detalle)) {
        $errores[] = 'Debe especificar el detalle en "Otros Sistemas / Complementos" al haberlo marcado.';
    }

    if (!$sistema_erp_sai && !$sistema_cobranzas_netcob && !$sistema_otros) {
        $errores[] = 'Debe seleccionar al menos una Plataforma / Sistema afectado.';
    }

    // 3. Naturaleza Técnica del Fallo (Marcar y rellenar al marcado)
    $fallo_base_datos                 = !empty($datos['fallo_base_datos']) ? true : false;
    $detalle_base_datos               = isset($datos['detalle_base_datos']) ? trim($datos['detalle_base_datos']) : null;
    $fallo_infraestructura_servidor   = !empty($datos['fallo_infraestructura_servidor']) ? true : false;
    $detalle_infraestructura_servidor = isset($datos['detalle_infraestructura_servidor']) ? trim($datos['detalle_infraestructura_servidor']) : null;
    $fallo_enlaces_conectividad       = !empty($datos['fallo_enlaces_conectividad']) ? true : false;
    $detalle_enlaces_conectividad     = isset($datos['detalle_enlaces_conectividad']) ? trim($datos['detalle_enlaces_conectividad']) : null;

    if ($fallo_base_datos && empty($detalle_base_datos)) {
        $errores[] = 'Debe ingresar la descripción o tipo de error para Base de Datos.';
    }
    if ($fallo_infraestructura_servidor && empty($detalle_infraestructura_servidor)) {
        $errores[] = 'Debe ingresar la descripción o tipo de error para Infraestructura / Servidor.';
    }
    if ($fallo_enlaces_conectividad && empty($detalle_enlaces_conectividad)) {
        $errores[] = 'Debe ingresar la descripción o tipo de error para Enlaces / Conectividad.';
    }

    if (!$fallo_base_datos && !$fallo_infraestructura_servidor && !$fallo_enlaces_conectividad) {
        $errores[] = 'Debe indicar al menos un componente en la Naturaleza Técnica del Fallo.';
    }

    // 4. Nivel de Criticidad Institucional
    $criticidades_validas = array('critica_nivel_1', 'alta_nivel_2', 'media_baja_nivel_3');
    $nivel_criticidad = isset($datos['nivel_criticidad']) ? trim($datos['nivel_criticidad']) : '';
    if (empty($nivel_criticidad) || !in_array($nivel_criticidad, $criticidades_validas, true)) {
        $errores[] = 'Debe seleccionar un Nivel de Criticidad Institucional válido (Nivel 1, Nivel 2 o Nivel 3).';
    }

    // Estado Operativo
    $estados_validos = array('abierta', 'en_atencion', 'resuelta', 'cerrada');
    $estado = isset($datos['estado']) ? trim($datos['estado']) : 'abierta';
    if (!in_array($estado, $estados_validos, true)) {
        $estado = 'abierta';
    }

    // 5. Descripción Técnica y Logs de Error
    $descripcion_tecnica_logs = isset($datos['descripcion_tecnica_logs']) ? trim($datos['descripcion_tecnica_logs']) : '';
    if (empty($descripcion_tecnica_logs)) {
        $errores[] = 'El campo Descripción Técnica y Logs de Error es obligatorio.';
    }

    // 6. Solución Aplicada y Tiempos
    $accion_realizada = isset($datos['accion_realizada']) && trim($datos['accion_realizada']) !== '' ? trim($datos['accion_realizada']) : null;
    $detalle_tecnico  = isset($datos['detalle_tecnico']) && trim($datos['detalle_tecnico']) !== '' ? trim($datos['detalle_tecnico']) : null;
    $fecha_hora_cierre = !empty($datos['fecha_hora_cierre']) ? str_replace('T', ' ', $datos['fecha_hora_cierre']) : null;

    if (($estado === 'resuelta' || $estado === 'cerrada') && empty($accion_realizada)) {
        $errores[] = 'La Acción Realizada es obligatoria para marcar la incidencia como resuelta o cerrada.';
    }

    // Si hay acción realizada y no viene fecha de cierre, asignar fecha actual
    if (!empty($accion_realizada) && empty($fecha_hora_cierre)) {
        $fecha_hora_cierre = date('Y-m-d H:i:s');
    }

    // 7. Seguimiento y Recomendaciones
    $observaciones_recomendaciones = isset($datos['observaciones_recomendaciones']) && trim($datos['observaciones_recomendaciones']) !== '' 
        ? trim($datos['observaciones_recomendaciones']) 
        : null;

    $datos_procesados = array(
        'responsable_reporte'              => $responsable_reporte,
        'fecha_hora_reporte'              => $fecha_hora_reporte,
        'sistema_erp_sai'                  => $sistema_erp_sai,
        'sistema_cobranzas_netcob'         => $sistema_cobranzas_netcob,
        'sistema_otros'                    => $sistema_otros,
        'sistema_otros_detalle'            => $sistema_otros ? $sistema_otros_detalle : null,
        'fallo_base_datos'                 => $fallo_base_datos,
        'detalle_base_datos'               => $fallo_base_datos ? $detalle_base_datos : null,
        'fallo_infraestructura_servidor'   => $fallo_infraestructura_servidor,
        'detalle_infraestructura_servidor' => $fallo_infraestructura_servidor ? $detalle_infraestructura_servidor : null,
        'fallo_enlaces_conectividad'       => $fallo_enlaces_conectividad,
        'detalle_enlaces_conectividad'     => $fallo_enlaces_conectividad ? $detalle_enlaces_conectividad : null,
        'nivel_criticidad'                 => $nivel_criticidad,
        'estado'                           => $estado,
        'descripcion_tecnica_logs'         => $descripcion_tecnica_logs,
        'accion_realizada'                 => $accion_realizada,
        'detalle_tecnico'                  => $detalle_tecnico,
        'fecha_hora_cierre'                => $fecha_hora_cierre,
        'observaciones_recomendaciones'    => $observaciones_recomendaciones
    );

    return array(
        'valido'           => empty($errores),
        'errores'          => $errores,
        'datos_procesados' => $datos_procesados
    );
}
