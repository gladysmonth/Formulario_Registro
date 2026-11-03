<?php
/**
 * API: Listar Usuarios del Sistema
 * Compatible con PHP 7.3
 * Archivo: back_form/api/usuarios/listar_usuarios.php
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

    // Validar permiso de administrador
    $auth = verificar_permiso_admin($conexion);
    if (!$auth['autorizado']) {
        responder_error($auth['mensaje'], 403);
    }

    $sql = "SELECT id, usuario, nombre_completo, rol, departamento, cargo, activo,
                   TO_CHAR(creado_en, 'YYYY-MM-DD HH24:MI') as creado_en,
                   TO_CHAR(ultimo_acceso, 'YYYY-MM-DD HH24:MI') as ultimo_acceso
            FROM usuarios
            ORDER BY id ASC";

    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    responder_exito('Listado de usuarios obtenido correctamente', array(
        'total'    => count($usuarios),
        'usuarios' => $usuarios
    ));
} catch (Exception $e) {
    responder_error('Error al listar usuarios: ' . $e->getMessage(), 500);
}
