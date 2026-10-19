<?php
/**
 * Endpoint: Crear Ticket de Soporte Técnico (Orquestador Principal)
 * Método: POST
 * Compatible con PHP 7.3
 * Archivo: crear_ticket.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/conexion_bd.php';

// Inclusión de submódulos de servicios especializados
require_once __DIR__ . '/servicios/servicio_equipos.php';
require_once __DIR__ . '/servicios/servicio_firmas.php';
require_once __DIR__ . '/servicios/servicio_atencion.php';
require_once __DIR__ . '/servicios/servicio_codigo_soporte.php';
require_once __DIR__ . '/servicios/servicio_telemetria_soporte.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_error('Método no permitido. Utilice POST', 405);
}

$datos = obtener_cuerpo_json();

// 1. Validar requerimientos obligatorios generales
$errores = array();

$nombre_solicitante = isset($datos['nombre_solicitante']) ? trim($datos['nombre_solicitante']) : '';
$departamento_area  = isset($datos['departamento_area']) ? trim($datos['departamento_area']) : '';
$descripcion_prob   = isset($datos['descripcion_problema']) ? trim($datos['descripcion_problema']) : '';

if (empty($nombre_solicitante)) {
    $errores[] = 'El campo Nombre del Solicitante es obligatorio.';
}

if (empty($departamento_area)) {
    $errores[] = 'El campo Departamento / Área es obligatorio.';
}

if (empty($descripcion_prob)) {
    $errores[] = 'El campo Descripción Detallada del Problema es obligatorio.';
}

$soporte_hardware = !empty($datos['soporte_hardware']) ? true : false;
$soporte_software = !empty($datos['soporte_software']) ? true : false;

if (!$soporte_hardware && !$soporte_software) {
    $errores[] = 'Debe seleccionar al menos un tipo de soporte (Hardware o Software).';
}

// 2. Control de Rol RBAC y Determinación de Resolución
$rol_usuario = !empty($datos['rol_usuario']) ? strtolower(trim($datos['rol_usuario'])) : (!empty($datos['rol']) ? strtolower(trim($datos['rol'])) : 'usuario');
$es_personal_tecnico = in_array($rol_usuario, array('admin', 'tecnico'));

if (!$es_personal_tecnico) {
    $datos['solucion_aplicada']   = null;
    $datos['diagnostico']         = null;
    $datos['firma_sistemas']      = null;
    $datos['tecnico_asignado']    = null;
    $datos['tecnico_asignado_id'] = null;
    $datos['estado']              = 'pendiente';
}

$es_resolucion = $es_personal_tecnico && (!empty($datos['solucion_aplicada']) || (!empty($datos['estado']) && strtolower($datos['estado']) === 'resuelto'));
$resultado_firmas = validar_y_procesar_firmas($datos, $es_resolucion);
if (!$resultado_firmas['valido']) {
    $errores = array_merge($errores, $resultado_firmas['errores']);
}

if (!empty($errores)) {
    responder_error('Datos incompletos o inválidos', 422, $errores);
}

// 3. Extraer y sanear valores
$fecha_solicitud        = !empty($datos['fecha_solicitud']) ? $datos['fecha_solicitud'] : date('Y-m-d');
$usuario_solicitante_id = !empty($datos['usuario_solicitante_id']) ? (int)$datos['usuario_solicitante_id'] : (!empty($datos['usuario_id']) ? (int)$datos['usuario_id'] : null);
$tecnico_asignado_id    = !empty($datos['tecnico_asignado_id']) ? (int)$datos['tecnico_asignado_id'] : null;

$prioridades_validas  = array('urgente', 'alta', 'media', 'baja');
$prioridad            = !empty($datos['prioridad']) && in_array(strtolower($datos['prioridad']), $prioridades_validas) 
    ? strtolower($datos['prioridad']) 
    : 'media';

// Determinar estado inicial
$estado = 'pendiente';
if ($es_resolucion) {
    $estado = 'resuelto';
} elseif ($es_personal_tecnico && (!empty($datos['tecnico_asignado']) || !empty($tecnico_asignado_id) || !empty($datos['diagnostico']))) {
    $estado = 'en_proceso';
}

try {
    $conexion = obtener_conexion_bd();
    $conexion->beginTransaction();

    // 4. Submódulo de Equipos: Verificar o registrar en inventario
    $equipo = procesar_equipo_en_inventario($conexion, $datos);

    // 5. Submódulo de Telemetría: Resolver MAC y vincular último diagnóstico
    $info_telemetria = resolver_telemetria_soporte($conexion, $datos, $equipo);
    $direccion_mac   = $info_telemetria['direccion_mac'];
    $diagnostico_id  = $info_telemetria['diagnostico_id'];

    // 6. Submódulo de Código: Generar correlativo institucional
    $codigo_ticket = generar_codigo_ticket($conexion);

    // 7. Insertar ticket principal
    $sql_ticket = "INSERT INTO tickets_soporte (
                codigo_ticket,
                nombre_solicitante,
                usuario_solicitante_id,
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
                area,
                encargado,
                centro_costo,
                prioridad,
                estado,
                diagnostico_id,
                direccion_mac,
                tecnico_asignado_id,
                firma_solicitante,
                firma_sistemas
            ) VALUES (
                :codigo_ticket,
                :nombre_solicitante,
                :usuario_solicitante_id,
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
                :area,
                :encargado,
                :centro_costo,
                :prioridad,
                :estado,
                :diagnostico_id,
                :direccion_mac,
                :tecnico_asignado_id,
                :firma_solicitante,
                :firma_sistemas
            ) RETURNING id, codigo_ticket, creado_en";

    $stmt_ticket = $conexion->prepare($sql_ticket);
    $stmt_ticket->execute(array(
        ':codigo_ticket'          => $codigo_ticket,
        ':nombre_solicitante'     => $nombre_solicitante,
        ':usuario_solicitante_id' => $usuario_solicitante_id,
        ':fecha_solicitud'        => $fecha_solicitud,
        ':departamento_area'      => $departamento_area,
        ':soporte_hardware'       => $soporte_hardware ? 'true' : 'false',
        ':soporte_software'       => $soporte_software ? 'true' : 'false',
        ':descripcion_problema'   => $descripcion_prob,
        ':equipo_id'              => $equipo['equipo_id'],
        ':codigo_activo'          => $equipo['codigo_activo'],
        ':numero_serie'           => $equipo['numero_serie'],
        ':tipo_equipo'            => $equipo['tipo_equipo'],
        ':marca_modelo'           => $equipo['marca_modelo'],
        ':sistema_operativo'      => $equipo['sistema_operativo'],
        ':area'                   => $equipo['area'],
        ':encargado'              => $equipo['encargado'],
        ':centro_costo'           => $equipo['centro_costo'],
        ':prioridad'              => $prioridad,
        ':estado'                 => $estado,
        ':diagnostico_id'         => $diagnostico_id,
        ':direccion_mac'          => $direccion_mac,
        ':tecnico_asignado_id'    => $tecnico_asignado_id,
        ':firma_solicitante'      => $resultado_firmas['firma_solicitante'],
        ':firma_sistemas'         => $resultado_firmas['firma_sistemas']
    ));

    $resultado = $stmt_ticket->fetch();
    $ticket_id = (int)$resultado['id'];

    // 8. Submódulo de Atención: Registrar atención técnica si aplica
    if ($es_personal_tecnico) {
        $datos_atencion = $datos;
        if (!empty($tecnico_asignado_id)) {
            $datos_atencion['tecnico_id'] = $tecnico_asignado_id;
        }
        registrar_atencion_tecnica($conexion, $ticket_id, $datos_atencion, $resultado_firmas['firma_sistemas']);
    }

    $conexion->commit();

    responder_exito('Ticket de soporte técnico registrado con éxito', array(
        'id'             => $ticket_id,
        'codigo_ticket'  => $resultado['codigo_ticket'],
        'estado'         => $estado,
        'diagnostico_id' => $diagnostico_id,
        'direccion_mac'  => $direccion_mac,
        'creado_en'      => $resultado['creado_en']
    ), 201);

} catch (Exception $e) {
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }
    responder_error('Error al guardar el ticket: ' . $e->getMessage(), 500);
}
