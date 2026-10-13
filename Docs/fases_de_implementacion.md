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
| **FASE 2** | **Backend Core: RBAC, Derivación y Telemetría** | **COMPLETADA** | Sanitización BOM UTF-8, `/api/auth/listar_tecnicos.php`, derivación de tickets, auto-enlace de telemetría y consultas enriquecidas. |
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

### FASE 2: Backend API Core, RBAC, Derivación y Telemetría [COMPLETADA]
* **Objetivo:** Blindar la recepción de datos JSON contra caracteres invisibles de Windows (BOM), crear endpoints de login/perfil, derivación técnica RBAC y auto-vinculación con el módulo de telemetría de PCs.
* **Archivos Modificados / Creados:**
  - `database/migracion_fase2_soporte_telemetria_rbac.sql` (migración aplicada: `usuario_solicitante_id`, `tecnico_asignado_id`, `tecnico_id`, `asignado_por_id`).
  - `database/formularios_inicial.sql` (actualización de DDL con nuevas llaves foráneas e índices).
  - `back_form/configuracion/respuestas_api.php` (sanitización de BOM UTF-8 y fallback de payload).
  - `back_form/api/auth/login.php` y `perfil.php` (autenticación y perfil de usuario).
  - `back_form/api/auth/listar_tecnicos.php` (GET: lista de técnicos y administradores activos para derivación).
  - `back_form/api/diagnostico/detectar_mi_pc.php` (GET: auto-detección de PC por IP en red local `REMOTE_ADDR`).
  - `back_form/api/soporte_tecnico/crear_ticket.php` (RBAC, respaldo por IP del cliente, auto-vinculación con telemetría de PC por MAC/equipo y persistencia de solicitante).
  - `back_form/api/soporte_tecnico/actualizar_ticket.php` (derivación de tickets por administradores, cambio a `en_proceso`, cierre con firma y actualización de diagnósticos).
  - `back_form/api/soporte_tecnico/obtener_ticket.php` (respuesta enriquecida con objeto `telemetria_pc`, `telemetria_actual_en_vivo`, técnicos y `motivo_imposibilidad`).
  - `back_form/api/soporte_tecnico/listar_tickets.php` (filtros `usuario_solicitante_id`, `tecnico_asignado_id`, indicador `tiene_diagnostico` y semáforo de salud de PC).
  - `back_form/api/diagnostico/detalle.php` (inspección de hardware, SMART, Defender, Prefetch e historial cruzado de tickets de la PC).
  - `back_form/api/diagnostico/listar.php` (filtros por estado de salud `critico`/`advertencia`, buscador y métricas globales).
* **Pruebas Realizadas:**
  - Auto-detección de PC en vivo vía `/api/diagnostico/detectar_mi_pc.php?ip=192.168.11.75` retornando MAC `2C:3B:70:D8:1E:81`, hostname y métricas de salud.
  - Creación de ticket con auto-detección por IP remota (`SOP-2026-0005`) sin enviar MAC explícita, resolviendo exitosamente `diagnostico_id: 6` y `direccion_mac`.
  - Derivación técnica por Administrador a técnico específico con actualización de trazabilidad (`asignado_por_id: 1`, `tecnico_asignado_id: 2`).
  - Endpoint `obtener_ticket.php` probado retornando el bloque completo de telemetría con alertas de disco y uso de RAM.
  - Endpoint `detalle.php` de telemetría probado retornando el historial cruzado de tickets asociados a la computadora física.

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

