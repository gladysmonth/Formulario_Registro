/**
 * Lógica del Formulario de Registro de Mantenimiento Preventivo (PC / Laptop)
 * Archivo: formulario_mantenimiento.js
 */

document.addEventListener('DOMContentLoaded', () => {
    const formMP = document.getElementById('formMantenimientoPreventivo');
    const alertaValidacion = document.getElementById('alertaValidacionMP');
    const btnGuardar = document.getElementById('btnGuardarMP');
    const btnCancelar = document.getElementById('btnCancelarMP');

    // Elementos de búsqueda de equipo
    const inputBuscarEquipo = document.getElementById('buscar_equipo_mp');
    const dataListEquipos = document.getElementById('listaEquiposMP');
    const badgeEstadoEquipo = document.getElementById('badgeEstadoEquipoMP');
    const btnLimpiarEquipo = document.getElementById('btnLimpiarEquipoMP');

    let catalogoEquipos = [];

    // ==========================================================
    // 1. CONTROL DE LIENZOS DE FIRMA DIGITAL
    // ==========================================================
    function inicializarLienzoFirma(canvasId, badgeId, btnLimpiarId) {
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
                canvas.width = rect.width;
                canvas.height = 150;
                ctx.lineWidth = 2.5;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.strokeStyle = '#0f172a';
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

        // Mouse events
        canvas.addEventListener('mousedown', iniciarDibujo);
        canvas.addEventListener('mousemove', trazar);
        window.addEventListener('mouseup', terminarDibujo);

        // Touch events
        canvas.addEventListener('touchstart', iniciarDibujo, { passive: false });
        canvas.addEventListener('touchmove', trazar, { passive: false });
        canvas.addEventListener('touchend', terminarDibujo);
        canvas.addEventListener('touchcancel', terminarDibujo);

        // Botón limpiar
        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                tieneFirma = false;
                if (badge) {
                    badge.className = 'badge bg-danger-subtle text-danger border';
                    badge.textContent = 'Pendiente';
                }
            });
        }

        return {
            tieneFirma: () => tieneFirma,
            obtenerBase64: () => tieneFirma ? canvas.toDataURL('image/png') : null,
            limpiar: () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                tieneFirma = false;
                if (badge) {
                    badge.className = 'badge bg-danger-subtle text-danger border';
                    badge.textContent = 'Pendiente';
                }
            }
        };
    }

    const firmaResponsable = inicializarLienzoFirma('canvasFirmaResponsable', 'badgeFirmaResponsable', 'btnLimpiarFirmaResponsable');
    const firmaSistemas = inicializarLienzoFirma('canvasFirmaSistemasMP', 'badgeFirmaSistemasMP', 'btnLimpiarFirmaSistemasMP');

    // ==========================================================
    // 2. AUTOCOMPLETADO DESDE CATÁLOGO DE INVENTARIO
    // ==========================================================
    async function precargarEquipos() {
        try {
            const res = await clienteApi.buscarEquipos('');
            if (res.exito && Array.isArray(res.datos)) {
                catalogoEquipos = res.datos;
                if (dataListEquipos) {
                    dataListEquipos.innerHTML = '';
                    catalogoEquipos.forEach(eq => {
                        const opt = document.createElement('option');
                        opt.value = `${eq.codigo_activo || eq.numero_serie || ''} - ${eq.marca_modelo || eq.tipo_equipo || ''}`;
                        opt.setAttribute('data-id', eq.id);
                        dataListEquipos.appendChild(opt);
                    });
                }
            }
        } catch (e) {
            // Catálogo aún vacío o backend iniciando
        }
    }

    precargarEquipos();

    if (inputBuscarEquipo) {
        inputBuscarEquipo.addEventListener('input', (e) => {
            const texto = e.target.value.trim().toLowerCase();
            if (!texto) return;

            const eqEncontrado = catalogoEquipos.find(eq => {
                const cod = (eq.codigo_activo || '').toLowerCase();
                const mod = (eq.marca_modelo || '').toLowerCase();
                const ser = (eq.numero_serie || '').toLowerCase();
                return (cod && texto.includes(cod)) || (mod && texto.includes(mod)) || (ser && texto.includes(ser));
            });

            if (eqEncontrado) {
                seleccionarEquipo(eqEncontrado);
            }
        });
    }

    function seleccionarEquipo(eq) {
        const inputEquipoId = document.getElementById('equipo_id');
        const inputCodActivo = document.getElementById('codigo_activo');
        const inputMarcaMod = document.getElementById('marca_modelo');
        const inputSO = document.getElementById('sistema_operativo');
        const inputUbicacion = document.getElementById('ubicacion_equipo');

        if (inputEquipoId) inputEquipoId.value = eq.id;
        if (inputCodActivo) inputCodActivo.value = eq.codigo_activo || '';
        if (inputMarcaMod) inputMarcaMod.value = eq.marca_modelo || '';
        if (inputSO) inputSO.value = eq.sistema_operativo || '';
        const ubicacionVal = eq.area || eq.area_encargado || '';
        if (inputUbicacion && ubicacionVal && !inputUbicacion.value) {
            inputUbicacion.value = ubicacionVal;
        }

        // Seleccionar tipo de equipo (PC o LAPTOP)
        if (eq.tipo_equipo) {
            const tipo = eq.tipo_equipo.toUpperCase();
            if (tipo.includes('LAPTOP')) {
                const radioLaptop = document.getElementById('tipo_laptop');
                if (radioLaptop) radioLaptop.checked = true;
            } else {
                const radioPC = document.getElementById('tipo_pc');
                if (radioPC) radioPC.checked = true;
            }
        }

        if (badgeEstadoEquipo) {
            badgeEstadoEquipo.className = 'badge bg-success-subtle text-success border';
            badgeEstadoEquipo.textContent = 'Vinculado a Inventario';
        }
        if (btnLimpiarEquipo) btnLimpiarEquipo.classList.remove('d-none');
    }

    if (btnLimpiarEquipo) {
        btnLimpiarEquipo.addEventListener('click', () => {
            const inputEquipoId = document.getElementById('equipo_id');
            if (inputEquipoId) inputEquipoId.value = '';
            if (inputBuscarEquipo) inputBuscarEquipo.value = '';
            if (badgeEstadoEquipo) {
                badgeEstadoEquipo.className = 'badge bg-secondary-subtle text-secondary border';
                badgeEstadoEquipo.textContent = 'Nuevo / No vinculado';
            }
            btnLimpiarEquipo.classList.add('d-none');
        });
    }

    // ==========================================================
    // 3. ENVÍO DEL FORMULARIO DE MANTENIMIENTO
    // ==========================================================
    if (formMP) {
        formMP.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Validar Firmas Obligatorias
            if (!firmaResponsable || !firmaResponsable.tieneFirma()) {
                mostrarAlerta('La firma digital del Responsable del Equipo es obligatoria. Firme en el recuadro blanco de RESPONSABLE DEL EQUIPO.');
                const c1 = document.getElementById('canvasFirmaResponsable');
                if (c1) c1.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            if (!firmaSistemas || !firmaSistemas.tieneFirma()) {
                mostrarAlerta('La firma digital del Dpto. de Sistemas es obligatoria. Firme en el recuadro blanco de DPTO. DE SISTEMAS.');
                const c2 = document.getElementById('canvasFirmaSistemasMP');
                if (c2) c2.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            ocultarAlerta();

            const formData = new FormData(formMP);

            // Armar objeto de datos completo
            const datos = {
                // 1. Datos Generales
                tecnico_responsable: formData.get('tecnico_responsable'),
                fecha_mantenimiento: formData.get('fecha_mantenimiento'),
                ubicacion_equipo: formData.get('ubicacion_equipo'),

                // 2. Información del Equipo
                equipo_id: formData.get('equipo_id') || null,
                tipo_equipo: formData.get('tipo_equipo'),
                tipo_red: formData.get('tipo_red'),
                codigo_activo: formData.get('codigo_activo'),
                nombre_equipo: formData.get('nombre_equipo'),
                marca_modelo: formData.get('marca_modelo'),
                sistema_operativo: formData.get('sistema_operativo'),
                procesador: formData.get('procesador'),
                memoria_ram: formData.get('memoria_ram'),
                almacenamiento: formData.get('almacenamiento'),
                direccion_ip: formData.get('direccion_ip'),

                // 3. Mantenimiento Externo
                limpieza_carcasa_componentes: document.getElementById('limpieza_carcasa_componentes').checked,
                limpieza_pantalla_teclado: document.getElementById('limpieza_pantalla_teclado').checked,
                verificacion_conectores: document.getElementById('verificacion_conectores').checked,
                limpieza_otros: formData.get('limpieza_otros'),

                // 4. Mantenimiento Interno
                actualizacion_so: document.getElementById('actualizacion_so').checked,
                eliminacion_temporales: document.getElementById('eliminacion_temporales').checked,
                desfragmentacion_optimizacion: document.getElementById('desfragmentacion_optimizacion').checked,
                escaneo_antivirus: document.getElementById('escaneo_antivirus').checked,
                verificacion_drivers: document.getElementById('verificacion_drivers').checked,
                copia_seguridad: document.getElementById('copia_seguridad').checked,
                mantenimiento_interno_otros: formData.get('mantenimiento_interno_otros'),

                // 5. Verificación de Funcionamiento
                verificacion_encendido_apagado: document.getElementById('verificacion_encendido_apagado').checked,
                verificacion_rendimiento: document.getElementById('verificacion_rendimiento').checked,
                verificacion_red: document.getElementById('verificacion_red').checked,
                verificacion_perifericos: document.getElementById('verificacion_perifericos').checked,
                verificacion_temperatura_anomalias: document.getElementById('verificacion_temperatura_anomalias').checked,

                // 6. Observaciones
                observaciones_incidencias: formData.get('observaciones_incidencias'),

                // 7. Firmas
                firma_responsable_equipo: firmaResponsable.obtenerBase64(),
                firma_sistemas: firmaSistemas.obtenerBase64()
            };

            const textoOriginalBtn = btnGuardar ? btnGuardar.innerHTML : 'Guardar';
            if (btnGuardar) {
                btnGuardar.disabled = true;
                btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Registrando mantenimiento...';
            }

            try {
                const res = await clienteApi.crearMantenimientoPreventivo(datos);

                if (res.exito) {
                    Swal.fire({
                        title: '¡Mantenimiento Registrado con Éxito!',
                        html: `
                            <div class="text-start p-3 bg-light rounded-3 my-2 border">
                                <p class="mb-1"><strong>Código de Ficha:</strong> <span class="badge bg-primary fs-6">${res.datos.codigo_mantenimiento}</span></p>
                                <p class="mb-1 text-success"><i class="bi bi-shield-check me-1"></i> Doble firma digital certificada</p>
                                <p class="mb-0 text-muted small">El registro técnico ha sido guardado de forma permanente en el historial del equipo.</p>
                            </div>
                        `,
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonText: '<i class="bi bi-card-checklist me-1"></i> Ver Bandeja de Registros',
                        cancelButtonText: '<i class="bi bi-plus-circle me-1"></i> Registrar Otro Mantenimiento',
                        confirmButtonColor: '#0d6efd',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'gestion_mantenimientos.php';
                        } else {
                            formMP.reset();
                            if (firmaResponsable) firmaResponsable.limpiar();
                            if (firmaSistemas) firmaSistemas.limpiar();
                            const inputFecha = document.getElementById('fecha_mantenimiento');
                            if (inputFecha) inputFecha.value = new Date().toISOString().split('T')[0];
                            if (btnLimpiarEquipo) btnLimpiarEquipo.click();
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    });
                }
            } catch (err) {
                let msg = err.message || 'Error al guardar el registro de mantenimiento.';
                if (err.detalles && Array.isArray(err.detalles)) {
                    msg = err.detalles.join('<br>');
                }
                Swal.fire({
                    title: 'Error de Validación',
                    html: msg,
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            } finally {
                if (btnGuardar) {
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = textoOriginalBtn;
                }
            }
        });
    }

    if (btnCancelar) {
        btnCancelar.addEventListener('click', () => {
            if (firmaResponsable) firmaResponsable.limpiar();
            if (firmaSistemas) firmaSistemas.limpiar();
            ocultarAlerta();
        });
    }

    function mostrarAlerta(msg) {
        if (alertaValidacion) {
            alertaValidacion.textContent = msg;
            alertaValidacion.classList.remove('d-none');
            alertaValidacion.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function ocultarAlerta() {
        if (alertaValidacion) alertaValidacion.classList.add('d-none');
    }
});
