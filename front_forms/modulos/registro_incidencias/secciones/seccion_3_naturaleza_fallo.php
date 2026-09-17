<?php
/**
 * Subcomponente: Sección 3 - Naturaleza Técnica del Fallo (FOR_RIS_001, V-1)
 * Funcionalidad: Marcar y rellenar al marcado
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/registro_incidencias/secciones/seccion_3_naturaleza_fallo.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex align-items-center mb-3">
        <span class="numero-seccion me-2">3</span>
        <div>
            <h5 class="fw-bold mb-0 text-dark">Naturaleza Técnica del Fallo</h5>
            <small class="text-muted">¿Dónde se originó el problema? Marque el componente afectado y especifique el tipo de evento o error</small>
        </div>
    </div>

    <div class="table-responsive rounded-3 border">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-secondary text-uppercase">
                    <th style="width: 35%;">Componente Afectado</th>
                    <th style="width: 15%; text-align: center;">Estado</th>
                    <th style="width: 50%;">Descripción / Tipo de Error Común</th>
                </tr>
            </thead>
            <tbody>
                <!-- 1. BASE DE DATOS -->
                <tr id="fila_base_datos">
                    <td>
                        <div class="form-check form-switch d-flex align-items-center m-0">
                            <input class="form-check-input check-naturaleza fs-5 me-3" type="checkbox" role="switch" 
                                   id="fallo_base_datos" name="fallo_base_datos" value="1" 
                                   data-target-input="detalle_base_datos" data-target-badge="badge_base_datos">
                            <label class="form-check-label fw-bold text-dark cursor-pointer" for="fallo_base_datos">
                                <i class="bi bi-database text-primary fs-5 me-2"></i> BASE DE DATOS
                                <small class="text-muted d-block fw-normal">Bloqueos, timeout, corrupción, réplica</small>
                            </label>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary-subtle text-muted border" id="badge_base_datos">Sin Falla</span>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm input-naturaleza" 
                               id="detalle_base_datos" name="detalle_base_datos" 
                               placeholder="Marque la casilla para ingresar el tipo de error en BD" disabled>
                    </td>
                </tr>

                <!-- 2. INFRAESTRUCTURA / SERVIDOR -->
                <tr id="fila_servidor">
                    <td>
                        <div class="form-check form-switch d-flex align-items-center m-0">
                            <input class="form-check-input check-naturaleza fs-5 me-3" type="checkbox" role="switch" 
                                   id="fallo_infraestructura_servidor" name="fallo_infraestructura_servidor" value="1" 
                                   data-target-input="detalle_infraestructura_servidor" data-target-badge="badge_servidor">
                            <label class="form-check-label fw-bold text-dark cursor-pointer" for="fallo_infraestructura_servidor">
                                <i class="bi bi-server text-info fs-5 me-2"></i> INFRAESTRUCTURA / SERVIDOR
                                <small class="text-muted d-block fw-normal">Consumo CPU/RAM, disco lleno, caída de servicio</small>
                            </label>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary-subtle text-muted border" id="badge_servidor">Sin Falla</span>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm input-naturaleza" 
                               id="detalle_infraestructura_servidor" name="detalle_infraestructura_servidor" 
                               placeholder="Marque la casilla para ingresar el tipo de error en servidor" disabled>
                    </td>
                </tr>

                <!-- 3. ENLACES / CONECTIVIDAD -->
                <tr id="fila_conectividad">
                    <td>
                        <div class="form-check form-switch d-flex align-items-center m-0">
                            <input class="form-check-input check-naturaleza fs-5 me-3" type="checkbox" role="switch" 
                                   id="fallo_enlaces_conectividad" name="fallo_enlaces_conectividad" value="1" 
                                   data-target-input="detalle_enlaces_conectividad" data-target-badge="badge_conectividad">
                            <label class="form-check-label fw-bold text-dark cursor-pointer" for="fallo_enlaces_conectividad">
                                <i class="bi bi-router text-warning fs-5 me-2"></i> ENLACES / CONECTIVIDAD
                                <small class="text-muted d-block fw-normal">Caída WAN/LAN, latencia alta, fallo VPN o switch</small>
                            </label>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary-subtle text-muted border" id="badge_conectividad">Sin Falla</span>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm input-naturaleza" 
                               id="detalle_enlaces_conectividad" name="detalle_enlaces_conectividad" 
                               placeholder="Marque la casilla para ingresar el tipo de error en conectividad" disabled>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
