# Módulo 1: Formulario de Soporte Técnico

Guía técnica y funcional del módulo de soporte técnico institucional.

---

## 1. Secciones del Formulario

El formulario consta de las siguientes 6 secciones ordenadas:

1. **Datos Generales:**
   - Nombre del solicitante (`nombre_solicitante`)
   - Fecha de registro (`fecha_solicitud`)
   - Departamento o área (`departamento_area`)
2. **Tipo de Soporte Requerido:**
   - Hardware: equipo, periférico (`soporte_hardware`)
   - Software: aplicaciones, sistema (`soporte_software`)
3. **Detalle del Problema / Requerimiento:**
   - Descripción detallada (`descripcion_problema`)
   - Equipo afectado (si aplica):
     - Número de serie / inventario (`numero_serie`)
     - Marca / modelo (`marca_modelo`)
     - Sistema operativo (`sistema_operativo`)
     - Dirección MAC / IP local (`direccion_mac`, `diagnostico_id`)
4. **Prioridad (A definir por soporte):**
   - Urgente (afecta operaciones críticas)
   - Alta (afecta productividad significativa)
   - Media (molestia operativa pero no detiene trabajo)
   - Baja (requerimiento rutinario/menor)
5. **Datos del Técnico (Para completar por soporte):**
   - Fecha / hora de atención (`fecha_hora_atencion`)
   - Técnico asignado (`tecnico_asignado` / `tecnico_asignado_id`)
   - Diagnóstico (`diagnostico`)
   - Solución aplicada (`solucion_aplicada`)
   - Tipo de resolución (`tipo_resolucion`)
6. **Observaciones / Recomendaciones:**
   - Observaciones y sugerencias (`observaciones_recomendacion`)
   - Firmas digitales canvas (`firma_solicitante`, `firma_sistemas`)

---

## 2. Estructura en Base de Datos (PostgreSQL)

- **Tabla `tickets_soporte`:** Almacena Secciones 1 a 4 (solicitud, prioridad, equipo, estado y auditoría). Correlativo único institucional: `SOP-YYYY-XXXX`.
- **Tabla `atenciones_soporte`:** Almacena Secciones 5 y 6 (datos del técnico, resolución y observaciones) vinculada por `ticket_id` (FK).

---

## 3. Endpoints del Backend (`back_form/api/soporte_tecnico/`)

- `crear_ticket.php` (POST): Registrar ticket y auto-vincular inventario y telemetría.
- `actualizar_ticket.php` (POST/PUT): Gestión, asignación/derivación y resolución con auditoría.
- `listar_tickets.php` (GET): Listado con filtros de estado, prioridad y paginación.
- `obtener_ticket.php` (GET): Detalle completo del ticket y su atención.
- `buscar_equipos.php` (GET): Catálogo de equipos por serie o hostname.
- `servicios/`:
  - `servicio_codigo_soporte.php`: Correlativo `SOP-YYYY-XXXX`.
  - `servicio_telemetria_soporte.php`: Vinculación de MAC/IP.
  - `servicio_atencion.php`: Inserción y actualización de ficha técnica.
  - `servicio_equipos.php`: Resolución de inventario.
  - `servicio_firmas.php`: Validación de firmas base64.
