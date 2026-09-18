<?php
/**
 * Subcomponente: Sección 3 - Datos del Usuario (Asignación de Accesos)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/asignacion_accesos/secciones/seccion_3_datos_usuario.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex align-items-center mb-3">
        <span class="numero-seccion me-2">3</span>
        <div>
            <h5 class="fw-bold mb-0 text-dark">Datos del Usuario</h5>
            <small class="text-muted">Condición de la cuenta y especificaciones de nombre de usuario</small>
        </div>
    </div>

    <div class="row g-3 align-items-center">
        <!-- Switch / Checkbox: Usuario Nuevo -->
        <div class="col-md-4 col-sm-6">
            <div class="p-3 bg-light rounded-3 border">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input fs-5" type="checkbox" role="switch" 
                           id="es_usuario_nuevo" name="es_usuario_nuevo" value="1" checked>
                    <label class="form-check-label fw-bold text-dark ms-2 pt-1" for="es_usuario_nuevo">
                        USUARIO NUEVO
                    </label>
                </div>
                <small class="text-muted d-block ps-4 ms-2 mt-1" id="textoCondicionUsuario">
                    Marque si se trata de una cuenta nueva que aún no existe en los sistemas.
                </small>
            </div>
        </div>

        <!-- Campo de texto: Username / Detalles de la cuenta -->
        <div class="col-md-8 col-sm-6">
            <label for="nombre_usuario_detalles" class="form-label small fw-semibold">
                Username / Nombre de Usuario / Detalles de Cuenta
            </label>
            <div class="input-group">
                <span class="input-group-text bg-white text-secondary"><i class="bi bi-at"></i></span>
                <input type="text" class="form-control" id="nombre_usuario_detalles" name="nombre_usuario_detalles" 
                       placeholder="Ej: jsmith, usuario institucional o dejar en blanco para asignación por Sistemas">
            </div>
            <div class="form-text small text-muted">
                Especifique el username propuesto o existente si se trata de una modificación o ampliación de accesos.
            </div>
        </div>
    </div>
</div>
