# Especificación de Campos para Frontend: Vista Cliente vs. Vista Técnico
**Plataforma Modular de Formularios y Auditoría Técnica - Cosmol R.L.**  
*Documento de Referencia Técnica y Contrato de Datos para el Desarrollo Frontend*  
*Ubicación: `Formulario_Registro/Docs/especificacion_campos_frontend_cliente_vs_tecnico.md`*  
*Versión: 1.0 | Octubre 2026*

---

## 1. Principio Fundamental de Diseño (UX y Roles)

El personal administrativo y operativo de Cosmol (Contabilidad, Cobranzas, Recursos Humanos, Atención al Socio) **no posee conocimientos informáticos avanzados**. 

Pedirles que ingresen su *Número de Serie*, *Sistema Operativo*, *Código de Activo*, *Prioridad Técnica* o *Trazas de error* provoca errores de registro, frustración y rechazo del sistema.

### Reglas Clave para el Frontend:
1. **El Cliente / Solicitante (`rol: 'usuario'`):**
   - Solo ve campos sencillos, preguntas humanas y comprensibles.
   - Solo puede registrar tickets en estado **`pendiente`**.
   - Solo firma en el lienzo de **Firma del Solicitante**.
   - **No ve ni puede activar** selectores técnicos, campos de diagnóstico, solución ni firma de Sistemas.
2. **La PC reporta sola (Telemetría Cosmol):**
   - El agente silencioso en PowerShell ya recopila y envía al backend las especificaciones físicas, el número de serie, la dirección MAC, salud SMART y uso de memoria.
   - El frontend del cliente no necesita pedirle esos datos: el backend asocia la radiografía técnica automáticamente en segundo plano.
3. **El Técnico / Administrador (`rol: 'tecnico' | 'admin'`):**
   - Opera desde la **Bandeja de Gestión** y el **Modal de Atención**.
   - Ve la tarjeta de telemetría viva de la PC vinculada.
   - Asigna o deriva el ticket a un técnico activo.
   - Registra el diagnóstico formal, solución aplicada, tipo de resolución y cierra con su **Firma de Sistemas**.

---

## 2. Formulario 1: Soporte Técnico

### 2.1. Vista del Solicitante (Formulario de Creación de Ticket)
* **Archivo Frontend a simplificar:** `front_forms/modulos/soporte_tecnico/formulario_soporte.php`
* **Endpoint de envío:** `POST /api/soporte_tecnico/crear_ticket.php`

#### Campos que SÍ debe renderizar la interfaz del Cliente:

| Campo UI | Nombre de Variable / Payload | Tipo de Input | Descripción y Comportamiento |
| :--- | :--- | :--- | :--- |
| **Nombre del Solicitante** | `nombre_solicitante` | `text` (requerido) | Autocompletado si hay sesión iniciada (`usuario.nombre_completo`). |
| **ID de Usuario** | `usuario_solicitante_id` | `hidden` | ID del usuario logueado en sesión. |
| **Departamento / Área** | `departamento_area` | `select` (requerido) | Área o gerencia del solicitante (autocompletada por sesión). |
| **Fecha de Solicitud** | `fecha_solicitud` | `date` (solo lectura) | Fecha actual por defecto (`YYYY-MM-DD`). |
| **¿En qué equipo ocurre la falla?** | `referencia_equipo` | `select` amigable | Opciones claras:<br>1. **"En mi computadora actual"** (toma la MAC local).<br>2. **"En una impresora / periférico"**.<br>3. **"En otro equipo / sala"** (habilita campo de texto breve). |
| **¿Qué tipo de problema nota?** | `soporte_hardware`<br>`soporte_software` | Checkboxes amigables | $\square$ **Falla de Equipo / Físico** (ej. No enciende, pantalla rota, impresora trabada).<br>$\square$ **Problema de Programas** (ej. No abre el sistema, Office pide clave, lentitud). *(Al menos 1 obligatorio)*. |
| **Descripción de la Falla** | `descripcion_problema` | `textarea` (requerido) | Espacio para que el usuario relate en sus palabras qué ocurre y desde cuándo. |
| **Firma del Solicitante** | `firma_solicitante` | `canvas` (requerido) | Firma digital en Base64 de quien solicita el servicio. |
| **Rol del Emisor** | `rol_usuario` | `hidden` | Valor `'usuario'` (para garantizar que el backend no acepte cierres no autorizados). |

