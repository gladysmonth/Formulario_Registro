/**
 * Lógica del Formulario de Creación y/o Asignación de Accesos de Usuarios de Sistemas
 * Compatible con PHP 7.3 y Bootstrap 5
 * Archivo: front_forms/recursos/js/formulario_accesos.js
 */

document.addEventListener('DOMContentLoaded', () => {
    const formAccesos = document.getElementById('formRegistroAccesos');
    const alertaValidacion = document.getElementById('alertaValidacionAcc');
    const btnEnviar = document.getElementById('btnEnviarSolicitudAccesos');
    const btnLimpiar = document.getElementById('btnLimpiarFormAccesos');

    // ==========================================================
    // 1. CONTROL DE LIENZOS DE FIRMA DIGITAL (Canvas x3)
    // ==========================================================
    function inicializarLienzoFirma(canvasId, badgeId, btnLimpiarId, esObligatorio = false) {
        const canvas = document.getElementById(canvasId);
        const badge = document.getElementById(badgeId);
        const btnLimpiar = document.getElementById(btnLimpiarId);
        if (!canvas) return null;

        const ctx = canvas.getContext('2d');
        let dibujando = false;
        let tieneFirma = false;

        function redimensionarCanvas() {
            const rect = canvas.getBoundingClientRect();
            if (rect.width > 0) {
                let imagenData = null;
                if (tieneFirma) {
                    try {
                        imagenData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    } catch (e) {}
                }

                canvas.width = rect.width;
                canvas.height = 140;
                ctx.lineWidth = 2.5;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.strokeStyle = '#0f172a';

                if (imagenData) {
                    try {
                        ctx.putImageData(imagenData, 0, 0);
                    } catch (e) {}
                }
            }
        }

        window.addEventListener('resize', redimensionarCanvas);
        setTimeout(redimensionarCanvas, 200);

        function obtenerPos(e) {
            const rect = canvas.getBoundingClientRect();
            const cx = (e.touches && e.touches.length > 0) ? e.touches[0].clientX : (e.clientX || 0);
            const cy = (e.touches && e.touches.length > 0) ? e.touches[0].clientY : (e.clientY || 0);
            return { x: cx - rect.left, y: cy - rect.top };
        }

        function iniciarDibujo(e) {
            if (e.cancelable) e.preventDefault();
            dibujando = true;
            const pos = obtenerPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        }

        function trazar(e) {
            if (!dibujando) return;
            if (e.cancelable) e.preventDefault();
            const pos = obtenerPos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            if (!tieneFirma) {
                tieneFirma = true;
                if (badge) {
                    badge.className = 'badge bg-success-subtle text-success border';
                    badge.textContent = 'Firmado';
                }
            }
        }

        function terminarDibujo() {
            dibujando = false;
        }

        canvas.addEventListener('mousedown', iniciarDibujo);
        canvas.addEventListener('mousemove', trazar);
        canvas.addEventListener('mouseup', terminarDibujo);
        canvas.addEventListener('mouseleave', terminarDibujo);

        canvas.addEventListener('touchstart', iniciarDibujo, { passive: false });
        canvas.addEventListener('touchmove', trazar, { passive: false });
        canvas.addEventListener('touchend', terminarDibujo);

        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                tieneFirma = false;
                if (badge) {
                    badge.className = 'badge bg-secondary-subtle text-secondary border';
                    badge.textContent = 'Sin firmar';
                }
            });
        }

        return {
            obtenerBase64: () => {
                if (!tieneFirma) return null;
                return canvas.toDataURL('image/png');
            },
            tieneFirma: () => tieneFirma,
            limpiar: () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                tieneFirma = false;
                if (badge) {
                    badge.className = 'badge bg-secondary-subtle text-secondary border';
                    badge.textContent = 'Sin firmar';
                }
            },
            redimensionar: redimensionarCanvas
        };
    }

    const firmaSolicitante = inicializarLienzoFirma('canvasFirmaSolicitanteAcc', 'badgeFirmaSolicitanteAcc', 'btnLimpiarFirmaSolicitanteAcc', true);
    const firmaAutoriza    = inicializarLienzoFirma('canvasFirmaAutorizaAcc', 'badgeFirmaAutorizaAcc', 'btnLimpiarFirmaAutorizaAcc', false);
    const firmaSistemas    = inicializarLienzoFirma('canvasFirmaSistemasAcc', 'badgeFirmaSistemasAcc', 'btnLimpiarFirmaSistemasAcc', false);

    // ==========================================================
    // 2. DINAMISMO DE PLATAFORMAS Y USUARIO
    // ==========================================================
    const checkOtros = document.getElementById('sistema_otros');
    const bloqueOtros = document.getElementById('bloqueOtrosSistemas');
    const inputOtrosDetalle = document.getElementById('sistema_otros_detalle');
    const notaOtros = document.getElementById('notaOtrosSistemas');

    if (checkOtros && bloqueOtros) {
        checkOtros.addEventListener('change', () => {
            if (checkOtros.checked) {
                bloqueOtros.classList.remove('d-none');
                if (notaOtros) notaOtros.classList.add('d-none');
                if (inputOtrosDetalle) inputOtrosDetalle.focus();
            } else {
                bloqueOtros.classList.add('d-none');
                if (notaOtros) notaOtros.classList.remove('d-none');
                if (inputOtrosDetalle) inputOtrosDetalle.value = '';
            }
        });
    }

    const checkUsuarioNuevo = document.getElementById('es_usuario_nuevo');
    const textoCondicion = document.getElementById('textoCondicionUsuario');
    const inputUsername = document.getElementById('nombre_usuario_detalles');

    if (checkUsuarioNuevo) {
        checkUsuarioNuevo.addEventListener('change', () => {
            if (checkUsuarioNuevo.checked) {
                if (textoCondicion) textoCondicion.textContent = 'Marque si se trata de una cuenta nueva que aún no existe en los sistemas.';
                if (inputUsername) inputUsername.placeholder = 'Ej: Nombre de usuario sugerido o dejar en blanco para Sistemas';
            } else {
                if (textoCondicion) textoCondicion.textContent = 'Cuenta existente: Indique el username actual para modificar o agregar accesos.';
                if (inputUsername) inputUsername.placeholder = 'Especifique el username actual del usuario (Requerido para cuentas existentes)';
            }
        });
    }

    // ==========================================================
    // 3. ENVÍO Y VALIDACIÓN DEL FORMULARIO
    // ==========================================================
    let enviando = false;

    async function procesarEnvioAccesos(e) {
        if (e && e.preventDefault) e.preventDefault();
        if (enviando) return;

        if (alertaValidacion) {
            alertaValidacion.classList.add('d-none');
            alertaValidacion.innerHTML = '';
        }

        const elFechaSolicitud = document.getElementById('fecha_solicitud');
        const fechaSolicitud   = elFechaSolicitud ? elFechaSolicitud.value.trim() : new Date().toISOString();

        const elNombre = document.getElementById('nombre_solicitante');
        const nombre   = elNombre ? elNombre.value.trim() : '';

        const elCargo = document.getElementById('cargo_solicitante');
        const cargo   = elCargo ? elCargo.value.trim() : '';

        const elArea = document.getElementById('area_departamento');
        const area   = elArea ? elArea.value.trim() : '';

        const elSai    = document.getElementById('sistema_erp_sai');
        const sai      = elSai ? elSai.checked : false;

        const elNetcob = document.getElementById('sistema_cobranzas_netcob');
        const netcob   = elNetcob ? elNetcob.checked : false;

        const elOtros  = document.getElementById('sistema_otros');
        const otros    = elOtros ? elOtros.checked : false;
        const otrosDetalle = inputOtrosDetalle ? inputOtrosDetalle.value.trim() : '';

        const esNuevo = checkUsuarioNuevo ? checkUsuarioNuevo.checked : true;
        const usuarioDetalles = inputUsername ? inputUsername.value.trim() : '';

        const elRequerimientos = document.getElementById('requerimientos_accesos');
        const requerimientos  = elRequerimientos ? elRequerimientos.value.trim() : '';

        const elAtendidoPor   = document.getElementById('atendido_por');
        const atendidoPor     = elAtendidoPor ? elAtendidoPor.value.trim() : null;

        const elFechaAtencion = document.getElementById('fecha_hora_atencion');
        const fechaAtencion   = elFechaAtencion ? elFechaAtencion.value.trim() : null;

        const elEstado        = document.getElementById('estado');
        const estado          = elEstado ? elEstado.value : 'pendiente';

        const elComentarios   = document.getElementById('comentarios_sistemas');
        const comentarios     = elComentarios ? elComentarios.value.trim() : null;

        const elNombreAutoriza = document.getElementById('nombre_autoriza');
        const nombreAutoriza  = elNombreAutoriza ? elNombreAutoriza.value.trim() : null;

        const errores = [];

        if (!nombre) errores.push('El nombre del solicitante es obligatorio.');
        if (!cargo) errores.push('El cargo del solicitante es obligatorio.');
        if (!area) errores.push('El área o departamento es obligatorio.');

        if (!sai && !netcob && !otros) {
            errores.push('Debe seleccionar al menos una plataforma o sistema (SAI, NETCOB u Otros).');
        }

        if (otros && !otrosDetalle) {
            errores.push('Ha marcado "Otros Sistemas / Complementos", debe especificar el aplicativo.');
        }

        if (!requerimientos) {
            errores.push('El campo de requerimientos de accesos (perfiles, permisos o módulos) es obligatorio.');
        }

        if (firmaSolicitante && !firmaSolicitante.tieneFirma()) {
            errores.push('La firma digital del Solicitante es obligatoria para certificar la solicitud.');
        }

        if (errores.length > 0) {
            if (alertaValidacion) {
                alertaValidacion.innerHTML = `
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <strong>Por favor complete los campos obligatorios:</strong>
                    </div>
                    <ul class="mb-0 ps-3">
                        ${errores.map(err => `<li>${err}</li>`).join('')}
                    </ul>
                `;
                alertaValidacion.classList.remove('d-none');
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos requeridos incompletos',
                    html: `
                        <div class="text-start small p-2 bg-light rounded border">
                            <p class="mb-2 fw-semibold text-danger">Por favor complete lo siguiente antes de enviar:</p>
                            <ul class="mb-0 ps-3">
                                ${errores.map(err => `<li>${err}</li>`).join('')}
                            </ul>
                        </div>
                    `,
                    confirmButtonText: 'Entendido, completar datos',
                    confirmButtonColor: '#0d6efd'
                });
            } else if (alertaValidacion) {
                alertaValidacion.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }

        // Preparar carga útil JSON
        const payload = {
            nro_solicitud:           null,
            fecha_solicitud:         fechaSolicitud,
            nombre_solicitante:      nombre,
            cargo_solicitante:       cargo,
            area_departamento:       area,
            sistema_erp_sai:          sai,
            sistema_cobranzas_netcob: netcob,
            sistema_otros:            otros,
            sistema_otros_detalle:    otros ? otrosDetalle : null,
            es_usuario_nuevo:        esNuevo,
            nombre_usuario_detalles: usuarioDetalles || null,
            requerimientos_accesos:  requerimientos,
            atendido_por:            atendidoPor || null,
            fecha_hora_atencion:     fechaAtencion || null,
            estado:                  estado,
            comentarios_sistemas:    comentarios || null,
            nombre_autoriza:         nombreAutoriza || null,
            firma_solicitante:       firmaSolicitante ? firmaSolicitante.obtenerBase64() : null,
            firma_autoriza:          firmaAutoriza ? firmaAutoriza.obtenerBase64() : null,
            firma_sistemas:          firmaSistemas ? firmaSistemas.obtenerBase64() : null
        };

        enviando = true;
        if (btnEnviar) {
            btnEnviar.disabled = true;
            btnEnviar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Registrando solicitud...';
        }

        try {
            const respuesta = await clienteApi.crearSolicitudAccesos(payload);
            if (respuesta.exito) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Solicitud Registrada con Éxito!',
                        html: `
                            <div class="text-start p-3 bg-light rounded-3 my-2 border small">
                                <p class="mb-1"><strong>N° Solicitud:</strong> <span class="badge bg-primary fs-6">${respuesta.datos.nro_solicitud}</span></p>
                                <p class="mb-1"><strong>Estado:</strong> <span class="badge bg-warning text-dark">${(respuesta.datos.estado || 'PENDIENTE').toUpperCase()}</span></p>
                                <p class="mb-0 text-muted">Su requerimiento formal ha sido enviado al Departamento de Sistemas.</p>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: '<i class="bi bi-clock-history me-1"></i> Ver Mis Solicitudes',
                        cancelButtonText: '<i class="bi bi-plus-circle me-1"></i> Nueva Solicitud',
                        confirmButtonColor: '#0d6efd',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const esPersonalTecnico = window.USUARIO_SESION && (window.USUARIO_SESION.rol === 'admin' || window.USUARIO_SESION.rol === 'tecnico');
                            window.location.href = esPersonalTecnico ? 'gestion_accesos.php' : '../mis_solicitudes/mis_solicitudes.php';
                        } else {
                            if (formAccesos) formAccesos.reset();
                            if (firmaSolicitante) firmaSolicitante.limpiar();
                            if (firmaAutoriza) firmaAutoriza.limpiar();
                            if (firmaSistemas) firmaSistemas.limpiar();
                            if (bloqueOtros) bloqueOtros.classList.add('d-none');
                            if (notaOtros) notaOtros.classList.remove('d-none');
                            if (alertaValidacion) alertaValidacion.classList.add('d-none');
                            enviando = false;
                            if (btnEnviar) {
                                btnEnviar.disabled = false;
                                btnEnviar.innerHTML = '<i class="bi bi-send-fill me-1"></i> Registrar Solicitud de Acceso';
                            }
                        }
                    });
                } else {
                    clienteApi.mostrarToast('¡Solicitud de accesos registrada con éxito!', 'exito');
                    const esPersonalTecnico = window.USUARIO_SESION && (window.USUARIO_SESION.rol === 'admin' || window.USUARIO_SESION.rol === 'tecnico');
                    setTimeout(() => {
                        window.location.href = esPersonalTecnico ? 'gestion_accesos.php' : '../mis_solicitudes/mis_solicitudes.php';
                    }, 1200);
                }
            } else {
                enviando = false;
                if (btnEnviar) {
                    btnEnviar.disabled = false;
                    btnEnviar.innerHTML = '<i class="bi bi-send-fill me-1"></i> Registrar Solicitud de Acceso';
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al registrar',
                        text: respuesta.mensaje || 'No se pudo guardar la solicitud.',
                        confirmButtonColor: '#dc3545'
                    });
                }
                if (alertaValidacion) {
                    alertaValidacion.innerHTML = `<strong>Error del servidor:</strong> ${respuesta.mensaje || 'No se pudo guardar el registro.'}`;
                    alertaValidacion.classList.remove('d-none');
                }
            }
        } catch (error) {
            console.error('Error de conexión:', error);
            enviando = false;
            if (btnEnviar) {
                btnEnviar.disabled = false;
                btnEnviar.innerHTML = '<i class="bi bi-send-fill me-1"></i> Registrar Solicitud de Acceso';
            }
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de comunicación',
                    text: error.message || 'No se pudo conectar con el backend.',
                    confirmButtonColor: '#dc3545'
                });
            }
            if (alertaValidacion) {
                alertaValidacion.innerHTML = `<strong>Error de comunicación:</strong> No se pudo conectar con el backend. (${error.message})`;
                alertaValidacion.classList.remove('d-none');
            }
        }
    }

    if (btnEnviar) {
        btnEnviar.addEventListener('click', procesarEnvioAccesos);
    }

    if (formAccesos) {
        formAccesos.addEventListener('submit', procesarEnvioAccesos);
    }

    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', () => {
            if (firmaSolicitante) firmaSolicitante.limpiar();
            if (firmaAutoriza) firmaAutoriza.limpiar();
            if (firmaSistemas) firmaSistemas.limpiar();
            if (bloqueOtros) bloqueOtros.classList.add('d-none');
            if (notaOtros) notaOtros.classList.remove('d-none');
            if (alertaValidacion) alertaValidacion.classList.add('d-none');
        });
    }
});
