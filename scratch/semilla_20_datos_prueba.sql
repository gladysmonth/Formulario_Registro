-- =============================================================================
-- SCRIPT DE DATOS DE PRUEBA (20 REGISTROS OPERATIVOS + 5 TELEMETRÍA PC)
-- Diseñado para validar el Dashboard de Gestión Técnica en Cosmol R.L.
-- =============================================================================

BEGIN;

-- 1. Limpieza opcional de pruebas anteriores para consistencia
DELETE FROM atenciones_soporte WHERE ticket_id IN (SELECT id FROM tickets_soporte WHERE codigo_ticket LIKE 'TCK-2026-%');
DELETE FROM tickets_soporte WHERE codigo_ticket LIKE 'TCK-2026-%';
DELETE FROM incidencias_sistemas WHERE nro_incidencia LIKE 'INC-2026-%';
DELETE FROM solicitudes_accesos WHERE nro_solicitud LIKE 'ACC-2026-%';
DELETE FROM mantenimientos_preventivos WHERE codigo_mantenimiento LIKE 'MP-2026-%';
DELETE FROM historial_diagnosticos WHERE direccion_mac LIKE 'AA:BB:CC:%';

-- =============================================================================
-- A. TICKETS DE SOPORTE TÉCNICO (6 REGISTROS)
-- =============================================================================
INSERT INTO tickets_soporte (
    codigo_ticket, nombre_solicitante, fecha_solicitud, departamento_area, 
    soporte_hardware, soporte_software, descripcion_problema, prioridad, estado, creado_en
) VALUES 
('TCK-2026-0001', 'Carlos Mendoza', '2026-10-12', 'Facturación', true, false, 'Impresora de boletas térmicas no responde al emitir facturas.', 'alta', 'resuelto', '2026-10-12 09:00:00'),
('TCK-2026-0002', 'Elena Rojas', '2026-10-12', 'Cobranzas', false, true, 'Lentitud extrema al cargar el formulario de pagos en Windows 11.', 'media', 'resuelto', '2026-10-12 14:30:00'),
('TCK-2026-0003', 'Mariana Paz', '2026-10-13', 'Contabilidad', false, true, 'Fallo al exportar planillas de balance a formato Excel.', 'alta', 'en_proceso', '2026-10-13 10:00:00'),
('TCK-2026-0004', 'Jorge Vaca', '2026-10-13', 'Atención al Cliente', true, false, 'Mouse y teclado inalámbrico presentan desconexiones intermitentes.', 'media', 'pendiente', '2026-10-13 15:45:00'),
('TCK-2026-0005', 'Patricia Justiniano', '2026-10-14', 'Recursos Humanos', true, false, 'Configuración de escáner en red compartida.', 'baja', 'resuelto', '2026-10-14 11:20:00'),
('TCK-2026-0006', 'Alejandro Suárez', '2026-10-15', 'Facturación', true, false, 'Monitor principal parpadea y pierde señal de video.', 'urgente', 'pendiente', '2026-10-15 08:15:00');

-- Atenciones técnicas para los tickets resueltos
INSERT INTO atenciones_soporte (
    ticket_id, fecha_hora_atencion, tecnico_asignado, tipo_resolucion, 
    diagnostico, solucion_aplicada, observaciones_recomendacion
) VALUES
((SELECT id FROM tickets_soporte WHERE codigo_ticket = 'TCK-2026-0001'), '2026-10-12 10:45:00', 'Técnico Soporte', 'Presencial', 'Puerto USB sulfatado', 'Limpieza física y cambio de cable USB', 'Revisar conector periódicamente'),
((SELECT id FROM tickets_soporte WHERE codigo_ticket = 'TCK-2026-0002'), '2026-10-12 16:15:00', 'Técnico Soporte', 'Remota', 'Caché de navegador y temporales saturados', 'Liberación de espacio temporal y optimización de navegador', 'Capacitar usuario en borrado de temporales'),
((SELECT id FROM tickets_soporte WHERE codigo_ticket = 'TCK-2026-0005'), '2026-10-14 12:30:00', 'Técnico Soporte', 'Presencial', 'Conflicto de IP fija en escáner', 'Reconfiguración en switch y reserva DHCP', 'Equipo operativo');

