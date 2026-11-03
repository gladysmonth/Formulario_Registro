/**
 * Controlador JavaScript: Gestión de Usuarios y Roles
 * Compatible con ES6 / Navegadores modernos
 * Archivo: front_forms/recursos/js/gestion_usuarios.js
 */

(function () {
    'use strict';

    let listaUsuarios = [];
    const modalElemento = document.getElementById('modalUsuario');
    let modalUsuarioBS = null;

    document.addEventListener('DOMContentLoaded', () => {
        if (modalElemento && typeof bootstrap !== 'undefined') {
            modalUsuarioBS = new bootstrap.Modal(modalElemento);
        }

        inicializarEventos();
        cargarUsuarios();
    });

    function inicializarEventos() {
        const btnNuevo = document.getElementById('btnNuevoUsuario');
        if (btnNuevo) {
            btnNuevo.addEventListener('click', abrirModalCrear);
        }

        const form = document.getElementById('formUsuario');
        if (form) {
            form.addEventListener('submit', manejarGuardarUsuario);
        }

        const filtroBusqueda = document.getElementById('filtroBusquedaUsuario');
        if (filtroBusqueda) {
            filtroBusqueda.addEventListener('input', filtrarUsuarios);
        }

        const filtroRol = document.getElementById('filtroRolUsuario');
        if (filtroRol) {
            filtroRol.addEventListener('change', filtrarUsuarios);
        }

        const filtroEstado = document.getElementById('filtroEstadoUsuario');
        if (filtroEstado) {
            filtroEstado.addEventListener('change', filtrarUsuarios);
        }
    }

    async function cargarUsuarios() {
        const tbody = document.getElementById('cuerpoTablaUsuarios');
        if (!tbody) return;

        try {
            const respuesta = await clienteApi.listarUsuarios();
            if (respuesta.exito && respuesta.datos) {
                listaUsuarios = respuesta.datos.usuarios || [];
                actualizarKpis(listaUsuarios);
                renderizarTabla(listaUsuarios);
            }
        } catch (error) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        ${error.message || 'Error al conectar con la API de usuarios'}
                    </td>
                </tr>
            `;
        }
    }

    function actualizarKpis(usuarios) {
        let total = usuarios.length;
        let admins = 0;
        let tecnicos = 0;
        let solicitantes = 0;

        usuarios.forEach(u => {
            if (u.rol === 'admin') admins++;
            else if (u.rol === 'tecnico') tecnicos++;
            else solicitantes++;
        });

        const elTotal = document.getElementById('kpiTotalUsuarios');
        const elAdmins = document.getElementById('kpiAdmins');
        const elTecnicos = document.getElementById('kpiTecnicos');
        const elSol = document.getElementById('kpiSolicitantes');

        if (elTotal) elTotal.textContent = total;
        if (elAdmins) elAdmins.textContent = admins;
        if (elTecnicos) elTecnicos.textContent = tecnicos;
        if (elSol) elSol.textContent = solicitantes;
    }

    function renderizarTabla(usuarios) {
        const tbody = document.getElementById('cuerpoTablaUsuarios');
        if (!tbody) return;

        if (usuarios.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                        No se encontraron usuarios que coincidan con los filtros.
                    </td>
                </tr>
            `;
            return;
        }

        const usuarioActualId = (window.USUARIO_SESION && window.USUARIO_SESION.id) ? parseInt(window.USUARIO_SESION.id) : 0;

        tbody.innerHTML = usuarios.map(u => {
            const esMismoUsuario = (parseInt(u.id) === usuarioActualId);

            // Badge Rol
            let badgeRol = '<span class="badge bg-secondary text-white"><i class="bi bi-person me-1"></i>Solicitante</span>';
            if (u.rol === 'admin') {
                badgeRol = '<span class="badge bg-danger text-white"><i class="bi bi-shield-shaded me-1"></i>Administrador</span>';
            } else if (u.rol === 'tecnico') {
                badgeRol = '<span class="badge bg-info text-dark"><i class="bi bi-tools me-1"></i>Técnico</span>';
            }

            // Badge Estado
            const badgeEstado = u.activo
                ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i>Activo</span>'
                : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="bi bi-x-circle-fill me-1"></i>Inactivo</span>';

            const deptoCargo = (u.departamento || u.cargo)
                ? `<span class="fw-semibold text-dark">${u.departamento || 'Sin área'}</span><small class="text-muted d-block">${u.cargo || 'Sin cargo'}</small>`
                : '<span class="text-muted fst-italic">No especificado</span>';

            const botonEstado = esMismoUsuario
                ? `<button class="btn btn-sm btn-outline-secondary opacity-50" title="No puedes desactivar tu propia cuenta" disabled><i class="bi bi-slash-circle"></i></button>`
                : (u.activo
                    ? `<button class="btn btn-sm btn-outline-danger" title="Desactivar usuario" onclick="window.gestorUsuarios.cambiarEstado(${u.id}, false)"><i class="bi bi-person-x"></i></button>`
                    : `<button class="btn btn-sm btn-outline-success" title="Activar usuario" onclick="window.gestorUsuarios.cambiarEstado(${u.id}, true)"><i class="bi bi-person-check"></i></button>`);

            return `
                <tr>
                    <td class="ps-4 text-muted fw-semibold">${u.id}</td>
                    <td>
                        <strong class="text-dark font-monospace">${u.usuario}</strong>
                        ${esMismoUsuario ? '<span class="badge bg-primary-subtle text-primary ms-1">Tú</span>' : ''}
                    </td>
                    <td>${u.nombre_completo}</td>
                    <td>${badgeRol}</td>
                    <td>${deptoCargo}</td>
                    <td class="text-center">${badgeEstado}</td>
                    <td class="text-muted">${u.ultimo_acceso || 'Sin accesos'}</td>
                    <td class="text-end pe-4">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" title="Editar usuario" onclick="window.gestorUsuarios.abrirEditar(${u.id})">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            ${botonEstado}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function filtrarUsuarios() {
        const texto = (document.getElementById('filtroBusquedaUsuario')?.value || '').toLowerCase().trim();
        const rol = document.getElementById('filtroRolUsuario')?.value || '';
        const estado = document.getElementById('filtroEstadoUsuario')?.value || '';

        const filtrados = listaUsuarios.filter(u => {
            const coincideTexto = !texto ||
                u.usuario.toLowerCase().includes(texto) ||
                u.nombre_completo.toLowerCase().includes(texto) ||
                (u.departamento && u.departamento.toLowerCase().includes(texto)) ||
                (u.cargo && u.cargo.toLowerCase().includes(texto));

            const coincideRol = !rol || (u.rol === rol);
            const coincideEstado = !estado ||
                (estado === 'activo' && u.activo) ||
                (estado === 'inactivo' && !u.activo);

            return coincideTexto && coincideRol && coincideEstado;
        });

        renderizarTabla(filtrados);
    }

    function abrirModalCrear() {
        document.getElementById('formUsuario').reset();
        document.getElementById('form_usuario_id').value = '0';
        document.getElementById('modalUsuarioTitulo').innerHTML = '<i class="bi bi-person-plus-fill me-2"></i>Registrar Nuevo Usuario';
        document.getElementById('btnGuardarTexto').textContent = 'Guardar Usuario';

        const spanClave = document.getElementById('spanClaveObligatoria');
        const inputClave = document.getElementById('form_clave');
        const ayudaClave = document.getElementById('ayudaClave');

        if (spanClave) spanClave.style.display = 'inline';
        if (inputClave) {
            inputClave.required = true;
            inputClave.placeholder = 'Mínimo 6 caracteres';
        }
        if (ayudaClave) ayudaClave.textContent = 'Mínimo 6 caracteres para nuevos usuarios.';

        document.getElementById('form_activo').checked = true;

        if (modalUsuarioBS) modalUsuarioBS.show();
    }

    function abrirModalEditar(id) {
        const usuario = listaUsuarios.find(u => parseInt(u.id) === parseInt(id));
        if (!usuario) return;

        document.getElementById('formUsuario').reset();
        document.getElementById('form_usuario_id').value = usuario.id;
        document.getElementById('form_usuario').value = usuario.usuario;
        document.getElementById('form_nombre_completo').value = usuario.nombre_completo;
        document.getElementById('form_rol').value = usuario.rol;
        document.getElementById('form_departamento').value = usuario.departamento || '';
        document.getElementById('form_cargo').value = usuario.cargo || '';
        document.getElementById('form_activo').checked = Boolean(usuario.activo);

        document.getElementById('modalUsuarioTitulo').innerHTML = `<i class="bi bi-pencil-square me-2"></i>Editar Usuario: ${usuario.usuario}`;
        document.getElementById('btnGuardarTexto').textContent = 'Actualizar Usuario';

        const spanClave = document.getElementById('spanClaveObligatoria');
        const inputClave = document.getElementById('form_clave');
        const ayudaClave = document.getElementById('ayudaClave');

        if (spanClave) spanClave.style.display = 'none';
        if (inputClave) {
            inputClave.required = false;
            inputClave.placeholder = 'Dejar en blanco para no modificar';
        }
        if (ayudaClave) ayudaClave.textContent = 'Dejar en blanco para conservar la contraseña actual.';

        if (modalUsuarioBS) modalUsuarioBS.show();
    }

    async function manejarGuardarUsuario(e) {
        e.preventDefault();

        const btn = document.getElementById('btnGuardarUsuario');
        const txtOriginal = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Guardando...';

        const datos = {
            id: parseInt(document.getElementById('form_usuario_id').value) || 0,
            usuario: document.getElementById('form_usuario').value.trim(),
            clave: document.getElementById('form_clave').value.trim(),
            nombre_completo: document.getElementById('form_nombre_completo').value.trim(),
            rol: document.getElementById('form_rol').value,
            departamento: document.getElementById('form_departamento').value.trim(),
            cargo: document.getElementById('form_cargo').value.trim(),
            activo: document.getElementById('form_activo').checked
        };

        try {
            const respuesta = await clienteApi.guardarUsuario(datos);
            if (respuesta.exito) {
                if (modalUsuarioBS) modalUsuarioBS.hide();
                Swal.fire({
                    icon: 'success',
                    title: '¡Operación Exitosa!',
                    text: respuesta.mensaje,
                    timer: 2000,
                    showConfirmButton: false
                });
                await cargarUsuarios();
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'No se pudo guardar el usuario'
            });
        } finally {
            btn.disabled = false;
            btn.innerHTML = txtOriginal;
        }
    }

    async function cambiarEstado(id, nuevoEstado) {
        const accionTexto = nuevoEstado ? 'activar' : 'desactivar';
        const confirmacion = await Swal.fire({
            title: `¿Desea ${accionTexto} este usuario?`,
            text: `El usuario ${nuevoEstado ? 'podrá' : 'dejará de poder'} iniciar sesión en el sistema.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: `Sí, ${accionTexto}`,
            cancelButtonText: 'Cancelar'
        });

        if (!confirmacion.isConfirmed) return;

        try {
            const respuesta = await clienteApi.cambiarEstadoUsuario(id, nuevoEstado);
            if (respuesta.exito) {
                Swal.fire({
                    icon: 'success',
                    title: 'Estado Actualizado',
                    text: respuesta.mensaje,
                    timer: 1800,
                    showConfirmButton: false
                });
                await cargarUsuarios();
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'No se pudo cambiar el estado'
            });
        }
    }

    // Exponer al objeto global para botones onclick de la tabla
    window.gestorUsuarios = {
        abrirEditar: abrirModalEditar,
        cambiarEstado: cambiarEstado
    };
})();

