<?php
/**
 * Endpoint: Crear Ticket de Soporte Técnico
 * Método: POST
 * Compatible con PHP 7.3
 * Archivo: crear_ticket.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/conexion_bd.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_error('Método no permitido. Utilice POST', 405);
}

$datos = obtener_cuerpo_json();

// 1. Validaciones básicas de campos obligatorios
$errores = array();

if (empty(trim($datos['nombre_solicitante'] ?? ''))) {
    $errores[] = 'El campo Nombre del Solicitante es obligatorio.';
}

if (empty(trim($datos['departamento_area'] ?? ''))) {
    $errores[] = 'El campo Departamento / Área es obligatorio.';
}

if (empty(trim($datos['descripcion_problema'] ?? ''))) {
    $errores[] = 'El campo Descripción Detallada del Problema es obligatorio.';
}

$soporte_hardware = !empty($datos['soporte_hardware']) ? true : false;
$soporte_software = !empty($datos['soporte_software']) ? true : false;

if (!$soporte_hardware && !$soporte_software) {
    $errores[] = 'Debe seleccionar al menos un tipo de soporte (Hardware o Software).';
}

if (empty($datos['firma_solicitante'])) {
    $errores[] = 'La firma digital del Solicitante es obligatoria para registrar el ticket.';
}

if (!empty($errores)) {
    responder_error('Datos incompletos o inválidos', 422, $errores);
}

// 2. Extraer y sanear valores
$nombre_solicitante = trim($datos['nombre_solicitante']);
$fecha_solicitud    = !empty($datos['fecha_solicitud']) ? $datos['fecha_solicitud'] : date('Y-m-d');
$departamento_area  = trim($datos['departamento_area']);

$descripcion_problema = trim($datos['descripcion_problema']);

// Campos del Equipo Afectado
$equipo_id          = !empty($datos['equipo_id']) ? (int)$datos['equipo_id'] : null;
$codigo_activo      = !empty($datos['codigo_activo']) ? trim($datos['codigo_activo']) : null;
$numero_serie       = !empty($datos['numero_serie']) ? trim($datos['numero_serie']) : null;
$tipo_equipo        = !empty($datos['tipo_equipo']) ? trim($datos['tipo_equipo']) : null;
$marca_modelo       = !empty($datos['marca_modelo']) ? trim($datos['marca_modelo']) : null;
$sistema_operativo  = !empty($datos['sistema_operativo']) ? trim($datos['sistema_operativo']) : null;
$area_encargado     = !empty($datos['area_encargado']) ? trim($datos['area_encargado']) : null;
$centro_costo       = !empty($datos['centro_costo']) ? trim($datos['centro_costo']) : null;
$guardar_inventario = !empty($datos['guardar_en_inventario']) ? true : false;

// Firmas Digitales
$firma_solicitante  = !empty($datos['firma_solicitante']) ? $datos['firma_solicitante'] : null;
$firma_sistemas     = !empty($datos['firma_sistemas']) ? $datos['firma_sistemas'] : null;

$prioridades_validas = array('urgente', 'alta', 'media', 'baja');
$prioridad = !empty($datos['prioridad']) && in_array(strtolower($datos['prioridad']), $prioridades_validas) 
    ? strtolower($datos['prioridad']) 
    : 'media';

// Campos de soporte técnico (opcionales al registrar)
$tecnico_asignado    = !empty($datos['tecnico_asignado']) ? trim($datos['tecnico_asignado']) : null;
$fecha_hora_atencion = !empty($datos['fecha_hora_atencion']) ? $datos['fecha_hora_atencion'] : null;
$diagnostico         = !empty($datos['diagnostico']) ? trim($datos['diagnostico']) : null;
$solucion_aplicada   = !empty($datos['solucion_aplicada']) ? trim($datos['solucion_aplicada']) : null;
$tipo_resolucion     = !empty($datos['tipo_resolucion']) ? trim($datos['tipo_resolucion']) : null;
$observaciones       = !empty($datos['observaciones_recomendacion']) ? trim($datos['observaciones_recomendacion']) : null;

// Determinar estado inicial
$estado = 'pendiente';
if (!empty($solucion_aplicada)) {
    $estado = 'resuelto';
} elseif (!empty($tecnico_asignado) || !empty($diagnostico)) {
    $estado = 'en_proceso';
}

try {
    $conexion = obtener_conexion_bd();
    $conexion->beginTransaction();

    // 0. Si se solicita registrar el equipo en el inventario o no existía equipo_id pero hay datos
    if ($guardar_inventario || (empty($equipo_id) && (!empty($numero_serie) || !empty($codigo_activo)) && !empty($tipo_equipo))) {
        // Verificar si ya existe registrado
        $stmt_check = $conexion->prepare("SELECT id FROM equipos_inventario WHERE (numero_serie = :serie AND :serie != '') OR (codigo_activo = :cod AND :cod != '') LIMIT 1");
        $stmt_check->execute(array(
            ':serie' => $numero_serie ?? '',
            ':cod'   => $codigo_activo ?? ''
        ));
        $eq_previo = $stmt_check->fetch();

        if ($eq_previo) {
            $equipo_id = $eq_previo['id'];
        } else if (!empty($tipo_equipo)) {
            $sql_eq = "INSERT INTO equipos_inventario (
                        codigo_activo,
                        numero_serie,
                        tipo_equipo,
                        marca_modelo,
                        sistema_operativo,
                        area_encargado,
                        centro_costo
                    ) VALUES (
                        :codigo_activo,
                        :numero_serie,
                        :tipo_equipo,
                        :marca_modelo,
                        :sistema_operativo,
                        :area_encargado,
                        :centro_costo
                    ) RETURNING id";
            $stmt_eq = $conexion->prepare($sql_eq);
            $stmt_eq->execute(array(
                ':codigo_activo'    => $codigo_activo,
                ':numero_serie'     => $numero_serie ?? 'S/N',
                ':tipo_equipo'      => $tipo_equipo,
                ':marca_modelo'     => $marca_modelo ?? 'No especificado',
                ':sistema_operativo'=> $sistema_operativo,
                ':area_encargado'   => $area_encargado ?? $departamento_area,
                ':centro_costo'     => $centro_costo
            ));
            $res_eq = $stmt_eq->fetch();
            $equipo_id = $res_eq['id'];
        }
    }

    // Generar código de ticket único (ej: SOP-2026-0001)
    $stmt_secuencia = $conexion->query("SELECT COALESCE(MAX(id), 0) + 1 AS siguiente FROM tickets_soporte");
    $fila_secuencia = $stmt_secuencia->fetch();
    $consecutivo = $fila_secuencia['siguiente'];
    $codigo_ticket = sprintf('SOP-%s-%04d', date('Y'), $consecutivo);

    // 1. Insertar requerimiento principal en tickets_soporte
    $sql_ticket = "INSERT INTO tickets_soporte (
                codigo_ticket,
                nombre_solicitante,
                fecha_solicitud,
                departamento_area,
                soporte_hardware,
                soporte_software,
                descripcion_problema,
                equipo_id,
                codigo_activo,
                numero_serie,
                tipo_equipo,
                marca_modelo,
                sistema_operativo,
                area_encargado,
                centro_costo,
                prioridad,
                estado,
                firma_solicitante,
                firma_sistemas
            ) VALUES (
                :codigo_ticket,
                :nombre_solicitante,
                :fecha_solicitud,
                :departamento_area,
                :soporte_hardware,
                :soporte_software,
                :descripcion_problema,
                :equipo_id,
                :codigo_activo,
                :numero_serie,
                :tipo_equipo,
                :marca_modelo,
                :sistema_operativo,
                :area_encargado,
                :centro_costo,
                :prioridad,
                :estado,
                :firma_solicitante,
                :firma_sistemas
            ) RETURNING id, codigo_ticket, creado_en";

    $stmt_ticket = $conexion->prepare($sql_ticket);
    $stmt_ticket->execute(array(
        ':codigo_ticket'        => $codigo_ticket,
        ':nombre_solicitante'   => $nombre_solicitante,
        ':fecha_solicitud'      => $fecha_solicitud,
        ':departamento_area'    => $departamento_area,
        ':soporte_hardware'     => $soporte_hardware ? 'true' : 'false',
        ':soporte_software'     => $soporte_software ? 'true' : 'false',
        ':descripcion_problema' => $descripcion_problema,
        ':equipo_id'            => $equipo_id,
        ':codigo_activo'        => $codigo_activo,
        ':numero_serie'         => $numero_serie,
        ':tipo_equipo'          => $tipo_equipo,
        ':marca_modelo'         => $marca_modelo,
        ':sistema_operativo'    => $sistema_operativo,
        ':area_encargado'       => $area_encargado,
        ':centro_costo'         => $centro_costo,
        ':prioridad'            => $prioridad,
        ':estado'               => $estado,
        ':firma_solicitante'    => $firma_solicitante,
        ':firma_sistemas'       => $firma_sistemas
    ));

    $resultado = $stmt_ticket->fetch();
    $ticket_id = $resultado['id'];

    // 2. Si se ingresaron datos de soporte técnico, registrar atención en atenciones_soporte
    $tiene_datos_tecnicos = !empty($tecnico_asignado) || !empty($diagnostico) || !empty($solucion_aplicada) || !empty($observaciones) || !empty($tipo_resolucion) || !empty($firma_sistemas);
    
    if ($tiene_datos_tecnicos) {
        $sql_atencion = "INSERT INTO atenciones_soporte (
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

        $stmt_atencion = $conexion->prepare($sql_atencion);
        $stmt_atencion->execute(array(
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

    $conexion->commit();

    responder_exito('Ticket de soporte técnico registrado con éxito', array(
        'id'            => $ticket_id,
        'codigo_ticket' => $resultado['codigo_ticket'],
        'estado'        => $estado,
        'creado_en'     => $resultado['creado_en']
    ), 201);

} catch (Exception $e) {
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }
    responder_error('Error al guardar el ticket: ' . $e->getMessage(), 500);
}