### FASE 4: Frontend - Menú Desplegable Izquierdo y Pantalla Dedicada "MÉTRICA" (`metricas.php`) [COMPLETADA]
* **Objetivo:** Disponer el menú lateral en el margen izquierdo (`offcanvas-start`) y centralizar el dashboard de estadísticas en una pantalla completa dedicada con su propia dirección URL (`front_forms/metricas.php`), accesible mediante el botón **"MÉTRICA"** sin ventanas emergentes.
* **Archivos Involucrados:**
  - `front_forms/metricas.php` (nueva pantalla completa con URL propia `/metricas.php`, KPIs en vivo, filtración rápida y accesos a módulos).
  - `front_forms/componentes/barra_navegacion.php` (botón de menú en lado izquierdo, panel lateral izquierdo y enlaces directos a `metricas.php`).
  - `front_forms/recursos/js/cliente_api.js` (método `obtenerMetricasDashboard()` y actualización de indicadores en vivo).
  - `front_forms/recursos/css/estilos_personalizados.css` (estilos para botón MÉTRICA y diseño de panel).
* **Estructura Implementada:**
  1. **Navegación Izquierda:**
     - Botón de 3 Rayas (`☰ Menú`) posicionado en el extremo izquierdo del navbar en todas las vistas.
     - Panel lateral deslizante desde la izquierda (`offcanvas-start`).
     - Botón destacado **"MÉTRICA"** que navega directamente a `/metricas.php` (también presente en la barra superior para personal técnico).
  2. **Pantalla Dedicada "MÉTRICA" (`/metricas.php`):**
     - **Dirección propia:** Permite acceso directo y navegación estándar en el navegador sin popups.
     - **KPIs en tiempo real:** Tickets Abiertos/Pendientes (ámbar), En Proceso (celeste), Resueltos (verde), Fuerza Mayor (púrpura), y Equipos con Alertas SMART/CPU/Defender (rojo).
     - **Bandeja de Tickets con Filtración Rápida:** Enlace principal a Tickets Abiertos prioritarios, y botones secundarios para filtrar por estado con 1 clic.
     - **Auditoría de Hardware:** Tarjeta destacada con alerta de equipos y enlace a telemetría.
  3. **Accesos Compactos a Formularios:**
     - Enlaces directos a los 4 formularios activos (+ telemetría técnica) con botones para diligenciar y gestionar.
     - Botón inferior hacia el Catálogo Detallado.

---

### FASE 5: Limpieza de Tarjetas y Simplificación de Formularios (COMPLETADA)
* **Objetivo:** Quitar menús confusos dentro de las tarjetas del catálogo y adaptar los formularios según el rol del usuario conectado.
* **Estado:** `COMPLETADA`
* **Archivos Involucrados:**
  - `front_forms/index.php`: Un solo botón de acción claro por tarjeta (`Llenar Solicitud` para solicitantes; `Bandeja de Gestión` / `Registrar Mantenimiento` para personal técnico). Mantenimiento Preventivo bloqueado con candado informativo para solicitantes.
  - `front_forms/modulos/soporte_tecnico/formulario_soporte.php`: Oculta el switch de campos técnicos y la sección de resolución a solicitantes.
  - `front_forms/modulos/soporte_tecnico/secciones/seccion_3_detalle_problema.php`: Bloque amigable de equipo para solicitantes; buscador e inventario técnico completo para técnicos.
  - `front_forms/modulos/soporte_tecnico/secciones/seccion_firmas.php`: Oculta la Firma del Dpto. de Sistemas al solicitante (centra la del Solicitante).
  - `front_forms/modulos/mantenimiento_preventivo/formulario_mantenimiento.php`: Restringido estrictamente a roles `admin` y `tecnico` (`$roles_requeridos`).
  - `front_forms/modulos/registro_incidencias/secciones/seccion_firmas.php`: Oculta Firma de Sistemas al solicitante.
  - `front_forms/modulos/asignacion_accesos/secciones/seccion_firmas.php`: Oculta Firma de Sistemas al solicitante, manteniendo Solicitante y Autorización de Jefatura.
  - `front_forms/recursos/js/formulario_soporte.js`, `formulario_incidencias.js`, `formulario_accesos.js`: Redirección inteligente tras el registro (solicitante vuelve a `index.php`, técnico va a su bandeja de gestión).
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