<?php
/**
 * Endpoint API: Recepción de Diagnóstico y Telemetría de PC
 * Compatible con PHP 7.3 y PostgreSQL 13
 * Ruta: /api/diagnostico/recibir.php
 */

require_once __DIR__ . '/../../configuracion/conexion_bd.php';
require_once __DIR__ . '/../../configuracion/respuestas_api.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_error('Método no permitido. Solo se acepta POST.', 405);
}

// Obtener cuerpo crudo y remover BOM UTF-8 (habitual en PowerShell Windows)
$cuerpo_crudo = file_get_contents('php://input');
if (empty($cuerpo_crudo)) {
    responder_error('El cuerpo de la solicitud está vacío.', 400);
}

// Eliminar BOM si está presente
$cuerpo_crudo = preg_replace('/^\xEF\xBB\xBF/', '', $cuerpo_crudo);
$cuerpo = json_decode($cuerpo_crudo, true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($cuerpo)) {
    responder_error('JSON inválido: ' . json_last_error_msg(), 400);
}

if (!isset($cuerpo['metadatos'])) {
    responder_error('Estructura de telemetría inválida. Falta sección metadatos.', 400);
}

$metadatos = $cuerpo['metadatos'];
$resumen   = isset($cuerpo['resumen_salud']) ? $cuerpo['resumen_salud'] : array();
$hardware  = isset($cuerpo['hardware']) ? $cuerpo['hardware'] : array();
$software  = isset($cuerpo['software_y_seguridad']) ? $cuerpo['software_y_seguridad'] : array();

$mac       = isset($metadatos['direccion_mac']) ? trim($metadatos['direccion_mac']) : '00:00:00:00:00:00';
$hostname  = isset($metadatos['hostname']) ? trim($metadatos['hostname']) : 'DESCONOCIDO';
$usuario   = isset($metadatos['usuario_windows']) ? trim($metadatos['usuario_windows']) : '';
$ip_local  = isset($metadatos['ip_local']) ? trim($metadatos['ip_local']) : '';
$tipo_evt  = isset($metadatos['tipo_evento']) ? trim($metadatos['tipo_evento']) : 'programado';

$eventos_validos = array('programado', 'inicio_jornada', 'alerta_critica', 'manual', 'ticket_soporte');
if (!in_array($tipo_evt, $eventos_validos)) {
    $tipo_evt = 'manual';
}

