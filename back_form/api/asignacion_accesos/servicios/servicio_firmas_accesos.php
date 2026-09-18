<?php
/**
 * Submódulo: Validación y Procesamiento de Firmas Digitales para Asignación de Accesos
 * Compatible con PHP 7.3
 * Archivo: back_form/api/asignacion_accesos/servicios/servicio_firmas_accesos.php
 */

/**
 * Valida las firmas digitales institucionales (Solicitante, Autorización, Sistemas)
 *
 * @param array $datos
 * @return array ['valido' => bool, 'errores' => array, 'firma_solicitante' => string|null, 'firma_autoriza' => string|null, 'firma_sistemas' => string|null]
 */
function validar_firmas_accesos($datos) {
    $errores = array();

    $firma_solicitante = !empty($datos['firma_solicitante']) ? $datos['firma_solicitante'] : null;
    $firma_autoriza    = !empty($datos['firma_autoriza']) ? $datos['firma_autoriza'] : null;
    $firma_sistemas    = !empty($datos['firma_sistemas']) ? $datos['firma_sistemas'] : null;

    // Validación de formato Base64 de las firmas presentes
    if (!empty($firma_solicitante) && strpos($firma_solicitante, 'data:image/') !== 0) {
        $errores[] = 'El formato de la firma del Solicitante no corresponde a una imagen digital válida.';
    }

    if (!empty($firma_autoriza) && strpos($firma_autoriza, 'data:image/') !== 0) {
        $errores[] = 'El formato de la firma de Autorización no corresponde a una imagen digital válida.';
    }

    if (!empty($firma_sistemas) && strpos($firma_sistemas, 'data:image/') !== 0) {
        $errores[] = 'El formato de la firma del Dpto. de Sistemas no corresponde a una imagen digital válida.';
    }

    return array(
        'valido'            => empty($errores),
        'errores'           => $errores,
        'firma_solicitante' => $firma_solicitante,
        'firma_autoriza'    => $firma_autoriza,
        'firma_sistemas'    => $firma_sistemas
    );
}
