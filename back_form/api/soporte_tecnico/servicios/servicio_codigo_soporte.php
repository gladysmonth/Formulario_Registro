<?php
/**
 * Submódulo: Generación de Código Correlativo de Tickets de Soporte
 * Compatible con PHP 7.3
 * Archivo: back_form/api/soporte_tecnico/servicios/servicio_codigo_soporte.php
 */

/**
 * Genera el siguiente código institucional para un ticket de soporte (ej: SOP-2026-0001)
 * @param PDO $conexion
 * @return string
 */
function generar_codigo_ticket($conexion) {
    $stmt_sec = $conexion->query("SELECT COALESCE(MAX(id), 0) + 1 AS siguiente FROM tickets_soporte");
    $fila_sec = $stmt_sec->fetch();
    $siguiente = isset($fila_sec['siguiente']) ? (int)$fila_sec['siguiente'] : 1;
    return sprintf('SOP-%s-%04d', date('Y'), $siguiente);
}
