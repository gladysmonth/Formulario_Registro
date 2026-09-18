<?php
/**
 * Endpoint: Crear Registro de Solicitud de Accesos de Usuarios de Sistemas
 * Compatible con PHP 7.3
 * Archivo: back_form/api/asignacion_accesos/crear_solicitud.php
 */

require_once __DIR__ . '/../../configuracion/conexion_bd.php';
require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/servicios/servicio_validaciones_accesos.php';
require_once __DIR__ . '/servicios/servicio_firmas_accesos.php';
require_once __DIR__ . '/servicios/servicio_codigo_accesos.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_error('Método HTTP no permitido. Debe utilizar POST.', 405);
}

// 1. Lectura del cuerpo de la petición
$cuerpo = file_get_contents('php://input');
$datos = json_decode($cuerpo, true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($datos)) {
    responder_error('El cuerpo de la petición no contiene un formato JSON válido.', 400);
}

// 2. Validación de campos de negocio
$resultado_val = validar_datos_solicitud_accesos($datos);
if (!$resultado_val['valido']) {
    responder_error('Existen errores en los datos enviados.', 422, $resultado_val['errores']);
}
$limpios = $resultado_val['datos_limpios'];

// 3. Validación de firmas institucionales
$resultado_firmas = validar_firmas_accesos($datos);
if (!$resultado_firmas['valido']) {
    responder_error('Existen errores en las firmas registradas.', 422, $resultado_firmas['errores']);
}

// 4. Conexión a Base de Datos
$conexion = obtener_conexion_bd();
if (!$conexion) {
    responder_error('No se pudo establecer conexión con el servidor de base de datos.', 500);
}

try {
    $conexion->beginTransaction();

    // 5. Asignación del código de solicitud (ACC-YYYY-XXXX)
    $nro_solicitud = generar_codigo_accesos($conexion, $limpios['nro_solicitud']);

    // 6. Inserción en la tabla solicitudes_accesos
    $sql = "
        INSERT INTO solicitudes_accesos (
            nro_solicitud,
            fecha_solicitud,
            nombre_solicitante,
            cargo_solicitante,
            area_departamento,
            sistema_erp_sai,
            sistema_cobranzas_netcob,
            sistema_otros,
            sistema_otros_detalle,
            es_usuario_nuevo,
            nombre_usuario_detalles,
            requerimientos_accesos,
            estado,
            atendido_por,
            fecha_hora_atencion,
            comentarios_sistemas,
            firma_solicitante,
            nombre_autoriza,
            firma_autoriza,
            firma_sistemas
        ) VALUES (
            :nro_solicitud,
            :fecha_solicitud,
            :nombre_solicitante,
            :cargo_solicitante,
            :area_departamento,
            :sistema_erp_sai,
            :sistema_cobranzas_netcob,
            :sistema_otros,
            :sistema_otros_detalle,
            :es_usuario_nuevo,
            :nombre_usuario_detalles,
            :requerimientos_accesos,
            :estado,
            :atendido_por,
            :fecha_hora_atencion,
            :comentarios_sistemas,
            :firma_solicitante,
            :nombre_autoriza,
            :firma_autoriza,
            :firma_sistemas
        ) RETURNING id
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':nro_solicitud', $nro_solicitud, PDO::PARAM_STR);
    $stmt->bindValue(':fecha_solicitud', $limpios['fecha_solicitud'], PDO::PARAM_STR);
    $stmt->bindValue(':nombre_solicitante', $limpios['nombre_solicitante'], PDO::PARAM_STR);
    $stmt->bindValue(':cargo_solicitante', $limpios['cargo_solicitante'], PDO::PARAM_STR);
    $stmt->bindValue(':area_departamento', $limpios['area_departamento'], PDO::PARAM_STR);
    $stmt->bindValue(':sistema_erp_sai', $limpios['sistema_erp_sai'], PDO::PARAM_BOOL);
    $stmt->bindValue(':sistema_cobranzas_netcob', $limpios['sistema_cobranzas_netcob'], PDO::PARAM_BOOL);
    $stmt->bindValue(':sistema_otros', $limpios['sistema_otros'], PDO::PARAM_BOOL);
    $stmt->bindValue(':sistema_otros_detalle', $limpios['sistema_otros_detalle'], $limpios['sistema_otros_detalle'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':es_usuario_nuevo', $limpios['es_usuario_nuevo'], PDO::PARAM_BOOL);
    $stmt->bindValue(':nombre_usuario_detalles', $limpios['nombre_usuario_detalles'], $limpios['nombre_usuario_detalles'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':requerimientos_accesos', $limpios['requerimientos_accesos'], PDO::PARAM_STR);
    $stmt->bindValue(':estado', $limpios['estado'], PDO::PARAM_STR);
    $stmt->bindValue(':atendido_por', $limpios['atendido_por'], $limpios['atendido_por'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':fecha_hora_atencion', $limpios['fecha_hora_atencion'], $limpios['fecha_hora_atencion'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':comentarios_sistemas', $limpios['comentarios_sistemas'], $limpios['comentarios_sistemas'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':firma_solicitante', $resultado_firmas['firma_solicitante'], $resultado_firmas['firma_solicitante'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':nombre_autoriza', $limpios['nombre_autoriza'], $limpios['nombre_autoriza'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':firma_autoriza', $resultado_firmas['firma_autoriza'], $resultado_firmas['firma_autoriza'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':firma_sistemas', $resultado_firmas['firma_sistemas'], $resultado_firmas['firma_sistemas'] ? PDO::PARAM_STR : PDO::PARAM_NULL);

    $stmt->execute();
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
    $nuevo_id = intval($fila['id']);

    $conexion->commit();

    responder_exito('Solicitud de accesos registrada con éxito', array(
        'id'            => $nuevo_id,
        'nro_solicitud' => $nro_solicitud,
        'estado'        => $limpios['estado']
    ), 201);

} catch (PDOException $e) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    error_log('Error PDO en crear_solicitud.php: ' . $e->getMessage());
    responder_error('Error interno al registrar la solicitud: ' . $e->getMessage(), 500);
}
