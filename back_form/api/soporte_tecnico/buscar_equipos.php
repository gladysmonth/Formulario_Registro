<?php
/**
 * Endpoint: Buscar y Listar Equipos del Inventario
 * Método: GET
 * Compatible con PHP 7.3
 * Archivo: buscar_equipos.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/conexion_bd.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método no permitido. Utilice GET', 405);
}

$buscar = isset($_GET['q']) ? trim($_GET['q']) : '';
$limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 50;

if ($limite <= 0 || $limite > 100) {
    $limite = 50;
}

try {
    $conexion = obtener_conexion_bd();

    if (!empty($buscar)) {
        $sql = "SELECT 
                    id,
                    codigo_activo,
                    numero_serie,
                    tipo_equipo,
                    marca_modelo,
                    sistema_operativo,
                    area,
                    encargado,
                    COALESCE(area, area_encargado) AS area_encargado,
                    centro_costo,
                    creado_en
                FROM equipos_inventario
                WHERE numero_serie ILIKE :buscar 
                   OR codigo_activo ILIKE :buscar 
                   OR marca_modelo ILIKE :buscar 
                   OR area ILIKE :buscar
                   OR encargado ILIKE :buscar
                   OR tipo_equipo ILIKE :buscar
                ORDER BY marca_modelo ASC
                LIMIT :limite";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':buscar', '%' . $buscar . '%');
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $sql = "SELECT 
                    id,
                    codigo_activo,
                    numero_serie,
                    tipo_equipo,
                    marca_modelo,
                    sistema_operativo,
                    area,
                    encargado,
                    COALESCE(area, area_encargado) AS area_encargado,
                    centro_costo,
                    creado_en
                FROM equipos_inventario
                ORDER BY id DESC
                LIMIT :limite";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
    }

    $equipos = $stmt->fetchAll();

    responder_exito('Equipos recuperados correctamente', $equipos);

} catch (Exception $e) {
    responder_error('Error al buscar equipos: ' . $e->getMessage(), 500);
}
