<?php
/**
 * Subcomponente: Sección 4 - Nivel de Criticidad Institucional (FOR_RIS_001, V-1)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/registro_incidencias/secciones/seccion_4_nivel_criticidad.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex align-items-center mb-3">
        <span class="numero-seccion me-2">4</span>
        <div>
            <h5 class="fw-bold mb-0 text-dark">Nivel de Criticidad Institucional</h5>
            <small class="text-muted">A definir por el Departamento de Sistemas según el impacto operativo institucional</small>
        </div>
    </div>

    <div class="row g-3">
        <!-- CRÍTICA (Nivel 1) -->
        <div class="col-md-4">
            <label class="card h-100 p-3 border-2 border-danger-subtle rounded-3 cursor-pointer tarjeta-criticidad" 
                   id="card_criticidad_1" for="critica_nivel_1">
                <div class="d-flex align-items-start">
                    <input class="form-check-input mt-1 me-2 radio-criticidad" type="radio" 
                           name="nivel_criticidad" id="critica_nivel_1" value="critica_nivel_1" required>
                    <div>
                        <div class="d-flex align-items-center mb-1">
                            <span class="badge bg-danger text-white me-2">Nivel 1</span>
                            <span class="fw-bold text-danger">CRÍTICA</span>
                        </div>
                        <p class="small text-muted mb-0">
                            <strong>Paralización del ciclo de facturación.</strong> Afecta directamente la recaudación, cajas o la atención a usuarios y público en general.
                        </p>
                    </div>
                </div>
            </label>
        </div>

        <!-- ALTA (Nivel 2) -->
        <div class="col-md-4">
            <label class="card h-100 p-3 border-2 border-warning-subtle rounded-3 cursor-pointer tarjeta-criticidad" 
                   id="card_criticidad_2" for="alta_nivel_2">
                <div class="d-flex align-items-start">
                    <input class="form-check-input mt-1 me-2 radio-criticidad" type="radio" 
                           name="nivel_criticidad" id="alta_nivel_2" value="alta_nivel_2">
                    <div>
                        <div class="d-flex align-items-center mb-1">
                            <span class="badge bg-warning text-dark me-2">Nivel 2</span>
                            <span class="fw-bold text-dark">ALTA</span>
                        </div>
                        <p class="small text-muted mb-0">
                            <strong>Falla en un módulo o programa importante.</strong> Se generan trabas en procesos clave pero la operatividad institucional continúa.
                        </p>
                    </div>
                </div>
            </label>
        </div>

        <!-- MEDIA / BAJA (Nivel 3) -->
        <div class="col-md-4">
            <label class="card h-100 p-3 border-2 border-primary-subtle rounded-3 cursor-pointer tarjeta-criticidad" 
                   id="card_criticidad_3" for="media_baja_nivel_3">
                <div class="d-flex align-items-start">
                    <input class="form-check-input mt-1 me-2 radio-criticidad" type="radio" 
                           name="nivel_criticidad" id="media_baja_nivel_3" value="media_baja_nivel_3">
                    <div>
                        <div class="d-flex align-items-center mb-1">
                            <span class="badge bg-primary text-white me-2">Nivel 3</span>
                            <span class="fw-bold text-primary">MEDIA / BAJA</span>
                        </div>
                        <p class="small text-muted mb-0">
                            <strong>Error estético o lentitud intermitente.</strong> Solicitudes de cambio menores, reportes auxiliares o incidencias de baja repercusión.
                        </p>
                    </div>
                </div>
            </label>
        </div>
    </div>
</div>
