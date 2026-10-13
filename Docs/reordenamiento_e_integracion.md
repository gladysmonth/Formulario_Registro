# Plan Maestro de Reordenamiento, Seguridad e Integración del Sistema
**Plataforma Unificada de Formularios y Auditoría PC - Cosmol R.L.**  
*Documento de Arquitectura, Decisiones Técnicas y Guía de Implementación*  
*Fecha: Octubre 2026 | Versión: 2.0-Modular*

---

## 1. Resumen Ejecutivo y Objetivos

El presente documento define la reestructuración integral del frontend, backend, esquema de datos y despliegue del repositorio **`Formulario_Registro`**, incorporando plenamente el nuevo módulo de **Auditoría y Telemetría de PCs** desarrollado en conjunto con `Cosmol-DiagnosticoP`.

### Objetivos Clave:
1. **Control de Acceso y Roles (RBAC):** Implementar autenticación para diferenciar entre **Personal/Usuario Solicitante** (acceso únicamente a datos generales de solicitud) y **Administrador/Técnico de Sistemas** (control total de diagnósticos, resoluciones y telemetría).
2. **Navegación Limpia y Dashboard en Menú Desplegable:** Sustituir submenús fragmentados dentro de tarjetas por un menú lateral *Offcanvas* unificado que incluya en primera vista un **Dashboard de Estadísticas (KPIs)**, accesos directos a los formularios y una bandeja de tickets que priorice por defecto los **Tickets Abiertos**.
3. **Casos de Uso Especiales de Tickets:** Contemplar estados para contingencias que imposibiliten la resolución inmediata por **Fuerza Mayor** (ej. falta de repuestos, proveedor externo, impedimento ajeno).
4. **Seguridad y Dockerización con `.env`:** Parametrizar credenciales y red en `docker-compose.yml` utilizando `.env`, fijando la IP del host (`192.168.11.75`) para acceso fluido desde tablets y dispositivos móviles en la LAN institucional.
5. **Centralización de Base de Datos:** Reubicar `init.sql` a `database/formularios_inicial.sql` junto con `database/diagnostico.sql`, garantizando despliegues automatizados y limpios.
6. **Políticas Git:** Establecer un `.gitignore` integral para excluir `.env`, temporales y cachés.

---

## 2. Auditoría de Inconsistencias en el Backend y Opciones de Solución

Durante la auditoría del backend PHP 7.3 y PostgreSQL se identificaron las siguientes inconsistencias:

### Inconsistencia A: Parámetros y Credenciales Hardcodeadas en Docker
* **Diagnóstico actual:** En `docker-compose.yml`, `URL_BACKEND` tiene asignada la IP obsoleta `http://192.168.11.70:83` en lugar de la IP actual `192.168.11.75:83`. Además, las credenciales de PostgreSQL (`postgres_clave_secreta`) están explícitas en el compose y el archivo `.env` se encuentra vacío (0 bytes).
* **Solución Propuesta (Recomendada):**
  1. Poblar `.env` con variables: `HOST_IP=192.168.11.75`, `FRONT_PORT=82`, `BACK_PORT=83`, `DB_PORT=5432`, `DB_NAME=formularios_db`, `DB_USER=postgres_usuario`, `DB_PASS=postgres_clave_secreta`.
  2. Actualizar `docker-compose.yml` para consumir `${HOST_IP}`, `${DB_USER}`, etc.
  3. En `front_forms/componentes/encabezado.php`, mantener la autodetección de host mediante `$_SERVER['HTTP_HOST']` con fallback al valor del `.env`.

### Inconsistencia B: Ubicación y Secuencia de Inicialización de la Base de Datos
* **Diagnóstico actual:** `init.sql` reside en la raíz de `Formulario_Registro`, mientras que `diagnostico.sql` reside en `database/`. En `docker-compose.yml` sólo se monta `init.sql` en `/docker-entrypoint-initdb.d/init.sql:ro`. Si se inicia un contenedor limpio, las tablas de telemetría no se crean automáticamente.
* **Solución Propuesta (Recomendada):**
  1. Mover `init.sql` a `database/formularios_inicial.sql`.
  2. Modificar el volumen de `db` en `docker-compose.yml` para montar la carpeta completa de esquemas:
     ```yaml
     volumes:
       - ./database/formularios_inicial.sql:/docker-entrypoint-initdb.d/01_formularios_inicial.sql:ro
       - ./database/diagnostico.sql:/docker-entrypoint-initdb.d/02_diagnostico.sql:ro
       - datos_postgres:/var/lib/postgresql/data
     ```

