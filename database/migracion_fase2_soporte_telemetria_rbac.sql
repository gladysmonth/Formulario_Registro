-- ====================================================================
-- MIGRACIÓN: VINCULACIÓN RBAC, TÉCNICOS DERIVADOS Y TELEMETRÍA EN TICKETS
-- Base de Datos: formularios_db (PostgreSQL 13+)
-- Archivo: database/migracion_fase2_soporte_telemetria_rbac.sql
-- ====================================================================

SET client_encoding = 'UTF8';

-- 1. Agregar trazabilidad de solicitante y técnico asignado en tickets_soporte
ALTER TABLE IF EXISTS tickets_soporte
    ADD COLUMN IF NOT EXISTS usuario_solicitante_id BIGINT REFERENCES usuarios(id) ON DELETE SET NULL,
    ADD COLUMN IF NOT EXISTS tecnico_asignado_id BIGINT REFERENCES usuarios(id) ON DELETE SET NULL;

CREATE INDEX IF NOT EXISTS idx_tickets_solicitante_id ON tickets_soporte (usuario_solicitante_id);
CREATE INDEX IF NOT EXISTS idx_tickets_tecnico_id ON tickets_soporte (tecnico_asignado_id);

-- 2. Agregar trazabilidad del técnico y del asignador (admin) en atenciones_soporte
ALTER TABLE IF EXISTS atenciones_soporte
    ADD COLUMN IF NOT EXISTS tecnico_id BIGINT REFERENCES usuarios(id) ON DELETE SET NULL,
    ADD COLUMN IF NOT EXISTS asignado_por_id BIGINT REFERENCES usuarios(id) ON DELETE SET NULL;

CREATE INDEX IF NOT EXISTS idx_atenciones_tecnico_id ON atenciones_soporte (tecnico_id);
CREATE INDEX IF NOT EXISTS idx_atenciones_asignado_por_id ON atenciones_soporte (asignado_por_id);
