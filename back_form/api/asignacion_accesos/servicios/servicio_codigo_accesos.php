<?php
/**
 * Submódulo: Generador de Código Correlativo de Asignación de Accesos
 * Formato: ACC-YYYY-XXXX (ej: ACC-2026-0001)
 * Compatible con PHP 7.3
 * Archivo: back_form/api/asignacion_accesos/servicios/servicio_codigo_accesos.php
 */

/**
 * Genera o valida el número correlativo de solicitud de accesos
 *
 * @param PDO $conexion
 * @param string|null $codigo_propuesto
 * @return string
 */
function generar_codigo_accesos($conexion, $codigo_propuesto = null) {
    $anio_actual = date('Y');
    
    // Si se envía un código propuesto válido, comprobar disponibilidad
    if (!empty($codigo_propuesto)) {
        $codigo_limpio = strtoupper(trim($codigo_propuesto));
        $stmt_check = $conexion->prepare("SELECT id FROM solicitudes_accesos WHERE nro_solicitud = :codigo LIMIT 1");
        $stmt_check->execute(array(':codigo' => $codigo_limpio));
        if (!$stmt_check->fetch()) {
            return $codigo_limpio;
        }
    }

    // Obtener el último número generado en el año en curso
    $patron = 'ACC-' . $anio_actual . '-%';
    $stmt = $conexion->prepare("
        SELECT nro_solicitud 
        FROM solicitudes_accesos 
        WHERE nro_solicitud LIKE :patron 
        ORDER BY id DESC 
        LIMIT 1
        FOR UPDATE
    ");
    $stmt->execute(array(':patron' => $patron));
    $ultimo = $stmt->fetch(PDO::FETCH_ASSOC);

    $siguiente_numero = 1;
    if ($ultimo && !empty($ultimo['nro_solicitud'])) {
        $partes = explode('-', $ultimo['nro_solicitud']);
        if (count($partes) === 3 && is_numeric($partes[2])) {
            $siguiente_numero = intval($partes[2]) + 1;
        }
    }

    return sprintf('ACC-%s-%04d', $anio_actual, $siguiente_numero);
}