-- =============================================================================
-- B. REGISTRO DE INCIDENCIAS DE SISTEMAS (5 REGISTROS)
-- =============================================================================
INSERT INTO incidencias_sistemas (
    nro_incidencia, fecha_hora_reporte, responsable_reporte,
    sistema_erp_sai, sistema_cobranzas_netcob, sistema_otros, sistema_otros_detalle,
    fallo_base_datos, fallo_infraestructura_servidor, fallo_enlaces_conectividad,
    nivel_criticidad, estado, descripcion_tecnica_logs, accion_realizada, fecha_hora_cierre
) VALUES 
('INC-2026-0001', '2026-10-09 10:15:00', 'Ramiro Soliz', true, false, false, NULL, true, false, false, 'critica_nivel_1', 'cerrada', 'Deadlock en tablas de facturación de agua potable por bloqueo transaccional.', 'Mantenimiento de índices y reinicio controlado de PostgreSQL.', '2026-10-09 12:45:00'),
('INC-2026-0002', '2026-10-12 09:30:00', 'Claudia Morales', false, true, false, NULL, false, false, true, 'alta_nivel_2', 'resuelta', 'Caída de enlace de fibra óptica en cajas sucursal.', 'Reinicio de OLT y conmutación a enlace secundario.', '2026-10-12 11:00:00'),
('INC-2026-0003', '2026-10-14 14:00:00', 'Marcos Torrico', false, false, true, 'Servidor de Reportes BI', false, true, false, 'media_baja_nivel_3', 'en_atencion', 'Consumo de memoria RAM superando 90% en servidor de reportes.', 'Depuración de consultas pesadas en curso.', NULL),
('INC-2026-0004', '2026-10-15 08:30:00', 'Ing. Sistemas', true, false, false, NULL, false, false, false, 'alta_nivel_2', 'abierta', 'Error 500 al timbrar facturas electrónicas en SAI.', 'Verificación de certificados digitales.', NULL),
('INC-2026-0005', '2026-10-09 15:20:00', 'Técnico Redes', false, false, true, 'Servidor de Correo', false, true, false, 'media_baja_nivel_3', 'cerrada', 'Cola de mensajes demorada por saturación de ancho de banda.', 'Ajuste de límites de ancho de banda en cortafuegos.', '2026-10-09 16:30:00');

-- =============================================================================
-- C. ASIGNACIÓN Y CREACIÓN DE ACCESOS (5 REGISTROS)
-- =============================================================================
INSERT INTO solicitudes_accesos (
    nro_solicitud, fecha_solicitud, nombre_solicitante, cargo_solicitante, area_departamento,
    sistema_erp_sai, sistema_cobranzas_netcob, sistema_otros, es_usuario_nuevo, nombre_usuario_detalles,
    requerimientos_accesos, estado, atendido_por, fecha_hora_atencion
) VALUES 
('ACC-2026-0001', '2026-10-12 08:30:00', 'Valeria Ortiz', 'Cajera', 'Cobranzas', false, true, false, true, 'vortiz', 'Habilitación de usuario para cobro de facturas y arqueos.', 'atendido', 'Admin Sistemas', '2026-10-12 10:00:00'),
('ACC-2026-0002', '2026-10-13 10:15:00', 'Gonzalo Miranda', 'Auxiliar Contable', 'Contabilidad', true, false, false, false, 'gmiranda', 'Ampliación de permisos para emisión de libro de compras y ventas.', 'atendido', 'Admin Sistemas', '2026-10-13 11:30:00'),
('ACC-2026-0003', '2026-10-14 11:45:00', 'Silvia Hurtado', 'Operadora de Ventas', 'Facturación', true, false, false, true, 'shurtado', 'Creación de credenciales para módulo de nuevos contratos.', 'pendiente', NULL, NULL),
('ACC-2026-0004', '2026-10-15 09:10:00', 'Rodrigo Céspedes', 'Auditor', 'Auditoría', true, true, false, false, 'rcespedes', 'Acceso de solo lectura a auditoría de cobros y facturas.', 'en_proceso', 'Admin Sistemas', '2026-10-15 09:30:00'),
('ACC-2026-0005', '2026-10-09 14:00:00', 'Karla Montaño', 'Almacenera', 'Almacén Central', true, false, false, false, 'kmontano', 'Habilitación de módulo de compras y salidas de materiales.', 'atendido', 'Admin Sistemas', '2026-10-09 15:30:00');

