-- ==========================================================
-- SCRIPT DE INICIALIZACIÓN DE BASE DE DATOS
-- Sistema Modular de Formularios de Registro
-- Motor: PostgreSQL 13+
-- Archivo: init.sql
-- ==========================================================

-- Asegurar codificación UTF-8
SET client_encoding = 'UTF8';

-- Función para actualización automática de marca de tiempo (actualizado_en)
CREATE OR REPLACE FUNCTION actualizar_marca_tiempo()
RETURNS TRIGGER AS $$
BEGIN
    NEW.actualizado_en = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- ==========================================================
-- TABLA DE CATÁLOGO / INVENTARIO DE EQUIPOS
-- ==========================================================
CREATE TABLE IF NOT EXISTS equipos_inventario (
    id BIGSERIAL PRIMARY KEY,
    codigo_activo VARCHAR(100),
    numero_serie VARCHAR(100) NOT NULL,
    tipo_equipo VARCHAR(100) NOT NULL,
    marca_modelo VARCHAR(150) NOT NULL,
    sistema_operativo VARCHAR(100),
    area_encargado VARCHAR(150),
    centro_costo VARCHAR(100),
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_equipos_serie ON equipos_inventario (numero_serie);
CREATE INDEX IF NOT EXISTS idx_equipos_codigo ON equipos_inventario (codigo_activo);
CREATE INDEX IF NOT EXISTS idx_equipos_tipo ON equipos_inventario (tipo_equipo);
CREATE INDEX IF NOT EXISTS idx_equipos_area ON equipos_inventario (area_encargado);

DROP TRIGGER IF EXISTS trigger_actualizar_equipos_inventario ON equipos_inventario;
CREATE TRIGGER trigger_actualizar_equipos_inventario
BEFORE UPDATE ON equipos_inventario
FOR EACH ROW
EXECUTE FUNCTION actualizar_marca_tiempo();


-- ==========================================================
-- MÓDULO 1: FORMULARIO DE SOPORTE TÉCNICO
-- ==========================================================

-- 1. TABLA PRINCIPAL DE TICKETS (Solicitud del Usuario)
CREATE TABLE IF NOT EXISTS tickets_soporte (
    id BIGSERIAL PRIMARY KEY,
    codigo_ticket VARCHAR(20) UNIQUE NOT NULL,

    -- 1. DATOS GENERALES
    nombre_solicitante VARCHAR(150) NOT NULL,
    fecha_solicitud DATE NOT NULL DEFAULT CURRENT_DATE,
    departamento_area VARCHAR(100) NOT NULL,

    -- 2. TIPO DE SOPORTE REQUERIDO
    soporte_hardware BOOLEAN DEFAULT FALSE,
    soporte_software BOOLEAN DEFAULT FALSE,

    -- 3. DETALLE DEL PROBLEMA / REQUERIMIENTO
    descripcion_problema TEXT NOT NULL,

    -- Equipo afectado (vinculación a inventario y datos capturados)
    equipo_id BIGINT REFERENCES equipos_inventario(id) ON DELETE SET NULL,
    codigo_activo VARCHAR(100),
    numero_serie VARCHAR(100),
    tipo_equipo VARCHAR(100),
    marca_modelo VARCHAR(150),
    sistema_operativo VARCHAR(100),
    area_encargado VARCHAR(150),
    centro_costo VARCHAR(100),

    -- 4. PRIORIDAD
    prioridad VARCHAR(20) NOT NULL DEFAULT 'media'
        CHECK (prioridad IN ('urgente', 'alta', 'media', 'baja')),

    -- CAMPOS DE CONTROL Y ESTADO
    estado VARCHAR(30) NOT NULL DEFAULT 'pendiente'
        CHECK (estado IN ('pendiente', 'en_proceso', 'resuelto', 'cancelado')),

    -- FIRMAS DIGITALES
    firma_solicitante TEXT,
    firma_sistemas TEXT,

    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Índices para optimizar búsquedas y filtrados en tickets_soporte
CREATE INDEX IF NOT EXISTS idx_tickets_codigo ON tickets_soporte (codigo_ticket);
CREATE INDEX IF NOT EXISTS idx_tickets_estado ON tickets_soporte (estado);
CREATE INDEX IF NOT EXISTS idx_tickets_prioridad ON tickets_soporte (prioridad);
CREATE INDEX IF NOT EXISTS idx_tickets_fecha ON tickets_soporte (fecha_solicitud);
CREATE INDEX IF NOT EXISTS idx_tickets_depto ON tickets_soporte (departamento_area);
CREATE INDEX IF NOT EXISTS idx_tickets_equipo_id ON tickets_soporte (equipo_id);

-- Trigger para actualizar campo actualizado_en en tickets_soporte
DROP TRIGGER IF EXISTS trigger_actualizar_tickets_soporte ON tickets_soporte;
CREATE TRIGGER trigger_actualizar_tickets_soporte
BEFORE UPDATE ON tickets_soporte
FOR EACH ROW
EXECUTE FUNCTION actualizar_marca_tiempo();


-- 2. TABLA DE ATENCIONES Y RESOLUCIONES TÉCNICAS (Soporte Técnico)
CREATE TABLE IF NOT EXISTS atenciones_soporte (
    id BIGSERIAL PRIMARY KEY,
    ticket_id BIGINT NOT NULL REFERENCES tickets_soporte(id) ON DELETE CASCADE,

    -- 5. DATOS DEL TÉCNICO
    fecha_hora_atencion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tecnico_asignado VARCHAR(150) NOT NULL,
    tipo_resolucion VARCHAR(100),
    diagnostico TEXT,
    solucion_aplicada TEXT,

    -- 6. OBSERVACIONES / RECOMENDACIÓN
    observaciones_recomendacion TEXT,

    -- FIRMA TÉCNICO DE SISTEMAS
    firma_sistemas TEXT,

    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Índices para optimizar búsquedas en atenciones_soporte
CREATE INDEX IF NOT EXISTS idx_atenciones_ticket_id ON atenciones_soporte (ticket_id);
CREATE INDEX IF NOT EXISTS idx_atenciones_tecnico ON atenciones_soporte (tecnico_asignado);

-- Trigger para actualizar campo actualizado_en en atenciones_soporte
DROP TRIGGER IF EXISTS trigger_actualizar_atenciones_soporte ON atenciones_soporte;
CREATE TRIGGER trigger_actualizar_atenciones_soporte
BEFORE UPDATE ON atenciones_soporte
FOR EACH ROW
EXECUTE FUNCTION actualizar_marca_tiempo();

-- ================================================================
-- TABLA 4: REGISTRO DE MANTENIMIENTO PREVENTIVO (PC / LAPTOP)
-- ================================================================
CREATE TABLE IF NOT EXISTS mantenimientos_preventivos (
    id BIGSERIAL PRIMARY KEY,
    codigo_mantenimiento VARCHAR(20) UNIQUE NOT NULL, -- Ej: MP-2026-0001
    
    -- 1. DATOS GENERALES
    tecnico_responsable VARCHAR(150) NOT NULL,
    fecha_mantenimiento DATE NOT NULL DEFAULT CURRENT_DATE,
    ubicacion_equipo VARCHAR(150) NOT NULL,

    -- 2. INFORMACIÓN DEL EQUIPO
    equipo_id BIGINT REFERENCES equipos_inventario(id) ON DELETE SET NULL,
    tipo_equipo VARCHAR(20) NOT NULL, -- 'PC' o 'LAPTOP'
    nombre_equipo VARCHAR(100),
    codigo_activo VARCHAR(50),
    memoria_ram VARCHAR(50),
    tipo_red VARCHAR(20), -- 'LAN' o 'WIFI'
    marca_modelo VARCHAR(150),
    sistema_operativo VARCHAR(100),
    procesador VARCHAR(100),
    almacenamiento VARCHAR(100),
    direccion_ip VARCHAR(50),

    -- 3. MANTENIMIENTO EXTERNO (LIMPIEZA FÍSICA)
    limpieza_carcasa_componentes BOOLEAN NOT NULL DEFAULT FALSE,
    limpieza_pantalla_teclado BOOLEAN NOT NULL DEFAULT FALSE,
    verificacion_conectores BOOLEAN NOT NULL DEFAULT FALSE,
    limpieza_otros TEXT,

    -- 4. MANTENIMIENTO INTERNO (SOFTWARE / CONFIGURACIÓN)
    actualizacion_so BOOLEAN NOT NULL DEFAULT FALSE,
    eliminacion_temporales BOOLEAN NOT NULL DEFAULT FALSE,
    desfragmentacion_optimizacion BOOLEAN NOT NULL DEFAULT FALSE,
    escaneo_antivirus BOOLEAN NOT NULL DEFAULT FALSE,
    verificacion_drivers BOOLEAN NOT NULL DEFAULT FALSE,
    copia_seguridad BOOLEAN NOT NULL DEFAULT FALSE,
    mantenimiento_interno_otros TEXT,

    -- 5. VERIFICACIÓN DE FUNCIONAMIENTO
    verificacion_encendido_apagado BOOLEAN NOT NULL DEFAULT FALSE,
    verificacion_rendimiento BOOLEAN NOT NULL DEFAULT FALSE,
    verificacion_red BOOLEAN NOT NULL DEFAULT FALSE,
    verificacion_perifericos BOOLEAN NOT NULL DEFAULT FALSE,
    verificacion_temperatura_anomalias BOOLEAN NOT NULL DEFAULT FALSE,

    -- 6. OBSERVACIONES / INCIDENCIAS
    observaciones_incidencias TEXT,

    -- 7. FIRMAS DIGITALES
    firma_responsable_equipo TEXT NOT NULL,
    firma_sistemas TEXT NOT NULL,

    -- Auditoría
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Índices para optimizar búsquedas en mantenimientos_preventivos
CREATE INDEX IF NOT EXISTS idx_mp_codigo ON mantenimientos_preventivos (codigo_mantenimiento);
CREATE INDEX IF NOT EXISTS idx_mp_tecnico ON mantenimientos_preventivos (tecnico_responsable);
CREATE INDEX IF NOT EXISTS idx_mp_fecha ON mantenimientos_preventivos (fecha_mantenimiento);
CREATE INDEX IF NOT EXISTS idx_mp_tipo_equipo ON mantenimientos_preventivos (tipo_equipo);
CREATE INDEX IF NOT EXISTS idx_mp_codigo_activo ON mantenimientos_preventivos (codigo_activo);
CREATE INDEX IF NOT EXISTS idx_mp_equipo_id ON mantenimientos_preventivos (equipo_id);

-- Trigger para actualizar campo actualizado_en en mantenimientos_preventivos
DROP TRIGGER IF EXISTS trigger_actualizar_mantenimientos_preventivos ON mantenimientos_preventivos;
CREATE TRIGGER trigger_actualizar_mantenimientos_preventivos
BEFORE UPDATE ON mantenimientos_preventivos
FOR EACH ROW
EXECUTE FUNCTION actualizar_marca_tiempo();

