<?php
/**
 * Componente Métricas: Banner de Alerta Temprana y Estado del Sistema
 * Archivo: front_forms/componentes_metricas/bloque_alerta_temprana.php
 */
?>
<div class="mb-4" id="contenedorBannerAlerta">
    <div class="alert alert-success border-0 shadow-sm rounded-3 py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2 mb-0" id="bannerAlertaDashboard">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill fs-5 text-success" id="iconoBannerAlerta"></i>
            <span class="small" id="textoBannerAlerta">
                <strong>Sistemas Institucionales Estables:</strong> No se registran incidencias críticas ni alertas de hardware pendientes en este momento.
            </span>
        </div>
        <div class="d-flex align-items-center gap-2" id="accionesBannerAlerta">
            <span class="badge bg-success bg-opacity-25 text-success small px-2 py-1" id="badgeEstadoGeneral">Operativo</span>
            <a href="#" class="btn btn-danger btn-sm px-3 py-1 fw-semibold d-none align-items-center gap-1 shadow-sm" id="btnAccionAlerta">
                <span>Ir a atender</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
