<?php
/**
 * API: Crear o Actualizar Usuario
 * Compatible con PHP 7.3
 * Archivo: back_form/api/usuarios/guardar_usuario.php
 */

require_once __DIR__ . '/../../configuracion/conexion_bd.php';
require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/seguridad_api.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_error('Método no permitido', 405);
}

$cuerpo = obtener_cuerpo_json();
$conexion = obtener_conexion_bd();

$auth = verificar_permiso_admin($conexion, $cuerpo);
if (!$auth['autorizado']) {
    responder_error($auth['mensaje'], 403);
}

$id              = isset($cuerpo['id']) ? (int)$cuerpo['id'] : 0;
$usuario         = isset($cuerpo['usuario']) ? strtolower(trim($cuerpo['usuario'])) : '';
$clave           = isset($cuerpo['clave']) ? trim($cuerpo['clave']) : '';
$nombre_completo = isset($cuerpo['nombre_completo']) ? trim($cuerpo['nombre_completo']) : '';
$rol             = isset($cuerpo['rol']) ? trim($cuerpo['rol']) : 'usuario';
$departamento    = isset($cuerpo['departamento']) ? trim($cuerpo['departamento']) : '';
$cargo           = isset($cuerpo['cargo']) ? trim($cuerpo['cargo']) : '';
$activo          = isset($cuerpo['activo']) ? (bool)$cuerpo['activo'] : true;

if (empty($usuario) || empty($nombre_completo)) {
    responder_error('El nombre de usuario y el nombre completo son obligatorios', 400);
}

$roles_validos = array('admin', 'tecnico', 'usuario');
if (!in_array($rol, $roles_validos)) {
    responder_error('El rol especificado no es válido (permitidos: admin, tecnico, usuario)', 400);
}

if ($id === 0 && (empty($clave) || strlen($clave) < 6)) {
    responder_error('Para nuevos usuarios, la contraseña debe tener al menos 6 caracteres', 400);
}

try {
    // Validar nombre de usuario único
    $stmt_check = $conexion->prepare("SELECT id FROM usuarios WHERE LOWER(usuario) = LOWER(:usuario) AND id != :id");
    $stmt_check->execute(array(':usuario' => $usuario, ':id' => $id));
    if ($stmt_check->fetch()) {
        responder_error("El nombre de usuario '{$usuario}' ya se encuentra registrado", 409);
    }

    if ($id > 0) {
        // Actualizar usuario existente
        if (!empty($clave)) {
            $clave_hash = password_hash($clave, PASSWORD_BCRYPT);
            $sql = "UPDATE usuarios SET usuario = :usuario, clave_hash = :hash, nombre_completo = :nombre,
                    rol = :rol, departamento = :depto, cargo = :cargo, activo = :activo WHERE id = :id";
            $params = array(
                ':usuario' => $usuario, ':hash' => $clave_hash, ':nombre' => $nombre_completo,
                ':rol' => $rol, ':depto' => $departamento, ':cargo' => $cargo, ':activo' => $activo ? 'true' : 'false', ':id' => $id
            );
        } else {
            $sql = "UPDATE usuarios SET usuario = :usuario, nombre_completo = :nombre,
                    rol = :rol, departamento = :depto, cargo = :cargo, activo = :activo WHERE id = :id";
            $params = array(
                ':usuario' => $usuario, ':nombre' => $nombre_completo,
                ':rol' => $rol, ':depto' => $departamento, ':cargo' => $cargo, ':activo' => $activo ? 'true' : 'false', ':id' => $id
            );
        }
        $stmt = $conexion->prepare($sql);
        $stmt->execute($params);

        registrar_auditoria_operacion($conexion, 'usuarios', $id, $usuario, 'actualizar_usuario', null, $rol, $auth['usuario']['id'], $auth['usuario']['nombre_completo']);
        responder_exito("Usuario '{$usuario}' actualizado correctamente", array('id' => $id));
    } else {
        // Crear nuevo usuario
        $clave_hash = password_hash($clave, PASSWORD_BCRYPT);
        $sql = "INSERT INTO usuarios (usuario, clave_hash, nombre_completo, rol, departamento, cargo, activo)
                VALUES (:usuario, :hash, :nombre, :rol, :depto, :cargo, :activo) RETURNING id";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(array(
            ':usuario' => $usuario, ':hash' => $clave_hash, ':nombre' => $nombre_completo,
            ':rol' => $rol, ':depto' => $departamento, ':cargo' => $cargo, ':activo' => $activo ? 'true' : 'false'
        ));
        $nuevo_id = (int)$stmt->fetchColumn();

        registrar_auditoria_operacion($conexion, 'usuarios', $nuevo_id, $usuario, 'crear_usuario', null, $rol, $auth['usuario']['id'], $auth['usuario']['nombre_completo']);
        responder_exito("Usuario '{$usuario}' creado exitosamente con rol '{$rol}'", array('id' => $nuevo_id));
    }
} catch (Exception $e) {
    responder_error('Error al guardar usuario: ' . $e->getMessage(), 500);
}
