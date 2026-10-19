<?php
/**
 * Submódulo: Resolución y Auto-vinculación de Telemetría para Soporte Técnico
 * Compatible con PHP 7.3
 * Archivo: back_form/api/soporte_tecnico/servicios/servicio_telemetria_soporte.php
 */

/**
 * Resuelve la dirección MAC y el diagnóstico de telemetría asociado a la solicitud
 * 
 * @param PDO   $conexion
 * @param array $datos
 * @param array &$equipo
 * @return array array('direccion_mac' => string|null, 'diagnostico_id' => int|null)
 */
function resolver_telemetria_soporte($conexion, $datos, &$equipo) {
    $direccion_mac  = !empty($datos['direccion_mac']) ? trim($datos['direccion_mac']) : null;
    $diagnostico_id = !empty($datos['diagnostico_id']) ? (int)$datos['diagnostico_id'] : null;

    // 1. Respaldo de MAC desde el equipo en inventario si no vino explícitamente
    if (empty($direccion_mac) && !empty($equipo['equipo_id'])) {
        $stmt_eq_mac = $conexion->prepare("SELECT direccion_mac FROM equipos_inventario WHERE id = :id");
        $stmt_eq_mac->execute(array(':id' => $equipo['equipo_id']));
        $eq_mac = $stmt_eq_mac->fetchColumn();
        if (!empty($eq_mac)) {
            $direccion_mac = trim($eq_mac);
        }
    }

    // 2. Respaldo por IP del cliente (si no se indicó MAC y no vino vinculada por equipo)
    if (empty($direccion_mac)) {
        $ip_cliente = '';
        if (!empty($datos['ip_cliente'])) {
            $ip_cliente = trim($datos['ip_cliente']);
        } elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip_cliente = trim($_SERVER['HTTP_CF_CONNECTING_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $partes_ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip_cliente = trim($partes_ip[0]);
        } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $ip_cliente = trim($_SERVER['HTTP_X_REAL_IP']);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip_cliente = trim($_SERVER['REMOTE_ADDR']);
        }

        if ($ip_cliente === '::1') {
            $ip_cliente = '127.0.0.1';
        }

        if (!empty($ip_cliente)) {
            $stmt_ip = $conexion->prepare("
                SELECT d.id AS diagnostico_id, d.direccion_mac, d.equipo_id,
                       eq.marca_modelo, eq.sistema_operativo, eq.numero_serie, eq.tipo_equipo
                FROM historial_diagnosticos d
                LEFT JOIN equipos_inventario eq ON eq.id = d.equipo_id OR eq.direccion_mac = d.direccion_mac
                WHERE d.ip_local = :ip
                ORDER BY d.fecha_diagnostico DESC
                LIMIT 1
            ");
            $stmt_ip->execute(array(':ip' => $ip_cliente));
            $hallado_ip = $stmt_ip->fetch(PDO::FETCH_ASSOC);

            if ($hallado_ip) {
                $direccion_mac = $hallado_ip['direccion_mac'];
                if (empty($diagnostico_id)) {
                    $diagnostico_id = (int)$hallado_ip['diagnostico_id'];
                }
                if (empty($equipo['equipo_id']) && !empty($hallado_ip['equipo_id'])) {
                    $equipo['equipo_id'] = (int)$hallado_ip['equipo_id'];
                }
                if (empty($equipo['marca_modelo']) && !empty($hallado_ip['marca_modelo'])) {
                    $equipo['marca_modelo'] = $hallado_ip['marca_modelo'];
                }
                if (empty($equipo['sistema_operativo']) && !empty($hallado_ip['sistema_operativo'])) {
                    $equipo['sistema_operativo'] = $hallado_ip['sistema_operativo'];
                }
            }
        }
    }

    // 3. Auto-vinculación de telemetría (último diagnóstico reportado por agente Cosmol)
    if (empty($diagnostico_id) && (!empty($direccion_mac) || !empty($equipo['equipo_id']))) {
        $stmt_diag = $conexion->prepare("
            SELECT id, direccion_mac 
            FROM historial_diagnosticos 
            WHERE (direccion_mac = :mac AND :mac != '') 
               OR (equipo_id = :eq_id AND :eq_id > 0)
            ORDER BY fecha_diagnostico DESC 
            LIMIT 1
        ");
        $stmt_diag->execute(array(
            ':mac'   => $direccion_mac ? $direccion_mac : '',
            ':eq_id' => !empty($equipo['equipo_id']) ? (int)$equipo['equipo_id'] : 0
        ));
        $diag_encontrado = $stmt_diag->fetch(PDO::FETCH_ASSOC);
        if ($diag_encontrado) {
            $diagnostico_id = (int)$diag_encontrado['id'];
            if (empty($direccion_mac)) {
                $direccion_mac = $diag_encontrado['direccion_mac'];
            }
        }
    }

    return array(
        'direccion_mac'  => $direccion_mac,
        'diagnostico_id' => $diagnostico_id
    );
}
