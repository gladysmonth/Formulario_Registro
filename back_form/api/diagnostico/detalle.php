<?php
/**
 * Endpoint API: Detalle Técnico Completo e Historial de una PC
 * Compatible con PHP 7.3 y PostgreSQL 13
 * Ruta: /api/diagnostico/detalle.php
 */

require_once __DIR__ . '/../../configuracion/conexion_bd.php';
require_once __DIR__ . '/../../configuracion/respuestas_api.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método no permitido. Solo se acepta GET.', 405);
}

$mac = isset($_GET['mac']) ? trim($_GET['mac']) : '';
$equipo_id = isset($_GET['equipo_id']) ? (int)$_GET['equipo_id'] : 0;
$diagnostico_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (empty($mac) && $equipo_id <= 0 && $diagnostico_id <= 0) {
    responder_error('Debe especificar un parámetro de búsqueda: mac, equipo_id o id.', 400);
}

try {
    $conexion = obtener_conexion_bd();

    // 1. Obtener el registro más reciente o el solicitado
    if ($diagnostico_id > 0) {
        $sql = "
            SELECT d.*, eq.codigo_activo, eq.area, eq.encargado, eq.marca_modelo, eq.numero_serie, eq.sistema_operativo
            FROM historial_diagnosticos d
            LEFT JOIN equipos_inventario eq ON eq.id = d.equipo_id OR eq.direccion_mac = d.direccion_mac
            WHERE d.id = :id
            LIMIT 1
        ";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(array(':id' => $diagnostico_id));
    } elseif (!empty($mac)) {
        $sql = "
            SELECT d.*, eq.codigo_activo, eq.area, eq.encargado, eq.marca_modelo, eq.numero_serie, eq.sistema_operativo
            FROM historial_diagnosticos d
            LEFT JOIN equipos_inventario eq ON eq.id = d.equipo_id OR eq.direccion_mac = d.direccion_mac
            WHERE d.direccion_mac = :mac
            ORDER BY d.fecha_diagnostico DESC
            LIMIT 1
        ";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(array(':mac' => $mac));
    } else {
        $sql = "
            SELECT d.*, eq.codigo_activo, eq.area, eq.encargado, eq.marca_modelo, eq.numero_serie, eq.sistema_operativo
            FROM historial_diagnosticos d
            LEFT JOIN equipos_inventario eq ON eq.id = d.equipo_id
            WHERE d.equipo_id = :equipo_id
            ORDER BY d.fecha_diagnostico DESC
            LIMIT 1
        ";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(array(':equipo_id' => $equipo_id));
    }

    $detalle = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$detalle) {
        responder_error('No se encontró ningún diagnóstico para los parámetros indicados.', 404);
    }

    $mac_referencia = $detalle['direccion_mac'];

    // Decodificar campos JSONB
    $campos_json = array(
        'detalle_cpu', 'detalle_ram', 'detalle_almacenamiento',
        'detalle_red', 'detalle_placa_bios', 'apps_recientes_prefetch',
        'descargas_recientes', 'seguridad_antivirus', 'alertas_detectadas'
    );

    foreach ($campos_json as $campo) {
        if (isset($detalle[$campo]) && is_string($detalle[$campo])) {
            $detalle[$campo] = json_decode($detalle[$campo], true);
        }
    }

    // Omitir payload completo duplicado para no sobrecargar el ancho de banda
    unset($detalle['payload_completo']);

    // 2. Obtener el historial cronológico de esta PC
    $sql_historial = "
        SELECT 
            id, 
            tipo_evento, 
            estado_salud_general, 
            uso_cpu_porcentaje, 
            uso_ram_porcentaje, 
            espacio_libre_c_gb, 
            salud_disco_smart, 
            antivirus_activo,
            fecha_diagnostico
        FROM historial_diagnosticos
        WHERE direccion_mac = :mac
        ORDER BY fecha_diagnostico DESC
        LIMIT 20
    ";
    $stmt_hist = $conexion->prepare($sql_historial);
    $stmt_hist->execute(array(':mac' => $mac_referencia));
    $historial = $stmt_hist->fetchAll(PDO::FETCH_ASSOC);

    responder_exito('Detalle técnico obtenido correctamente', array(
        'actual'    => $detalle,
        'historial' => $historial
    ));

} catch (Exception $e) {
    responder_error('Error al consultar detalle del equipo: ' . $e->getMessage(), 500);
}
