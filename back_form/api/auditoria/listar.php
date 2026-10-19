<?php
/**
 * Endpoint: Listar Historial de Auditoría de Operaciones
 * Compatible con PHP 7.3
 * Archivo: back_form/api/auditoria/listar.php
 */

require_once __DIR__ . '/../../configuracion/conexion_bd.php';
require_once __DIR__ . '/../../configuracion/respuestas_api.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método HTTP no permitido. Debe utilizar GET.', 405);
}

try {
    $conexion = obtener_conexion_bd();

    $modulo      = isset($_GET['modulo']) ? trim($_GET['modulo']) : '';
    $registro_id = isset($_GET['registro_id']) ? (int)$_GET['registro_id'] : 0;
    $codigo      = isset($_GET['codigo']) ? trim($_GET['codigo']) : '';
    $limite      = isset($_GET['limite']) ? (int)$_GET['limite'] : 50;
    $offset      = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

    if ($limite <= 0 || $limite > 200) {
        $limite = 50;
    }
    if ($offset < 0) {
        $offset = 0;
    }

    $condiciones = array();
    $params = array();

    if (!empty($modulo)) {
        $condiciones[] = "modulo = :modulo";
        $params[':modulo'] = $modulo;
    }

    if ($registro_id > 0) {
        $condiciones[] = "registro_id = :registro_id";
        $params[':registro_id'] = $registro_id;
    }

    if (!empty($codigo)) {
        $condiciones[] = "codigo_registro ILIKE :codigo";
        $params[':codigo'] = '%' . $codigo . '%';
    }

    $where = !empty($condiciones) ? 'WHERE ' . implode(' AND ', $condiciones) : '';

    // Conteo total
    $stmt_conteo = $conexion->prepare("SELECT COUNT(*) FROM auditoria_operaciones {$where}");
    $stmt_conteo->execute($params);
    $total = (int)$stmt_conteo->fetchColumn();

    // Consulta de registros
    $sql = "
        SELECT 
            id,
            modulo,
            registro_id,
            codigo_registro,
            accion,
            estado_anterior,
            estado_nuevo,
            usuario_id,
            usuario_nombre,
            ip_origen,
            detalles,
            creado_en
        FROM auditoria_operaciones
        {$where}
        ORDER BY id DESC
        LIMIT :limite OFFSET :offset
    ";

    $stmt = $conexion->prepare($sql);
    foreach ($params as $clave => $valor) {
        $stmt->bindValue($clave, $valor);
    }
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Decodificar JSON de detalles para PHP array
    foreach ($registros as &$reg) {
        $reg['id'] = (int)$reg['id'];
        $reg['registro_id'] = $reg['registro_id'] ? (int)$reg['registro_id'] : null;
        $reg['usuario_id'] = $reg['usuario_id'] ? (int)$reg['usuario_id'] : null;
        if (!empty($reg['detalles']) && is_string($reg['detalles'])) {
            $dec = json_decode($reg['detalles'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $reg['detalles'] = $dec;
            }
        }
    }

    responder_exito('Historial de auditoría recuperado correctamente', array(
        'total'     => $total,
        'limite'    => $limite,
        'offset'    => $offset,
        'registros' => $registros
    ));

} catch (Exception $e) {
    responder_error('Error al consultar historial de auditoría: ' . $e->getMessage(), 500);
}
