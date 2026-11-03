<?php
/**
 * API: Cambiar Estado (Activar/Desactivar) de un Usuario
 * Compatible con PHP 7.3
 * Archivo: back_form/api/usuarios/cambiar_estado.php
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

$id     = isset($cuerpo['id']) ? (int)$cuerpo['id'] : 0;
$activo = isset($cuerpo['activo']) ? (bool)$cuerpo['activo'] : false;

if ($id <= 0) {
    responder_error('ID de usuario inválido', 400);
}

// Proteger al administrador de desactivarse a sí mismo
if ($id === (int)$auth['usuario']['id'] && !$activo) {
    responder_error('No puedes desactivar tu propia cuenta de Administrador activa', 400);
}

try {
    $stmt = $conexion->prepare("UPDATE usuarios SET activo = :activo WHERE id = :id");
    $stmt->execute(array(
        ':activo' => $activo ? 'true' : 'false',
        ':id'     => $id
    ));

    $estado_txt = $activo ? 'activado' : 'desactivado';
    registrar_auditoria_operacion($conexion, 'usuarios', $id, 'ID:' . $id, 'cambiar_estado', null, $estado_txt, $auth['usuario']['id'], $auth['usuario']['nombre_completo']);

    responder_exito("Usuario {$estado_txt} correctamente", array(
        'id'     => $id,
        'activo' => $activo
    ));
} catch (Exception $e) {
    responder_error('Error al cambiar estado del usuario: ' . $e->getMessage(), 500);
}
