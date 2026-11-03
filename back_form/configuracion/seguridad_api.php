<?php
/**
 * Componente: Seguridad, Control de Acceso (RBAC) y Auditoría para API
 * Compatible con PHP 7.3
 * Archivo: back_form/configuracion/seguridad_api.php
 */

require_once __DIR__ . '/respuestas_api.php';
require_once __DIR__ . '/conexion_bd.php';

/**
 * Obtiene la dirección IP real del cliente que realiza la petición
 * @return string
 */
function obtener_ip_cliente() {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    }
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return trim($_SERVER['HTTP_CLIENT_IP']);
    }
    return isset($_SERVER['REMOTE_ADDR']) ? trim($_SERVER['REMOTE_ADDR']) : '127.0.0.1';
}

/**
 * Obtiene el usuario emisor de la petición a partir de cabeceras o cuerpo de la solicitud
 * @param PDO $conexion
 * @param array $datos_peticion
 * @return array|null
 */
function obtener_usuario_actor_api($conexion, $datos_peticion = array()) {
    $usuario_id = 0;

    // 1. Revisar cabecera Authorization (Bearer <id>)
    if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
        if (preg_match('/Bearer\s+(\d+)/i', $_SERVER['HTTP_AUTHORIZATION'], $coincidencias)) {
            $usuario_id = (int)$coincidencias[1];
        }
    }

    // 2. Revisar cabecera personalizada X-Usuario-Id
    if ($usuario_id <= 0 && !empty($_SERVER['HTTP_X_USUARIO_ID'])) {
        $usuario_id = (int)$_SERVER['HTTP_X_USUARIO_ID'];
    }

    // 3. Revisar en los datos del cuerpo JSON
    if ($usuario_id <= 0 && !empty($datos_peticion['usuario_id'])) {
        $usuario_id = (int)$datos_peticion['usuario_id'];
    }
    if ($usuario_id <= 0 && !empty($datos_peticion['asignado_por_id'])) {
        $usuario_id = (int)$datos_peticion['asignado_por_id'];
    }
    if ($usuario_id <= 0 && !empty($datos_peticion['tecnico_id'])) {
        $usuario_id = (int)$datos_peticion['tecnico_id'];
    }

    if ($usuario_id <= 0) {
        return null;
    }

    try {
        $stmt = $conexion->prepare("SELECT id, usuario, nombre_completo, rol, departamento, cargo, activo FROM usuarios WHERE id = :id LIMIT 1");
        $stmt->execute(array(':id' => $usuario_id));
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && $usuario['activo']) {
            return $usuario;
        }
        return null;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Verifica si la petición tiene rol técnico o administrador
 * @param PDO $conexion
 * @param array $datos_peticion
 * @return array array('autorizado' => bool, 'usuario' => array|null, 'mensaje' => string)
 */
function verificar_permiso_tecnico($conexion, $datos_peticion = array()) {
    $usuario = obtener_usuario_actor_api($conexion, $datos_peticion);

    if (!$usuario) {
        return array(
            'autorizado' => false,
            'usuario'    => null,
            'mensaje'    => 'Usuario no identificado o inactivo en el sistema'
        );
    }

    $roles_permitidos = array('admin', 'tecnico');
    if (!in_array($usuario['rol'], $roles_permitidos)) {
        return array(
            'autorizado' => false,
            'usuario'    => $usuario,
            'mensaje'    => 'Acceso denegado: se requieren privilegios de Administrador o Técnico'
        );
    }

    return array(
        'autorizado' => true,
        'usuario'    => $usuario,
        'mensaje'    => 'Acceso autorizado'
    );
}

/**
 * Verifica si la petición tiene rol exclusivo de Administrador
 * @param PDO $conexion
 * @param array $datos_peticion
 * @return array array('autorizado' => bool, 'usuario' => array|null, 'mensaje' => string)
 */
function verificar_permiso_admin($conexion, $datos_peticion = array()) {
    $usuario = obtener_usuario_actor_api($conexion, $datos_peticion);

    if (!$usuario) {
        return array(
            'autorizado' => false,
            'usuario'    => null,
            'mensaje'    => 'Usuario no identificado o inactivo en el sistema'
        );
    }

    if ($usuario['rol'] !== 'admin') {
        return array(
            'autorizado' => false,
            'usuario'    => $usuario,
            'mensaje'    => 'Acceso denegado: se requieren privilegios exclusivos de Administrador'
        );
    }

    return array(
        'autorizado' => true,
        'usuario'    => $usuario,
        'mensaje'    => 'Acceso autorizado'
    );
}

/**
 * Registra una acción en la tabla de auditoría de operaciones de forma segura
 * @param PDO $conexion
 * @param string $modulo
 * @param int $registro_id
 * @param string $codigo_registro
 * @param string $accion
 * @param string|null $estado_anterior
 * @param string|null $estado_nuevo
 * @param int|null $usuario_id
 * @param string|null $usuario_nombre
 * @param mixed $detalles
 * @return bool
 */
function registrar_auditoria_operacion(
    $conexion,
    $modulo,
    $registro_id,
    $codigo_registro,
    $accion,
    $estado_anterior = null,
    $estado_nuevo = null,
    $usuario_id = null,
    $usuario_nombre = null,
    $detalles = null
) {
    try {
        $ip = obtener_ip_cliente();
        $detalles_json = null;
        if ($detalles !== null) {
            $detalles_json = is_string($detalles) ? $detalles : json_encode($detalles, JSON_UNESCAPED_UNICODE);
        }

        $sql = "INSERT INTO auditoria_operaciones (
            modulo,
            registro_id,
            codigo_registro,
            accion,
            estado_anterior,
            estado_nuevo,
            usuario_id,
            usuario_nombre,
            ip_origen,
            detalles
        ) VALUES (
            :modulo,
            :registro_id,
            :codigo_registro,
            :accion,
            :estado_anterior,
            :estado_nuevo,
            :usuario_id,
            :usuario_nombre,
            :ip_origen,
            :detalles
        )";

        $stmt = $conexion->prepare($sql);
        return $stmt->execute(array(
            ':modulo'          => $modulo,
            ':registro_id'     => $registro_id ? (int)$registro_id : null,
            ':codigo_registro' => $codigo_registro,
            ':accion'          => $accion,
            ':estado_anterior' => $estado_anterior,
            ':estado_nuevo'    => $estado_nuevo,
            ':usuario_id'      => $usuario_id ? (int)$usuario_id : null,
            ':usuario_nombre'  => $usuario_nombre,
            ':ip_origen'       => $ip,
            ':detalles'        => $detalles_json
        ));
    } catch (Exception $e) {
        error_log('[Auditoría Error] ' . $e->getMessage());
        return false;
    }
}
