<?php
/**
 * API: Listar Solicitudes Históricas del Usuario en Sesión
 * Compatible con PHP 7.3
 * Archivo: back_form/api/solicitudes_usuario/listar_mis_solicitudes.php
 */

require_once __DIR__ . '/../../configuracion/conexion_bd.php';
require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/seguridad_api.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método no permitido', 405);
}

try {
    $conexion = obtener_conexion_bd();
    $usuario = obtener_usuario_actor_api($conexion);

    if (!$usuario) {
        responder_error('Usuario no autenticado o sesión expirada', 401);
    }

    $usuario_id = (int)$usuario['id'];
    $nombre     = trim($usuario['nombre_completo']);
    $login      = trim($usuario['usuario']);

    $sql = "
        SELECT 'soporte' AS modulo, 'Soporte Técnico' AS modulo_nombre, id, codigo_ticket AS codigo,
               TO_CHAR(fecha_solicitud, 'YYYY-MM-DD') AS fecha,
               descripcion_problema AS resumen, prioridad, estado,
               'Departamento de Sistemas' AS tecnico,
               creado_en AS fecha_orden
        FROM tickets_soporte
        WHERE usuario_solicitante_id = :id_s OR LOWER(nombre_solicitante) = LOWER(:nombre_s) OR LOWER(nombre_solicitante) = LOWER(:login_s)

        UNION ALL

        SELECT 'incidencia' AS modulo, 'Incidencia de Sistemas' AS modulo_nombre, id, nro_incidencia AS codigo,
               TO_CHAR(fecha_hora_reporte, 'YYYY-MM-DD HH24:MI') AS fecha,
               descripcion_tecnica_logs AS resumen, nivel_criticidad AS prioridad, estado,
               'Departamento de Sistemas' AS tecnico,
               creado_en AS fecha_orden
        FROM incidencias_sistemas
        WHERE LOWER(responsable_reporte) = LOWER(:nombre_i) OR LOWER(responsable_reporte) = LOWER(:login_i)

        UNION ALL

        SELECT 'accesos' AS modulo, 'Asignación de Accesos' AS modulo_nombre, id, nro_solicitud AS codigo,
               TO_CHAR(fecha_solicitud, 'YYYY-MM-DD HH24:MI') AS fecha,
               requerimientos_accesos AS resumen, 'normal' AS prioridad, estado,
               COALESCE(atendido_por, 'Pendiente de atención') AS tecnico,
               creado_en AS fecha_orden
        FROM solicitudes_accesos
        WHERE LOWER(nombre_solicitante) = LOWER(:nombre_a) OR LOWER(nombre_solicitante) = LOWER(:login_a)

        ORDER BY fecha_orden DESC
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute(array(
        ':id_s'     => $usuario_id,
        ':nombre_s' => $nombre,
        ':login_s'  => $login,
        ':nombre_i' => $nombre,
        ':login_i'  => $login,
        ':nombre_a' => $nombre,
        ':login_a'  => $login
    ));

    $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Contadores de estado
    $total = count($solicitudes);
    $pendientes = 0;
    $en_proceso = 0;
    $resueltas  = 0;

    foreach ($solicitudes as $s) {
        $est = strtolower($s['estado']);
        if ($est === 'pendiente' || $est === 'abierta') {
            $pendientes++;
        } elseif ($est === 'en_proceso' || $est === 'en_atencion') {
            $en_proceso++;
        } else {
            $resueltas++;
        }
    }

    responder_exito('Solicitudes del usuario obtenidas correctamente', array(
        'kpis' => array(
            'total'      => $total,
            'pendientes' => $pendientes,
            'en_proceso' => $en_proceso,
            'resueltas'  => $resueltas
        ),
        'solicitudes' => $solicitudes
    ));
} catch (Exception $e) {
    responder_error('Error al consultar solicitudes: ' . $e->getMessage(), 500);
}

