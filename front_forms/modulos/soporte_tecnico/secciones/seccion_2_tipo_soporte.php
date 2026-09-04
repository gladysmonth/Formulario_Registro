<?php
/**
 * Sección 2: Tipo de Soporte Requerido
 * Formulario de Soporte Técnico
 * Compatible con PHP 7.3
 */
?>
<div class="col-12">
    <div class="tarjeta-formulario p-4">
        <div class="encabezado-seccion d-flex align-items-center">
            <span class="numero-seccion">2</span>
            <div>
                <h2 class="h5 fw-bold mb-0">Tipo de Soporte Requerido</h2>
                <small class="text-muted">Marque lo que corresponda (puede marcar una o ambas casillas)</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="border rounded-3 p-3 bg-light-subtle h-100">
                    <div class="form-check form-switch fs-6">
                        <input class="form-check-input" type="checkbox" role="switch" id="soporte_hardware" name="soporte_hardware" value="1">
                        <label class="form-check-label fw-semibold" for="soporte_hardware">
                            <i class="bi bi-pc-display text-primary me-1"></i> Hardware
                        </label>
                    </div>
                    <p class="text-muted small mb-0 mt-2 ps-4">
                        Equipos de cómputo, monitores, impresoras, teclado, mouse, cableado o periféricos físicos.
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="border rounded-3 p-3 bg-light-subtle h-100">
                    <div class="form-check form-switch fs-6">
                        <input class="form-check-input" type="checkbox" role="switch" id="soporte_software" name="soporte_software" value="1">
                        <label class="form-check-label fw-semibold" for="soporte_software">
                            <i class="bi bi-code-square text-info me-1"></i> Software
                        </label>
                    </div>
                    <p class="text-muted small mb-0 mt-2 ps-4">
                        Aplicaciones, sistema operativo, correo electrónico, accesos, paquetería de oficina o errores de sistema.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
