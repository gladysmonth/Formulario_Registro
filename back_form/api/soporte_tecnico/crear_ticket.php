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

if (!empty($errores)) {
    responder_error('Datos incompletos o inválidos', 422, $errores);
}

// 2. Extraer y sanear valores
$nombre_solicitante = trim($datos['nombre_solicitante']);
$fecha_solicitud    = !empty($datos['fecha_solicitud']) ? $datos['fecha_solicitud'] : date('Y-m-d');
$departamento_area  = trim($datos['departamento_area']);

$descripcion_problema = trim($datos['descripcion_problema']);
$numero_serie         = !empty($datos['numero_serie']) ? trim($datos['numero_serie']) : null;
$marca_modelo         = !empty($datos['marca_modelo']) ? trim($datos['marca_modelo']) : null;
$sistema_operativo    = !empty($datos['sistema_operativo']) ? trim($datos['sistema_operativo']) : null;

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

    // Generar código de ticket único (ej: SOP-2026-0003)
    $stmt_secuencia = $conexion->query("SELECT COALESCE(MAX(id), 0) + 1 AS siguiente FROM tickets_soporte");
    $fila_secuencia = $stmt_secuencia->fetch();
    $consecutivo = $fila_secuencia['siguiente'];
    $codigo_ticket = sprintf('SOP-%s-%04d', date('Y'), $consecutivo);

    $sql = "INSERT INTO tickets_soporte (
                codigo_ticket,
                nombre_solicitante,
                fecha_solicitud,
                departamento_area,
                soporte_hardware,
                soporte_software,
                descripcion_problema,
                numero_serie,
                marca_modelo,
                sistema_operativo,
                prioridad,
                estado,
                fecha_hora_atencion,
                tecnico_asignado,
                diagnostico,
                solucion_aplicada,
                tipo_resolucion,
                observaciones_recomendacion
            ) VALUES (
                :codigo_ticket,
                :nombre_solicitante,
                :fecha_solicitud,
                :departamento_area,
                :soporte_hardware,
                :soporte_software,
                :descripcion_problema,
                :numero_serie,
                :marca_modelo,
                :sistema_operativo,
                :prioridad,
                :estado,
                :fecha_hora_atencion,
                :tecnico_asignado,
                :diagnostico,
                :solucion_aplicada,
                :tipo_resolucion,
                :observaciones_recomendacion
            ) RETURNING id, codigo_ticket, creado_en";

    $stmt = $conexion->prepare($sql);
    $stmt->execute(array(
        ':codigo_ticket'              => $codigo_ticket,
        ':nombre_solicitante'         => $nombre_solicitante,
        ':fecha_solicitud'            => $fecha_solicitud,
        ':departamento_area'          => $departamento_area,
        ':soporte_hardware'           => $soporte_hardware ? 'true' : 'false',
        ':soporte_software'           => $soporte_software ? 'true' : 'false',
        ':descripcion_problema'       => $descripcion_problema,
        ':numero_serie'               => $numero_serie,
        ':marca_modelo'               => $marca_modelo,
        ':sistema_operativo'          => $sistema_operativo,
        ':prioridad'                  => $prioridad,
        ':estado'                     => $estado,
        ':fecha_hora_atencion'        => $fecha_hora_atencion,
        ':tecnico_asignado'           => $tecnico_asignado,
        ':diagnostico'                => $diagnostico,
        ':solucion_aplicada'          => $solucion_aplicada,
        ':tipo_resolucion'            => $tipo_resolucion,
        ':observaciones_recomendacion'=> $observaciones
    ));

    $resultado = $stmt->fetch();

    responder_exito('Ticket de soporte técnico registrado con éxito', array(
        'id'            => $resultado['id'],
        'codigo_ticket' => $resultado['codigo_ticket'],
        'estado'        => $estado,
        'creado_en'     => $resultado['creado_en']
    ), 201);

} catch (Exception $e) {
    responder_error('Error al guardar el ticket: ' . $e->getMessage(), 500);
}
