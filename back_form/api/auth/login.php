<?php
/**
 * Endpoint: Inicio de Sesión de Usuarios
 * Compatible con PHP 7.3
 * Ruta: POST /api/auth/login.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/conexion_bd.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_error('Método no permitido. Utilice POST', 405);
}

$cuerpo = obtener_cuerpo_json();

$usuario = isset($cuerpo['usuario']) ? trim($cuerpo['usuario']) : '';
$clave   = isset($cuerpo['clave']) ? trim($cuerpo['clave']) : '';

if (empty($usuario) || empty($clave)) {
    responder_error('Debe ingresar usuario y contraseña', 400);
}

try {
    $conexion = obtener_conexion_bd();

    $stmt = $conexion->prepare(
        "SELECT id, usuario, clave_hash, nombre_completo, rol, departamento, cargo, activo 
         FROM usuarios 
         WHERE LOWER(usuario) = LOWER(:usuario) 
         LIMIT 1"
    );
    $stmt->execute(array(':usuario' => $usuario));
    $usuario_bd = $stmt->fetch();

    if (!$usuario_bd) {
        responder_error('Credenciales incorrectas', 401);
    }

    if (!$usuario_bd['activo']) {
        responder_error('El usuario se encuentra inactivo. Consulte con el administrador', 403);
    }

    if (!password_verify($clave, $usuario_bd['clave_hash'])) {
        responder_error('Credenciales incorrectas', 401);
    }

    // Actualizar último acceso
    $stmt_update = $conexion->prepare("UPDATE usuarios SET ultimo_acceso = CURRENT_TIMESTAMP WHERE id = :id");
    $stmt_update->execute(array(':id' => $usuario_bd['id']));

    // Retornar datos del usuario sin el hash
    $datos_usuario = array(
        'id'              => (int)$usuario_bd['id'],
        'usuario'         => $usuario_bd['usuario'],
        'nombre_completo' => $usuario_bd['nombre_completo'],
        'rol'             => $usuario_bd['rol'],
        'departamento'    => $usuario_bd['departamento'],
        'cargo'           => $usuario_bd['cargo']
    );

    responder_exito('Inicio de sesión exitoso', array(
        'usuario' => $datos_usuario
    ));

} catch (Exception $e) {
    responder_error('Error interno al autenticar usuario: ' . $e->getMessage(), 500);
}