<?php
/**
 * Endpoint: Crear Registro de Mantenimiento Preventivo (PC / Laptop)
 * Método: POST
 * Compatible con PHP 7.3
 * Archivo: crear_mantenimiento.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/conexion_bd.php';

// Inclusión de submódulos de servicio especializados
require_once __DIR__ . '/servicios/servicio_equipo_mp.php';
require_once __DIR__ . '/servicios/servicio_checklist_mp.php';
require_once __DIR__ . '/servicios/servicio_firmas_mp.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_error('Método no permitido. Utilice POST', 405);
}

$datos = obtener_cuerpo_json();

// 1. Validar Sección 1: Datos Generales
$errores = array();

$tecnico_responsable = trim($datos['tecnico_responsable'] ?? '');
if (empty($tecnico_responsable)) {
    $errores[] = 'El campo Técnico Responsable es obligatorio.';
}

$ubicacion_equipo = trim($datos['ubicacion_equipo'] ?? '');
if (empty($ubicacion_equipo)) {
    $errores[] = 'El campo Ubicación del Equipo es obligatorio.';
}

$fecha_mantenimiento = !empty($datos['fecha_mantenimiento']) ? $datos['fecha_mantenimiento'] : date('Y-m-d');

try {
    $conexion = obtener_conexion_bd();

    // 2. Submódulo de Equipo (Sección 2)
    $res_equipo = procesar_equipo_mantenimiento($conexion, $datos);
    if (!$res_equipo['valido']) {
        $errores = array_merge($errores, $res_equipo['errores']);
    }
    $equipo = $res_equipo['datos_equipo'];

    // 3. Submódulo de Firmas (Sección 7)
    $res_firmas = validar_firmas_mantenimiento($datos);
    if (!$res_firmas['valido']) {
        $errores = array_merge($errores, $res_firmas['errores']);
    }

    if (!empty($errores)) {
        responder_error('Datos incompletos o inválidos', 422, $errores);
    }

    // 4. Submódulo de Checklist (Secciones 3, 4 y 5)
    $chk = procesar_checklist_mantenimiento($datos);

    // 5. Sección 6: Observaciones
    $observaciones_incidencias = !empty(trim($datos['observaciones_incidencias'] ?? '')) 
        ? trim($datos['observaciones_incidencias']) 
        : null;

    $conexion->beginTransaction();

    // 6. Generar código único correlativo (Ej: MP-2026-0001)
    $stmt_sec = $conexion->query("SELECT COALESCE(MAX(id), 0) + 1 AS siguiente FROM mantenimientos_preventivos");
    $fila_sec = $stmt_sec->fetch();
    $codigo_mantenimiento = sprintf('MP-%s-%04d', date('Y'), $fila_sec['siguiente']);

    // 7. Insertar registro completo
    $sql = "INSERT INTO mantenimientos_preventivos (
                codigo_mantenimiento,
                tecnico_responsable,
                fecha_mantenimiento,
                ubicacion_equipo,
                equipo_id,
                tipo_equipo,
                nombre_equipo,
                codigo_activo,
                memoria_ram,
                tipo_red,
                marca_modelo,
                sistema_operativo,
                procesador,
                almacenamiento,
                direccion_ip,
                limpieza_carcasa_componentes,
                limpieza_pantalla_teclado,
                verificacion_conectores,
                limpieza_otros,
                actualizacion_so,
                eliminacion_temporales,
                desfragmentacion_optimizacion,
                escaneo_antivirus,
                verificacion_drivers,
                copia_seguridad,
                mantenimiento_interno_otros,
                verificacion_encendido_apagado,
                verificacion_rendimiento,
                verificacion_red,
                verificacion_perifericos,
                verificacion_temperatura_anomalias,
                observaciones_incidencias,
                firma_responsable_equipo,
                firma_sistemas
            ) VALUES (
                :codigo_mantenimiento,
                :tecnico_responsable,
                :fecha_mantenimiento,
                :ubicacion_equipo,
                :equipo_id,
                :tipo_equipo,
                :nombre_equipo,
                :codigo_activo,
                :memoria_ram,
                :tipo_red,
                :marca_modelo,
                :sistema_operativo,
                :procesador,
                :almacenamiento,
                :direccion_ip,
                :limpieza_carcasa_componentes,
                :limpieza_pantalla_teclado,
                :verificacion_conectores,
                :limpieza_otros,
                :actualizacion_so,
                :eliminacion_temporales,
                :desfragmentacion_optimizacion,
                :escaneo_antivirus,
                :verificacion_drivers,
                :copia_seguridad,
                :mantenimiento_interno_otros,
                :verificacion_encendido_apagado,
                :verificacion_rendimiento,
                :verificacion_red,
                :verificacion_perifericos,
                :verificacion_temperatura_anomalias,
                :observaciones_incidencias,
                :firma_responsable_equipo,
                :firma_sistemas
            ) RETURNING id, codigo_mantenimiento, creado_en";

    $stmt = $conexion->prepare($sql);
    $stmt->execute(array(
        ':codigo_mantenimiento'              => $codigo_mantenimiento,
        ':tecnico_responsable'               => $tecnico_responsable,
        ':fecha_mantenimiento'               => $fecha_mantenimiento,
        ':ubicacion_equipo'                  => $ubicacion_equipo,
        ':equipo_id'                         => $equipo['equipo_id'],
        ':tipo_equipo'                       => $equipo['tipo_equipo'],
        ':nombre_equipo'                     => $equipo['nombre_equipo'],
        ':codigo_activo'                     => $equipo['codigo_activo'],
        ':memoria_ram'                       => $equipo['memoria_ram'],
        ':tipo_red'                          => $equipo['tipo_red'],
        ':marca_modelo'                      => $equipo['marca_modelo'],
        ':sistema_operativo'                 => $equipo['sistema_operativo'],
        ':procesador'                        => $equipo['procesador'],
        ':almacenamiento'                    => $equipo['almacenamiento'],
        ':direccion_ip'                      => $equipo['direccion_ip'],
        ':limpieza_carcasa_componentes'      => $chk['limpieza_carcasa_componentes'] ? 'true' : 'false',
        ':limpieza_pantalla_teclado'         => $chk['limpieza_pantalla_teclado'] ? 'true' : 'false',
        ':verificacion_conectores'           => $chk['verificacion_conectores'] ? 'true' : 'false',
        ':limpieza_otros'                    => $chk['limpieza_otros'],
        ':actualizacion_so'                  => $chk['actualizacion_so'] ? 'true' : 'false',
        ':eliminacion_temporales'            => $chk['eliminacion_temporales'] ? 'true' : 'false',
        ':desfragmentacion_optimizacion'     => $chk['desfragmentacion_optimizacion'] ? 'true' : 'false',
        ':escaneo_antivirus'                 => $chk['escaneo_antivirus'] ? 'true' : 'false',
        ':verificacion_drivers'              => $chk['verificacion_drivers'] ? 'true' : 'false',
        ':copia_seguridad'                   => $chk['copia_seguridad'] ? 'true' : 'false',
        ':mantenimiento_interno_otros'       => $chk['mantenimiento_interno_otros'],
        ':verificacion_encendido_apagado'    => $chk['verificacion_encendido_apagado'] ? 'true' : 'false',
        ':verificacion_rendimiento'          => $chk['verificacion_rendimiento'] ? 'true' : 'false',
        ':verificacion_red'                  => $chk['verificacion_red'] ? 'true' : 'false',
        ':verificacion_perifericos'          => $chk['verificacion_perifericos'] ? 'true' : 'false',
        ':verificacion_temperatura_anomalias'=> $chk['verificacion_temperatura_anomalias'] ? 'true' : 'false',
        ':observaciones_incidencias'         => $observaciones_incidencias,
        ':firma_responsable_equipo'          => $res_firmas['firma_responsable'],
        ':firma_sistemas'                    => $res_firmas['firma_sistemas']
    ));

    $resultado = $stmt->fetch();
    $mantenimiento_id = (int)$resultado['id'];

    $conexion->commit();

    responder_exito('Registro de mantenimiento preventivo guardado con éxito', array(
        'id'                   => $mantenimiento_id,
        'codigo_mantenimiento' => $resultado['codigo_mantenimiento'],
        'creado_en'            => $resultado['creado_en']
    ), 201);

} catch (Exception $e) {
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }
    responder_error('Error al registrar mantenimiento: ' . $e->getMessage(), 500);
}
