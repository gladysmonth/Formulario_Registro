<?php
/**
 * Submódulo: Validación y Tratamiento de Firmas Digitales
 * Compatible con PHP 7.3
 * Archivo: back_form/api/soporte_tecnico/servicios/servicio_firmas.php
 */

/**
 * Valida y extrae las firmas digitales del solicitante y del Dpto. de Sistemas
 * 
 * @param array $datos
 * @return array ['valido' => bool, 'errores' => array, 'firma_solicitante' => string, 'firma_sistemas' => string|null]
 */
function validar_y_procesar_firmas($datos, $exigir_firma_sistemas = false) {
    $errores = array();
    $firma_solicitante = !empty($datos['firma_solicitante']) ? $datos['firma_solicitante'] : null;
    $firma_sistemas    = !empty($datos['firma_sistemas']) ? $datos['firma_sistemas'] : null;

    if (empty($firma_solicitante)) {
        $errores[] = 'La firma digital del Solicitante es obligatoria para registrar el ticket.';
    } else if (strpos($firma_solicitante, 'data:image/') !== 0) {
        $errores[] = 'El formato de la firma del Solicitante no corresponde a una imagen digital válida.';
    }

    if ($exigir_firma_sistemas && empty($firma_sistemas)) {
        $errores[] = 'La firma digital del Dpto. de Sistemas es obligatoria para resolver o cerrar el ticket.';
    } else if (!empty($firma_sistemas) && strpos($firma_sistemas, 'data:image/') !== 0) {
        $errores[] = 'El formato de la firma del Dpto. de Sistemas no corresponde a una imagen digital válida.';
    }

    return array(
        'valido'            => empty($errores),
        'errores'           => $errores,
        'firma_solicitante' => $firma_solicitante,
        'firma_sistemas'    => $firma_sistemas
    );
}