### Inconsistencia C: Restricción de Estados en Tickets y Casos de Fuerza Mayor
* **Diagnóstico actual:** En `init.sql` la tabla `tickets_soporte` posee la restricción:
  ```sql
  CHECK (estado IN ('pendiente', 'en_proceso', 'resuelto', 'cancelado'))
  ```
  Esto no permite reflejar casos donde el técnico no puede completar el trabajo por causas de fuerza mayor (ej. equipo en garantía de fábrica, repuestos descontinuados, falla de enlace ISP).
* **Opciones de Solución:**
  * **Opción C.1 (Recomendada):** Ampliar el constraint de PostgreSQL incorporando `'fuerza_mayor'` (o `'bloqueado'`) y añadir el campo `motivo_imposibilidad TEXT`. Permite filtrar en la bandeja con una etiqueta ámbar/violeta distintiva y contabilizarlo en el Dashboard.
  * **Opción C.2:** Conservar `'cancelado'` y añadir un campo complementario `subtipo_cierre VARCHAR(50)` ('fuerza_mayor', 'anulado_usuario', 'fuera_alcance').
  * *Recomendación:* Implementar la **Opción C.1** por ser transparente en la analítica de soporte.

### Inconsistencia D: Sanitización de UTF-8 BOM en Respuestas API
* **Diagnóstico actual:** Peticiones HTTP enviadas desde scripts PowerShell nativos en Windows pueden incluir un Byte Order Mark (`\xEF\xBB\xBF`), lo que provoca que `json_decode()` en PHP 7.3 devuelva `NULL` con `JSON_ERROR_SYNTAX`.
* **Solución Propuesta:** En `back_form/configuracion/respuestas_api.php`, dentro de `obtener_cuerpo_json()`, aplicar la limpieza inmediata:
  ```php
  $cuerpo_crudo = preg_replace('/^\xEF\xBB\xBF/', '', $cuerpo_crudo);
  ```

### Inconsistencia E: Catálogo de Endpoints Incompleto en `back_form/index.php`
* **Diagnóstico actual:** El healthcheck únicamente lista los endpoints de `soporte_tecnico`.
* **Solución Propuesta:** Actualizar el mapa de `modulos_disponibles` para incluir `mantenimiento_preventivo`, `registro_incidencias`, `asignacion_accesos` y `diagnostico`.

---

## 3. Matriz de Permisos y Perfiles de Usuario (RBAC)

Para evitar que los usuarios administrativos o de planta completen campos técnicos o modifiquen estados que no les corresponden, se establece la siguiente división:

| Aspecto / Módulo | Rol: Personal / Solicitante (`usuario`) | Rol: Técnico / Administrador (`admin` / `tecnico`) |
| :--- | :--- | :--- |
| **Inicio de Sesión** | Usuario/clave institucional asignado. | Usuario/clave de Sistemas con privilegios elevados. |
| **Formulario Soporte** | Secciones 1, 2, 3 (Datos Solicitante, Equipo/Problema, Prioridad estimada, Firma Solicitante). | Secciones 1 a 6 completas + Diagnóstico + Solución + Técnico Asignado + Firma Técnica. |
| **Mantenimiento Preventivo** | Solo lectura de ficha básica o registro de solicitud. | Inspección física interna/externa, lubricación, voltajes, temperaturas y checklists. |
| **Incidencias y Accesos** | Reporte básico de anomalía o solicitud de cuenta. | Diagnóstico de plataformas ERP, análisis de logs, asignación formal de perfiles y cierres. |
| **Bandeja de Gestión** | Vista restringida a "Mis Solicitudes" (solo estado y fecha). | Bandeja completa: cambiar estados, clasificar urgencias, atender y cerrar tickets. |
| **Módulo de Diagnóstico PC** | Oculto o restringido. | Acceso completo: telemetría en tiempo real, alertas de hardware, SMART, Prefetch y Defender. |
| **Dashboard de Estadísticas** | Métricas personales (solicitudes pendientes del usuario). | Métricas globales: tickets por estado, equipos en advertencia, tiempos promedio de atención. |

---

## 4. Reestructuración de la Navegación y Menú Desplegable

