<?php
/**
 * Componente: Modal para Crear o Editar Usuario
 * Compatible con PHP 7.3
 * Archivo: front_forms/modulos/usuarios/componentes/modal_usuario.php
 */
?>
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white p-3 px-4">
                <h5 class="modal-title fw-bold fs-6" id="modalUsuarioTitulo">
                    <i class="bi bi-person-plus-fill me-2"></i>Registrar Nuevo Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formUsuario" autocomplete="off">
                <input type="hidden" id="form_usuario_id" value="0">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="form_usuario" class="form-label small fw-bold text-dark">Nombre de Usuario <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light"><i class="bi bi-at"></i></span>
                                <input type="text" class="form-control" id="form_usuario" required placeholder="ej. mtorres" maxlength="50">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="form_clave" class="form-label small fw-bold text-dark">
                                Contraseña <span id="spanClaveObligatoria" class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light"><i class="bi bi-key-fill"></i></span>
                                <input type="password" class="form-control" id="form_clave" placeholder="Mínimo 6 caracteres">
                            </div>
                            <div class="form-text" id="ayudaClave" style="font-size: 0.75rem;">Mínimo 6 caracteres para nuevos usuarios.</div>
                        </div>

                        <div class="col-md-8">
                            <label for="form_nombre_completo" class="form-label small fw-bold text-dark">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="form_nombre_completo" required placeholder="ej. Mario Torres Rojas" maxlength="150">
                        </div>
                        <div class="col-md-4">
                            <label for="form_rol" class="form-label small fw-bold text-dark">Rol Institucional <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="form_rol" required>
                                <option value="usuario">Solicitante (Personal)</option>
                                <option value="tecnico">Técnico de Soporte</option>
                                <option value="admin">Administrador Total</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="form_departamento" class="form-label small fw-bold text-dark">Departamento o Área</label>
                            <input type="text" class="form-control form-control-sm" id="form_departamento" placeholder="ej. Administración y Finanzas" list="listaDeptosSugeridos">
                            <datalist id="listaDeptosSugeridos">
                                <option value="Sistemas e Informática">
                                <option value="Administración y Finanzas">
                                <option value="Cobranzas">
                                <option value="Comercial / Ventas">
                                <option value="Operaciones">
                                <option value="Transporte">
                                <option value="Gerencia General">
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label for="form_cargo" class="form-label small fw-bold text-dark">Cargo o Puesto</label>
                            <input type="text" class="form-control form-control-sm" id="form_cargo" placeholder="ej. Auxiliar Administrativo" maxlength="100">
                        </div>

                        <div class="col-12 pt-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="form_activo" checked>
                                <label class="form-check-label small fw-semibold text-dark" for="form_activo">
                                    Cuenta de usuario activa y con permiso de inicio de sesión
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3 px-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-semibold d-flex align-items-center gap-1" id="btnGuardarUsuario">
                        <i class="bi bi-check-circle-fill"></i>
                        <span id="btnGuardarTexto">Guardar Usuario</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

