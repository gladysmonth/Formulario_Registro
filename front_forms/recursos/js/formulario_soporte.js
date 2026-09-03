/**
 * Lógica y Validaciones del Formulario de Soporte Técnico
 * Archivo: formulario_soporte.js
 */

document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.getElementById('formularioSoporteTecnico');
    const inputFecha = document.getElementById('fecha_solicitud');
    const checkHardware = document.getElementById('soporte_hardware');
    const checkSoftware = document.getElementById('soporte_software');
    const alertaValidacion = document.getElementById('alertaValidacion');
    const btnGuardar = document.getElementById('btnGuardarTicket');

    // Asignar fecha actual por defecto si está vacío
    if (inputFecha && !inputFecha.value) {
        const hoy = new Date().toISOString().split('T')[0];
        inputFecha.value = hoy;
    }

    // Toggle de sección técnica opcional
    const switchSeccionTecnica = document.getElementById('switchSeccionTecnica');
    const seccionTecnicaContenedor = document.getElementById('seccionTecnicaContenedor');

    if (switchSeccionTecnica && seccionTecnicaContenedor) {
        switchSeccionTecnica.addEventListener('change', (e) => {
            if (e.target.checked) {
                seccionTecnicaContenedor.classList.remove('d-none');
                seccionTecnicaContenedor.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                seccionTecnicaContenedor.classList.add('d-none');
            }
        });
    }

    if (formulario) {
        formulario.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Validar casillas de tipo de soporte
            const tieneHardware = checkHardware ? checkHardware.checked : false;
            const tieneSoftware = checkSoftware ? checkSoftware.checked : false;

            if (!tieneHardware && !tieneSoftware) {
                if (alertaValidacion) {
                    alertaValidacion.textContent = 'Debe seleccionar al menos una opción en Tipo de Soporte Requerido (Hardware o Software).';
                    alertaValidacion.classList.remove('d-none');
                    alertaValidacion.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            } else {
                if (alertaValidacion) alertaValidacion.classList.add('d-none');
            }

            // Recoger datos del formulario
            const formData = new FormData(formulario);
            const datos = {
                nombre_solicitante: formData.get('nombre_solicitante'),
                fecha_solicitud: formData.get('fecha_solicitud'),
                departamento_area: formData.get('departamento_area'),
                soporte_hardware: tieneHardware,
                soporte_software: tieneSoftware,
                descripcion_problema: formData.get('descripcion_problema'),
                numero_serie: formData.get('numero_serie'),
                marca_modelo: formData.get('marca_modelo'),
                sistema_operativo: formData.get('sistema_operativo'),
                prioridad: formData.get('prioridad') || 'media',
                // Campos de técnico (si fueron completados)
                tecnico_asignado: formData.get('tecnico_asignado'),
                fecha_hora_atencion: formData.get('fecha_hora_atencion'),
                diagnostico: formData.get('diagnostico'),
                solucion_aplicada: formData.get('solucion_aplicada'),
                tipo_resolucion: formData.get('tipo_resolucion'),
                observaciones_recomendacion: formData.get('observaciones_recomendacion')
            };

            // Deshabilitar botón durante el envío
            const textoOriginalBtn = btnGuardar ? btnGuardar.innerHTML : 'Guardar';
            if (btnGuardar) {
                btnGuardar.disabled = true;
                btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Registrando ticket...';
            }

            try {
                const respuesta = await clienteApi.crearTicketSoporte(datos);

                if (respuesta.exito) {
                    const ticket = respuesta.datos;
                    
                    Swal.fire({
                        title: '¡Ticket Registrado con Éxito!',
                        html: `
                            <div class="text-start p-3 bg-light rounded-3 my-2 border">
                                <p class="mb-1"><strong>Código de Ticket:</strong> <span class="badge bg-primary fs-6">${ticket.codigo_ticket}</span></p>
                                <p class="mb-1"><strong>Estado Inicial:</strong> <span class="badge bg-warning text-dark">${ticket.estado.toUpperCase()}</span></p>
                                <p class="mb-0 text-muted small">Conserve este código para hacer seguimiento a la atención técnica.</p>
                            </div>
                        `,
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonText: '<i class="bi bi-kanban me-1"></i> Ir al Panel de Gestión',
                        cancelButtonText: '<i class="bi bi-plus-circle me-1"></i> Registrar Otro Ticket',
                        confirmButtonColor: '#0d6efd',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'gestion_tickets.php';
                        } else {
                            formulario.reset();
                            if (inputFecha) inputFecha.value = new Date().toISOString().split('T')[0];
                            if (switchSeccionTecnica) switchSeccionTecnica.checked = false;
                            if (seccionTecnicaContenedor) seccionTecnicaContenedor.classList.add('d-none');
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    });
                }
            } catch (error) {
                let mensajeError = error.message || 'No se pudo conectar con el servicio backend.';
                if (error.detalles && Array.isArray(error.detalles)) {
                    mensajeError = error.detalles.join('<br>');
                }

                Swal.fire({
                    title: 'Error al Registrar',
                    html: mensajeError,
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
});
