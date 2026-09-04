<?php
/**
 * Sección 3: Detalle del Problema / Requerimiento
 * Formulario de Soporte Técnico
 * Compatible con PHP 7.3
 */
?>
<div class="col-12">
    <div class="tarjeta-formulario p-4">
        <div class="encabezado-seccion d-flex align-items-center">
            <span class="numero-seccion">3</span>
            <div>
                <h2 class="h5 fw-bold mb-0">Detalle del Problema / Requerimiento</h2>
                <small class="text-muted">Describa la falla y detalle el equipo afectado si aplica</small>
            </div>
        </div>

        <div class="mb-4">
            <label for="descripcion_problema" class="form-label fw-semibold">
                Descripción Detallada del Problema <span class="text-danger">*</span>
            </label>
            <textarea class="form-control" id="descripcion_problema" name="descripcion_problema" rows="4" placeholder="Indique qué ocurre, mensaje de error si existe, y cuándo comenzó la falla..." required></textarea>
            <div class="form-text">Sea lo más específico posible para agilizar el diagnóstico.</div>
        </div>

        <!-- Sub-bloque: Equipo Afectado (Si aplica) -->
        <div class="bloque-equipo-afectado">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-laptop text-secondary fs-5"></i>
                <h3 class="h6 fw-bold mb-0 text-dark">Equipo Afectado <span class="fw-normal text-muted small">(Opcional / Si aplica)</span></h3>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="numero_serie" class="form-label small fw-medium">Número de Serie / Inventario</label>
                    <input type="text" class="form-control form-control-sm" id="numero_serie" name="numero_serie" placeholder="Ej: SN-49204 / INV-004">
                </div>
                <div class="col-md-4">
                    <label for="marca_modelo" class="form-label small fw-medium">Marca / Modelo</label>
                    <input type="text" class="form-control form-control-sm" id="marca_modelo" name="marca_modelo" placeholder="Ej: Dell OptiPlex 7080 / HP ProBook">
                </div>
                <div class="col-md-4">
                    <label for="sistema_operativo" class="form-label small fw-medium">Sistema Operativo</label>
                    <input type="text" class="form-control form-control-sm" id="sistema_operativo" name="sistema_operativo" list="listaSistemas" placeholder="Ej: Windows 11 Pro">
                    <datalist id="listaSistemas">
                        <option value="Windows 11 Pro">
                        <option value="Windows 10 Pro">
                        <option value="Windows Server 2022">
                        <option value="macOS Sonoma">
                        <option value="Ubuntu Linux 22.04">
                    </datalist>
                </div>
            </div>
        </div>
    </div>
</div>
