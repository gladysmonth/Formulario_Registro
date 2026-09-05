<?php
/**
 * Submódulo: Procesamiento de Checklists de Mantenimiento Preventivo
 * Compatible con PHP 7.3
 * Archivo: back_form/api/mantenimiento_preventivo/servicios/servicio_checklist_mp.php
 */

/**
 * Normaliza y extrae las casillas de verificación de las secciones 3, 4 y 5
 * 
 * @param array $datos
 * @return array Casillas convertidas a booleanos estrictos y textos de 'otros'
 */
function procesar_checklist_mantenimiento($datos) {
    return array(
        // Sección 3: Mantenimiento Externo (Limpieza Física)
        'limpieza_carcasa_componentes'       => !empty($datos['limpieza_carcasa_componentes']) ? true : false,
        'limpieza_pantalla_teclado'          => !empty($datos['limpieza_pantalla_teclado']) ? true : false,
        'verificacion_conectores'            => !empty($datos['verificacion_conectores']) ? true : false,
        'limpieza_otros'                     => !empty(trim($datos['limpieza_otros'] ?? '')) ? trim($datos['limpieza_otros']) : null,

        // Sección 4: Mantenimiento Interno (Software / Configuración)
        'actualizacion_so'                   => !empty($datos['actualizacion_so']) ? true : false,
        'eliminacion_temporales'             => !empty($datos['eliminacion_temporales']) ? true : false,
        'desfragmentacion_optimizacion'      => !empty($datos['desfragmentacion_optimizacion']) ? true : false,
        'escaneo_antivirus'                  => !empty($datos['escaneo_antivirus']) ? true : false,
        'verificacion_drivers'               => !empty($datos['verificacion_drivers']) ? true : false,
        'copia_seguridad'                    => !empty($datos['copia_seguridad']) ? true : false,
        'mantenimiento_interno_otros'        => !empty(trim($datos['mantenimiento_interno_otros'] ?? '')) ? trim($datos['mantenimiento_interno_otros']) : null,

        // Sección 5: Verificación de Funcionamiento
        'verificacion_encendido_apagado'     => !empty($datos['verificacion_encendido_apagado']) ? true : false,
        'verificacion_rendimiento'           => !empty($datos['verificacion_rendimiento']) ? true : false,
        'verificacion_red'                   => !empty($datos['verificacion_red']) ? true : false,
        'verificacion_perifericos'           => !empty($datos['verificacion_perifericos']) ? true : false,
        'verificacion_temperatura_anomalias' => !empty($datos['verificacion_temperatura_anomalias']) ? true : false
    );
}
