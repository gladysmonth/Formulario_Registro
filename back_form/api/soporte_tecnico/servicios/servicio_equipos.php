<?php
/**
 * Submódulo: Gestión y Vinculación de Equipos de Inventario
 * Compatible con PHP 7.3
 * Archivo: back_form/api/soporte_tecnico/servicios/servicio_equipos.php
 */

/**
 * Procesa el equipo afectado: busca si ya existe en inventario o lo registra si fue solicitado
 * 
 * @param PDO   $conexion
 * @param array $datos
 * @return array Datos saneados del equipo incluyendo 'equipo_id'
 */
function procesar_equipo_en_inventario($conexion, $datos) {
    $equipo_id          = !empty($datos['equipo_id']) ? (int)$datos['equipo_id'] : null;
    $codigo_activo      = !empty($datos['codigo_activo']) ? trim($datos['codigo_activo']) : null;
    $numero_serie       = !empty($datos['numero_serie']) ? trim($datos['numero_serie']) : null;
    $tipo_equipo        = !empty($datos['tipo_equipo']) ? trim($datos['tipo_equipo']) : null;
    $marca_modelo       = !empty($datos['marca_modelo']) ? trim($datos['marca_modelo']) : null;
    $sistema_operativo  = !empty($datos['sistema_operativo']) ? trim($datos['sistema_operativo']) : null;
    $area               = !empty($datos['area']) ? trim($datos['area']) : (!empty($datos['area_encargado']) ? trim($datos['area_encargado']) : null);
    $encargado          = !empty($datos['encargado']) ? trim($datos['encargado']) : null;
    $centro_costo       = !empty($datos['centro_costo']) ? trim($datos['centro_costo']) : null;
    $guardar_inventario = !empty($datos['guardar_en_inventario']) ? true : false;

    // Si se solicita registrar el equipo en el inventario o no existía equipo_id pero hay datos suficientes
    if ($guardar_inventario || (empty($equipo_id) && (!empty($numero_serie) || !empty($codigo_activo)) && !empty($tipo_equipo))) {
        // Verificar si ya existe registrado por número de serie o código de activo
        $stmt_check = $conexion->prepare("SELECT id FROM equipos_inventario WHERE (numero_serie = :serie AND :serie != '') OR (codigo_activo = :cod AND :cod != '') LIMIT 1");
        $stmt_check->execute(array(
            ':serie' => $numero_serie !== null ? $numero_serie : '',
            ':cod'   => $codigo_activo !== null ? $codigo_activo : ''
        ));
        $eq_previo = $stmt_check->fetch();

        if ($eq_previo) {
            $equipo_id = $eq_previo['id'];
        } else if (!empty($tipo_equipo)) {
            $sql_eq = "INSERT INTO equipos_inventario (
                        codigo_activo,
                        numero_serie,
                        tipo_equipo,
                        marca_modelo,
                        sistema_operativo,
                        area,
                        encargado,
                        centro_costo
                    ) VALUES (
                        :codigo_activo,
                        :numero_serie,
                        :tipo_equipo,
                        :marca_modelo,
                        :sistema_operativo,
                        :area,
                        :encargado,
                        :centro_costo
                    ) RETURNING id";
            $stmt_eq = $conexion->prepare($sql_eq);
            $stmt_eq->execute(array(
                ':codigo_activo'    => $codigo_activo,
                ':numero_serie'     => $numero_serie !== null ? $numero_serie : 'S/N',
                ':tipo_equipo'      => $tipo_equipo,
                ':marca_modelo'     => $marca_modelo !== null ? $marca_modelo : 'No especificado',
                ':sistema_operativo'=> $sistema_operativo,
                ':area'             => $area !== null ? $area : (!empty($datos['departamento_area']) ? trim($datos['departamento_area']) : null),
                ':encargado'        => $encargado,
                ':centro_costo'     => $centro_costo
            ));
            $res_eq = $stmt_eq->fetch();
            $equipo_id = $res_eq['id'];
        }
    }

    return array(
        'equipo_id'         => $equipo_id,
        'codigo_activo'     => $codigo_activo,
        'numero_serie'      => $numero_serie,
        'tipo_equipo'       => $tipo_equipo,
        'marca_modelo'      => $marca_modelo,
        'sistema_operativo' => $sistema_operativo,
        'area'              => $area,
        'encargado'         => $encargado,
        'centro_costo'      => $centro_costo
    );
}