-- =============================================================================
-- D. MANTENIMIENTOS PREVENTIVOS (4 REGISTROS)
-- =============================================================================
INSERT INTO mantenimientos_preventivos (
    codigo_mantenimiento, tecnico_responsable, fecha_mantenimiento, ubicacion_equipo,
    tipo_equipo, nombre_equipo, codigo_activo, memoria_ram, tipo_red, marca_modelo,
    sistema_operativo, procesador, almacenamiento, direccion_ip,
    limpieza_carcasa_componentes, limpieza_pantalla_teclado, verificacion_conectores,
    actualizacion_so, eliminacion_temporales, desfragmentacion_optimizacion, escaneo_antivirus,
    verificacion_drivers, copia_seguridad, verificacion_encendido_apagado, verificacion_rendimiento,
    verificacion_red, verificacion_perifericos, verificacion_temperatura_anomalias,
    observaciones_incidencias, firma_responsable_equipo, firma_sistemas
) VALUES 
('MP-2026-0001', 'Técnico Hardware', '2026-10-09', 'Cajas y Ventanillas', 'PC', 'PC-CAJA-01', 'ACT-2026-0010', '8 GB', 'LAN', 'Dell OptiPlex 3080', 'Windows 10 Pro', 'Intel Core i5-10500', 'SSD 256 GB', '192.168.1.51', true, true, true, true, true, true, true, true, true, true, true, true, true, true, 'Equipo en óptimas condiciones térmicas.', 'data:image/png;base64,demo1', 'data:image/png;base64,demo2'),
('MP-2026-0002', 'Técnico Hardware', '2026-10-12', 'Facturación', 'PC', 'PC-FACT-01', 'ACT-2026-0015', '16 GB', 'LAN', 'HP ProDesk 400 G6', 'Windows 11 Pro', 'Intel Core i7-10700', 'SSD 512 GB', '192.168.1.62', true, true, true, true, true, true, true, true, true, true, true, true, true, true, 'Mantenimiento físico y lógico completado sin novedades.', 'data:image/png;base64,demo1', 'data:image/png;base64,demo2'),
('MP-2026-0003', 'Técnico Hardware', '2026-10-13', 'Atención al Cliente', 'LAPTOP', 'LAPTOP-ATN-02', 'ACT-2026-0022', '16 GB', 'WIFI', 'Lenovo ThinkPad E14', 'Windows 11 Pro', 'AMD Ryzen 5 5500U', 'SSD 512 GB', '192.168.1.75', true, true, true, true, true, true, true, true, true, true, true, true, true, true, 'Batería con 94% de vida útil. Pasta térmica renovada.', 'data:image/png;base64,demo1', 'data:image/png;base64,demo2'),
('MP-2026-0004', 'Técnico Hardware', '2026-10-14', 'Recursos Humanos', 'PC', 'PC-RRHH-01', 'ACT-2026-0030', '8 GB', 'LAN', 'Dell Vostro 3681', 'Windows 10 Pro', 'Intel Core i3-10100', 'HDD 1 TB', '192.168.1.88', true, true, true, true, true, true, true, true, true, true, true, true, true, true, 'Desfragmentación de disco duro ejecutada.', 'data:image/png;base64,demo1', 'data:image/png;base64,demo2');

-- =============================================================================
-- E. TELEMETRÍA DE COMPUTADORAS (5 PUESTOS DE TRABAJO EN VIVO)
-- =============================================================================
INSERT INTO historial_diagnosticos (
    direccion_mac, hostname, usuario_windows, ip_local, tipo_evento, fecha_diagnostico,
    estado_salud_general, temperatura_cpu, uso_cpu_porcentaje, uso_ram_porcentaje,
    espacio_libre_c_gb, salud_disco_smart, antivirus_activo, amenazas_activas
) VALUES
('AA:BB:CC:11:22:33', 'PC-FACT-02', 'carlos.mendoza', '192.168.1.63', 'alerta_critica', NOW(), 'critico', 68.5, 75.0, 78.0, 8.4, 'Falla Inminente (SMART)', true, 0),
('AA:BB:CC:44:55:66', 'PC-COB-01', 'elena.rojas', '192.168.1.52', 'programado', NOW() - INTERVAL '15 minutes', 'advertencia', 54.0, 35.0, 91.5, 21.0, 'OK', true, 0),
('AA:BB:CC:77:88:99', 'PC-CONTAB-01', 'gonzalo.miranda', '192.168.1.41', 'inicio_jornada', NOW() - INTERVAL '1 hour', 'optimo', 41.5, 12.0, 38.0, 185.0, 'OK', true, 0),
('AA:BB:CC:AA:BB:CC', 'PC-CAJA-01', 'valeria.ortiz', '192.168.1.51', 'programado', NOW() - INTERVAL '30 minutes', 'bueno', 45.0, 22.0, 50.0, 120.0, 'OK', true, 0),
('AA:BB:CC:DD:EE:FF', 'PC-SISTEMAS-01', 'admin.sistemas', '192.168.1.10', 'programado', NOW() - INTERVAL '5 minutes', 'optimo', 39.0, 15.0, 44.0, 310.0, 'OK', true, 0);

COMMIT;
