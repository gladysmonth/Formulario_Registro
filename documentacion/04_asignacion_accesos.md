# Módulo 4: Formulario de Creación y Asignación de Accesos

Guía técnica y funcional del módulo de solicitud de cuentas y permisos.

---

## 1. Secciones del Formulario

El formulario consta de las siguientes 6 secciones y triple firma digital:

1. **Datos Generales:**
   - Nro. de Solicitud (`nro_solicitud`) -> Ej: `ACC-YYYY-XXXX`
   - Fecha de Solicitud (`fecha_solicitud`)
   - Nombre del Solicitante (`nombre_solicitante`)
   - Cargo del Solicitante (`cargo_solicitante`)
   - Área / Departamento (`area_departamento`)
2. **Plataforma / Sistema Afectado:**
   - SISTEMA ERP - SAI (`sistema_erp_sai`)
   - SISTEMA DE COBRANZAS - NETCOB (`sistema_cobranzas_netcob`)
   - OTROS SISTEMAS / COMPLEMENTOS (`sistema_otros` y `sistema_otros_detalle`)
3. **Datos del Usuario:**
   - Usuario Nuevo (`es_usuario_nuevo`: boolean)
   - Username / Detalles de cuenta (`nombre_usuario_detalles`)
4. **Requerimientos de Accesos:**
   - Detalle amplio de perfiles, permisos específicos o módulos solicitados (`requerimientos_accesos`: text)
5. **Departamento de Sistemas (Atención y Comentarios):**
   - Atendido por: Técnico responsable (`atendido_por`)
   - Fecha y Hora de Atención (`fecha_hora_atencion`)
   - Estado de la Solicitud (`estado`: 'pendiente', 'en_proceso', 'atendido', 'rechazado')
   - Comentarios técnicos / Restricciones (`comentarios_sistemas`)
6. **Firmas / Validaciones Institucionales (Lienzos Canvas):**
   - Firma del Solicitante (`firma_solicitante`)
   - Nombre y Firma de Autorización / Jefatura (`nombre_autoriza`, `firma_autoriza`)
   - Firma del Dpto. de Sistemas (`firma_sistemas`)

---

## 2. Estructura en Base de Datos (PostgreSQL)

- **Tabla `solicitudes_accesos`:** Almacena la solicitud, estados y triple firma base64. Correlativo institucional: `ACC-YYYY-XXXX`.

---

## 3. Endpoints del Backend (`back_form/api/asignacion_accesos/`)

- `crear_solicitud.php` (POST): Registrar solicitud de accesos.
- `actualizar_solicitud.php` (POST): Atención técnica, asignación de permisos y cierre.
- `listar_solicitudes.php` (GET): Listado con KPIs de pendientes, aprobados y filtros.
- `obtener_solicitud.php` (GET): Detalle completo de la solicitud de acceso.
- `servicios/`:
  - `servicio_codigo_accesos.php`: Correlativo `ACC-YYYY-XXXX`.
  - `servicio_validaciones_accesos.php`: Validación de requerimientos.
  - `servicio_firmas_accesos.php`: Triple validación de firmas digitales.
