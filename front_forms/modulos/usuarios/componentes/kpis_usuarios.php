<?php
/**
 * Componente: Tarjetas KPI de Usuarios (Ultra Adaptable Móvil)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/usuarios/componentes/kpis_usuarios.php
 */
?>
<div class="row g-2 g-md-3 mb-3 mb-md-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100 p-2 p-sm-3">
            <div class="d-flex align-items-center justify-content-between gap-1">
                <div class="pe-1 text-truncate">
                    <span class="text-muted text-uppercase fw-semibold d-block text-truncate" style="font-size: 0.68rem; letter-spacing: 0.3px;">Total Usuarios</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1 fs-5 fs-sm-4" id="kpiTotalUsuarios">-</h3>
                </div>
                <div class="caja-icono-kpi bg-primary-subtle text-primary flex-shrink-0">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100 p-2 p-sm-3">
            <div class="d-flex align-items-center justify-content-between gap-1">
                <div class="pe-1 text-truncate">
                    <span class="text-muted text-uppercase fw-semibold d-block text-truncate" style="font-size: 0.68rem; letter-spacing: 0.3px;">Administradores</span>
                    <h3 class="fw-bold mb-0 text-danger mt-1 fs-5 fs-sm-4" id="kpiAdmins">-</h3>
                </div>
                <div class="caja-icono-kpi bg-danger-subtle text-danger flex-shrink-0">
                    <i class="bi bi-shield-shaded"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100 p-2 p-sm-3">
            <div class="d-flex align-items-center justify-content-between gap-1">
                <div class="pe-1 text-truncate">
                    <span class="text-muted text-uppercase fw-semibold d-block text-truncate" style="font-size: 0.68rem; letter-spacing: 0.3px;">Técnicos</span>
                    <h3 class="fw-bold mb-0 text-info mt-1 fs-5 fs-sm-4" id="kpiTecnicos">-</h3>
                </div>
                <div class="caja-icono-kpi bg-info-subtle text-info flex-shrink-0">
                    <i class="bi bi-tools"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100 p-2 p-sm-3">
            <div class="d-flex align-items-center justify-content-between gap-1">
                <div class="pe-1 text-truncate">
                    <span class="text-muted text-uppercase fw-semibold d-block text-truncate" style="font-size: 0.68rem; letter-spacing: 0.3px;">Solicitantes</span>
                    <h3 class="fw-bold mb-0 text-secondary mt-1 fs-5 fs-sm-4" id="kpiSolicitantes">-</h3>
                </div>
                <div class="caja-icono-kpi bg-secondary-subtle text-secondary flex-shrink-0">
                    <i class="bi bi-person"></i>
                </div>
            </div>
        </div>
    </div>
</div>
