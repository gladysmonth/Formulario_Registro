<?php
/**
 * Componente: Pestaña 3 - Historial Cronológico de Telemetría
 * Archivo: front_forms/modulos/diagnostico/componentes/tab_historial.php
 */
?>
<div class="tab-pane fade" id="tab-historial" role="tabpanel">
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Historial de Reportes Cronológicos</h5>
                <small class="text-muted">Evolución de salud, CPU, RAM y almacenamiento en el tiempo</small>
            </div>
            <span class="badge bg-secondary-subtle text-secondary border">Últimos 20 snapshots</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary small text-uppercase" style="font-size: 0.72rem;">
                    <tr>
                        <th>ID</th>
                        <th>Fecha y Hora</th>
                        <th>Tipo Evento</th>
                        <th>Estado Salud</th>
                        <th>Carga CPU</th>
                        <th>Uso RAM</th>
                        <th>Espacio C:</th>
                        <th>SMART</th>
                    </tr>
                </thead>
                <tbody id="cuerpoTablaHistorial">
                    <tr><td colspan="8" class="text-center py-4 text-muted">Cargando historial...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
