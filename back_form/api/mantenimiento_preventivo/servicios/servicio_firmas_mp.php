<?php
/**
 * Submódulo: Validación de Firmas Digitales para Mantenimiento Preventivo
 * Compatible con PHP 7.3
 * Archivo: back_form/api/mantenimiento_preventivo/servicios/servicio_firmas_mp.php
 */

/**
 * Valida las firmas digitales del Responsable del Equipo y del Dpto. de Sistemas
 * 
 * @param array $datos
 * @return array ['valido' => bool, 'errores' => array, 'firma_responsable' => string, 'firma_sistemas' => string]
 */
function validar_firmas_mantenimiento($datos) {
    $errores = array();
    $firma_responsable = !empty($datos['firma_responsable_equipo']) ? $datos['firma_responsable_equipo'] : null;
    $firma_sistemas    = !empty($datos['firma_sistemas']) ? $datos['firma_sistemas'] : null;

    if (empty($firma_responsable)) {
        $errores[] = 'La firma digital del Responsable del Equipo es obligatoria para registrar el mantenimiento.';
    } else if (strpos($firma_responsable, 'data:image/') !== 0) {
        $errores[] = 'El formato de la firma del Responsable del Equipo no corresponde a una imagen digital válida.';
    }

    if (empty($firma_sistemas)) {
        $errores[] = 'La firma digital del Dpto. de Sistemas es obligatoria para certificar la rutina de mantenimiento.';
    } else if (strpos($firma_sistemas, 'data:image/') !== 0) {
        $errores[] = 'El formato de la firma del Dpto. de Sistemas no corresponde a una imagen digital válida.';
    }

    return array(
        'valido'            => empty($errores),
        'errores'           => $errores,
        'firma_responsable' => $firma_responsable,
        'firma_sistemas'    => $firma_sistemas
    );
}
