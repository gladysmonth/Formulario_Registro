# AGENTS.md - Guía de Desarrollo y Convenciones del Proyecto

Bienvenido al repositorio del **Sistema Modular de Formularios de Registro**.
Este documento define la arquitectura general, normas de codificación, estructura del proyecto y directrices obligatorias para agentes de IA y desarrolladores.

---

## 1. Visión General y Pila Tecnológica

El sistema gestiona de forma modular y autónoma los formularios de registro y telemetría institucional:
- **Backend:** PHP 7.3 con Apache (Extensión `pdo_pgsql`).
- **Frontend:** PHP 7.3 con Apache y componentes de interfaz con **Bootstrap 5**.
- **Base de Datos:** PostgreSQL 13+.
- **Infraestructura:** Docker Compose con 3 servicios (`db`, `back_form` en puerto 83, `front_forms` en puerto 82).

---

## 2. Reglas Críticas de Desarrollo

1. **Nombres de Archivos y Carpetas en Español:**
   - Todos los archivos, directorios, funciones y tablas deben nombrarse en español (excepto `docker-compose.yml`, `Dockerfile`, `index.php` y `AGENTS.md`).
2. **Compatibilidad Estricta con PHP 7.3:**
   - **Prohibido** usar sintaxis de PHP 8+ (no argumentos nombrados, no `match`, no tipos de unión, no operador nullsafe `?->`).
3. **Separación Modular de Responsabilidades:**
   - `front_forms/`: No realiza consultas SQL directas; se comunica exclusivamente mediante peticiones HTTP (Fetch API / cURL) a `back_form/`.
   - `back_form/`: Procesa validaciones, reglas de negocio e interactúa con PostgreSQL retornando JSON estandarizado.
   - `init.sql`: Archivo físico único de inicialización DDL del esquema relacional.
4. **Modificación Modular y Aislamiento de Código:**
   - **Prohibido generar código monolítico o archivos gigantes.**
   - Mantener las vistas y endpoints divididos en submódulos, servicios y componentes independientes (`servicios/`, `componentes/`, `recursos/js/`).
   - Las modificaciones deben ser quirúrgicas: alterar únicamente la sección o componente requerido sin desestabilizar el resto.

---

## 3. Convención de Respuestas JSON de la API

Todas las respuestas del backend deben emitir el encabezado `Content-Type: application/json; charset=utf-8`:

```json
// Éxito:
{ "exito": true, "mensaje": "Operación exitosa", "datos": { ... } }

// Error:
{ "exito": false, "mensaje": "Descripción clara del error", "errores": [ ... ] }
```

---

## 4. Estructura General del Proyecto

```text
Formulario_Registro/
├── AGENTS.md                          # Este documento raíz de directrices
├── docker-compose.yml                 # Orquestador de contenedores
├── init.sql                           # Script físico DDL para PostgreSQL
├── documentacion/                     # Especificaciones modulares detalladas
│   ├── 01_soporte_tecnico.md
│   ├── 02_mantenimiento_preventivo.md
│   ├── 03_registro_incidencias.md
│   ├── 04_asignacion_accesos.md
│   └── 05_telemetria_diagnostico.md
├── back_form/                         # Backend API (PHP 7.3)
│   ├── index.php                      # Healthcheck del servicio
│   ├── configuracion/                 # Conexión PDO, respuestas API y seguridad RBAC
│   └── api/                           # auth/, soporte_tecnico/, mantenimiento_preventivo/,
│                                      # registro_incidencias/, asignacion_accesos/, diagnostico/,
│                                      # metricas/ y auditoria/
└── front_forms/                       # Frontend (PHP 7.3 + Bootstrap 5)
    ├── index.php                      # Portal central de selección de módulos
    ├── login.php                      # Autenticación institucional
    ├── metricas.php                   # Tablero global de indicadores y KPIs
    ├── componentes/                   # Encabezado, navegación, pie y control de sesión
    ├── recursos/                      # Estilos CSS y controladores JS modulares
    └── modulos/                       # Vistas modulares por formulario y telemetría
```

---

## 5. Índice de Documentación por Módulo

Para consultar campos de formulario, modelos de datos, endpoints específicos y componentes frontend de cada módulo, remitirse a la documentación dedicada:

- [Módulo 1: Soporte Técnico](file:///d:/RESPALDO%20DISCO%20C/Documents/FormularioRegistroCosmol/Formulario_Registro/documentacion/01_soporte_tecnico.md) - Ticket `SOP-YYYY-XXXX`, atención técnica y telemetría de PC.
- [Módulo 2: Mantenimiento Preventivo](file:///d:/RESPALDO%20DISCO%20C/Documents/FormularioRegistroCosmol/Formulario_Registro/documentacion/02_mantenimiento_preventivo.md) - Ficha `MP-YYYY-XXXX`, checklist físico/lógico y firmas canvas.
- [Módulo 3: Registro de Incidencias](file:///d:/RESPALDO%20DISCO%20C/Documents/FormularioRegistroCosmol/Formulario_Registro/documentacion/03_registro_incidencias.md) - Incidencias `INC-YYYY-XXXX`, criticidad, plataformas ERP/Cobranzas y cierre.
- [Módulo 4: Asignación de Accesos](file:///d:/RESPALDO%20DISCO%20C/Documents/FormularioRegistroCosmol/Formulario_Registro/documentacion/04_asignacion_accesos.md) - Solicitudes `ACC-YYYY-XXXX`, perfiles de usuario y triple firma.
- [Módulo 5: Telemetría y Diagnóstico de PCs](file:///d:/RESPALDO%20DISCO%20C/Documents/FormularioRegistroCosmol/Formulario_Registro/documentacion/05_telemetria_diagnostico.md) - Agente Cosmol, auto-detección en vivo y monitoreo de salud.

---

## 6. Procedimiento para Agregar Nuevos Módulos

1. **Base de Datos:** Añadir la tabla del módulo en `init.sql` con prefijo institucional y campos estándar de auditoría (`fecha_creacion`, `creado_por`, etc.).
2. **Backend:** Crear la ruta en `back_form/api/<nombre_modulo>/` con sus endpoints CRUD y carpeta interna `servicios/` para la lógica aislada.
3. **Frontend:** Crear la carpeta en `front_forms/modulos/<nombre_modulo>/` reutilizando los componentes base y separando la lógica en `recursos/js/`.
4. **Portal y Navegación:** Activar el acceso correspondiente en `front_forms/index.php` y `front_forms/componentes/barra_navegacion.php`.