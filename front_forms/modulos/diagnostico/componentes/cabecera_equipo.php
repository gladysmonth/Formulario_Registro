<?php
/**
 * Componente: Cabecera y Alertas de Ficha Técnica de Equipo
 * Archivo: front_forms/modulos/diagnostico/componentes/cabecera_equipo.php
 */
?>
<div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden border-top border-4 border-primary">
    <div class="card-body p-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-primary-subtle text-primary rounded-4 fs-2">
                        <i class="bi bi-display"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h2 class="h3 fw-bold text-dark mb-0" id="txtHostname">--</h2>
                            <span id="badgeSaludGlobal"></span>
                            <span class="badge bg-light text-secondary border rounded-pill" id="txtTipoEvento">Programado</span>
                        </div>
                        <div class="text-muted small mt-1">
                            <span class="me-3"><i class="bi bi-person-badge me-1"></i><strong class="text-dark" id="txtUsuario">--</strong></span>
                            <span class="me-3"><i class="bi bi-hdd-network me-1"></i>IP: <span id="txtIpLocal">--</span></span>
                            <span><i class="bi bi-upc-scan me-1"></i>MAC: <code class="text-dark" id="txtMac">--</code></span>
                        </div>
                        <div class="text-secondary small mt-1" style="font-size: 0.75rem;">
                            <i class="bi bi-motherboard me-1"></i>Placa / Modelo: <span id="txtPlacaModelo">--</span>
                            &bull; Serial: <span id="txtSerial">--</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="text-muted small">Último reporte recibido:</div>
                <div class="fw-bold text-dark fs-6" id="txtFechaReporte">--</div>
                <div class="text-muted small" style="font-size: 0.72rem;" id="txtFechaRelativa">--</div>
            </div>
        </div>
    </div>

    <!-- Alertas Activas en Cabecera (si las hay) -->
    <div id="alertaBanner" class="p-3 bg-warning-subtle border-top border-warning-subtle d-none">
        <div class="d-flex align-items-center gap-2 text-warning-emphasis small fw-medium">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div id="alertaBannerTexto">Se han detectado advertencias en este equipo.</div>
        </div>
    </div>
</div>
