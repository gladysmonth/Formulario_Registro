# Regla de Seguridad, Roles de Usuario y Entorno Docker (.env)

A partir de la integración del sistema modular y el módulo de Auditoría de PCs, cualquier modificación en la plataforma debe cumplir con las siguientes directrices:

## 1. Separación de Roles (RBAC)
- **Rol Personal / Usuario:** Solo debe tener acceso a registrar solicitudes (campos básicos: Solicitante, Departamento, Cargo, Problema/Requerimiento y Firma de Solicitud).
- **Prohibido exponer campos técnicos al Personal:** Diagnóstico técnico, soluciones aplicadas, checkboxes de mantenimiento interno/externo, voltajes, temperaturas y asignación de técnicos deben permanecer estrictamente para el rol `admin` o `tecnico`.
- **Módulo de Diagnóstico PC:** Es de acceso exclusivo para el personal técnico y administrativo de Sistemas.

## 2. Variables de Entorno y Seguridad Docker
- **Cero secretos en repositorios:** Toda credencial de base de datos (`POSTGRES_USER`, `POSTGRES_PASSWORD`), puertos e IPs de red deben leerse desde el archivo `.env`.
- El archivo `.env` nunca debe commitearse a Git y está protegido por el `.gitignore` raíz.
- La IP del servidor debe configurarse dinámicamente (`window.URL_BACKEND = 'http://' + window.location.hostname + ':83'`) para garantizar compatibilidad con tablets y móviles conectados a la red local.

## 3. Centralización de Base de Datos
- Todos los scripts SQL de inicialización o migraciones residen exclusivamente en `Formulario_Registro/database/`.
- No deben crearse carpetas `database` en clientes ligeros periféricos.
