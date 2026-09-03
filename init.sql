-- ==========================================================
-- SCRIPT DE INICIALIZACIÓN DE BASE DE DATOS
-- Sistema Modular de Formularios de Registro
-- Motor: PostgreSQL 13+
-- Archivo: init.sql
-- ==========================================================

-- Asegurar codificación UTF-8
SET client_encoding = 'UTF8';

-- Crear tabla de secuencia o función para actualización automática de fecha si no existe
CREATE OR REPLACE FUNCTION actualizar_marca_tiempo()
RETURNS TRIGGER AS $$
BEGIN
    NEW.actualizado_en = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- ==========================================================
-- MÓDULO 1: FORMULARIO DE SOPORTE TÉCNICO
-- ==========================================================
CREATE TABLE IF NOT EXISTS tickets_soporte (
    id SERIAL PRIMARY KEY,
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
    -- Equipo afectado (si aplica)
    numero_serie VARCHAR(100),
    marca_modelo VARCHAR(100),
    sistema_operativo VARCHAR(100),

    -- 4. PRIORIDAD (A definir por el área de soporte)
    prioridad VARCHAR(20) NOT NULL DEFAULT 'media'
        CHECK (prioridad IN ('urgente', 'alta', 'media', 'baja')),

    -- 5. DATOS DEL TÉCNICO (Para completar por soporte)
    fecha_hora_atencion TIMESTAMP,
    tecnico_asignado VARCHAR(150),
    diagnostico TEXT,
    solucion_aplicada TEXT,
    tipo_resolucion VARCHAR(100),

    -- 6. OBSERVACIONES / RECOMENDACIÓN
    observaciones_recomendacion TEXT,

    -- CAMPOS DE CONTROL Y ESTADO
    estado VARCHAR(30) NOT NULL DEFAULT 'pendiente'
        CHECK (estado IN ('pendiente', 'en_proceso', 'resuelto', 'cancelado')),
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Índices para optimizar búsquedas y filtrados
CREATE INDEX IF NOT EXISTS idx_tickets_codigo ON tickets_soporte (codigo_ticket);
CREATE INDEX IF NOT EXISTS idx_tickets_estado ON tickets_soporte (estado);
CREATE INDEX IF NOT EXISTS idx_tickets_prioridad ON tickets_soporte (prioridad);
CREATE INDEX IF NOT EXISTS idx_tickets_fecha ON tickets_soporte (fecha_solicitud);
CREATE INDEX IF NOT EXISTS idx_tickets_depto ON tickets_soporte (departamento_area);

-- Trigger para actualizar campo actualizado_en automáticamente
DROP TRIGGER IF EXISTS trigger_actualizar_tickets_soporte ON tickets_soporte;
CREATE TRIGGER trigger_actualizar_tickets_soporte
BEFORE UPDATE ON tickets_soporte
FOR EACH ROW
EXECUTE FUNCTION actualizar_marca_tiempo();

-- ==========================================================
-- REGISTROS INICIALES DE PRUEBA (SEMILLAS)
-- ==========================================================
INSERT INTO tickets_soporte (
    codigo_ticket,
    nombre_solicitante,
    fecha_solicitud,
    departamento_area,
    soporte_hardware,
    soporte_software,
    descripcion_problema,
    numero_serie,
    marca_modelo,
    sistema_operativo,
    prioridad,
    estado,
    tecnico_asignado,
    diagnostico,
    solucion_aplicada,
    tipo_resolucion,
    observaciones_recomendacion
) VALUES 
(
    'SOP-2026-0001',
    'Carlos Mendoza Ramos',
    CURRENT_DATE - INTERVAL '1 day',
    'Contabilidad y Finanzas',
    TRUE,
    FALSE,
    'La impresora multifuncional de red no responde a los comandos de impresión y presenta error de atasco continuo.',
    'SN-PRN-884210',
    'HP LaserJet Pro M404dw',
    'Windows 10 Pro 64-bit',
    'alta',
    'en_proceso',
    'Ing. Rodrigo Alarcón',
    'Rodillo de tracción obstruido por residuos de papel y firmware desactualizado.',
    'Limpieza profunda de rodillos y actualización de controladores en la estación de trabajo.',
    'Mantenimiento correctivo',
    'Se recomienda utilizar papel de gramaje recomendado (75-80g) y evitar hojas arrugadas.'
),
(
    'SOP-2026-0002',
    'Mariana Silva Morales',
    CURRENT_DATE,
    'Recursos Humanos',
    FALSE,
    TRUE,
    'Error al intentar ingresar al sistema de planillas, la pantalla muestra advertencia de certificado vencido y bloqueo de sesión.',
    'SN-LAP-441092',
    'Dell Latitude 3420',
    'Windows 11 Enterprise',
    'urgente',
    'pendiente',
    NULL,
    NULL,
    NULL,
    NULL,
    'Requiere atención prioritaria para cierre de planillas mensual.'
)
ON CONFLICT (codigo_ticket) DO NOTHING;
