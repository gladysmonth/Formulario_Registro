<?php
/**
 * Endpoint: Obtener Detalle Enriquecido de un Ticket de Soporte Técnico
 * Método: GET
 * Compatible con PHP 7.3
 * Archivo: obtener_ticket.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/conexion_bd.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método no permitido. Utilice GET', 405);
}

$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;
$codigo = isset($_GET['codigo']) ? trim($_GET['codigo']) : null;

if (empty($id) && empty($codigo)) {
    responder_error('Debe proporcionar el parámetro id o codigo', 400);
}

try {
    $conexion = obtener_conexion_bd();

    $sql_base = "SELECT 
                    t.id,
                    t.codigo_ticket,
                    t.nombre_solicitante,
                    t.usuario_solicitante_id,
                    t.fecha_solicitud,
                    t.departamento_area,
                    t.soporte_hardware,
                    t.soporte_software,
                    t.descripcion_problema,
                    t.equipo_id,
                    t.codigo_activo,
                    t.numero_serie,
                    t.tipo_equipo,
                    t.marca_modelo,
                    t.sistema_operativo,
                    t.area,
                    t.encargado,
                    COALESCE(t.area, t.area_encargado) AS area_encargado,
                    t.centro_costo,
                    t.prioridad,
                    t.estado,
                    t.motivo_imposibilidad,
                    t.diagnostico_id,
                    t.direccion_mac,
                    t.tecnico_asignado_id,
                    ut.nombre_completo AS nombre_tecnico_asignado,
                    t.firma_solicitante,
                    COALESCE(a.firma_sistemas, t.firma_sistemas) AS firma_sistemas,
                    t.creado_en,
                    t.actualizado_en,
                    a.id AS atencion_id,
                    a.fecha_hora_atencion,
                    a.tecnico_id,
                    a.asignado_por_id,
                    ua.nombre_completo AS nombre_asignado_por,
                    COALESCE(a.tecnico_asignado, ut.nombre_completo) AS tecnico_asignado,
                    a.tipo_resolucion,
                    a.diagnostico,
                    a.solucion_aplicada,
                    a.observaciones_recomendacion
                FROM tickets_soporte t
                LEFT JOIN atenciones_soporte a ON a.ticket_id = t.id
                LEFT JOIN usuarios ut ON ut.id = COALESCE(a.tecnico_id, t.tecnico_asignado_id)
                LEFT JOIN usuarios ua ON ua.id = a.asignado_por_id";

    if (!empty($id)) {
        $sql = "{$sql_base} WHERE t.id = :id ORDER BY a.id DESC LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(array(':id' => $id));
    } else {
        $sql = "{$sql_base} WHERE t.codigo_ticket = :codigo ORDER BY a.id DESC LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(array(':codigo' => $codigo));
    }

    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        responder_error('Ticket no encontrado', 404);
    }

    $ticket['id']                     = (int)$ticket['id'];
    $ticket['soporte_hardware']       = (bool)$ticket['soporte_hardware'];
    $ticket['soporte_software']       = (bool)$ticket['soporte_software'];
    $ticket['usuario_solicitante_id'] = $ticket['usuario_solicitante_id'] ? (int)$ticket['usuario_solicitante_id'] : null;
    $ticket['tecnico_asignado_id']    = $ticket['tecnico_asignado_id'] ? (int)$ticket['tecnico_asignado_id'] : null;
    $ticket['diagnostico_id']         = $ticket['diagnostico_id'] ? (int)$ticket['diagnostico_id'] : null;

    // 1. Obtener telemetría capturada al momento del ticket (diagnóstico inicial)
    $telemetria_pc = null;
    if (!empty($ticket['diagnostico_id'])) {
        $stmt_diag = $conexion->prepare("
            SELECT id, direccion_mac, hostname, usuario_windows, ip_local,
                   tipo_evento, fecha_diagnostico, estado_salud_general,
                   temperatura_cpu, uso_cpu_porcentaje, uso_ram_porcentaje,
                   espacio_libre_c_gb, salud_disco_smart, antivirus_activo,
                   amenazas_activas, alertas_detectadas
            FROM historial_diagnosticos
            WHERE id = :diag_id
            LIMIT 1
        ");
        $stmt_diag->execute(array(':diag_id' => $ticket['diagnostico_id']));
        $diag = $stmt_diag->fetch(PDO::FETCH_ASSOC);
        if ($diag) {
            if (!empty($diag['alertas_detectadas']) && is_string($diag['alertas_detectadas'])) {
                $diag['alertas_detectadas'] = json_decode($diag['alertas_detectadas'], true);
            }
            $telemetria_pc = array(
                'diagnostico_id'       => (int)$diag['id'],
                'direccion_mac'        => $diag['direccion_mac'],
                'hostname'             => $diag['hostname'],
                'usuario_windows'      => $diag['usuario_windows'],
                'ip_local'             => $diag['ip_local'],
                'fecha_captura'        => $diag['fecha_diagnostico'],
                'estado_salud'         => $diag['estado_salud_general'],
                'temperatura_cpu'      => $diag['temperatura_cpu'] !== null ? (float)$diag['temperatura_cpu'] : null,
                'uso_cpu_porcentaje'   => (float)$diag['uso_cpu_porcentaje'],
                'uso_ram_porcentaje'   => (float)$diag['uso_ram_porcentaje'],
                'espacio_libre_c_gb'   => (float)$diag['espacio_libre_c_gb'],
                'salud_disco_smart'    => $diag['salud_disco_smart'],
                'antivirus_activo'     => (bool)$diag['antivirus_activo'],
                'amenazas_activas'     => (int)$diag['amenazas_activas'],
                'alertas_detectadas'   => $diag['alertas_detectadas'] ?: array()
            );
        }
    }
    $ticket['telemetria_pc'] = $telemetria_pc;

    // 2. Punto B.2: Obtener el estado actual más reciente en vivo de la PC (si cuenta con agente)
    $ticket['telemetria_actual_en_vivo'] = null;
    if (!empty($ticket['direccion_mac'])) {
        $stmt_vivo = $conexion->prepare("
            SELECT diagnostico_id, fecha_diagnostico, estado_salud_general,
                   temperatura_cpu, uso_cpu_porcentaje, uso_ram_porcentaje,
                   espacio_libre_c_gb, salud_disco_smart, antivirus_activo, alertas_detectadas
            FROM vista_ultimo_diagnostico_pc
            WHERE direccion_mac = :mac
            LIMIT 1
        ");
        $stmt_vivo->execute(array(':mac' => $ticket['direccion_mac']));
        $vivo = $stmt_vivo->fetch(PDO::FETCH_ASSOC);
        if ($vivo) {
            if (!empty($vivo['alertas_detectadas']) && is_string($vivo['alertas_detectadas'])) {
                $vivo['alertas_detectadas'] = json_decode($vivo['alertas_detectadas'], true);
            }
            $ticket['telemetria_actual_en_vivo'] = array(
                'diagnostico_id'     => (int)$vivo['diagnostico_id'],
                'fecha_diagnostico'  => $vivo['fecha_diagnostico'],
                'estado_salud'       => $vivo['estado_salud_general'],
                'temperatura_cpu'    => $vivo['temperatura_cpu'] !== null ? (float)$vivo['temperatura_cpu'] : null,
                'uso_cpu_porcentaje' => (float)$vivo['uso_cpu_porcentaje'],
                'uso_ram_porcentaje' => (float)$vivo['uso_ram_porcentaje'],
                'espacio_libre_c_gb' => (float)$vivo['espacio_libre_c_gb'],
                'salud_disco_smart'  => $vivo['salud_disco_smart'],
                'antivirus_activo'   => (bool)$vivo['antivirus_activo'],
                'alertas_detectadas' => $vivo['alertas_detectadas'] ?: array()
            );
        }
    }

    responder_exito('Detalle de ticket recuperado exitosamente', $ticket);

} catch (Exception $e) {
    responder_error('Error al obtener el ticket: ' . $e->getMessage(), 500);
}