try {
    $conexion = obtener_conexion_bd();
    $conexion->beginTransaction();

    // 1. Identificar o Registrar el Equipo en equipos_inventario
    $consulta_equipo = $conexion->prepare("SELECT id FROM equipos_inventario WHERE direccion_mac = :mac LIMIT 1");
    $consulta_equipo->execute(array(':mac' => $mac));
    $equipo_existente = $consulta_equipo->fetch();

    $equipo_id = null;
    $placa = isset($hardware['placa_madre']) ? $hardware['placa_madre'] : array();
    $marca_modelo = (isset($placa['fabricante']) ? $placa['fabricante'] : 'PC') . ' ' . (isset($placa['modelo']) ? $placa['modelo'] : '');
    $serial = isset($placa['serial']) && !empty($placa['serial']) ? $placa['serial'] : $mac;

    if ($equipo_existente) {
        $equipo_id = $equipo_existente['id'];
        $actualizar_eq = $conexion->prepare("
            UPDATE equipos_inventario 
            SET hostname = :hostname, 
                ip_actual = :ip, 
                usuario_actual = :usuario, 
                actualizado_en = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $actualizar_eq->execute(array(
            ':hostname' => $hostname,
            ':ip'       => $ip_local,
            ':usuario'  => $usuario,
            ':id'       => $equipo_id
        ));
    } else {
        $insertar_eq = $conexion->prepare("
            INSERT INTO equipos_inventario (
                numero_serie, tipo_equipo, marca_modelo, direccion_mac, hostname, ip_actual, usuario_actual, sistema_operativo
            ) VALUES (
                :serial, 'PC Escritorio / Portátil', :marca_modelo, :mac, :hostname, :ip, :usuario, 'Windows 10/11'
            ) RETURNING id
        ");
        $insertar_eq->execute(array(
            ':serial'       => $serial,
            ':marca_modelo' => trim($marca_modelo),
            ':mac'          => $mac,
            ':hostname'     => $hostname,
            ':ip'           => $ip_local,
            ':usuario'      => $usuario
        ));
        $nuevo_eq = $insertar_eq->fetch();
        $equipo_id = $nuevo_eq['id'];
    }

    // 2. Extraer métricas numéricas y de estado
    $estado_salud = isset($resumen['estado_salud_general']) ? $resumen['estado_salud_general'] : 'bueno';
    $temp_cpu     = isset($resumen['temperatura_cpu']) && is_numeric($resumen['temperatura_cpu']) ? $resumen['temperatura_cpu'] : null;
    $uso_cpu      = isset($resumen['uso_cpu_porcentaje']) ? $resumen['uso_cpu_porcentaje'] : 0;
    $uso_ram      = isset($resumen['uso_ram_porcentaje']) ? $resumen['uso_ram_porcentaje'] : 0;
    $espacio_c    = isset($resumen['espacio_libre_c_gb']) ? $resumen['espacio_libre_c_gb'] : 0;
    $salud_smart  = isset($resumen['salud_disco_smart']) ? $resumen['salud_disco_smart'] : 'OK';
    $av_activo    = isset($resumen['antivirus_activo']) ? ($resumen['antivirus_activo'] ? 'true' : 'false') : 'true';
    $amenazas     = isset($resumen['amenazas_activas']) ? (int)$resumen['amenazas_activas'] : 0;

    // 3. Preparar bloques JSONB
    $det_cpu     = json_encode(isset($hardware['cpu']) ? $hardware['cpu'] : array(), JSON_UNESCAPED_UNICODE);
    $det_ram     = json_encode(isset($hardware['ram']) ? $hardware['ram'] : array(), JSON_UNESCAPED_UNICODE);
    $det_disco   = json_encode(isset($hardware['almacenamiento']) ? $hardware['almacenamiento'] : array(), JSON_UNESCAPED_UNICODE);
    $det_red     = json_encode(isset($hardware['red']) ? $hardware['red'] : array(), JSON_UNESCAPED_UNICODE);
    $det_placa   = json_encode(isset($hardware['placa_madre']) ? $hardware['placa_madre'] : array(), JSON_UNESCAPED_UNICODE);

    $apps_pf     = json_encode(isset($software['apps_recientes_prefetch']) ? $software['apps_recientes_prefetch'] : array(), JSON_UNESCAPED_UNICODE);
    $descargas   = json_encode(isset($software['descargas_recientes']) ? $software['descargas_recientes'] : array(), JSON_UNESCAPED_UNICODE);
    $seg_av      = json_encode(isset($software['antivirus']) ? $software['antivirus'] : array(), JSON_UNESCAPED_UNICODE);
    $alertas     = json_encode(isset($software['alertas_detectadas']) ? $software['alertas_detectadas'] : array(), JSON_UNESCAPED_UNICODE);
    $payload_all = json_encode($cuerpo, JSON_UNESCAPED_UNICODE);

    // 4. Insertar en historial_diagnosticos
    $sql_insert = "
        INSERT INTO historial_diagnosticos (
            equipo_id, direccion_mac, hostname, usuario_windows, ip_local, tipo_evento,
            estado_salud_general, temperatura_cpu, uso_cpu_porcentaje, uso_ram_porcentaje,
            espacio_libre_c_gb, salud_disco_smart, antivirus_activo, amenazas_activas,
            detalle_cpu, detalle_ram, detalle_almacenamiento, detalle_red, detalle_placa_bios,
            apps_recientes_prefetch, descargas_recientes, seguridad_antivirus, alertas_detectadas, payload_completo
        ) VALUES (
            :equipo_id, :mac, :hostname, :usuario, :ip_local, :tipo_evento,
            :estado_salud, :temp_cpu, :uso_cpu, :uso_ram,
            :espacio_c, :salud_smart, :av_activo, :amenazas,
            :det_cpu, :det_ram, :det_disco, :det_red, :det_placa,
            :apps_pf, :descargas, :seg_av, :alertas, :payload_all
        ) RETURNING id
    ";

    $stmt_insert = $conexion->prepare($sql_insert);
    $stmt_insert->execute(array(
        ':equipo_id'     => $equipo_id,
        ':mac'           => $mac,
        ':hostname'      => $hostname,
        ':usuario'       => $usuario,
        ':ip_local'      => $ip_local,
        ':tipo_evento'   => $tipo_evt,
        ':estado_salud'  => $estado_salud,
        ':temp_cpu'      => $temp_cpu,
        ':uso_cpu'       => $uso_cpu,
        ':uso_ram'       => $uso_ram,
        ':espacio_c'     => $espacio_c,
        ':salud_smart'   => $salud_smart,
        ':av_activo'     => $av_activo,
        ':amenazas'      => $amenazas,
        ':det_cpu'       => $det_cpu,
        ':det_ram'       => $det_ram,
        ':det_disco'     => $det_disco,
        ':det_red'       => $det_red,
        ':det_placa'     => $det_placa,
        ':apps_pf'       => $apps_pf,
        ':descargas'     => $descargas,
        ':seg_av'        => $seg_av,
        ':alertas'       => $alertas,
        ':payload_all'   => $payload_all
    ));

    $nuevo_diag = $stmt_insert->fetch();
    $conexion->commit();

    responder_exito('Telemetría y diagnóstico recibidos correctamente', array(
        'diagnostico_id' => $nuevo_diag['id'],
        'equipo_id'      => $equipo_id,
        'direccion_mac'  => $mac,
        'hostname'       => $hostname,
        'estado_salud'   => $estado_salud
    ), 201);

} catch (Exception $e) {
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }
    responder_error('Error procesando telemetría en la base de datos: ' . $e->getMessage(), 500);
}
