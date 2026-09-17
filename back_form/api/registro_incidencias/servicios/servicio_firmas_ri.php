<?php
/**
 * Submódulo: Validación y Procesamiento de Firmas Digitales para Incidencias
 * Compatible con PHP 7.3
 * Archivo: back_form/api/registro_incidencias/servicios/servicio_firmas_ri.php
 */

/**
 * Valida las firmas digitales del Responsable del Reporte y del Dpto. de Sistemas
 *
 * @param array $datos
 * @param string $estado
 * @return array ['valido' => bool, 'errores' => array, 'firma_responsable' => string|null, 'firma_sistemas' => string|null]
 */
function validar_firmas_incidencia($datos, $estado = 'abierta') {
    $errores = array();

    $firma_responsable = !empty($datos['firma_responsable_reporte']) ? $datos['firma_responsable_reporte'] : null;
    $firma_sistemas    = !empty($datos['firma_sistemas']) ? $datos['firma_sistemas'] : null;

    // Validación de firma del responsable del reporte
    if (!empty($firma_responsable)) {
        if (strpos($firma_responsable, 'data:image/') !== 0) {
            $errores[] = 'El formato de la firma del Responsable del Reporte no corresponde a una imagen digital válida.';
        }
    }

    // Validación de firma de sistemas
    if (!empty($firma_sistemas)) {
        if (strpos($firma_sistemas, 'data:image/') !== 0) {
            $errores[] = 'El formato de la firma del Dpto. de Sistemas no corresponde a una imagen digital válida.';
        }
    }

    // Si el estado es 'resuelta' o 'cerrada', la firma de sistemas es obligatoria para certificar el cierre
    if (($estado === 'resuelta' || $estado === 'cerrada') && empty($firma_sistemas)) {
        $errores[] = 'La firma digital del Dpto. de Sistemas es obligatoria para certificar la resolución y cierre de la incidencia.';
    }

    return array(
        'valido'            => empty($errores),
        'errores'           => $errores,
        'firma_responsable' => $firma_responsable,
        'firma_sistemas'    => $firma_sistemas
    );
}
