<?php
/**
 * Submódulo: Generador de Código Correlativo de Incidencias
 * Formato: INC-YYYY-XXXX (ej: INC-2026-0001)
 * Compatible con PHP 7.3
 * Archivo: back_form/api/registro_incidencias/servicios/servicio_codigo_incidencia.php
 */

/**
 * Genera o valida el número correlativo de incidencia institucional
 *
 * @param PDO $conexion
 * @param string|null $codigo_propuesto
 * @return string
 */
function generar_codigo_incidencia($conexion, $codigo_propuesto = null) {
    $anio_actual = date('Y');
    
    // Si se envía un código propuesto válido y con formato correcto, comprobar disponibilidad
    if (!empty($codigo_propuesto)) {
        $codigo_limpio = strtoupper(trim($codigo_propuesto));
        $stmt_check = $conexion->prepare("SELECT id FROM incidencias_sistemas WHERE nro_incidencia = :codigo LIMIT 1");
        $stmt_check->execute(array(':codigo' => $codigo_limpio));
        if (!$stmt_check->fetch()) {
            return $codigo_limpio;
        }
    }

    // Obtener el último número generado en el año en curso
    $patron = 'INC-' . $anio_actual . '-%';
    $stmt = $conexion->prepare("
        SELECT nro_incidencia 
        FROM incidencias_sistemas 
        WHERE nro_incidencia LIKE :patron 
        ORDER BY id DESC 
        LIMIT 1
        FOR UPDATE
    ");
    $stmt->execute(array(':patron' => $patron));
    $ultimo = $stmt->fetch(PDO::FETCH_ASSOC);

    $siguiente_numero = 1;
    if ($ultimo && !empty($ultimo['nro_incidencia'])) {
        $partes = explode('-', $ultimo['nro_incidencia']);
        if (count($partes) === 3 && is_numeric($partes[2])) {
            $siguiente_numero = intval($partes[2]) + 1;
        }
    }

    return sprintf('INC-%s-%04d', $anio_actual, $siguiente_numero);
}
