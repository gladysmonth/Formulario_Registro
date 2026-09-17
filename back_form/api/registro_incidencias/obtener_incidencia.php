<?php
/**
 * Endpoint: Obtener Detalle de una Incidencia de Sistemas (FOR_RIS_001, V-1)
 * Método: GET
 * Compatible con PHP 7.3
 * Archivo: back_form/api/registro_incidencias/obtener_incidencia.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/conexion_bd.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método no permitido. Utilice GET', 405);
}

$id     = isset($_GET['id']) ? intval($_GET['id']) : 0;
$codigo = isset($_GET['codigo']) ? trim($_GET['codigo']) : '';

if ($id <= 0 && empty($codigo)) {
    responder_error('Debe proporcionar el ID o código de la incidencia', 400);
}

try {
    $conexion = obtener_conexion_bd();

    if ($id > 0) {
        $stmt = $conexion->prepare("SELECT * FROM incidencias_sistemas WHERE id = :id LIMIT 1");
        $stmt->execute(array(':id' => $id));
    } else {
        $stmt = $conexion->prepare("SELECT * FROM incidencias_sistemas WHERE nro_incidencia = :codigo LIMIT 1");
        $stmt->execute(array(':codigo' => $codigo));
    }

    $incidencia = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$incidencia) {
        responder_error('La incidencia solicitada no existe o fue eliminada', 404);
    }

    responder_exito('Incidencia encontrada', $incidencia);

} catch (PDOException $e) {
    responder_error('Error de base de datos al obtener incidencia: ' . $e->getMessage(), 500);
} catch (Exception $e) {
    responder_error('Error interno del servidor: ' . $e->getMessage(), 500);
}
