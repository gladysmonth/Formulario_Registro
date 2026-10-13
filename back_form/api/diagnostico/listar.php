<?php
/**
 * Endpoint API: Listar Equipos con su Último Diagnóstico
 * Compatible con PHP 7.3 y PostgreSQL 13
 * Ruta: /api/diagnostico/listar.php
 */

require_once __DIR__ . '/../../configuracion/conexion_bd.php';
require_once __DIR__ . '/../../configuracion/respuestas_api.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método no permitido. Solo se acepta GET.', 405);
}

try {
    $conexion = obtener_conexion_bd();

    $sql = "
        SELECT 
            diagnostico_id,
            equipo_id,
            direccion_mac,
            hostname,
            usuario_windows,
            ip_local,
            tipo_evento,
            fecha_diagnostico,
            estado_salud_general,
            temperatura_cpu,
            uso_cpu_porcentaje,
            uso_ram_porcentaje,
            espacio_libre_c_gb,
            salud_disco_smart,
            antivirus_activo,
            amenazas_activas,
            detalle_cpu,
            detalle_ram,
            detalle_almacenamiento,
            alertas_detectadas,
            codigo_activo,
            area,
            encargado,
            marca_modelo
        FROM vista_ultimo_diagnostico_pc
        ORDER BY fecha_diagnostico DESC
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Decodificar campos JSONB si vienen como string desde PDO
    foreach ($equipos as &$eq) {
        if (isset($eq['detalle_cpu']) && is_string($eq['detalle_cpu'])) {
            $eq['detalle_cpu'] = json_decode($eq['detalle_cpu'], true);
        }
        if (isset($eq['detalle_ram']) && is_string($eq['detalle_ram'])) {
            $eq['detalle_ram'] = json_decode($eq['detalle_ram'], true);
        }
        if (isset($eq['detalle_almacenamiento']) && is_string($eq['detalle_almacenamiento'])) {
            $eq['detalle_almacenamiento'] = json_decode($eq['detalle_almacenamiento'], true);
        }
        if (isset($eq['alertas_detectadas']) && is_string($eq['alertas_detectadas'])) {
            $eq['alertas_detectadas'] = json_decode($eq['alertas_detectadas'], true);
        }
    }

    responder_exito('Lista de equipos obtenida correctamente', $equipos);

} catch (Exception $e) {
    responder_error('Error al consultar lista de equipos: ' . $e->getMessage(), 500);
}
