<?php
/**
 * Sección 3: Detalle del Problema / Requerimiento y Equipo Afectado
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

        <!-- Sub-bloque: Equipo Afectado (Con Catálogo e Inventario) -->
        <div class="bloque-equipo-afectado">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-laptop text-primary fs-5"></i>
                    <h3 class="h6 fw-bold mb-0 text-dark">Equipo Afectado <span class="fw-normal text-muted small">(Inventario y Registro)</span></h3>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" id="btnLimpiarEquipo" class="btn btn-outline-secondary btn-sm py-0 px-2 d-none" title="Limpiar selección para registrar nuevo">
                        <i class="bi bi-x-circle me-1"></i> Desvincular equipo
                    </button>
                    <span id="badgeEstadoEquipo" class="badge bg-secondary-subtle text-secondary border">Equipo nuevo</span>
                </div>
            </div>

            <!-- Buscador de Equipos Guardados -->
            <div class="mb-3">
                <label for="buscador_equipo" class="form-label small fw-semibold text-primary">
                    <i class="bi bi-search me-1"></i> Buscar equipo guardado en inventario
                </label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-upc-scan"></i></span>
                    <input type="text" class="form-control" id="buscador_equipo" list="listaEquiposRegistrados" placeholder="Escriba el código de activo, serie, marca o modelo para buscar y autocompletar...">
                    <datalist id="listaEquiposRegistrados">
                        <!-- Opciones cargadas dinámicamente desde la BD -->
                    </datalist>
                </div>
                <div class="form-text small">Si el equipo ya fue registrado anteriormente, selecciónelo aquí para autocompletar sus datos.</div>
            </div>

            <!-- ID oculto si proviene de catálogo existente -->
            <input type="hidden" id="equipo_id" name="equipo_id">

            <div class="row g-3">
                <!-- 1. Código de Activo -->
                <div class="col-md-3 col-sm-6">
                    <label for="codigo_activo" class="form-label small fw-semibold">Código de Activo</label>
                    <input type="text" class="form-control form-control-sm" id="codigo_activo" name="codigo_activo" placeholder="Ej: ACT-2026-0042">
                </div>

                <!-- 2. Número de Serie / Inventario -->
                <div class="col-md-3 col-sm-6">
                    <label for="numero_serie" class="form-label small fw-semibold">Número de Serie / Inventario</label>
                    <input type="text" class="form-control form-control-sm" id="numero_serie" name="numero_serie" placeholder="Ej: SN-49204 / INV-004">
                </div>

                <!-- 3. Tipo de Equipo -->
                <div class="col-md-3 col-sm-6">
                    <label for="tipo_equipo" class="form-label small fw-semibold">Tipo de Equipo</label>
                    <select class="form-select form-select-sm" id="tipo_equipo" name="tipo_equipo">
                        <option value="">Seleccione tipo...</option>
                        <option value="PC de Escritorio">PC de Escritorio</option>
                        <option value="Laptop / Portátil">Laptop / Portátil</option>
                        <option value="Impresora / Multifuncional">Impresora / Multifuncional</option>
                        <option value="Monitor / Pantalla">Monitor / Pantalla</option>
                        <option value="Servidor">Servidor</option>
                        <option value="Switch / Router / Red">Switch / Router / Red</option>
                        <option value="Scanner / Digitalizador">Scanner / Digitalizador</option>
                        <option value="Proyector">Proyector</option>
                        <option value="UPS / Regulador">UPS / Regulador</option>
                        <option value="Periférico / Otro">Periférico / Otro</option>
                    </select>
                </div>

                <!-- 4. Marca / Modelo -->
                <div class="col-md-3 col-sm-6">
                    <label for="marca_modelo" class="form-label small fw-semibold">Marca / Modelo</label>
                    <input type="text" class="form-control form-control-sm" id="marca_modelo" name="marca_modelo" placeholder="Ej: Dell OptiPlex 7080 / HP ProBook">
                </div>

                <!-- 5. Sistema Operativo -->
                <div class="col-md-3 col-sm-6">
                    <label for="sistema_operativo" class="form-label small fw-semibold">Sistema Operativo</label>
                    <input type="text" class="form-control form-control-sm" id="sistema_operativo" name="sistema_operativo" list="listaSistemas" placeholder="Ej: Windows 11 Pro">
                    <datalist id="listaSistemas">
                        <option value="Windows 11 Pro">
                        <option value="Windows 10 Pro">
                        <option value="Windows Server 2022">
                        <option value="macOS Sonoma">
                        <option value="Ubuntu Linux 22.04">
                        <option value="No aplica (Periférico/Red)">
                    </datalist>
                </div>

                <!-- 6. Área del Equipo -->
                <div class="col-md-3 col-sm-6">
                    <label for="area_equipo" class="form-label small fw-semibold">Área del Equipo</label>
                    <input type="text" class="form-control form-control-sm" id="area_equipo" name="area" placeholder="Ej: Contabilidad / Almacén">
                </div>

                <!-- 7. Encargado del Equipo -->
                <div class="col-md-3 col-sm-6">
                    <label for="encargado_equipo" class="form-label small fw-semibold">Encargado del Equipo</label>
                    <input type="text" class="form-control form-control-sm" id="encargado_equipo" name="encargado" placeholder="Ej: Lic. Juan Pérez (libre edición)">
                </div>

                <!-- 8. Centro de Costo -->
                <div class="col-md-3 col-sm-6">
                    <label for="centro_costo" class="form-label small fw-semibold">Centro de Costo</label>
                    <input type="text" class="form-control form-control-sm" id="centro_costo" name="centro_costo" placeholder="Ej: CC-102 Finanzas">
                </div>
            </div>

            <!-- Opción para guardar automáticamente en inventario -->
            <div class="form-check form-switch mt-3 pt-2 border-top">
                <input class="form-check-input" type="checkbox" id="guardar_en_inventario" name="guardar_en_inventario" value="1" checked>
                <label class="form-check-label small fw-semibold text-secondary" for="guardar_en_inventario">
                    <i class="bi bi-database-check text-success me-1"></i> Guardar o actualizar este equipo en el inventario institucional al enviar el ticket
                </label>
            </div>
        </div>
    </div>
</div>
