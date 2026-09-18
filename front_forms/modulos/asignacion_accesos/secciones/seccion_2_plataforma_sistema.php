<?php
/**
 * Subcomponente: Sección 2 - Plataforma / Sistema (Asignación de Accesos)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/asignacion_accesos/secciones/seccion_2_plataforma_sistema.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex align-items-center mb-3">
        <span class="numero-seccion me-2">2</span>
        <div>
            <h5 class="fw-bold mb-0 text-dark">Plataforma / Sistema</h5>
            <small class="text-muted">Marque los sistemas institucionales donde se requiere crear o asignar acceso</small>
        </div>
    </div>

    <div class="row g-3">
        <!-- SISTEMA ERP - SAI -->
        <div class="col-md-4 col-sm-6">
            <div class="card h-100 border p-3 shadow-none hover-shadow tarjeta-opcion">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input check-plataforma fs-5" type="checkbox" role="switch" 
                           id="sistema_erp_sai" name="sistema_erp_sai" value="1">
                    <label class="form-check-label fw-bold text-dark ms-2 pt-1" for="sistema_erp_sai">
                        SISTEMA ERP - SAI
                    </label>
                </div>
                <small class="text-muted d-block ps-4 ms-2">
                    Módulos contables, presupuestos, almacenes, facturación y operaciones institucionales.
                </small>
            </div>
        </div>

        <!-- SISTEMA DE COBRANZAS - NETCOB -->
        <div class="col-md-4 col-sm-6">
            <div class="card h-100 border p-3 shadow-none hover-shadow tarjeta-opcion">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input check-plataforma fs-5" type="checkbox" role="switch" 
                           id="sistema_cobranzas_netcob" name="sistema_cobranzas_netcob" value="1">
                    <label class="form-check-label fw-bold text-dark ms-2 pt-1" for="sistema_cobranzas_netcob">
                        SISTEMA DE COBRANZAS - NETCOB
                    </label>
                </div>
                <small class="text-muted d-block ps-4 ms-2">
                    Cajas, recaudación en ventanilla, liquidaciones, cobro de servicios y consultas de usuarios.
                </small>
            </div>
        </div>

        <!-- OTROS SISTEMAS / COMPLEMENTOS -->
        <div class="col-md-4 col-sm-12">
            <div class="card h-100 border p-3 shadow-none hover-shadow tarjeta-opcion">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input check-plataforma fs-5" type="checkbox" role="switch" 
                           id="sistema_otros" name="sistema_otros" value="1">
                    <label class="form-check-label fw-bold text-dark ms-2 pt-1" for="sistema_otros">
                        OTROS SISTEMAS / COMPLEMENTOS
                    </label>
                </div>
                <div id="bloqueOtrosSistemas" class="d-none mt-2 ps-4 ms-2">
                    <label for="sistema_otros_detalle" class="form-label small fw-semibold text-muted">
                        Especificar sistema o aplicativo:
                    </label>
                    <input type="text" class="form-control form-control-sm" id="sistema_otros_detalle" 
                           name="sistema_otros_detalle" placeholder="Ej: Correo, Base de Datos, Servidor">
                </div>
                <small class="text-muted d-block ps-4 ms-2" id="notaOtrosSistemas">
                    Herramientas periféricas, correo institucional, servidores o plataformas específicas.
                </small>
            </div>
        </div>
    </div>
</div>