#### 🚫 Campos que DEBEN OCULTARSE al Solicitante:
- ❌ Código de Activo
- ❌ Número de Serie / Inventario
- ❌ Sistema Operativo
- ❌ Centro de Costo
- ❌ Prioridad Técnica (Urgente / Alta / Media / Baja)
- ❌ Interruptor de *"¿Llenar campos de soporte inmediatamente?"*
- ❌ Datos del Técnico, Diagnóstico, Solución y Tipo de Resolución
- ❌ Firma del Departamento de Sistemas

#### Payload JSON enviado por el Solicitante (`POST /api/soporte_tecnico/crear_ticket.php`):
```json
{
  "nombre_solicitante": "María López Pérez",
  "usuario_solicitante_id": 3,
  "departamento_area": "Contabilidad y Finanzas",
  "fecha_solicitud": "2026-10-13",
  "soporte_hardware": true,
  "soporte_software": false,
  "descripcion_problema": "La impresora emite un pitido constante y no saca las facturas.",
  "direccion_mac": "2C:3B:70:D8:1E:81",
  "rol_usuario": "usuario",
  "firma_solicitante": "data:image/png;base64,iVBORw0KGgoAAA..."
}
```

---

### 2.2. Vista del Técnico / Administrador (Modal de Atención y Cierre)
* **Archivo Frontend:** `front_forms/modulos/soporte_tecnico/componentes_gestion/modal_atencion.php`
* **Endpoint de carga de datos:** `GET /api/soporte_tecnico/obtener_ticket.php?id={id}`
* **Endpoint de guardado/cierre:** `POST /api/soporte_tecnico/actualizar_ticket.php`

#### Campos y Bloques a mostrar en el Modal:

1. **Tarjeta de Telemetría y Salud del Equipo (Solo lectura para el técnico):**
   - Viene en la respuesta de `obtener_ticket.php` dentro del objeto `telemetria_pc`.
   - Muestra:
     - Badge de Salud: `optimo` (verde), `bueno` (azul), `advertencia` (amarillo) o `critico` (rojo).
     - Uso de CPU y Memoria RAM (%).
     - Espacio libre en disco C: (GB).
     - Estado de salud SMART del disco.
     - Alertas automáticas detectadas por el agente.
2. **Prioridad Oficial de Soporte:**
   - Selector: `urgente`, `alta`, `media`, `baja`.
3. **Derivación de Técnico (Si el usuario es `admin`):**
   - Selector alimentado por `GET /api/auth/listar_tecnicos.php`.
   - Envía: `tecnico_asignado_id` y `asignado_por_id`.
4. **Datos de la Intervención Técnica:**
   - **Fecha y Hora de Atención:** `datetime-local` (`fecha_hora_atencion`).
   - **Tipo de Resolución:** `select` (`tipo_resolucion`): *Mantenimiento Correctivo*, *Configuración de Software*, *Reemplazo de Hardware*, *Capacitación*, *Garantía Externa*.
   - **Diagnóstico Técnico:** `textarea` (`diagnostico`): Causa técnica real de la falla.
   - **Solución Aplicada:** `textarea` (`solucion_aplicada`): Pasos realizados para solucionarlo.
   - **Observaciones / Recomendaciones:** `textarea` (`observaciones_recomendacion`).
5. **Estado Operativo:**
   - Selector: `en_proceso`, `resuelto`, `fuerza_mayor`, `cancelado`.
   - **Si selecciona `fuerza_mayor`:** Mostrar obligatoriamente el campo de texto `motivo_imposibilidad` (ej: *"Falta de repuesto / Equipo enviado a taller externo"*).
6. **Firma de Conformidad Técnica:**
   - Lienzo Canvas: `firma_sistemas` (Obligatoria si el estado pasa a `resuelto`).

#### Payload JSON enviado por el Técnico (`POST /api/soporte_tecnico/actualizar_ticket.php`):
```json
{
  "id": 15,
  "tecnico_asignado_id": 2,
  "asignado_por_id": 1,
  "prioridad": "alta",
  "estado": "resuelto",
  "fecha_hora_atencion": "2026-10-13 11:30:00",
  "tipo_resolucion": "Mantenimiento Correctivo",
  "diagnostico": "Rodillo de arrastre de papel obstruido por grapa.",
  "solucion_aplicada": "Limpieza y lubricación de mecanismo de arrastre. Pruebas de impresión satisfactorias.",
  "observaciones_recomendacion": "Revisar papel antes de introducirlo a la bandeja.",
  "firma_sistemas": "data:image/png;base64,iVBORw0KGgoAAA..."
}
```

---

## 3. Formulario 2: Mantenimiento Preventivo (PC / Laptop)

Este formulario es una **Ficha Técnica de Servicio Especializado**.

