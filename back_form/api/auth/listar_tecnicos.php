<?php
/**
 * Endpoint: Listar Técnicos y Administradores Activos
 * Método: GET
 * Compatible con PHP 7.3
 * Ruta: /api/auth/listar_tecnicos.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/conexion_bd.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método no permitido. Utilice GET', 405);
}

try {
    $conexion = obtener_conexion_bd();

    $sql = "SELECT id, usuario, nombre_completo, rol, departamento, cargo
            FROM usuarios
            WHERE activo = true AND rol IN ('tecnico', 'admin')
            ORDER BY rol ASC, nombre_completo ASC";

    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $tecnicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatear IDs numéricos para consistencia
    foreach ($tecnicos as &$tec) {
        $tec['id'] = (int)$tec['id'];
    }

    responder_exito('Lista de personal técnico obtenida correctamente', $tecnicos);

} catch (Exception $e) {
    responder_error('Error al consultar lista de técnicos: ' . $e->getMessage(), 500);
}
