<?php
/**
 * Submódulo: Generación de Código Correlativo de Mantenimiento Preventivo
 * Compatible con PHP 7.3
 * Archivo: back_form/api/mantenimiento_preventivo/servicios/servicio_codigo_mp.php
 */

/**
 * Genera el siguiente código institucional para un mantenimiento preventivo (ej: MP-2026-0001)
 * @param PDO $conexion
 * @return string
 */
function generar_codigo_mantenimiento($conexion) {
    $stmt_sec = $conexion->query("SELECT COALESCE(MAX(id), 0) + 1 AS siguiente FROM mantenimientos_preventivos");
    $fila_sec = $stmt_sec->fetch();
    $siguiente = isset($fila_sec['siguiente']) ? (int)$fila_sec['siguiente'] : 1;
    return sprintf('MP-%s-%04d', date('Y'), $siguiente);
}
