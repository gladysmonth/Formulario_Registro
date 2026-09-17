/**
 * Lógica del Formulario de Registro de Incidencias de Sistemas (FOR_RIS_001, V-1)
 * Compatible con PHP 7.3 y Bootstrap 5
 * Archivo: front_forms/recursos/js/formulario_incidencias.js
 */

document.addEventListener('DOMContentLoaded', () => {
    const formRI = document.getElementById('formRegistroIncidencia');
    const alertaValidacion = document.getElementById('alertaValidacionRI');
    const btnGuardar = document.getElementById('btnGuardarRI');
    const btnRestablecer = document.getElementById('btnRestablecerRI');

    // ==========================================================
    // 1. CONTROL DE LIENZOS DE FIRMA DIGITAL (Canvas)
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
                // Preservar contenido si ya hay trazo
                let imagenData = null;
                if (tieneFirma) {
                    try {
                        imagenData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    } catch (e) {}
                }

                canvas.width = rect.width;
                canvas.height = 150;
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
        setTimeout(redimensionarCanvas, 250);

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
                    if (esObligatorio) {
                        badge.className = 'badge bg-danger-subtle text-danger border';
                        badge.textContent = 'Pendiente';
                    } else {
                        badge.className = 'badge bg-secondary-subtle text-secondary border';
                        badge.textContent = 'Opcional / Cierre';
                    }
                }
            });
        }

        return {
            estaFirmado: () => tieneFirma,
            obtenerBase64: () => tieneFirma ? canvas.toDataURL('image/png') : null,
            limpiar: () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                tieneFirma = false;
                if (badge) {
                    if (esObligatorio) {
                        badge.className = 'badge bg-danger-subtle text-danger border';
                        badge.textContent = 'Pendiente';
                    } else {
                        badge.className = 'badge bg-secondary-subtle text-secondary border';
                        badge.textContent = 'Opcional / Cierre';
                    }
                }
            }
        };
    }

    const controlFirmaResponsable = inicializarLienzoFirma(
        'canvasFirmaResponsableRI',
        'badgeFirmaResponsableRI',
        'btnLimpiarFirmaResponsableRI',
        true
    );

    const controlFirmaSistemas = inicializarLienzoFirma(
        'canvasFirmaSistemasRI',
        'badgeFirmaSistemasRI',
        'btnLimpiarFirmaSistemasRI',
        false
    );

    // ==========================================================
    // 2. MARCAR Y RELLENAR AL MARCADO (Sección 3: Naturaleza del Fallo)
    // ==========================================================
    const checkboxesNaturaleza = document.querySelectorAll('.check-naturaleza');
    checkboxesNaturaleza.forEach(check => {
        check.addEventListener('change', () => {
            const inputId = check.getAttribute('data-target-input');
            const badgeId = check.getAttribute('data-target-badge');
            const input = document.getElementById(inputId);
            const badge = document.getElementById(badgeId);
            const fila = check.closest('tr');

            if (check.checked) {
                // Habilitar campo y enfocarlo
                if (input) {
                    input.disabled = false;
                    input.classList.remove('bg-light');
                    input.classList.add('bg-white', 'border-danger');
                    input.focus();
                }
                if (badge) {
                    badge.className = 'badge bg-danger text-white';
                    badge.textContent = 'Falla Reportada';
                }
                if (fila) {
                    fila.classList.add('table-danger');
                }
            } else {
                // Deshabilitar campo y limpiar
                if (input) {
                    input.value = '';
                    input.disabled = true;
                    input.classList.remove('bg-white', 'border-danger');
                    input.classList.add('bg-light');
                }
                if (badge) {
                    badge.className = 'badge bg-secondary-subtle text-muted border';
                    badge.textContent = 'Sin Falla';
                }
                if (fila) {
                    fila.classList.remove('table-danger');
                }
            }
        });
    });

    // ==========================================================
    // 3. CONTROL DE SELECCIÓN DE PLATAFORMA / SISTEMA (Sección 2)
    // ==========================================================
    const checkOtros = document.getElementById('sistema_otros');
    const contenedorOtrosDetalle = document.getElementById('contenedor_otros_sistemas_detalle');
    const inputOtrosDetalle = document.getElementById('sistema_otros_detalle');

    if (checkOtros && contenedorOtrosDetalle) {
        checkOtros.addEventListener('change', () => {
            if (checkOtros.checked) {
                contenedorOtrosDetalle.classList.remove('d-none');
                if (inputOtrosDetalle) {
                    inputOtrosDetalle.focus();
                    inputOtrosDetalle.setAttribute('required', 'required');
                }
            } else {
                contenedorOtrosDetalle.classList.add('d-none');
                if (inputOtrosDetalle) {
                    inputOtrosDetalle.value = '';
                    inputOtrosDetalle.removeAttribute('required');
                }
            }
        });
    }

    // Efecto visual en tarjetas de selección de plataforma
    const tarjetasSeleccion = document.querySelectorAll('.tarjeta-seleccion');
    tarjetasSeleccion.forEach(tarjeta => {
        const switchInput = tarjeta.querySelector('.form-check-input');
        if (switchInput) {
            switchInput.addEventListener('change', () => {
                if (switchInput.checked) {
                    tarjeta.classList.add('border-primary', 'bg-primary-subtle');
                } else {
                    tarjeta.classList.remove('border-primary', 'bg-primary-subtle');
                }
            });
        }
    });

    // ==========================================================
    // 4. CONTROL DE CRITICIDAD (Sección 4)
    // ==========================================================
    const radiosCriticidad = document.querySelectorAll('.radio-criticidad');
    radiosCriticidad.forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.tarjeta-criticidad').forEach(card => {
                card.classList.remove('shadow', 'bg-light');
            });
            const cardActiva = radio.closest('.tarjeta-criticidad');
            if (cardActiva) {
                cardActiva.classList.add('shadow', 'bg-light');
            }
        });
    });

    // ==========================================================
    // 5. SWITCH DE SOLUCIÓN INMEDIATA / CIERRE (Sección 6)
    // ==========================================================
    const switchSolucion = document.getElementById('switch_solucion_inmediata');
    const selectEstado = document.getElementById('estado');
    const inputFechaCierre = document.getElementById('fecha_hora_cierre');
    const badgeFirmaSistemas = document.getElementById('badgeFirmaSistemasRI');

    if (switchSolucion) {
        switchSolucion.addEventListener('change', () => {
            if (switchSolucion.checked) {
                if (selectEstado) selectEstado.value = 'resuelta';
                if (inputFechaCierre && !inputFechaCierre.value) {
                    const ahora = new Date();
                    const anio = ahora.getFullYear();
                    const mes = String(ahora.getMonth() + 1).padStart(2, '0');
                    const dia = String(ahora.getDate()).padStart(2, '0');
                    const horas = String(ahora.getHours()).padStart(2, '0');
                    const minutos = String(ahora.getMinutes()).padStart(2, '0');
                    inputFechaCierre.value = `${anio}-${mes}-${dia}T${horas}:${minutos}`;
                }
                if (badgeFirmaSistemas && !controlFirmaSistemas.estaFirmado()) {
                    badgeFirmaSistemas.className = 'badge bg-danger-subtle text-danger border';
                    badgeFirmaSistemas.textContent = 'Requerido para Cierre';
                }
            } else {
                if (selectEstado && (selectEstado.value === 'resuelta' || selectEstado.value === 'cerrada')) {
                    selectEstado.value = 'abierta';
                }
                if (badgeFirmaSistemas && !controlFirmaSistemas.estaFirmado()) {
                    badgeFirmaSistemas.className = 'badge bg-secondary-subtle text-secondary border';
                    badgeFirmaSistemas.textContent = 'Opcional / Cierre';
                }
            }
        });
    }

    if (selectEstado) {
        selectEstado.addEventListener('change', () => {
            const esCierre = selectEstado.value === 'resuelta' || selectEstado.value === 'cerrada';
            if (switchSolucion) switchSolucion.checked = esCierre;
            if (badgeFirmaSistemas && !controlFirmaSistemas.estaFirmado()) {
                if (esCierre) {
                    badgeFirmaSistemas.className = 'badge bg-danger-subtle text-danger border';
                    badgeFirmaSistemas.textContent = 'Requerido para Cierre';
                } else {
                    badgeFirmaSistemas.className = 'badge bg-secondary-subtle text-secondary border';
                    badgeFirmaSistemas.textContent = 'Opcional / Cierre';
                }
            }
        });
    }

    // ==========================================================
    // 6. VALIDACIÓN Y ENVÍO DEL FORMULARIO
    // ==========================================================
    function mostrarErrores(mensajes) {
        if (!alertaValidacion) return;
        alertaValidacion.innerHTML = `
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-danger"></i>
                <strong>Por favor corrija los siguientes campos antes de continuar:</strong>
            </div>
            <ul class="mb-0 ps-3">
                ${mensajes.map(m => `<li>${m}</li>`).join('')}
            </ul>
        `;
        alertaValidacion.classList.remove('d-none');
        alertaValidacion.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function ocultarErrores() {
        if (!alertaValidacion) return;
        alertaValidacion.classList.add('d-none');
        alertaValidacion.innerHTML = '';
    }

    formRI.addEventListener('submit', async (e) => {
        e.preventDefault();
        ocultarErrores();

        const errores = [];

        // 1. Datos Generales
        const nroIncidencia = document.getElementById('nro_incidencia')?.value.trim() || '';
        if (!nroIncidencia) {
            errores.push('El campo "Nro. de Incidencia" es obligatorio.');
        }

        const responsableReporte = document.getElementById('responsable_reporte')?.value.trim() || '';
        if (!responsableReporte) {
            errores.push('El campo "Responsable del Reporte" es obligatorio.');
        }

        const fechaHoraReporte = document.getElementById('fecha_hora_reporte')?.value || '';
        if (!fechaHoraReporte) {
            errores.push('La "Fecha y Hora del Reporte" es obligatoria.');
        }

        // 2. Plataforma / Sistema
        const erpSai = document.getElementById('sistema_erp_sai')?.checked || false;
        const netcob = document.getElementById('sistema_cobranzas_netcob')?.checked || false;
        const otrosSistemas = document.getElementById('sistema_otros')?.checked || false;
        const otrosDetalle = document.getElementById('sistema_otros_detalle')?.value.trim() || '';

        if (!erpSai && !netcob && !otrosSistemas) {
            errores.push('Debe seleccionar al menos una Plataforma o Sistema afectado en la Sección 2.');
        }

        if (otrosSistemas && !otrosDetalle) {
            errores.push('Debe especificar el nombre o detalle en "Otros Sistemas / Complementos".');
        }

        // 3. Naturaleza Técnica del Fallo
        const falloBD = document.getElementById('fallo_base_datos')?.checked || false;
        const detalleBD = document.getElementById('detalle_base_datos')?.value.trim() || '';
        const falloServidor = document.getElementById('fallo_infraestructura_servidor')?.checked || false;
        const detalleServidor = document.getElementById('detalle_infraestructura_servidor')?.value.trim() || '';
        const falloRed = document.getElementById('fallo_enlaces_conectividad')?.checked || false;
        const detalleRed = document.getElementById('detalle_enlaces_conectividad')?.value.trim() || '';

        if (!falloBD && !falloServidor && !falloRed) {
            errores.push('Debe marcar al menos un componente en la Sección 3 (Naturaleza Técnica del Fallo).');
        }

        if (falloBD && !detalleBD) {
            errores.push('Ha marcado "Base de Datos", por favor ingrese la descripción o tipo de error.');
        }
        if (falloServidor && !detalleServidor) {
            errores.push('Ha marcado "Infraestructura / Servidor", por favor ingrese la descripción o tipo de error.');
        }
        if (falloRed && !detalleRed) {
            errores.push('Ha marcado "Enlaces / Conectividad", por favor ingrese la descripción o tipo de error.');
        }

        // 4. Criticidad
        let criticidadSeleccionada = '';
        radiosCriticidad.forEach(r => {
            if (r.checked) criticidadSeleccionada = r.value;
        });
        if (!criticidadSeleccionada) {
            errores.push('Debe seleccionar un Nivel de Criticidad Institucional (Nivel 1, 2 o 3).');
        }

        // 5. Descripción y Logs
        const descripcionLogs = document.getElementById('descripcion_tecnica_logs')?.value.trim() || '';
        if (!descripcionLogs) {
            errores.push('La "Descripción Técnica y Logs de Error" es obligatoria.');
        }

        // 6. Solución / Cierre si aplica
        const estadoActual = selectEstado ? selectEstado.value : 'abierta';
        const accionRealizada = document.getElementById('accion_realizada')?.value.trim() || '';
        const detalleTecnico = document.getElementById('detalle_tecnico')?.value.trim() || '';
        const fechaHoraCierre = inputFechaCierre?.value || '';

        if ((estadoActual === 'resuelta' || estadoActual === 'cerrada') && !accionRealizada) {
            errores.push('Si marca el reporte como Resuelto o Cerrado, la "Acción Realizada" es obligatoria.');
        }

        // 7. Firmas Digitales
        if (!controlFirmaResponsable || !controlFirmaResponsable.estaFirmado()) {
            errores.push('La firma digital del Responsable del Reporte es obligatoria.');
        }

        if ((estadoActual === 'resuelta' || estadoActual === 'cerrada') && (!controlFirmaSistemas || !controlFirmaSistemas.estaFirmado())) {
            errores.push('La firma digital del Dpto. de Sistemas es obligatoria para cerrar o resolver la incidencia.');
        }

        if (errores.length > 0) {
            mostrarErrores(errores);
            return;
        }

        // Armar objeto de datos
        const payload = {
            nro_incidencia: document.getElementById('nro_incidencia')?.value.trim() || '',
            fecha_hora_reporte: fechaHoraReporte,
            responsable_reporte: responsableReporte,
            sistema_erp_sai: erpSai,
            sistema_cobranzas_netcob: netcob,
            sistema_otros: otrosSistemas,
            sistema_otros_detalle: otrosSistemas ? otrosDetalle : null,
            fallo_base_datos: falloBD,
            detalle_base_datos: falloBD ? detalleBD : null,
            fallo_infraestructura_servidor: falloServidor,
            detalle_infraestructura_servidor: falloServidor ? detalleServidor : null,
            fallo_enlaces_conectividad: falloRed,
            detalle_enlaces_conectividad: falloRed ? detalleRed : null,
            nivel_criticidad: criticidadSeleccionada,
            estado: estadoActual,
            descripcion_tecnica_logs: descripcionLogs,
            accion_realizada: accionRealizada || null,
            detalle_tecnico: detalleTecnico || null,
            fecha_hora_cierre: fechaHoraCierre || null,
            observaciones_recomendaciones: document.getElementById('observaciones_recomendaciones')?.value.trim() || null,
            firma_responsable_reporte: controlFirmaResponsable.obtenerBase64(),
            firma_sistemas: controlFirmaSistemas.obtenerBase64()
        };

        // Estado de carga en botón
        btnGuardar.disabled = true;
        const textoOriginal = btnGuardar.innerHTML;
        btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Guardando Incidencia...';

        try {
            const respuesta = await clienteApi.crearIncidencia(payload);

            if (respuesta.exito) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Incidencia Registrada!',
                    html: `
                        <div class="text-center">
                            <p class="mb-2">El reporte institucional se ha generado exitosamente.</p>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-6 px-3 py-2 font-monospace">
                                Nro: ${respuesta.datos.nro_incidencia}
                            </span>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-card-checklist me-1"></i> Ir a Bandeja de Incidencias',
                    cancelButtonText: '<i class="bi bi-plus-circle me-1"></i> Nuevo Reporte',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d'
                }).then((resultado) => {
                    if (resultado.isConfirmed) {
                        window.location.href = 'gestion_incidencias.php';
                    } else {
                        formRI.reset();
                        controlFirmaResponsable.limpiar();
                        controlFirmaSistemas.limpiar();
                        checkboxesNaturaleza.forEach(ch => {
                            ch.checked = false;
                            ch.dispatchEvent(new Event('change'));
                        });
                        tarjetasSeleccion.forEach(t => t.classList.remove('border-primary', 'bg-primary-subtle'));
                        document.querySelectorAll('.tarjeta-criticidad').forEach(c => c.classList.remove('shadow', 'bg-light'));
                        if (contenedorOtrosDetalle) contenedorOtrosDetalle.classList.add('d-none');
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                });
            } else {
                mostrarErrores(respuesta.errores || [respuesta.mensaje || 'Error desconocido al registrar la incidencia.']);
            }
        } catch (error) {
            console.error('Error al enviar formulario:', error);
            mostrarErrores([error.message || 'No fue posible comunicarse con el servidor.']);
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = textoOriginal;
        }
    });

    // Botón restablecer
    if (btnRestablecer) {
        btnRestablecer.addEventListener('click', () => {
            setTimeout(() => {
                controlFirmaResponsable.limpiar();
                controlFirmaSistemas.limpiar();
                checkboxesNaturaleza.forEach(ch => {
                    ch.checked = false;
                    ch.dispatchEvent(new Event('change'));
                });
                tarjetasSeleccion.forEach(t => t.classList.remove('border-primary', 'bg-primary-subtle'));
                document.querySelectorAll('.tarjeta-criticidad').forEach(c => c.classList.remove('shadow', 'bg-light'));
                if (contenedorOtrosDetalle) contenedorOtrosDetalle.classList.add('d-none');
                ocultarErrores();
            }, 50);
        });
    }
});
