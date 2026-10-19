# Módulo 5: Diagnóstico, Telemetría y Monitoreo de PCs

Guía técnica y de integración del ecosistema de auditoría de hardware y telemetría en tiempo real (Cosmol-DiagnosticoP).

---

## 1. Arquitectura de Telemetría e Ingesta

El sistema integra telemetría desatendida desde las PCs institucionales mediante un agente PowerShell nativo (`Cosmol-DiagnosticoP`).

1. **Ingesta Automática (`recibir.php`):**
   - El agente envía una carga JSON periódica con metadatos de red, resumen de salud ponderado (0-100), componentes de hardware, almacenamiento SMART y estado de seguridad de Windows Defender.
2. **Auto-Detección en Formularios (`detectar_mi_pc.php`):**
   - Al abrir un formulario (Soporte o Mantenimiento Preventivo), el navegador consulta la IP del cliente (`REMOTE_ADDR`) y auto-detecta la MAC, Hostname y métricas de salud del equipo sin intervención manual del usuario.
3. **Tablero de Monitoreo Central (`monitoreo_equipos.php`):**
   - Panel de control para administradores y técnicos con semaforización de salud (Óptimo, Advertencia, Crítico), alertas de espacio en disco y antigüedad de telemetría.
4. **Inspección Profunda de Hardware (`detalle_equipo.php`):**
   - Vista detallada con pestañas de Hardware (CPU, RAM, Discos, Red), Seguridad (Defender, Prefetch) e Historial cronológico de diagnósticos.

---

## 2. Estructura en Base de Datos (PostgreSQL)

- **Tabla `historial_diagnosticos`:** Registra cada payload histórico de telemetría emitido por los agentes, indexado por `direccion_mac`, `fecha_registro` y `estado_general`.
- **Vista `vista_ultimo_diagnostico_pc`:** Vista SQL consolidada con `DISTINCT ON (direccion_mac)` que provee el último snapshot vigente de cada equipo para alimentar el tablero de monitoreo y la auto-detección.

---

## 3. Endpoints del Backend (`back_form/api/diagnostico/`)

- `recibir.php` (POST): Ingesta de payload JSON del agente PowerShell (`metadatos`, `resumen_salud`, `hardware`, `software_y_seguridad`).
- `listar.php` (GET): Catálogo y estado de salud de PCs desde `vista_ultimo_diagnostico_pc` con soporte para filtros de estado.
- `detalle.php` (GET): Diagnóstico profundo e historial cronológico filtrado por dirección MAC (`?mac=XX:XX:XX:XX:XX:XX`).
- `detectar_mi_pc.php` (GET): Detección en vivo de la PC del solicitante vía IP local (`REMOTE_ADDR`), retornando datos de hardware y último diagnóstico.

---

## 4. Frontend y Controladores (`front_forms/modulos/diagnostico/`)

- **Tablero de Monitoreo (`monitoreo_equipos.php`):**
  - `componentes/kpis_monitoreo.php`: Métricas agregadas (Total PCs, Saludables, En Advertencia, Críticos).
  - `componentes/tabla_monitoreo.php`: Listado tabular interactivo con badges de estado y enlaces a detalle.
  - `recursos/js/monitoreo_equipos.js`: Controlador asíncrono para refresco de tabla y filtros dinámicos.
- **Ficha Técnica de Detalle (`detalle_equipo.php`):**
  - `componentes/cabecera_equipo.php`: Badge de salud, hostname, MAC, IP y última sincronización.
  - `componentes/tab_hardware.php`: Desglose de procesador, memoria RAM, discos SMART e interfaces de red.
  - `componentes/tab_seguridad.php`: Estado de protección en tiempo real, definiciones de Defender y aplicaciones prefetch.
  - `componentes/tab_historial.php`: Historial cronológico de telemetría con comparación temporal.
  - `recursos/js/detalle_equipo.js`: Controlador modular de renderizado y conmutación de pestañas.

---

## 5. Control de Acceso (RBAC)

- La telemetría profunda y el tablero de monitoreo son de acceso exclusivo para roles **`admin`** y **`tecnico`**.
- Los usuarios con rol `solicitante` únicamente consumen el servicio transparente de auto-detección (`detectar_mi_pc.php`) para pre-cargar los datos de su equipo en los formularios.
