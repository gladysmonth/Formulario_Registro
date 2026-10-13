-- =================================================================
-- MIGRACIÓN FASE 1: TABLA DE USUARIOS Y CASOS DE FUERZA MAYOR
-- Base de Datos: formularios_db
-- =================================================================

-- 1. Tabla de Usuarios del Sistema (Control de Acceso y Roles RBAC)
CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    clave_hash VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(150) NOT NULL,
    rol VARCHAR(30) NOT NULL DEFAULT 'usuario'
        CHECK (rol IN ('admin', 'tecnico', 'usuario')),
    departamento VARCHAR(100),
    cargo VARCHAR(100),
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_usuarios_usuario ON usuarios (usuario);
CREATE INDEX IF NOT EXISTS idx_usuarios_rol ON usuarios (rol);

-- 2. Sembrar usuarios iniciales con contraseña 'Cosmol2026!'
INSERT INTO usuarios (usuario, clave_hash, nombre_completo, rol, departamento, cargo, activo)
VALUES 
    ('admin', '\\\.sONf/AwMSBL6tMFG8sP/bpAc5./2RtklG', 'Administrador de Sistemas', 'admin', 'Sistemas e Informática', 'Jefe de Sistemas', TRUE),
    ('tecnico', '\\\.sONf/AwMSBL6tMFG8sP/bpAc5./2RtklG', 'Técnico de Soporte', 'tecnico', 'Sistemas e Informática', 'Técnico de Soporte', TRUE),
    ('personal', '\\\.sONf/AwMSBL6tMFG8sP/bpAc5./2RtklG', 'Personal Solicitante', 'usuario', 'Administración y Finanzas', 'Auxiliar Administrativo', TRUE)
ON CONFLICT (usuario) DO UPDATE 
SET clave_hash = EXCLUDED.clave_hash,
    rol = EXCLUDED.rol,
    nombre_completo = EXCLUDED.nombre_completo,
    activo = TRUE;

-- 3. Ampliar estados en tickets_soporte para casos de fuerza mayor / imposibilidad técnica
ALTER TABLE tickets_soporte 
    ADD COLUMN IF NOT EXISTS motivo_imposibilidad TEXT;

DO $\$
BEGIN
    ALTER TABLE tickets_soporte DROP CONSTRAINT IF EXISTS tickets_soporte_estado_check;
    ALTER TABLE tickets_soporte ADD CONSTRAINT tickets_soporte_estado_check 
        CHECK (estado IN ('pendiente', 'en_proceso', 'resuelto', 'cancelado', 'fuerza_mayor'));
END $\$;