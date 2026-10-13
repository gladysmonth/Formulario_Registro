# Plan Maestro de Fases de Implementación y Reordenamiento
**Plataforma Modular de Formularios de Registro y Telemetría PC - Cosmol R.L.**  
*Documento de Control y Seguimiento del Proyecto*  
*Ubicación: `Formulario_Registro/Docs/fases_de_implementacion.md`*  
*Última Actualización: Octubre 2026*

---

## 1. Tablero de Estado y Progreso General

| Fase | Título y Enfoque | Estado | Entregables Principales |
| :---: | :--- | :---: | :--- |
| **FASE 1** | **Seguridad, Docker y Base de Datos Core** | **COMPLETADA** | `.env` seguro, IP local expuesta, tabla `usuarios`, restricción `fuerza_mayor` y `formularios_inicial.sql`. |
| **FASE 2** | **Inconsistencias Backend API y Autenticación** | **COMPLETADA** | Sanitización BOM UTF-8, `/api/auth/login.php`, `actualizar_ticket.php` con fuerza mayor y catálogo API. |
| **FASE 3** | **Frontend - Sesión y Control de Roles (RBAC)** | **EN ESPERA** | `control_sesion.php`, vista `login.php`, navbar con perfil de usuario y protección de rutas. |
| **FASE 4** | **Frontend - Menú Offcanvas y Dashboard KPIs** | **PENDIENTE** | Mini-dashboard a primera vista en el menú lateral, filtros directos a tickets abiertos y navegación de formularios. |
| **FASE 5** | **Limpieza de Tarjetas y Vistas de Formularios** | **PENDIENTE** | Eliminación de menús redundantes en tarjetas, separación de campos de solicitud vs campos técnicos. |
| **FASE 6** | **Integración de Diagnóstico, Pruebas y LAN** | **PENDIENTE** | Conexión del módulo de telemetría de PC, pruebas con tablets (`192.168.11.17:82`), validación y cierre. |

---

## 2. Detalle Exhaustivo de Cada Fase

### FASE 1: Seguridad, Docker y Base de Datos Core [COMPLETADA]
* **Objetivo:** Resolver exposición de credenciales, habilitar acceso móvil/tablet vía LAN, estructurar la tabla de usuarios y ampliar los estados de tickets.
* **Archivos Modificados / Creados:**
  - `.env` y `.env.example` (parámetros de red y BD con IP activa `192.168.11.17`).
  - `docker-compose.yml` (vinculación a variables `.env` y montaje de esquemas centralizados).
  - `.gitignore` (exclusión estricta de `.env`, logs y temporales).
  - `database/formularios_inicial.sql` (esquema consolidado con tabla `usuarios` y semillas).
  - `database/migracion_fase1_usuarios_y_estados.sql` (script de migración aplicado).
* **Acciones Ejecutadas:**
  1. Contenedores levantados y verificados en puertos `:82` (front), `:83` (back) y `:5432` (db).
  2. Detección dinámica de IP LAN Wi-Fi `192.168.11.17` tras reinicio de Windows.
  3. Creación de tabla `usuarios` con roles (`admin`, `tecnico`, `usuario`) y contraseñas bcrypt.
  4. Modificación de restricción CHECK en `tickets_soporte` para aceptar `'fuerza_mayor'` y creación de la columna `motivo_imposibilidad TEXT`.

---

### FASE 2: Inconsistencias Backend API y Autenticación [COMPLETADA]
* **Objetivo:** Blindar la recepción de datos JSON contra caracteres invisibles de Windows (BOM), crear endpoints de login/perfil y registrar todos los módulos en la API.
* **Archivos Modificados / Creados:**
  - `back_form/configuracion/respuestas_api.php` (sanitización de BOM UTF-8).
  - `back_form/api/auth/login.php` (POST: autenticación con `password_verify` y retorno de rol).
  - `back_form/api/auth/perfil.php` (GET: consulta de perfil de usuario).
  - `back_form/api/soporte_tecnico/actualizar_ticket.php` (soporte para estado `fuerza_mayor` y `motivo_imposibilidad`).
  - `back_form/api/soporte_tecnico/listar_tickets.php` (métrica de `fuerza_mayor` y campo de motivo).
  - `back_form/index.php` (catálogo oficial con los 6 módulos activos).
* **Pruebas Realizadas:**
  - Autenticación probada para `admin` (rol `admin`) y `personal` (rol `usuario`).
  - Filtrado de tickets por estado `pendiente`, `en_proceso` y `fuerza_mayor` validado vía REST.

---

