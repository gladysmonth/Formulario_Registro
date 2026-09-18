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
            const cx = e.touches ? e.touches[0].clientX : e.clientX;
            const cy = e.touches ? e.touches[0].clientY : e.clientY;
            return { x: cx - rect.left, y: cy - rect.top };
        }

        function iniciarDibujo(e) {
            e.preventDefault();
            dibujando = true;
            const pos = obtenerPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        }

        function trazar(e) {
            if (!dibujando) return;
            e.preventDefault();
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
                    badge.textContent = 'Firmado';
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
                    badge.textContent = 'Firmado';
                }
            },
            redimensionar: redimensionarCanvas
        };
    }

    const firmaSolicitante = inicializarLienzoFirma('canvasFirmaSolicitanteAcc', null, 'btnLimpiarFirmaSolicitanteAcc', true);
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
                textoCondicion.textContent = 'Marque si se trata de una cuenta nueva que aún no existe en los sistemas.';
                if (inputUsername) inputUsername.placeholder = 'Ej: Nombre de usuario sugerido o dejar en blanco para Sistemas';
            } else {
                textoCondicion.textContent = 'Cuenta existente: Indique el username actual para modificar o agregar accesos.';
                if (inputUsername) inputUsername.placeholder = 'Especifique el username actual del usuario (Requerido para cuentas existentes)';
            }
        });
    }

    // ==========================================================
    // 3. ENVÍO Y VALIDACIÓN DEL FORMULARIO
    // ==========================================================
    if (formAccesos) {
        formAccesos.addEventListener('submit', async (e) => {
            e.preventDefault();
            alertaValidacion.classList.add('d-none');
            alertaValidacion.innerHTML = '';

            const fechaSolicitud = document.getElementById('fecha_solicitud') ? document.getElementById('fecha_solicitud').value.trim() : new Date().toISOString();
            const nombre         = document.getElementById('nombre_solicitante').value.trim();
            const cargo          = document.getElementById('cargo_solicitante').value.trim();
            const area           = document.getElementById('area_departamento').value.trim();

            const sai    = document.getElementById('sistema_erp_sai').checked;
            const netcob = document.getElementById('sistema_cobranzas_netcob').checked;
            const otros  = document.getElementById('sistema_otros').checked;
            const otrosDetalle = inputOtrosDetalle ? inputOtrosDetalle.value.trim() : '';

            const esNuevo = checkUsuarioNuevo ? checkUsuarioNuevo.checked : true;
            const usuarioDetalles = inputUsername ? inputUsername.value.trim() : '';

            const requerimientos = document.getElementById('requerimientos_accesos').value.trim();
            const atendidoPor    = document.getElementById('atendido_por').value.trim();
            const fechaAtencion  = document.getElementById('fecha_hora_atencion').value.trim();
            const estado         = document.getElementById('estado').value;
            const comentarios    = document.getElementById('comentarios_sistemas').value.trim();
            const nombreAutoriza = document.getElementById('nombre_autoriza') ? document.getElementById('nombre_autoriza').value.trim() : '';

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
                alertaValidacion.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            // Preparar carga útil JSON
            const payload = {
                nro_solicitud:           nroSolicitud,
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

            // Enviar petición con indicador de carga
            btnEnviar.disabled = true;
            btnEnviar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Registrando solicitud...';

            try {
                const respuesta = await clienteApi.crearSolicitudAccesos(payload);
                if (respuesta.exito) {
                    clienteApi.mostrarToast('¡Solicitud de accesos registrada con éxito!', 'exito');
                    setTimeout(() => {
                        window.location.href = 'gestion_accesos.php';
                    }, 1200);
                } else {
                    alertaValidacion.innerHTML = `<strong>Error del servidor:</strong> ${respuesta.mensaje || 'No se pudo guardar el registro.'}`;
                    alertaValidacion.classList.remove('d-none');
                    btnEnviar.disabled = false;
                    btnEnviar.innerHTML = '<i class="bi bi-send-fill me-1"></i> Registrar Solicitud de Acceso';
                }
            } catch (error) {
                console.error('Error de conexión:', error);
                alertaValidacion.innerHTML = `<strong>Error de comunicación:</strong> No se pudo conectar con el backend. (${error.message})`;
                alertaValidacion.classList.remove('d-none');
                btnEnviar.disabled = false;
                btnEnviar.innerHTML = '<i class="bi bi-send-fill me-1"></i> Registrar Solicitud de Acceso';
            }
        });
    }

    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', () => {
            if (firmaSolicitante) firmaSolicitante.limpiar();
            if (firmaAutoriza) firmaAutoriza.limpiar();
            if (firmaSistemas) firmaSistemas.limpiar();
            if (bloqueOtros) bloqueOtros.classList.add('d-none');
            if (notaOtros) notaOtros.classList.remove('d-none');
            alertaValidacion.classList.add('d-none');
        });
    }
});
