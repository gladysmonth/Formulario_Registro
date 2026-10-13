-- ==========================================================
-- SCRIPT DE BASE DE DATOS: MÓDULO DE TELEMETRÍA Y DIAGNÓSTICO PC
-- Compatible con: PostgreSQL 13+ (formularios_db)
-- Proyecto: Sistema de Diagnóstico y Soporte Técnico Cosmol
-- Archivo: database/diagnostico.sql
-- ==========================================================

-- Asegurar codificación UTF-8
SET client_encoding = 'UTF8';

-- ----------------------------------------------------------
-- 1. EXTENSIÓN Y ACTUALIZACIÓN EN TABLA DE INVENTARIO
--    Asegura los campos de identificación física y de red
-- ----------------------------------------------------------
ALTER TABLE IF EXISTS equipos_inventario 
    ADD COLUMN IF NOT EXISTS direccion_mac VARCHAR(50),
    ADD COLUMN IF NOT EXISTS hostname VARCHAR(150),
    ADD COLUMN IF NOT EXISTS ip_actual VARCHAR(45),
    ADD COLUMN IF NOT EXISTS usuario_actual VARCHAR(150),
    ADD COLUMN IF NOT EXISTS uuid_sistema VARCHAR(100);

-- Índice único opcional para búsqueda ultrarrápida por MAC
CREATE INDEX IF NOT EXISTS idx_equipos_mac ON equipos_inventario (direccion_mac);
CREATE INDEX IF NOT EXISTS idx_equipos_hostname ON equipos_inventario (hostname);


-- ----------------------------------------------------------
-- 2. TABLA DE HISTORIAL DE DIAGNÓSTICOS Y TELEMETRÍA
--    Guarda cada snapshot enviado por el agente del equipo
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS historial_diagnosticos (
    id BIGSERIAL PRIMARY KEY,
    
    -- Identificadores del equipo
    equipo_id BIGINT REFERENCES equipos_inventario(id) ON DELETE SET NULL,
    direccion_mac VARCHAR(50) NOT NULL,
    hostname VARCHAR(150) NOT NULL,
    usuario_windows VARCHAR(150),
    ip_local VARCHAR(45),
    
    -- Tipo y momento del diagnóstico
    tipo_evento VARCHAR(30) NOT NULL DEFAULT 'programado'
        CHECK (tipo_evento IN ('programado', 'inicio_jornada', 'alerta_critica', 'manual', 'ticket_soporte')),
    fecha_diagnostico TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    -- Resumen de estado de salud general (para semáforos rápidos en el dashboard)
    estado_salud_general VARCHAR(20) NOT NULL DEFAULT 'bueno'
        CHECK (estado_salud_general IN ('optimo', 'bueno', 'advertencia', 'critico')),
    temperatura_cpu NUMERIC(5, 2), -- Grados Celsius (si el sensor está expuesto)
    uso_cpu_porcentaje NUMERIC(5, 2),
    uso_ram_porcentaje NUMERIC(5, 2),
    espacio_libre_c_gb NUMERIC(8, 2),
    salud_disco_smart VARCHAR(50) DEFAULT 'OK',
    antivirus_activo BOOLEAN DEFAULT TRUE,
    amenazas_activas INT DEFAULT 0,
    
    -- Datos estructurados detallados (PostgreSQL JSONB de alto rendimiento)
    -- Detalle estilo HWMonitor:
    detalle_cpu JSONB,            -- Modelo, sockets, núcleos, hilos, frecuencia
    detalle_ram JSONB,            -- Total GB, Usado GB, módulos instalados, velocidad
    detalle_almacenamiento JSONB, -- Discos físicos, particiones, modelo, SMART, salud
    detalle_red JSONB,            -- Adaptadores, MACs, Gateway, DNS, velocidad enlace
    detalle_placa_bios JSONB,     -- Fabricante Placa, Modelo, Versión BIOS, Número de Serie
    
    -- Auditoría de Software, Actividad y Seguridad:
    apps_recientes_prefetch JSONB,     -- Lista de aplicaciones ejecutadas recientemente con última fecha
    descargas_recientes JSONB,         -- Últimos archivos en Descargas, fecha, tamaño y tipo
    seguridad_antivirus JSONB,         -- Estado Windows Defender / AV, firmas, protección en tiempo real
    alertas_detectadas JSONB,          -- Discos casi llenos, servicios caídos, advertencias
    
    -- Payload íntegro original para trazabilidad o futuros campos
    payload_completo JSONB,
    
    creado_en TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Índices de consulta frecuente para telemetría
CREATE INDEX IF NOT EXISTS idx_diag_mac ON historial_diagnosticos (direccion_mac);
CREATE INDEX IF NOT EXISTS idx_diag_fecha ON historial_diagnosticos (fecha_diagnostico DESC);
CREATE INDEX IF NOT EXISTS idx_diag_equipo_id ON historial_diagnosticos (equipo_id);
CREATE INDEX IF NOT EXISTS idx_diag_estado_salud ON historial_diagnosticos (estado_salud_general);
CREATE INDEX IF NOT EXISTS idx_diag_tipo_evento ON historial_diagnosticos (tipo_evento);

-- Índice GIN para búsquedas avanzadas dentro de los JSON de apps y alertas
CREATE INDEX IF NOT EXISTS idx_diag_alertas_gin ON historial_diagnosticos USING gin (alertas_detectadas);


-- ----------------------------------------------------------
-- 3. VINCULACIÓN CON LA TABLA DE TICKETS DE SOPORTE
--    Adjunta automáticamente la "fotografía técnica" al ticket
-- ----------------------------------------------------------
ALTER TABLE IF EXISTS tickets_soporte 
    ADD COLUMN IF NOT EXISTS diagnostico_id BIGINT REFERENCES historial_diagnosticos(id) ON DELETE SET NULL,
    ADD COLUMN IF NOT EXISTS direccion_mac VARCHAR(50);

CREATE INDEX IF NOT EXISTS idx_tickets_diag_id ON tickets_soporte (diagnostico_id);
CREATE INDEX IF NOT EXISTS idx_tickets_mac ON tickets_soporte (direccion_mac);


-- ----------------------------------------------------------
-- 4. VISTA: ÚLTIMO DIAGNÓSTICO POR EQUIPO
--    Permite al dashboard consultar el estado en tiempo real
--    de cada máquina sin barrer toda la tabla de historial
-- ----------------------------------------------------------
CREATE OR REPLACE VIEW vista_ultimo_diagnostico_pc AS
SELECT DISTINCT ON (d.direccion_mac)
    d.id AS diagnostico_id,
    d.equipo_id,
    d.direccion_mac,
    d.hostname,
    d.usuario_windows,
    d.ip_local,
    d.tipo_evento,
    d.fecha_diagnostico,
    d.estado_salud_general,
    d.temperatura_cpu,
    d.uso_cpu_porcentaje,
    d.uso_ram_porcentaje,
    d.espacio_libre_c_gb,
    d.salud_disco_smart,
    d.antivirus_activo,
    d.amenazas_activas,
    d.detalle_cpu,
    d.detalle_ram,
    d.detalle_almacenamiento,
    d.apps_recientes_prefetch,
    d.alertas_detectadas,
    eq.codigo_activo,
    eq.area,
    eq.encargado,
    eq.marca_modelo
FROM historial_diagnosticos d
LEFT JOIN equipos_inventario eq ON eq.id = d.equipo_id OR eq.direccion_mac = d.direccion_mac
ORDER BY d.direccion_mac, d.fecha_diagnostico DESC;
