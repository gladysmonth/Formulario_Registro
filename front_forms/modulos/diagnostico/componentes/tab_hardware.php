<?php
/**
 * Componente: Pestaña 1 - Hardware & Sensores (CPU, RAM, Discos SMART, Placa y Red)
 * Archivo: front_forms/modulos/diagnostico/componentes/tab_hardware.php
 */
?>
<div class="tab-pane fade show active" id="tab-hardware" role="tabpanel">
    <div class="row g-4 mb-4">
        
        <!-- Tarjeta Procesador (CPU) -->
        <div class="col-md-6 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-primary-subtle text-primary rounded-3">
                            <i class="bi bi-cpu fs-5"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-dark">Procesador (CPU)</h5>
                    </div>
                    <span id="badgeEstadoCpu"></span>
                </div>
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3" id="txtCpuModelo">--</h6>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Carga del Procesador:</span>
                            <span class="fw-bold fs-6" id="txtCpuCargaValor">0%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary" id="progresoCpu" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="row g-3 text-center border-top pt-3">
                        <div class="col-4 border-end">
                            <div class="text-muted small" style="font-size: 0.72rem;">Núcleos Físicos</div>
                            <div class="fs-5 fw-bold text-dark" id="txtCpuNucleos">--</div>
                        </div>
                        <div class="col-4 border-end">
                            <div class="text-muted small" style="font-size: 0.72rem;">Hilos Lógicos</div>
                            <div class="fs-5 fw-bold text-dark" id="txtCpuHilos">--</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small" style="font-size: 0.72rem;">Temperatura</div>
                            <div class="fs-5 fw-bold text-dark" id="txtCpuTemp">N/A</div>
                        </div>
                    </div>
                    
                    <div id="alertaCpuCaja" class="alert alert-warning py-2 px-3 small mt-3 mb-0 d-none"></div>
                </div>
            </div>
        </div>

        <!-- Tarjeta Memoria RAM -->
        <div class="col-md-6 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-info-subtle text-info-emphasis rounded-3">
                            <i class="bi bi-memory fs-5"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-dark">Memoria RAM</h5>
                    </div>
                    <span id="badgeEstadoRam"></span>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <span class="text-muted small">Uso de Memoria Física:</span>
                        <span class="fw-bold fs-6" id="txtRamUsoValor">0%</span>
                    </div>
                    
                    <div class="progress mb-4" style="height: 10px;">
                        <div class="progress-bar bg-info" id="progresoRam" style="width: 0%"></div>
                    </div>

                    <div class="row g-3 text-center border-top pt-3">
                        <div class="col-3 border-end">
                            <div class="text-muted small" style="font-size: 0.72rem;">Total</div>
                            <div class="fs-5 fw-bold text-dark" id="txtRamTotal">--</div>
                        </div>
                        <div class="col-3 border-end">
                            <div class="text-muted small" style="font-size: 0.72rem;">En Uso</div>
                            <div class="fs-5 fw-bold text-primary" id="txtRamUsado">--</div>
                        </div>
                        <div class="col-3 border-end">
                            <div class="text-muted small" style="font-size: 0.72rem;">Libre</div>
                            <div class="fs-5 fw-bold text-success" id="txtRamLibre">--</div>
                        </div>
                        <div class="col-3">
                            <div class="text-muted small" style="font-size: 0.72rem;">Velocidad</div>
                            <div class="fs-5 fw-bold text-secondary" id="txtRamVelocidad">--</div>
                        </div>
                    </div>

                    <div id="alertaRamCaja" class="alert alert-warning py-2 px-3 small mt-3 mb-0 d-none"></div>
                </div>
            </div>
        </div>

    </div>

    <!-- Unidades de Almacenamiento y Salud SMART -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <div class="d-flex align-items-center gap-2">
                <div class="p-2 bg-secondary-subtle text-dark rounded-3">
                    <i class="bi bi-hdd-stack fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Almacenamiento y Salud de Discos (SMART)</h5>
                    <small class="text-muted">Particiones lógicas y estado físico preventivo</small>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-3" id="contenedorDiscos">
                <!-- Inyectado dinámicamente por detalle_equipo.js -->
            </div>
        </div>
    </div>

    <!-- Tarjeta de Placa Madre y Red -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-motherboard text-primary fs-5"></i>
                    <h6 class="fw-bold mb-0 text-dark">Placa Base & BIOS</h6>
                </div>
                <div class="small text-muted mb-2">Fabricante: <strong class="text-dark" id="txtPlacaFab">--</strong></div>
                <div class="small text-muted mb-2">Modelo de Tarjeta: <strong class="text-dark" id="txtPlacaMod">--</strong></div>
                <div class="small text-muted">Número de Serie: <strong class="text-dark" id="txtPlacaSer">--</strong></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-ethernet text-success fs-5"></i>
                        <h6 class="fw-bold mb-0 text-dark">Adaptador de Red</h6>
                    </div>
                    <span id="badgeEstadoRed"></span>
                </div>
                <div class="small text-muted mb-2">Controlador: <strong class="text-dark" id="txtRedNombre">--</strong></div>
                <div class="small text-muted mb-2">Puerta de Enlace (Gateway): <strong class="text-dark" id="txtRedGateway">--</strong></div>
                <div class="small text-muted">Servidores DNS: <span id="txtRedDns">--</span></div>
            </div>
        </div>
    </div>
</div>
