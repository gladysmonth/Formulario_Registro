<?php
/**
 * Submódulo: Gestión y Validación de Equipos para Mantenimiento Preventivo
 * Compatible con PHP 7.3
 * Archivo: back_form/api/mantenimiento_preventivo/servicios/servicio_equipo_mp.php
 */

/**
 * Procesa y valida la información del equipo (Sección 2)
 * 
 * @param PDO   $conexion
 * @param array $datos
 * @return array ['valido' => bool, 'errores' => array, 'datos_equipo' => array]
 */
function procesar_equipo_mantenimiento($conexion, $datos) {
    $errores = array();

    $tipo_equipo_raw = !empty($datos['tipo_equipo']) ? strtoupper(trim($datos['tipo_equipo'])) : '';
    if (!in_array($tipo_equipo_raw, array('PC', 'LAPTOP'))) {
        $errores[] = 'Debe seleccionar un tipo de equipo válido (PC o LAPTOP).';
    }

    $tipo_red_raw = !empty($datos['tipo_red']) ? strtoupper(trim($datos['tipo_red'])) : null;
    if (!empty($tipo_red_raw) && !in_array($tipo_red_raw, array('LAN', 'WIFI'))) {
        $tipo_red_raw = null;
    }

    $codigo_activo = !empty($datos['codigo_activo']) ? trim($datos['codigo_activo']) : null;
    $equipo_id     = !empty($datos['equipo_id']) ? (int)$datos['equipo_id'] : null;

    // Si no se proporcionó equipo_id pero sí código de activo, buscar en catálogo institucional
    if (empty($equipo_id) && !empty($codigo_activo)) {
        $stmt_eq = $conexion->prepare("SELECT id FROM equipos_inventario WHERE codigo_activo = :codigo LIMIT 1");
        $stmt_eq->execute(array(':codigo' => $codigo_activo));
        $encontrado = $stmt_eq->fetch();
        if ($encontrado) {
            $equipo_id = (int)$encontrado['id'];
        }
    }

    $datos_equipo = array(
        'equipo_id'         => $equipo_id,
        'tipo_equipo'       => $tipo_equipo_raw,
        'nombre_equipo'     => !empty($datos['nombre_equipo']) ? trim($datos['nombre_equipo']) : null,
        'codigo_activo'     => $codigo_activo,
        'memoria_ram'       => !empty($datos['memoria_ram']) ? trim($datos['memoria_ram']) : null,
        'tipo_red'          => $tipo_red_raw,
        'marca_modelo'      => !empty($datos['marca_modelo']) ? trim($datos['marca_modelo']) : null,
        'sistema_operativo' => !empty($datos['sistema_operativo']) ? trim($datos['sistema_operativo']) : null,
        'procesador'        => !empty($datos['procesador']) ? trim($datos['procesador']) : null,
        'almacenamiento'    => !empty($datos['almacenamiento']) ? trim($datos['almacenamiento']) : null,
        'direccion_ip'      => !empty($datos['direccion_ip']) ? trim($datos['direccion_ip']) : null
    );

    return array(
        'valido'       => empty($errores),
        'errores'      => $errores,
        'datos_equipo' => $datos_equipo
    );
}
