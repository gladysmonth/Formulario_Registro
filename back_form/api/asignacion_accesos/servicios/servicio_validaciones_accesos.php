<?php
/**
 * Submódulo: Validaciones de Negocio y Saneamiento de Datos de Accesos
 * Compatible con PHP 7.3
 * Archivo: back_form/api/asignacion_accesos/servicios/servicio_validaciones_accesos.php
 */

/**
 * Valida y sanea los datos de entrada para la creación de una solicitud de accesos
 *
 * @param array $datos
 * @return array ['valido' => bool, 'errores' => array, 'datos_limpios' => array]
 */
function validar_datos_solicitud_accesos($datos) {
    $errores = array();
    $datos_limpios = array();

    // 1. DATOS GENERALES (Requeridos)
    $nombre = !empty($datos['nombre_solicitante']) ? trim($datos['nombre_solicitante']) : '';
    if (empty($nombre)) {
        $errores[] = 'El nombre del solicitante es obligatorio.';
    } elseif (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 150) {
        $errores[] = 'El nombre del solicitante debe contener entre 3 y 150 caracteres.';
    }
    $datos_limpios['nombre_solicitante'] = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');

    $cargo = !empty($datos['cargo_solicitante']) ? trim($datos['cargo_solicitante']) : '';
    if (empty($cargo)) {
        $errores[] = 'El cargo del solicitante es obligatorio.';
    } elseif (mb_strlen($cargo) > 150) {
        $errores[] = 'El cargo no debe superar los 150 caracteres.';
    }
    $datos_limpios['cargo_solicitante'] = htmlspecialchars($cargo, ENT_QUOTES, 'UTF-8');

    $area = !empty($datos['area_departamento']) ? trim($datos['area_departamento']) : '';
    if (empty($area)) {
        $errores[] = 'El área o departamento es obligatorio.';
    } elseif (mb_strlen($area) > 150) {
        $errores[] = 'El área o departamento no debe superar los 150 caracteres.';
    }
    $datos_limpios['area_departamento'] = htmlspecialchars($area, ENT_QUOTES, 'UTF-8');

    // Nro de Solicitud (si el usuario lo personalizó)
    $datos_limpios['nro_solicitud'] = !empty($datos['nro_solicitud']) ? trim($datos['nro_solicitud']) : null;

    // Fecha de solicitud
    $datos_limpios['fecha_solicitud'] = !empty($datos['fecha_solicitud']) ? trim($datos['fecha_solicitud']) : date('Y-m-d H:i:s');

    // 2. PLATAFORMA / SISTEMA (Al menos una debe seleccionarse)
    $sai    = !empty($datos['sistema_erp_sai']) && filter_var($datos['sistema_erp_sai'], FILTER_VALIDATE_BOOLEAN);
    $netcob = !empty($datos['sistema_cobranzas_netcob']) && filter_var($datos['sistema_cobranzas_netcob'], FILTER_VALIDATE_BOOLEAN);
    $otros  = !empty($datos['sistema_otros']) && filter_var($datos['sistema_otros'], FILTER_VALIDATE_BOOLEAN);

    if (!$sai && !$netcob && !$otros) {
        $errores[] = 'Debe seleccionar al menos una plataforma o sistema (ERP SAI, NETCOB u Otros).';
    }

    $otros_detalle = !empty($datos['sistema_otros_detalle']) ? trim($datos['sistema_otros_detalle']) : null;
    if ($otros && empty($otros_detalle)) {
        $errores[] = 'Ha marcado la opción "Otros Sistemas / Complementos", por lo que debe especificar el detalle.';
    }

    $datos_limpios['sistema_erp_sai']          = $sai;
    $datos_limpios['sistema_cobranzas_netcob'] = $netcob;
    $datos_limpios['sistema_otros']            = $otros;
    $datos_limpios['sistema_otros_detalle']    = $otros ? htmlspecialchars($otros_detalle, ENT_QUOTES, 'UTF-8') : null;

    // 3. DATOS DEL USUARIO
    $es_nuevo = isset($datos['es_usuario_nuevo']) ? filter_var($datos['es_usuario_nuevo'], FILTER_VALIDATE_BOOLEAN) : true;
    $usuario_detalles = !empty($datos['nombre_usuario_detalles']) ? trim($datos['nombre_usuario_detalles']) : null;

    $datos_limpios['es_usuario_nuevo']        = $es_nuevo;
    $datos_limpios['nombre_usuario_detalles'] = !empty($usuario_detalles) ? htmlspecialchars($usuario_detalles, ENT_QUOTES, 'UTF-8') : null;

    // 4. REQUERIMIENTOS DE ACCESOS (Requerido)
    $requerimientos = !empty($datos['requerimientos_accesos']) ? trim($datos['requerimientos_accesos']) : '';
    if (empty($requerimientos)) {
        $errores[] = 'El campo de requerimientos de accesos (perfiles, permisos o módulos) es obligatorio.';
    } elseif (mb_strlen($requerimientos) < 5) {
        $errores[] = 'Por favor proporcione una descripción detallada de los accesos requeridos (mínimo 5 caracteres).';
    }
    $datos_limpios['requerimientos_accesos'] = htmlspecialchars($requerimientos, ENT_QUOTES, 'UTF-8');

    // 5. DEPARTAMENTO DE SISTEMAS (Atención opcional en creación)
    $atendido_por = !empty($datos['atendido_por']) ? trim($datos['atendido_por']) : null;
    $datos_limpios['atendido_por'] = !empty($atendido_por) ? htmlspecialchars($atendido_por, ENT_QUOTES, 'UTF-8') : null;

    $fecha_atencion = !empty($datos['fecha_hora_atencion']) ? trim($datos['fecha_hora_atencion']) : null;
    $datos_limpios['fecha_hora_atencion'] = $fecha_atencion;

    $estado = !empty($datos['estado']) ? trim($datos['estado']) : 'pendiente';
    $estados_permitidos = array('pendiente', 'en_proceso', 'atendido', 'rechazado');
    if (!in_array($estado, $estados_permitidos)) {
        $estado = 'pendiente';
    }
    $datos_limpios['estado'] = $estado;

    // 6. COMENTARIOS
    $comentarios = !empty($datos['comentarios_sistemas']) ? trim($datos['comentarios_sistemas']) : null;
    $datos_limpios['comentarios_sistemas'] = !empty($comentarios) ? htmlspecialchars($comentarios, ENT_QUOTES, 'UTF-8') : null;

    // Nombre de quien autoriza
    $autoriza = !empty($datos['nombre_autoriza']) ? trim($datos['nombre_autoriza']) : null;
    $datos_limpios['nombre_autoriza'] = !empty($autoriza) ? htmlspecialchars($autoriza, ENT_QUOTES, 'UTF-8') : null;

    return array(
        'valido'        => empty($errores),
        'errores'       => $errores,
        'datos_limpios' => $datos_limpios
    );
}