| Actor | Rol en la Interfaz | Qué campos llena |
| :--- | :--- | :--- |
| **Cliente / Usuario** | **Solo Recepción / Conformidad** | **Ningún campo técnico.** Únicamente firma el lienzo de *Firma del Responsable del Equipo* al finalizar el mantenimiento como constancia de recepción de su equipo. |
| **Técnico de Sistemas** | **Operador Total** | Llena las 6 secciones técnicas:<br>1. Datos Generales (Ubicación, fecha).<br>2. Info Hardware (CPU, RAM, Disco, IP).<br>3. Mantenimiento Físico Externo.<br>4. Mantenimiento Lógico Interno.<br>5. Pruebas de Rendimiento.<br>6. Observaciones y Firma Técnica de Sistemas. |

---

## 4. Formulario 3: Registro de Incidencias de Sistemas (ERP SAI / NETCOB)

### Vista del Cliente / Operador
- **Plataforma Afectada:** Selector (`SISTEMA ERP - SAI`, `SISTEMA DE COBRANZAS - NETCOB`, `OTROS`).
- **Módulo o Tarea que Realizaba:** Texto amigable (ej: *"Módulo de Facturación al intentar emitir recibo"*).
- **Mensaje de Error en Pantalla:** Texto o captura del error visual que le apareció.
- **Firma del Responsable del Reporte.**

### Vista del Técnico de Sistemas
- **Nivel de Criticidad Institucional:** Selector oficial (Crítica Nivel 1: Paralización, Alta Nivel 2: Módulo aislado, Media/Baja Nivel 3: Menor).
- **Naturaleza Técnica del Fallo:** Checkboxes de especialidad:
  - Base de Datos (Detalle técnico de bloqueo o timeout).
  - Infraestructura / Servidor (Consumo de recursos o servicio caído).
  - Enlaces / Conectividad (Corte de VPN o enlace de red).
- **Trazas de Logs / Excepciones:** Detalle técnico de depuración.
- **Solución y Cierre Técnico:** Acción realizada, fecha de cierre y firma técnica.

---

## 5. Formulario 4: Creación y Asignación de Accesos

### Vista del Solicitante / Jefatura
- **Datos del Solicitante:** Nombre, Cargo y Área.
- **Tipo de Usuario:** $\square$ Usuario Nuevo $\quad\square$ Modificación de permisos de usuario existente.
- **Nombre del Colaborador:** A quién se le creará la cuenta.
- **Requerimiento de Accesos:** Texto descriptivo (ej: *"Requiere acceso al módulo de Consultas y Reportes del ERP SAI"*).
- **Firmas Institucionales:** Firma del Solicitante + Nombre y Firma de la Jefatura que autoriza.

### Vista del Técnico de Sistemas
- **Atendido por:** Técnico responsable.
- **Detalle Técnico de la Cuenta:** Nombre de usuario asignado en Directorio Activo/ERP, grupos de seguridad asignados y restricciones de red/horario.
- **Estado de la Solicitud:** `pendiente`, `en_proceso`, `atendido`, `rechazado` (con motivo).
- **Firma Técnica de Sistemas.**

---

## 6. Catálogo de Endpoints Backend Disponibles para el Frontend

| Método | Endpoint Backend | Parámetros Clave | Uso en Frontend |
| :---: | :--- | :--- | :--- |
| `GET` | `/api/auth/listar_tecnicos.php` | Ninguno | Poblar el `<select>` de técnicos disponibles para derivación. |
| `POST` | `/api/soporte_tecnico/crear_ticket.php` | Payload simplificado de cliente | Envío del formulario de nuevo ticket por el usuario. |
| `GET` | `/api/soporte_tecnico/listar_tickets.php` | `?usuario_solicitante_id={id}` | Listar la bandeja *"Mis Tickets"* del cliente logueado. |
| `GET` | `/api/soporte_tecnico/listar_tickets.php` | `?tecnico_asignado_id={id}` | Listar la bandeja *"Tickets Asignados a Mí"* del técnico. |
| `GET` | `/api/soporte_tecnico/obtener_ticket.php` | `?id={id}` o `?codigo={codigo}` | Cargar el modal de detalle con la telemetría resumida. |
| `POST` | `/api/soporte_tecnico/actualizar_ticket.php` | `id`, `tecnico_asignado_id`, etc. | Derivar, atender o resolver ticket desde el modal. |
| `GET` | `/api/diagnostico/listar.php` | `?estado_salud=...&buscar=...` | Tablero de monitoreo de PCs (exclusivo para técnicos). |
| `GET` | `/api/diagnostico/detalle.php` | `?mac={MAC}` | Ficha profunda de la PC con su historial de tickets. |