### 4.1. Limpieza de Tarjetas en el Catálogo (`index.php`)
* **Problema actual:** Las tarjetas poseen múltiples botones en su pie (`Llenar Formulario` y `Bandeja de Gestión`), generando confusión sobre a dónde dirigirse.
* **Rediseño:**
  * Si el usuario es **Personal/Solicitante:** Las tarjetas muestran un único botón de llamada a la acción: `Llenar Solicitud`.
  * Si el usuario es **Administrador/Técnico:** La tarjeta muestra un botón principal `Registrar Formulario` y un botón secundario de redirección directa a la bandeja con el filtro de **Tickets Abiertos** activado por defecto.
  * Se eliminan selectores o menús anidados dentro del cuerpo de la tarjeta.

### 4.2. Menú Desplegable Lateral (Offcanvas)
El botón de 3 líneas (hamburguesa) en la barra superior desplegará un panel organizado en 3 bloques verticales:

1. **Bloque 1: Dashboard de Métricas Rápidas (A Primera Vista):**
   * Total de Tickets Abiertos / Pendientes (badge amarillo/azul).
   * Total en Proceso (badge celeste).
   * Total Resueltos (badge verde).
   * Contingencias / Fuerza Mayor (badge naranja).
   * Equipos con Alerta de Hardware (badge rojo, alimentado por el módulo de telemetría).
2. **Bloque 2: Catálogo de Formularios y Módulos:**
   * Formulario 1: Soporte Técnico.
   * Formulario 2: Mantenimiento Preventivo.
   * Formulario 3: Incidencias de Sistemas.
   * Formulario 4: Asignación de Accesos.
   * Módulo 5 (Técnico): Auditoría y Diagnóstico de PCs en Vivo.
   * *Acceso al Catálogo General:* Botón inferior destacado para ver la vista detallada de todos los formularios.
3. **Bloque 3: Bandeja de Tickets con Filtrado Directo:**
   * Enlace directo: **Ver Tickets Abiertos** (ruta por defecto: `gestion_tickets.php?estado=pendiente`).
   * Enlace directo: **Ver Tickets en Proceso** (`gestion_tickets.php?estado=en_proceso`).
   * Enlace directo: **Ver Tickets Resueltos** (`gestion_tickets.php?estado=resuelto`).
   * Enlace directo: **Ver Casos de Fuerza Mayor** (`gestion_tickets.php?estado=fuerza_mayor`).

---

## 5. Exposición de Red LAN y Compatibilidad Móvil (Tablets)

Para que los técnicos y personal puedan operar desde tablets y smartphones en la red `192.168.11.X`:
1. Los contenedores en `docker-compose.yml` exponen sus puertos al host de Windows:
   * Front-end: `192.168.11.75:82`
   * Back-end: `192.168.11.75:83`
2. En `front_forms/componentes/encabezado.php`:
   ```javascript
   // Resuelve automáticamente la IP con la que el cliente cargó la página
   window.URL_BACKEND = 'http://' + window.location.hostname + ':83';
   ```
   Esto elimina cualquier conflicto si la IP del servidor cambia o si se accede por nombre de host.

---

## 6. Seguridad del Repositorio (`.gitignore`)

Se crea en la raíz de `Formulario_Registro` el archivo `.gitignore` con las siguientes exclusiones:
* `.env` y `.env.local` (evita filtraciones de claves y contraseñas de BD en Git).
* Logs del sistema y de Apache (`*.log`, `back_form/logs/`).
* Archivos temporales de sistema y editores (`.DS_Store`, `Thumbs.db`, `.vscode/`, `.idea/`).
* Copias de respaldo (`*.bak`, `*.tmp`).

---

## 7. Plan de Ejecución Secuencial

1. **Paso 1 (Docker & Seguridad):** Crear `.env`, poblar variables seguras, actualizar `docker-compose.yml`, mover `init.sql` a `database/formularios_inicial.sql` y crear `.gitignore`.
2. **Paso 2 (Base de Datos):** Aplicar migración para tabla de `usuarios` y ampliar constraint de estados para `fuerza_mayor`.
3. **Paso 3 (Backend API):** Sanitizar BOM en `respuestas_api.php`, registrar módulos en `back_form/index.php`, y agregar endpoint de autenticación `/api/auth/login.php`.
4. **Paso 4 (Frontend - Roles y Sesión):** Crear vista `login.php` y componentes de control de sesión en `encabezado.php` y `barra_navegacion.php`.
5. **Paso 5 (Frontend - Menú Offcanvas y Dashboard):** Implementar las métricas de primera vista y los filtros directos en el menú desplegable.
6. **Paso 6 (Frontend - Limpieza de Tarjetas y Formularios):** Adecuar las tarjetas del catálogo e independizar los campos técnicos según el rol autenticado.
7. **Paso 7 (Documentación):** Actualizar `Docs/` y `.agents/` para documentar la arquitectura final.