### FASE 3: Frontend - Sistema de Sesión y Control de Roles (RBAC) [SIGUIENTE]
* **Objetivo:** Proveer la interfaz de login y controlar qué secciones puede ver el usuario solicitante frente al administrador/técnico.
* **Archivos Involucrados:**
  - `front_forms/componentes/control_sesion.php` (nuevo helper de sesiones PHP 7.3).
  - `front_forms/login.php` (nueva vista de inicio de sesión con branding institucional).
  - `front_forms/componentes/barra_navegacion.php` (integración de avatar, nombre de usuario, badge de rol y botón Cerrar Sesión).
  - `front_forms/componentes/encabezado.php` (verificación de sesión activa y control de redirección).
* **Matriz de Permisos a Aplicar:**
  - **Personal / Solicitante (`usuario`):** Solo llena datos generales de solicitud (Nombre, Área, Problema, Firma solicitante). No ve diagnósticos técnicos ni bandejas de administración.
  - **Administrador / Técnico (`admin`, `tecnico`):** Control total de formularios técnicos, cierre de tickets, edición de estados y módulo de diagnóstico de PCs.

---

### FASE 4: Frontend - Rediseño del Menú Desplegable (Offcanvas) y Dashboard
* **Objetivo:** Transformar el menú lateral en un centro de comando unificado con estadísticas inmediatas y filtración de tickets.
* **Archivos Involucrados:**
  - `front_forms/componentes/barra_navegacion.php` (reestructuración del panel Offcanvas).
  - `front_forms/recursos/js/cliente_api.js` (método para consultar métricas del dashboard).
  - `front_forms/recursos/css/estilos_personalizados.css` (estilos para tarjetas de KPIs en el menú).
* **Estructura del Menú Desplegable:**
  1. **Dashboard de Estadísticas en Primera Vista:**
     - Tickets Abiertos / Pendientes (Badge azul/ámbar).
     - Tickets En Proceso (Badge celeste).
     - Tickets Resueltos (Badge verde).
     - Fuerza Mayor / Contingencias (Badge púrpura).
     - PCs en Alerta de Hardware (Badge rojo).
  2. **Bandeja de Tickets con Filtración Rápida:**
     - Enlace predeterminado: **Ver Tickets ABIERTOS** (`gestion_tickets.php?estado=pendiente`).
     - Enlaces a: "En Proceso", "Resueltos" y "Fuerza Mayor".
  3. **Accesos a Formularios:**
     - Accesos compactos de 1 clic a los 4 formularios (+ diagnóstico de PCs solo para técnicos).
     - Botón al pie: *"Ver Catálogo Detallado de Formularios"*.

---

### FASE 5: Limpieza de Tarjetas y Simplificación de Formularios
* **Objetivo:** Quitar menús confusos dentro de las tarjetas del catálogo y adaptar los formularios según el rol del usuario conectado.
* **Archivos Involucrados:**
  - `front_forms/index.php` (limpieza de tarjetas).
  - `front_forms/modulos/soporte_tecnico/formulario_soporte.php` (ocultar campos técnicos a solicitantes).
  - `front_forms/modulos/mantenimiento_preventivo/formulario_mantenimiento.php` (restringir a técnicos).
  - `front_forms/modulos/registro_incidencias/formulario_incidencias.php`.
  - `front_forms/modulos/asignacion_accesos/formulario_accesos.php`.
* **Criterio de Limpieza:**
  - En las tarjetas del catálogo: un único botón claro por tarjeta (`Llenar Solicitud` para usuarios normales, o `Registrar Formulario` / acceso a bandeja para técnicos).
  - En los formularios: el solicitante no ve preguntas como *"¿Llenar campos de soporte técnico inmediatamente?"*. El técnico realiza la atención técnica desde la bandeja de gestión.

---

### FASE 6: Integración del Módulo de Diagnóstico, Pruebas y Validación Final
* **Objetivo:** Verificar la comunicación de telemetría de PCs en vivo, acceso desde dispositivos móviles en la LAN institucional y cierre documental.
* **Archivos Involucrados:**
  - `front_forms/modulos/diagnostico/monitoreo_equipos.php` y `detalle_equipo.php`.
  - `Cosmol-DiagnosticoP/agente_diagnostico_pc/` (recolector y disparador).
  - `Docs/reordenamiento_e_integracion.md` (documento de arquitectura final).
* **Validaciones Finales:**
  - Ejecución del recolector en Windows y verificación de recepción en PostgreSQL.
  - Comprobación de alertas de hardware en el menú desplegable.
  - Navegación responsive desde tablet/móvil en `http://192.168.11.17:82`.
  - Verificación de consistencia en Git.