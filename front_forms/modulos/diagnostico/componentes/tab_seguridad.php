<?php
/**
 * Componente: Pestaña 2 - Auditoría Forense & Software (Antivirus Defender, Prefetch y Descargas)
 * Archivo: front_forms/modulos/diagnostico/componentes/tab_seguridad.php
 */
?>
<div class="tab-pane fade" id="tab-seguridad" role="tabpanel">
    
    <!-- Estado Antivirus Windows Defender -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-success-subtle text-success rounded-3 fs-3" id="iconoAvBox">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="fw-bold mb-0 text-dark" id="txtAvNombre">Windows Defender</h5>
                            <span id="badgeEstadoAv"></span>
                        </div>
                        <div class="text-muted small mt-1" id="txtAvDetalle">Protección activa y definiciones al día.</div>
                    </div>
                </div>
                <div class="text-md-end">
                    <div class="small text-muted">Última actualización de firmas:</div>
                    <div class="fw-semibold text-dark small" id="txtAvFirmasFecha">--</div>
                    <div class="small text-danger fw-bold" id="txtAvAmenazas">0 Amenazas detectadas</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Últimas Apps (Prefetch) -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Aplicaciones Recientes</h5>
                            <small class="text-muted">Auditoría de ejecución (Windows Prefetch / Recent)</small>
                        </div>
                        <span class="badge bg-light text-secondary border">Últimos accesos</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light text-secondary small" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Ejecutable / Aplicación</th>
                                    <th>Última Ejecución</th>
                                    <th class="text-end">Fuente</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpoTablaPrefetch">
                                <tr><td colspan="3" class="text-center py-3 text-muted">Cargando aplicaciones...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Descargas Recientes y Alertas -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-0 text-dark">Descargas Recientes</h5>
                    <small class="text-muted">Inspección de archivos en carpeta Downloads</small>
                </div>
                <div class="card-body p-4">
                    <div class="list-group list-group-flush" id="listaDescargas">
                        <div class="text-muted small text-center py-3">Cargando descargas...</div>
                    </div>
                </div>
            </div>

            <!-- Resumen de Alertas -->
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-0 text-dark">Registro de Alertas</h5>
                    <small class="text-muted">Advertencias generadas durante el escaneo</small>
                </div>
                <div class="card-body p-4" id="contenedorAlertasLog">
                    <div class="text-muted small text-center py-2">Sin alertas activas.</div>
                </div>
            </div>
        </div>
    </div>
</div>
