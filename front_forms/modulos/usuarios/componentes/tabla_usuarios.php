<?php
/**
 * Componente: Tabla de Gestión de Usuarios
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/usuarios/componentes/tabla_usuarios.php
 */
?>
<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
    <div class="card-header bg-white border-bottom p-3 p-md-4">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-lg-4">
                <h5 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-person-lines-fill text-primary me-2"></i>Catálogo de Usuarios
                </h5>
                <p class="text-muted small mb-0">Gestión de cuentas institucionales y permisos de acceso</p>
            </div>
            <div class="col-12 col-lg-8">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end align-items-center">
                    <div class="input-group input-group-sm" style="max-width: 220px;">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="filtroBusquedaUsuario" class="form-control border-start-0 bg-light" placeholder="Buscar usuario...">
                    </div>
                    <select id="filtroRolUsuario" class="form-select form-select-sm bg-light" style="max-width: 140px;">
                        <option value="">Todos los roles</option>
                        <option value="admin">Administrador</option>
                        <option value="tecnico">Técnico</option>
                        <option value="usuario">Solicitante</option>
                    </select>
                    <select id="filtroEstadoUsuario" class="form-select form-select-sm bg-light" style="max-width: 130px;">
                        <option value="">Todos estados</option>
                        <option value="activo">Activos</option>
                        <option value="inactivo">Inactivos</option>
                    </select>
                    <button type="button" class="btn btn-primary btn-sm fw-semibold d-flex align-items-center gap-1" id="btnNuevoUsuario">
                        <i class="bi bi-plus-lg"></i>
                        <span>Nuevo Usuario</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tablaUsuarios">
            <thead class="table-light small text-uppercase text-secondary">
                <tr>
                    <th class="ps-4" style="width: 70px;">ID</th>
                    <th>Usuario</th>
                    <th>Nombre Completo</th>
                    <th>Rol en Sistema</th>
                    <th>Departamento / Cargo</th>
                    <th class="text-center">Estado</th>
                    <th>Último Acceso</th>
                    <th class="text-end pe-4" style="width: 130px;">Acciones</th>
                </tr>
            </thead>
            <tbody id="cuerpoTablaUsuarios" class="border-top-0 small">
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        Cargando catálogo de usuarios...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

