<?php
/**
 * Endpoint: Consulta de Perfil / Estado de Usuario
 * Compatible con PHP 7.3
 * Ruta: GET /api/auth/perfil.php?id={id}
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/conexion_bd.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método no permitido. Utilice GET', 405);
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$usuario = isset($_GET['usuario']) ? trim($_GET['usuario']) : '';

if ($id <= 0 && empty($usuario)) {
    responder_error('Parámetro id o usuario requerido', 400);
}

try {
    $conexion = obtener_conexion_bd();

    if ($id > 0) {
        $stmt = $conexion->prepare(
            "SELECT id, usuario, nombre_completo, rol, departamento, cargo, activo, ultimo_acceso 
             FROM usuarios WHERE id = :id LIMIT 1"
        );
        $stmt->execute(array(':id' => $id));
    } else {
        $stmt = $conexion->prepare(
            "SELECT id, usuario, nombre_completo, rol, departamento, cargo, activo, ultimo_acceso 
             FROM usuarios WHERE LOWER(usuario) = LOWER(:usuario) LIMIT 1"
        );
        $stmt->execute(array(':usuario' => $usuario));
    }

    $usuario_bd = $stmt->fetch();

    if (!$usuario_bd) {
        responder_error('Usuario no encontrado', 404);
    }

    responder_exito('Usuario consultado exitosamente', array(
        'usuario' => $usuario_bd
    ));

} catch (Exception $e) {
    responder_error('Error al consultar perfil: ' . $e->getMessage(), 500);
}