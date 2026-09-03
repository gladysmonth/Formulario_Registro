<?php
/**
 * Vista: Formulario de Registro de Soporte Técnico (6 Secciones)
 * Compatible con PHP 7.3
 * Archivo: formulario_soporte.php
 */

$titulo_pagina = 'Formulario de Registro - Soporte Técnico';
$pagina_activa = 'soporte_formulario';
$nivel_ruta = '../../';

require_once __DIR__ . '/../../componentes/encabezado.php';
require_once __DIR__ . '/../../componentes/barra_navegacion.php';
?>

<main class="container my-4 my-lg-5">
    <!-- Migas de Pan (Breadcrumbs) -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="../../index.php" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item text-muted">Soporte Técnico</li>
            <li class="breadcrumb-item active" aria-current="page">Nuevo Formulario de Registro</li>
        </ol>
    </nav>

    <!-- Encabezado del Formulario -->
    <div class="tarjeta-formulario p-4 mb-4 border-start border-4 border-primary">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-primary-subtle text-primary border px-3 py-2 rounded-pill fw-semibold mb-2">
                    <i class="bi bi-shield-check me-1"></i> Formulario N° 1
                </span>
                <h1 class="h3 fw-bold text-dark mb-1">Formulario de Registro: Soporte Técnico</h1>
                <p class="text-muted small mb-0">
                    Complete los datos del requerimiento o incidente técnico. Los campos marcados con <span class="text-danger">*</span> son obligatorios.
                </p>
            </div>
            <div>
                <a href="gestion_tickets.php" class="btn btn-outline-primary btn-sm px-3 py-2">
                    <i class="bi bi-kanban me-1"></i> Ver Panel de Tickets
                </a>
            </div>
        </div>
    </div>

    <!-- Alerta de Validación Dinámica -->
    <div id="alertaValidacion" class="alert alert-danger d-none shadow-sm" role="alert"></div>

    <!-- FORMULARIO PRINCIPAL -->
    <form id="formularioSoporteTecnico" novalidate>
        <div class="row g-4">
            
            <!-- SECCIÓN 1: DATOS GENERALES -->
            <div class="col-12">
                <div class="tarjeta-formulario p-4">
                    <div class="encabezado-seccion d-flex align-items-center">
                        <span class="numero-seccion">1</span>
                        <div>
                            <h2 class="h5 fw-bold mb-0">Datos Generales</h2>
                            <small class="text-muted">Información básica del solicitante y área de origen</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-5">
                            <label for="nombre_solicitante" class="form-label fw-semibold">
                                Nombre del Solicitante <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="nombre_solicitante" name="nombre_solicitante" placeholder="Ej: Juan Pérez Martínez" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="fecha_solicitud" class="form-label fw-semibold">
                                Fecha de Registro <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary"><i class="bi bi-calendar3"></i></span>
                                <input type="date" class="form-control" id="fecha_solicitud" name="fecha_solicitud" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="departamento_area" class="form-label fw-semibold">
                                Departamento / Área <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary"><i class="bi bi-building"></i></span>
                                <input type="text" class="form-control" id="departamento_area" name="departamento_area" list="listaDepartamentos" placeholder="Seleccione o escriba..." required>
                            </div>
                            <datalist id="listaDepartamentos">
                                <option value="Administración">
                                <option value="Contabilidad y Finanzas">
                                <option value="Recursos Humanos">
                                <option value="Sistemas / TI">
                                <option value="Comercial y Ventas">
                                <option value="Operaciones y Logística">
                                <option value="Atención al Cliente">
                                <option value="Gerencia General">
                            </datalist>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 2: TIPO DE SOPORTE REQUERIDO -->
            <div class="col-12">
                <div class="tarjeta-formulario p-4">
                    <div class="encabezado-seccion d-flex align-items-center">
                        <span class="numero-seccion">2</span>
                        <div>
                            <h2 class="h5 fw-bold mb-0">Tipo de Soporte Requerido</h2>
                            <small class="text-muted">Marque lo que corresponda (puede marcar una o ambas casillas)</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 bg-light-subtle h-100">
                                <div class="form-check form-switch fs-6">
                                    <input class="form-check-input" type="checkbox" role="switch" id="soporte_hardware" name="soporte_hardware" value="1">
                                    <label class="form-check-label fw-semibold" for="soporte_hardware">
                                        <i class="bi bi-pc-display text-primary me-1"></i> Hardware
                                    </label>
                                </div>
                                <p class="text-muted small mb-0 mt-2 ps-4">
                                    Equipos de cómputo, monitores, impresoras, teclado, mouse, cableado o periféricos físicos.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 bg-light-subtle h-100">
                                <div class="form-check form-switch fs-6">
                                    <input class="form-check-input" type="checkbox" role="switch" id="soporte_software" name="soporte_software" value="1">
                                    <label class="form-check-label fw-semibold" for="soporte_software">
                                        <i class="bi bi-code-square text-info me-1"></i> Software
                                    </label>
                                </div>
                                <p class="text-muted small mb-0 mt-2 ps-4">
                                    Aplicaciones, sistema operativo, correo electrónico, accesos, paquetería de oficina o errores de sistema.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 3: DETALLE DEL PROBLEMA / REQUERIMIENTO -->
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

                    <!-- Sub-bloque: Equipo Afectado (Si aplica) -->
                    <div class="bloque-equipo-afectado">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-laptop text-secondary fs-5"></i>
                            <h3 class="h6 fw-bold mb-0 text-dark">Equipo Afectado <span class="fw-normal text-muted small">(Opcional / Si aplica)</span></h3>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="numero_serie" class="form-label small fw-medium">Número de Serie / Inventario</label>
                                <input type="text" class="form-control form-control-sm" id="numero_serie" name="numero_serie" placeholder="Ej: SN-49204 / INV-004">
                            </div>
                            <div class="col-md-4">
                                <label for="marca_modelo" class="form-label small fw-medium">Marca / Modelo</label>
                                <input type="text" class="form-control form-control-sm" id="marca_modelo" name="marca_modelo" placeholder="Ej: Dell OptiPlex 7080 / HP ProBook">
                            </div>
                            <div class="col-md-4">
                                <label for="sistema_operativo" class="form-label small fw-medium">Sistema Operativo</label>
                                <input type="text" class="form-control form-control-sm" id="sistema_operativo" name="sistema_operativo" list="listaSistemas" placeholder="Ej: Windows 11 Pro">
                                <datalist id="listaSistemas">
                                    <option value="Windows 11 Pro">
                                    <option value="Windows 10 Pro">
                                    <option value="Windows Server 2022">
                                    <option value="macOS Sonoma">
                                    <option value="Ubuntu Linux 22.04">
                                </datalist>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- INTERRUPTOR PARA ACTIVAR SECCIONES 4, 5 Y 6 (SOPORTE TÉCNICO) -->
            <div class="col-12">
                <div class="p-3 bg-white border rounded-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 bg-info-subtle text-info rounded-circle">
                            <i class="bi bi-person-badge fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">¿Llenar campos de Soporte Técnico inmediatamente?</div>
                            <small class="text-muted">Si eres técnico o cuentas con los datos de diagnóstico/solución, activa este interruptor para llenar las secciones 4, 5 y 6.</small>
                        </div>
                    </div>
                    <div class="form-check form-switch fs-5">
                        <input class="form-check-input" type="checkbox" id="switchSeccionTecnica" role="switch">
                    </div>
                </div>
            </div>

            <!-- CONTENEDOR COLAPSABLE: SECCIONES 4, 5 Y 6 -->
            <div id="seccionTecnicaContenedor" class="col-12 d-none">
                <div class="row g-4">
                    
                    <!-- SECCIÓN 4: PRIORIDAD -->
                    <div class="col-12">
                        <div class="tarjeta-formulario p-4">
                            <div class="encabezado-seccion d-flex align-items-center">
                                <span class="numero-seccion numero-seccion-tecnico">4</span>
                                <div>
                                    <h2 class="h5 fw-bold mb-0">Prioridad</h2>
                                    <small class="text-muted">A definir por el área de soporte según impacto operativo</small>
                                </div>
                            </div>

                            <div class="row g-3">
                                <!-- Urgente -->
                                <div class="col-md-3 col-sm-6">
                                    <input type="radio" class="btn-check" name="prioridad" id="prio_urgente" value="urgente" autocomplete="off">
                                    <label class="btn btn-outline-danger w-100 text-start p-3 h-100 rounded-3" for="prio_urgente">
                                        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Urgente</div>
                                        <div class="small opacity-75">Afecta operaciones críticas del negocio.</div>
                                    </label>
                                </div>

                                <!-- Alta -->
                                <div class="col-md-3 col-sm-6">
                                    <input type="radio" class="btn-check" name="prioridad" id="prio_alta" value="alta" autocomplete="off">
                                    <label class="btn btn-outline-warning text-dark w-100 text-start p-3 h-100 rounded-3" for="prio_alta">
                                        <div class="fw-bold mb-1"><i class="bi bi-arrow-up-circle-fill me-1"></i> Alta</div>
                                        <div class="small opacity-75">Afecta productividad de forma significativa.</div>
                                    </label>
                                </div>

                                <!-- Media -->
                                <div class="col-md-3 col-sm-6">
                                    <input type="radio" class="btn-check" name="prioridad" id="prio_media" value="media" checked autocomplete="off">
                                    <label class="btn btn-outline-primary w-100 text-start p-3 h-100 rounded-3" for="prio_media">
                                        <div class="fw-bold mb-1"><i class="bi bi-dash-circle-fill me-1"></i> Media</div>
                                        <div class="small opacity-75">Molestia operativa pero no detiene el trabajo.</div>
                                    </label>
                                </div>

                                <!-- Baja -->
                                <div class="col-md-3 col-sm-6">
                                    <input type="radio" class="btn-check" name="prioridad" id="prio_baja" value="baja" autocomplete="off">
                                    <label class="btn btn-outline-success w-100 text-start p-3 h-100 rounded-3" for="prio_baja">
                                        <div class="fw-bold mb-1"><i class="bi bi-arrow-down-circle-fill me-1"></i> Baja</div>
                                        <div class="small opacity-75">Requerimiento rutinario o de baja criticidad.</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 5: DATOS DEL TÉCNICO -->
                    <div class="col-12">
                        <div class="tarjeta-formulario p-4 bloque-tecnico">
                            <div class="encabezado-seccion d-flex align-items-center">
                                <span class="numero-seccion numero-seccion-tecnico">5</span>
                                <div>
                                    <h2 class="h5 fw-bold mb-0">Datos del Técnico</h2>
                                    <small class="text-muted">Para completar por el área de soporte técnico</small>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label for="fecha_hora_atencion" class="form-label small fw-semibold">Fecha y Hora de Atención</label>
                                    <input type="datetime-local" class="form-control" id="fecha_hora_atencion" name="fecha_hora_atencion">
                                </div>

                                <div class="col-md-4">
                                    <label for="tecnico_asignado" class="form-label small fw-semibold">Técnico Asignado</label>
                                    <input type="text" class="form-control" id="tecnico_asignado" name="tecnico_asignado" placeholder="Ej: Ing. Marco Morales">
                                </div>

                                <div class="col-md-4">
                                    <label for="tipo_resolucion" class="form-label small fw-semibold">Tipo de Resolución</label>
                                    <select class="form-select" id="tipo_resolucion" name="tipo_resolucion">
                                        <option value="">Seleccione tipo...</option>
                                        <option value="Mantenimiento Correctivo">Mantenimiento Correctivo</option>
                                        <option value="Mantenimiento Preventivo">Mantenimiento Preventivo</option>
                                        <option value="Configuración / Soporte de Software">Configuración / Soporte de Software</option>
                                        <option value="Reemplazo de Hardware / Periférico">Reemplazo de Hardware / Periférico</option>
                                        <option value="Capacitación / Orientación a Usuario">Capacitación / Orientación a Usuario</option>
                                        <option value="Derivado a Garantía o Proveedor Externo">Derivado a Garantía o Proveedor Externo</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="diagnostico" class="form-label small fw-semibold">Diagnóstico Técnico</label>
                                    <textarea class="form-control" id="diagnostico" name="diagnostico" rows="3" placeholder="Diagnóstico de la causa raíz de la falla..."></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label for="solucion_aplicada" class="form-label small fw-semibold">Solución Aplicada</label>
                                    <textarea class="form-control" id="solucion_aplicada" name="solucion_aplicada" rows="3" placeholder="Detalle las acciones realizadas para resolver el incidente..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 6: OBSERVACIONES / RECOMENDACIÓN -->
                    <div class="col-12">
                        <div class="tarjeta-formulario p-4">
                            <div class="encabezado-seccion d-flex align-items-center">
                                <span class="numero-seccion numero-seccion-tecnico">6</span>
                                <div>
                                    <h2 class="h5 fw-bold mb-0">Observaciones / Recomendación</h2>
                                    <small class="text-muted">Recomendaciones preventivas o notas de seguimiento</small>
                                </div>
                            </div>

                            <div>
                                <label for="observaciones_recomendacion" class="form-label small fw-semibold">Observaciones y Recomendaciones</label>
                                <textarea class="form-control" id="observaciones_recomendacion" name="observaciones_recomendacion" rows="3" placeholder="Recomendaciones para evitar recurrencia de la falla o requerimientos futuros..."></textarea>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- BOTONES DE ACCIÓN -->
            <div class="col-12">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 p-3 bg-white border rounded-3">
                    <a href="../../index.php" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-arrow-left me-1"></i> Volver al Catálogo
                    </a>

                    <div class="d-flex gap-2 w-100 w-sm-auto justify-content-end">
                        <button type="reset" class="btn btn-light border px-3">
                            <i class="bi bi-eraser me-1"></i> Limpiar
                        </button>
                        <button type="submit" id="btnGuardarTicket" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                            <i class="bi bi-send-check me-2"></i> Registrar Ticket de Soporte
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</main>

<?php
$scripts_adicionales = array(
    'recursos/js/formulario_soporte.js'
);
require_once __DIR__ . '/../../componentes/pie_pagina.php';
?>
