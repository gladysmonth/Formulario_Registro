<?php
/**
 * Endpoint API: Auto-detección de PC por IP en Red Local
 * Compatible con PHP 7.3 y PostgreSQL 13
 * Ruta: /api/diagnostico/detectar_mi_pc.php
 */

require_once __DIR__ . '/../../configuracion/conexion_bd.php';
require_once __DIR__ . '/../../configuracion/respuestas_api.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método no permitido. Solo se acepta GET.', 405);
}

// 1. Detección de la IP del cliente
$ip_cliente = '';

// Parámetro manual opcional para pruebas/depuración o entornos locales con proxies
if (!empty($_GET['ip'])) {
    $ip_cliente = trim($_GET['ip']);
} elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    $ip_cliente = trim($_SERVER['HTTP_CF_CONNECTING_IP']);
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    // Si viene una lista separada por comas, tomar la primera
    $partes_ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    $ip_cliente = trim($partes_ip[0]);
} elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
    $ip_cliente = trim($_SERVER['HTTP_X_REAL_IP']);
} elseif (!empty($_SERVER['REMOTE_ADDR'])) {
    $ip_cliente = trim($_SERVER['REMOTE_ADDR']);
}

// Normalizar IP IPv6 loopback a IPv4 si aplica
if ($ip_cliente === '::1') {
    $ip_cliente = '127.0.0.1';
}

if (empty($ip_cliente)) {
    responder_exito('No se pudo determinar la IP del cliente.', array(
        'detectado'     => false,
        'ip'            => null,
        'direccion_mac' => null,
        'hostname'      => null
    ));
}

try {
    $conexion = obtener_conexion_bd();

    // 2. Buscar primeramente en el historial de diagnósticos por IP local
    $sql_diag = "
        SELECT 
            d.id AS diagnostico_id,
            d.direccion_mac,
            d.hostname,
            d.usuario_windows,
            d.ip_local,
            d.tipo_evento,
            d.fecha_diagnostico,
            d.estado_salud_general,
            d.temperatura_cpu,
            d.uso_cpu_porcentaje,
            d.uso_ram_porcentaje,
            d.espacio_libre_c_gb,
            d.salud_disco_smart,
            d.antivirus_activo,
            d.amenazas_activas,
            eq.id AS equipo_id,
            eq.codigo_activo,
            eq.area,
            eq.encargado,
            eq.marca_modelo,
            eq.sistema_operativo,
            eq.tipo_equipo
        FROM historial_diagnosticos d
        LEFT JOIN equipos_inventario eq ON eq.id = d.equipo_id OR eq.direccion_mac = d.direccion_mac
        WHERE d.ip_local = :ip
        ORDER BY d.fecha_diagnostico DESC
        LIMIT 1
    ";

    $stmt_diag = $conexion->prepare($sql_diag);
    $stmt_diag->execute(array(':ip' => $ip_cliente));
    $equipo_detectado = $stmt_diag->fetch(PDO::FETCH_ASSOC);

    // 3. Si no se encontró por diagnóstico, buscar en el inventario por IP actual
    if (!$equipo_detectado) {
        $sql_eq = "
            SELECT 
                eq.id AS equipo_id,
                eq.codigo_activo,
                eq.marca_modelo,
                eq.direccion_mac,
                eq.hostname,
                eq.ip_actual AS ip_local,
                eq.usuario_actual AS usuario_windows,
                eq.area,
                eq.encargado,
                eq.sistema_operativo,
                eq.tipo_equipo,
                v.diagnostico_id,
                v.estado_salud_general,
                v.temperatura_cpu,
                v.uso_cpu_porcentaje,
                v.uso_ram_porcentaje,
                v.espacio_libre_c_gb,
                v.salud_disco_smart,
                v.antivirus_activo,
                v.amenazas_activas,
                v.fecha_diagnostico
            FROM equipos_inventario eq
            LEFT JOIN vista_ultimo_diagnostico_pc v ON v.direccion_mac = eq.direccion_mac
            WHERE eq.ip_actual = :ip
            LIMIT 1
        ";
        $stmt_eq = $conexion->prepare($sql_eq);
        $stmt_eq->execute(array(':ip' => $ip_cliente));
        $equipo_detectado = $stmt_eq->fetch(PDO::FETCH_ASSOC);
    }

    // 4. Si se encontró coincidencia
    if ($equipo_detectado) {
        $datos_respuesta = array(
            'detectado'         => true,
            'ip'                => $ip_cliente,
            'direccion_mac'     => $equipo_detectado['direccion_mac'],
            'hostname'          => $equipo_detectado['hostname'],
            'usuario_windows'   => $equipo_detectado['usuario_windows'],
            'diagnostico_id'    => !empty($equipo_detectado['diagnostico_id']) ? (int)$equipo_detectado['diagnostico_id'] : null,
            'equipo_id'         => !empty($equipo_detectado['equipo_id']) ? (int)$equipo_detectado['equipo_id'] : null,
            'codigo_activo'     => $equipo_detectado['codigo_activo'] ? $equipo_detectado['codigo_activo'] : '',
            'marca_modelo'      => $equipo_detectado['marca_modelo'] ? $equipo_detectado['marca_modelo'] : '',
            'sistema_operativo' => $equipo_detectado['sistema_operativo'] ? $equipo_detectado['sistema_operativo'] : '',
            'area'              => $equipo_detectado['area'] ? $equipo_detectado['area'] : '',
            'encargado'         => $equipo_detectado['encargado'] ? $equipo_detectado['encargado'] : '',
            'estado_salud_general' => $equipo_detectado['estado_salud_general'] ? $equipo_detectado['estado_salud_general'] : 'bueno',
            'metricas_salud'    => array(
                'temperatura_cpu'    => $equipo_detectado['temperatura_cpu'] !== null ? (float)$equipo_detectado['temperatura_cpu'] : null,
                'uso_cpu_porcentaje' => $equipo_detectado['uso_cpu_porcentaje'] !== null ? (float)$equipo_detectado['uso_cpu_porcentaje'] : null,
                'uso_ram_porcentaje' => $equipo_detectado['uso_ram_porcentaje'] !== null ? (float)$equipo_detectado['uso_ram_porcentaje'] : null,
                'espacio_libre_c_gb' => $equipo_detectado['espacio_libre_c_gb'] !== null ? (float)$equipo_detectado['espacio_libre_c_gb'] : null,
                'salud_disco_smart'  => $equipo_detectado['salud_disco_smart'] ? $equipo_detectado['salud_disco_smart'] : 'OK',
                'antivirus_activo'   => ($equipo_detectado['antivirus_activo'] === true || $equipo_detectado['antivirus_activo'] === 't'),
                'amenazas_activas'   => (int)($equipo_detectado['amenazas_activas'] ?? 0)
            ),
            'fecha_ultimo_diagnostico' => $equipo_detectado['fecha_diagnostico']
        );

        responder_exito('Equipo detectado exitosamente por IP.', $datos_respuesta);
    } else {
        // No encontrado: respuesta exitosa (200) informando que no hay telemetría previa para esa IP
        responder_exito('No se encontró registro de telemetría para la IP detectada.', array(
            'detectado'         => false,
            'ip'                => $ip_cliente,
            'direccion_mac'     => null,
            'hostname'          => null,
            'diagnostico_id'    => null,
            'equipo_id'         => null
        ));
    }

} catch (Exception $e) {
    responder_error('Error al detectar equipo por IP: ' . $e->getMessage(), 500);
}
