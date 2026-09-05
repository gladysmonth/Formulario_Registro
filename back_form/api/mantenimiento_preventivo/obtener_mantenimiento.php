<?php
/**
 * Endpoint: Obtener Detalle de un Registro de Mantenimiento Preventivo
 * Método: GET
 * Compatible con PHP 7.3
 * Archivo: obtener_mantenimiento.php
 */

require_once __DIR__ . '/../../configuracion/respuestas_api.php';
require_once __DIR__ . '/../../configuracion/conexion_bd.php';

configurar_cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método no permitido. Utilice GET', 405);
}

$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;
$codigo = isset($_GET['codigo']) ? trim($_GET['codigo']) : null;

if (empty($id) && empty($codigo)) {
    responder_error('Debe proporcionar el parámetro id o codigo', 400);
}

try {
    $conexion = obtener_conexion_bd();

    if (!empty($id)) {
        $stmt = $conexion->prepare("SELECT * FROM mantenimientos_preventivos WHERE id = :id LIMIT 1");
        $stmt->execute(array(':id' => $id));
    } else {
        $stmt = $conexion->prepare("SELECT * FROM mantenimientos_preventivos WHERE codigo_mantenimiento = :codigo LIMIT 1");
        $stmt->execute(array(':codigo' => $codigo));
    }

    $mp = $stmt->fetch();

    if (!$mp) {
        responder_error('Registro de mantenimiento no encontrado', 404);
    }

    // Normalizar campos booleanos
    $campos_booleanos = array(
        'limpieza_carcasa_componentes',
        'limpieza_pantalla_teclado',
        'verificacion_conectores',
        'actualizacion_so',
        'eliminacion_temporales',
        'desfragmentacion_optimizacion',
        'escaneo_antivirus',
        'verificacion_drivers',
        'copia_seguridad',
        'verificacion_encendido_apagado',
        'verificacion_rendimiento',
        'verificacion_red',
        'verificacion_perifericos',
        'verificacion_temperatura_anomalias'
    );

    foreach ($campos_booleanos as $campo) {
        $mp[$campo] = (bool)$mp[$campo];
    }

    responder_exito('Ficha de mantenimiento recuperada con éxito', $mp);

} catch (Exception $e) {
    responder_error('Error al obtener el mantenimiento: ' . $e->getMessage(), 500);
}
