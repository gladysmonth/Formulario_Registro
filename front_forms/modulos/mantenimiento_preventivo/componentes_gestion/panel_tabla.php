<?php
/**
 * Componente: Tabla de Registros de Mantenimiento Preventivo
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/mantenimiento_preventivo/componentes_gestion/panel_tabla.php
 */
?>
<!-- Tabla de Registros -->
<div class="tarjeta-formulario overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-secondary small">
                <tr>
                    <th scope="col" style="width: 140px;">CÓDIGO</th>
                    <th scope="col" style="width: 120px;">FECHA</th>
                    <th scope="col">TÉCNICO</th>
                    <th scope="col">UBICACIÓN</th>
                    <th scope="col" style="width: 110px;">TIPO</th>
                    <th scope="col">EQUIPO / ACTIVO</th>
                    <th scope="col" style="width: 150px;">RED / S.O.</th>
                    <th scope="col" class="text-end" style="width: 120px;">ACCIÓN</th>
                </tr>
            </thead>
            <tbody id="cuerpoTablaMP">
                <!-- Los registros se cargan dinámicamente vía JavaScript (gestion_mantenimientos.js) -->
            </tbody>
        </table>
    </div>
</div>
