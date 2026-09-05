<?php
/**
 * Componente: Tabla de Registros de Tickets
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/soporte_tecnico/componentes_gestion/panel_tabla.php
 */
?>
<!-- Tabla de Registros -->
<div class="tarjeta-formulario overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-secondary small">
                <tr>
                    <th scope="col" style="width: 140px;">CÓDIGO</th>
                    <th scope="col">SOLICITANTE / ÁREA</th>
                    <th scope="col" style="width: 120px;">FECHA</th>
                    <th scope="col" style="width: 150px;">TIPO</th>
                    <th scope="col" style="width: 110px;">PRIORIDAD</th>
                    <th scope="col" style="width: 120px;">ESTADO</th>
                    <th scope="col">TÉCNICO</th>
                    <th scope="col" class="text-end" style="width: 100px;">ACCIÓN</th>
                </tr>
            </thead>
            <tbody id="cuerpoTablaTickets">
                <!-- Los tickets se cargan dinámicamente vía JavaScript (gestion_tickets.js) -->
            </tbody>
        </table>
    </div>
</div>
