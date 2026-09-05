<?php
/**
 * Subcomponente: Sección 2 - Información del Equipo (PC / Laptop)
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/mantenimiento_preventivo/secciones/seccion_2_informacion_equipo.php
 */
?>
<div class="tarjeta-formulario p-4 mb-4">
    <div class="d-flex align-items-center mb-3">
        <span class="numero-seccion me-2">2</span>
        <div>
            <h5 class="fw-bold mb-0 text-dark">Información del Equipo</h5>
            <small class="text-muted">Especificaciones técnicas y de red de la estación de trabajo</small>
        </div>
    </div>

    <!-- Barra de búsqueda rápida en inventario (opcional) -->
    <div class="p-3 bg-light rounded-3 border mb-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-7">
                <label for="buscar_equipo_mp" class="form-label small fw-semibold text-secondary mb-1">
                    <i class="bi bi-search me-1"></i> Buscar en Catálogo de Inventario (Opcional):
                </label>
                <input type="text" class="form-control form-control-sm" id="buscar_equipo_mp" 
                       list="listaEquiposMP" placeholder="Escriba código de activo o marca/modelo para autocompletar...">
                <datalist id="listaEquiposMP"></datalist>
            </div>
            <div class="col-md-5 d-flex align-items-end justify-content-md-end gap-2 pt-2 pt-md-0">
                <span id="badgeEstadoEquipoMP" class="badge bg-secondary-subtle text-secondary border">
                    Nuevo / No vinculado
                </span>
                <button type="button" class="btn btn-outline-secondary btn-sm d-none" id="btnLimpiarEquipoMP">
                    <i class="bi bi-x-circle me-1"></i> Limpiar
                </button>
            </div>
        </div>
    </div>

    <input type="hidden" id="equipo_id" name="equipo_id">

    <div class="row g-3">
        <!-- Tipo de Equipo: PC o LAPTOP -->
        <div class="col-md-4 col-sm-6">
            <label class="form-label small fw-semibold">Tipo de Equipo <span class="text-danger">*</span></label>
            <div class="d-flex gap-3 mt-1">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tipo_equipo" id="tipo_pc" value="PC" checked>
                    <label class="form-check-label fw-semibold" for="tipo_pc">
                        <i class="bi bi-pc-display me-1 text-primary"></i> PC (Escritorio)
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tipo_equipo" id="tipo_laptop" value="LAPTOP">
                    <label class="form-check-label fw-semibold" for="tipo_laptop">
                        <i class="bi bi-laptop me-1 text-primary"></i> Laptop
                    </label>
                </div>
            </div>
        </div>

        <!-- Tipo de Red: LAN o WIFI -->
        <div class="col-md-4 col-sm-6">
            <label class="form-label small fw-semibold">Tipo de Red Conectada</label>
            <div class="d-flex gap-3 mt-1">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tipo_red" id="red_lan" value="LAN" checked>
                    <label class="form-check-label" for="red_lan">
                        <i class="bi bi-ethernet me-1 text-secondary"></i> Cableada (LAN)
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tipo_red" id="red_wifi" value="WIFI">
                    <label class="form-check-label" for="red_wifi">
                        <i class="bi bi-wifi me-1 text-secondary"></i> Inalámbrica (Wi-Fi)
                    </label>
                </div>
            </div>
        </div>

        <!-- Código de Activo -->
        <div class="col-md-4 col-sm-12">
            <label for="codigo_activo" class="form-label small fw-semibold">Cód. Activo Fijo</label>
            <input type="text" class="form-control" id="codigo_activo" name="codigo_activo" 
                   placeholder="Ej: ACT-00124">
        </div>

        <!-- Nombre de Equipo -->
        <div class="col-md-4 col-sm-6">
            <label for="nombre_equipo" class="form-label small fw-semibold">Nombre de Equipo (Hostname)</label>
            <input type="text" class="form-control" id="nombre_equipo" name="nombre_equipo" 
                   placeholder="Ej: PC-CONTAB-01">
        </div>

        <!-- Marca / Modelo -->
        <div class="col-md-4 col-sm-6">
            <label for="marca_modelo" class="form-label small fw-semibold">Marca / Modelo</label>
            <input type="text" class="form-control" id="marca_modelo" name="marca_modelo" 
                   placeholder="Ej: HP ProDesk 400 G6">
        </div>

        <!-- Sistema Operativo -->
        <div class="col-md-4 col-sm-6">
            <label for="sistema_operativo" class="form-label small fw-semibold">Sistema Operativo</label>
            <input type="text" class="form-control" id="sistema_operativo" name="sistema_operativo" 
                   placeholder="Ej: Windows 11 Pro 64-bit">
        </div>

        <!-- Procesador -->
        <div class="col-md-4 col-sm-6">
            <label for="procesador" class="form-label small fw-semibold">Procesador (CPU)</label>
            <input type="text" class="form-control" id="procesador" name="procesador" 
                   placeholder="Ej: Intel Core i5-10400 / Ryzen 5">
        </div>

        <!-- Memoria RAM -->
        <div class="col-md-2 col-sm-6">
            <label for="memoria_ram" class="form-label small fw-semibold">RAM</label>
            <input type="text" class="form-control" id="memoria_ram" name="memoria_ram" 
                   placeholder="Ej: 16 GB DDR4">
        </div>

        <!-- Almacenamiento -->
        <div class="col-md-3 col-sm-6">
            <label for="almacenamiento" class="form-label small fw-semibold">Almacenamiento</label>
            <input type="text" class="form-control" id="almacenamiento" name="almacenamiento" 
                   placeholder="Ej: SSD 512GB NVMe">
        </div>

        <!-- Dirección IP -->
        <div class="col-md-3 col-sm-6">
            <label for="direccion_ip" class="form-label small fw-semibold">Dirección IP</label>
            <input type="text" class="form-control" id="direccion_ip" name="direccion_ip" 
                   placeholder="Ej: 192.168.1.105">
        </div>
    </div>
</div>
