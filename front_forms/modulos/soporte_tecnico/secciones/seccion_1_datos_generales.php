<?php
/**
 * Sección 1: Datos Generales
 * Formulario de Soporte Técnico
 * Compatible con PHP 7.3
 */
?>
<div class="col-12">
    <div class="tarjeta-formulario p-4">
        <div class="encabezado-seccion d-flex align-items-center">
            <span class="numero-seccion">1</span>
            <div>
                <h2 class="h5 fw-bold mb-0">Datos Generales</h2>
                <small class="text-muted">Información básica del solicitante y área de origen</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-5">
                <label for="nombre_solicitante" class="form-label fw-semibold">
                    Nombre del Solicitante <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-secondary"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" id="nombre_solicitante" name="nombre_solicitante" placeholder="Ej: Juan Pérez Martínez" required>
                </div>
            </div>

            <div class="col-md-3">
                <label for="fecha_solicitud" class="form-label fw-semibold">
                    Fecha de Registro <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-secondary"><i class="bi bi-calendar3"></i></span>
                    <input type="date" class="form-control" id="fecha_solicitud" name="fecha_solicitud" required>
                </div>
            </div>

            <div class="col-md-4">
                <label for="departamento_area" class="form-label fw-semibold">
                    Departamento / Área <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-secondary"><i class="bi bi-building"></i></span>
                    <input type="text" class="form-control" id="departamento_area" name="departamento_area" list="listaDepartamentos" placeholder="Seleccione o escriba..." required>
                </div>
                <datalist id="listaDepartamentos">
                    <option value="Administración">
                    <option value="Contabilidad y Finanzas">
                    <option value="Recursos Humanos">
                    <option value="Sistemas / TI">
                    <option value="Comercial y Ventas">
                    <option value="Operaciones y Logística">
                    <option value="Atención al Cliente">
                    <option value="Gerencia General">
                </datalist>
            </div>
        </div>
    </div>
</div>
