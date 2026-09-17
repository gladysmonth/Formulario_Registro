<?php
/**
 * Endpoint: Crear Registro de Incidencias de Sistemas (FOR_RIS_001, V-1)
 * Método: POST
 * Compatible con PHP 7.3
 * Archivo: back_form/api/registro_incidencias/crear_incidencia.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/conexion_bd.php';

// Inclusión de servicios especializados
require_once __DIR__ . '/servicios/servicio_codigo_incidencia.php';
require_once __DIR__ . '/servicios/servicio_validaciones_ri.php';
require_once __DIR__ . '/servicios/servicio_firmas_ri.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_error('Método no permitido. Utilice POST', 405);
}

$datos = obtener_cuerpo_json();

// 1. Validar campos del formulario
$res_validacion = validar_datos_incidencia($datos, true);
if (!$res_validacion['valido']) {
    responder_error('Datos del formulario incompletos o inválidos', 422, $res_validacion['errores']);
}

$p = $res_validacion['datos_procesados'];

// 2. Validar firmas digitales
$res_firmas = validar_firmas_incidencia($datos, $p['estado']);
if (!$res_firmas['valido']) {
    responder_error('Validación de firmas fallida', 422, $res_firmas['errores']);
}

try {
    $conexion = obtener_conexion_bd();
    $conexion->beginTransaction();

    // 3. Asignar Nro. de Incidencia escrito por el usuario (o correlativo si no viene)
    $codigo_propuesto = isset($datos['nro_incidencia']) ? trim($datos['nro_incidencia']) : null;
    if (!empty($codigo_propuesto)) {
        $stmt_check = $conexion->prepare("SELECT id FROM incidencias_sistemas WHERE nro_incidencia = :codigo LIMIT 1");
        $stmt_check->execute(array(':codigo' => $codigo_propuesto));
        if ($stmt_check->fetch()) {
            responder_error("El Nro. de Incidencia '{$codigo_propuesto}' ya existe en el sistema. Ingrese un número diferente.", 422);
        }
        $nro_incidencia = $codigo_propuesto;
    } else {
        $nro_incidencia = generar_codigo_incidencia($conexion);
    }

    // 4. Inserción en la base de datos
    $sql = "
        INSERT INTO incidencias_sistemas (
            codigo_documento,
            version_documento,
            nro_incidencia,
            fecha_hora_reporte,
            responsable_reporte,
            sistema_erp_sai,
            sistema_cobranzas_netcob,
            sistema_otros,
            sistema_otros_detalle,
            fallo_base_datos,
            detalle_base_datos,
            fallo_infraestructura_servidor,
            detalle_infraestructura_servidor,
            fallo_enlaces_conectividad,
            detalle_enlaces_conectividad,
            nivel_criticidad,
            estado,
            descripcion_tecnica_logs,
            accion_realizada,
            detalle_tecnico,
            fecha_hora_cierre,
            observaciones_recomendaciones,
            firma_responsable_reporte,
            firma_sistemas
        ) VALUES (
            'FOR_RIS_001',
            'V-1',
            :nro_incidencia,
            :fecha_hora_reporte,
            :responsable_reporte,
            :sistema_erp_sai,
            :sistema_cobranzas_netcob,
            :sistema_otros,
            :sistema_otros_detalle,
            :fallo_base_datos,
            :detalle_base_datos,
            :fallo_infraestructura_servidor,
            :detalle_infraestructura_servidor,
            :fallo_enlaces_conectividad,
            :detalle_enlaces_conectividad,
            :nivel_criticidad,
            :estado,
            :descripcion_tecnica_logs,
            :accion_realizada,
            :detalle_tecnico,
            :fecha_hora_cierre,
            :observaciones_recomendaciones,
            :firma_responsable_reporte,
            :firma_sistemas
        ) RETURNING id, nro_incidencia, creado_en
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute(array(
        ':nro_incidencia'                   => $nro_incidencia,
        ':fecha_hora_reporte'               => $p['fecha_hora_reporte'],
        ':responsable_reporte'              => $p['responsable_reporte'],
        ':sistema_erp_sai'                  => $p['sistema_erp_sai'] ? 1 : 0,
        ':sistema_cobranzas_netcob'         => $p['sistema_cobranzas_netcob'] ? 1 : 0,
        ':sistema_otros'                    => $p['sistema_otros'] ? 1 : 0,
        ':sistema_otros_detalle'            => $p['sistema_otros_detalle'],
        ':fallo_base_datos'                 => $p['fallo_base_datos'] ? 1 : 0,
        ':detalle_base_datos'               => $p['detalle_base_datos'],
        ':fallo_infraestructura_servidor'   => $p['fallo_infraestructura_servidor'] ? 1 : 0,
        ':detalle_infraestructura_servidor' => $p['detalle_infraestructura_servidor'],
        ':fallo_enlaces_conectividad'       => $p['fallo_enlaces_conectividad'] ? 1 : 0,
        ':detalle_enlaces_conectividad'     => $p['detalle_enlaces_conectividad'],
        ':nivel_criticidad'                 => $p['nivel_criticidad'],
        ':estado'                           => $p['estado'],
        ':descripcion_tecnica_logs'         => $p['descripcion_tecnica_logs'],
        ':accion_realizada'                 => $p['accion_realizada'],
        ':detalle_tecnico'                  => $p['detalle_tecnico'],
        ':fecha_hora_cierre'                => $p['fecha_hora_cierre'],
        ':observaciones_recomendaciones'    => $p['observaciones_recomendaciones'],
        ':firma_responsable_reporte'        => $res_firmas['firma_responsable'],
        ':firma_sistemas'                   => $res_firmas['firma_sistemas']
    ));

    $nuevo_registro = $stmt->fetch(PDO::FETCH_ASSOC);
    $conexion->commit();

    responder_exito('Incidencia de sistemas registrada exitosamente', array(
        'id'             => $nuevo_registro['id'],
        'nro_incidencia' => $nuevo_registro['nro_incidencia'],
        'creado_en'      => $nuevo_registro['creado_en']
    ), 201);

} catch (PDOException $e) {
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }
    responder_error('Error de base de datos al registrar incidencia: ' . $e->getMessage(), 500);
} catch (Exception $e) {
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }
    responder_error('Error interno del servidor: ' . $e->getMessage(), 500);
}
