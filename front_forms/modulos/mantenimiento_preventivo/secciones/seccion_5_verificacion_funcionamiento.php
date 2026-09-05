<?php
/**
 * Subcomponente: Sección 5 - Verificación de Funcionamiento Post-Mantenimiento
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/mantenimiento_preventivo/secciones/seccion_5_verificacion_funcionamiento.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex align-items-center mb-3">
        <span class="numero-seccion me-2">5</span>
        <div>
            <h5 class="fw-bold mb-0 text-dark">Verificación de Funcionamiento</h5>
            <small class="text-muted">Pruebas operativas finales para certificar el estado óptimo del equipo</small>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4 col-sm-6">
            <div class="form-check p-3 bg-light rounded-3 border h-100">
                <input class="form-check-input ms-0 me-2" type="checkbox" id="verificacion_encendido_apagado" name="verificacion_encendido_apagado">
                <label class="form-check-label fw-semibold small text-dark" for="verificacion_encendido_apagado">
                    <i class="bi bi-power text-warning me-1"></i> Encendido / Apagado correcto
                </label>
                <div class="text-muted small mt-1 ps-4">Arranque sin pitidos de BIOS, reinicio y apagado limpio.</div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="form-check p-3 bg-light rounded-3 border h-100">
                <input class="form-check-input ms-0 me-2" type="checkbox" id="verificacion_rendimiento" name="verificacion_rendimiento">
                <label class="form-check-label fw-semibold small text-dark" for="verificacion_rendimiento">
                    <i class="bi bi-speedometer2 text-warning me-1"></i> Rendimiento general fluido
                </label>
                <div class="text-muted small mt-1 ps-4">Apertura ágil de aplicaciones sin congelamiento ni lentitud.</div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="form-check p-3 bg-light rounded-3 border h-100">
                <input class="form-check-input ms-0 me-2" type="checkbox" id="verificacion_red" name="verificacion_red">
                <label class="form-check-label fw-semibold small text-dark" for="verificacion_red">
                    <i class="bi bi-globe text-warning me-1"></i> Conectividad Wi-Fi / Red funcional
                </label>
                <div class="text-muted small mt-1 ps-4">Navegación en red corporativa y acceso a servidores.</div>
            </div>
        </div>

        <div class="col-md-6 col-sm-6">
            <div class="form-check p-3 bg-light rounded-3 border h-100">
                <input class="form-check-input ms-0 me-2" type="checkbox" id="verificacion_perifericos" name="verificacion_perifericos">
                <label class="form-check-label fw-semibold small text-dark" for="verificacion_perifericos">
                    <i class="bi bi-mouse text-warning me-1"></i> Periféricos funcionales (Mouse, Teclado, Audio)
                </label>
                <div class="text-muted small mt-1 ps-4">Prueba de respuesta de teclas, touchpad/mouse y sonido.</div>
            </div>
        </div>

        <div class="col-md-6 col-sm-12">
            <div class="form-check p-3 bg-light rounded-3 border h-100">
                <input class="form-check-input ms-0 me-2" type="checkbox" id="verificacion_temperatura_anomalias" name="verificacion_temperatura_anomalias">
                <label class="form-check-label fw-semibold small text-dark" for="verificacion_temperatura_anomalias">
                    <i class="bi bi-thermometer-half text-warning me-1"></i> Temperatura normal (sin anomalías)
                </label>
                <div class="text-muted small mt-1 ps-4">Ausencia de sobrecalentamiento excesivo o ruido inusual de ventilador.</div>
            </div>
        </div>
    </div>
</div>
