<?php
/**
 * Subcomponente: Sección 2 - Plataforma / Sistema (FOR_RIS_001, V-1)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/registro_incidencias/secciones/seccion_2_plataforma_sistema.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex align-items-center mb-3">
        <span class="numero-seccion me-2">2</span>
        <div>
            <h5 class="fw-bold mb-0 text-dark">Plataforma / Sistema Afectado</h5>
            <small class="text-muted">Marque el sistema o plataforma institucional donde se presenta el incidente</small>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <!-- SISTEMA ERP - SAI -->
        <div class="col-md-4">
            <div class="card h-100 p-3 border rounded-3 bg-white shadow-none tarjeta-seleccion" id="card_erp_sai">
                <div class="form-check form-switch m-0 d-flex align-items-center justify-content-between">
                    <div>
                        <label class="form-check-label fw-bold text-dark d-block cursor-pointer" for="sistema_erp_sai">
                            <i class="bi bi-hdd-network text-primary me-2"></i> SISTEMA ERP - SAI
                        </label>
                        <small class="text-muted d-block">Módulos contables, almacén, comercial</small>
                    </div>
                    <input class="form-check-input ms-2 fs-5" type="checkbox" role="switch" 
                           id="sistema_erp_sai" name="sistema_erp_sai" value="1">
                </div>
            </div>
        </div>

        <!-- SISTEMA DE COBRANZAS - NETCOB -->
        <div class="col-md-4">
            <div class="card h-100 p-3 border rounded-3 bg-white shadow-none tarjeta-seleccion" id="card_netcob">
                <div class="form-check form-switch m-0 d-flex align-items-center justify-content-between">
                    <div>
                        <label class="form-check-label fw-bold text-dark d-block cursor-pointer" for="sistema_cobranzas_netcob">
                            <i class="bi bi-cash-coin text-success me-2"></i> SISTEMA DE COBRANZAS - NETCOB
                        </label>
                        <small class="text-muted d-block">Cajas, recaudación y cobro de servicios</small>
                    </div>
                    <input class="form-check-input ms-2 fs-5" type="checkbox" role="switch" 
                           id="sistema_cobranzas_netcob" name="sistema_cobranzas_netcob" value="1">
                </div>
            </div>
        </div>

        <!-- OTROS SISTEMAS / COMPLEMENTOS -->
        <div class="col-md-4">
            <div class="card h-100 p-3 border rounded-3 bg-white shadow-none tarjeta-seleccion" id="card_otros_sistemas">
                <div class="form-check form-switch m-0 d-flex align-items-center justify-content-between">
                    <div>
                        <label class="form-check-label fw-bold text-dark d-block cursor-pointer" for="sistema_otros">
                            <i class="bi bi-puzzle text-warning me-2"></i> OTROS SISTEMAS
                        </label>
                        <small class="text-muted d-block">Módulos complementarios o web</small>
                    </div>
                    <input class="form-check-input ms-2 fs-5" type="checkbox" role="switch" 
                           id="sistema_otros" name="sistema_otros" value="1">
                </div>
            </div>
        </div>
    </div>

    <!-- Campo desplegable para especificar Otros Sistemas -->
    <div id="contenedor_otros_sistemas_detalle" class="d-none mt-2 p-3 bg-light rounded-3 border border-dashed">
        <label for="sistema_otros_detalle" class="form-label small fw-semibold text-dark">
            <i class="bi bi-info-circle me-1 text-primary"></i> Especificar otros sistemas o complementos afectados:
        </label>
        <input type="text" class="form-control bg-white" id="sistema_otros_detalle" name="sistema_otros_detalle" 
               placeholder="Ej: Sistema de Facturación Computarizada, Portal Web, etc.">
    </div>
</div>
