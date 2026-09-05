# AGENTS.md - Guía de Desarrollo y Convenciones del Proyecto

Bienvenido al repositorio del **Sistema Modular de Formularios de Registro**.
Este documento define la arquitectura, normas de codificación, estructura de directorios y directrices para agentes de IA y desarrolladores que interactúan con este código.

---

## 1. Visión General del Proyecto

El objetivo del sistema es gestionar múltiples formularios de registro organizacionales de manera modular. Inicialmente el sistema contempla **4 formularios**, comenzando por:

1. **Formulario 1 (Activo): Soporte Técnico**
2. **Formulario 2 (Activo): Mantenimiento Preventivo (Equipos: PC / Laptop)**
3. **Formulario 3 (Futuro):** Control de Activos / Inventario
4. **Formulario 4 (Futuro):** Requerimiento de Compras / Suministros

Cada formulario debe funcionar de manera autónoma como un módulo, compartiendo la misma infraestructura tecnológica base.

---

## 2. Pila Tecnológica (Stack)

- **Backend:** PHP 7.3 con Apache (Extensión `pdo_pgsql`).
- **Frontend:** PHP 7.3 con Apache y componentes de interfaz con **Bootstrap 5**.
- **Base de Datos:** PostgreSQL 13+.
- **Contenedores:** Docker & Docker Compose con 3 servicios independientes:
  - Base de datos (`db`)
  - Backend API (`back_form`)
  - Frontend (`front_forms`)

---

## 3. Reglas Críticas de Desarrollo

### 3.1. Nombres de Archivos y Carpetas en Español
- Todos los directorios y archivos de lógica, configuración, componentes, recursos y endpoints **deben nombrarse en español**.
- **Excepciones exclusivas para archivos base del ecosistema:**
  - `docker-compose.yml`
  - `Dockerfile`
  - `init.sql`
  - `AGENTS.md`
  - `index.php` (punto de entrada estándar del servidor web)

### 3.2. Compatibilidad Estricta con PHP 7.3
- **NO USAR** características de PHP 8+ como:
  - Argumentos nombrados (`func(name: $val)`)
  - Expresión `match` (usar `switch` o `if/else`)
  - Tipos de unión (`int|string`)
  - Promoción de propiedades en constructor
  - Operador nullsafe (`?->`)
- Usar sintaxis compatible con PHP 7.3 (`array()`, `[]`, operadores ternarios estándar, tipado simple).

### 3.3. Separación Modular de Responsabilidades
- **`front_forms/`**: No realiza consultas SQL directas. Se comunica exclusivamente con el backend mediante peticiones HTTP (Fetch API / cURL) a `back_form/`.
- **`back_form/`**: Recibe peticiones HTTP, valida datos, procesa la lógica e interactúa con la base de datos PostgreSQL retornando respuestas JSON estandarizadas.
- **`init.sql`**: Es el archivo físico único de inicialización de esquemas, tablas, restricciones e índices. Cualquier cambio estructural en la base de datos debe reflejarse en este script.

### 3.4. Regla Estricta de Modificación Modular por Secciones (Vistas de Formulario)
Cada vez que se modifique o estructure código en `formulario_soporte.php` o en cualquier vista de formulario:
1. **No generar código monolítico ni archivos gigantes:** Prohibido crear bloques masivos e interminables de código de una sola vez.
2. **Respetar la modularidad por secciones:** El formulario debe mantenerse dividido lógicamente en sus secciones ordenadas (Datos Generales, Tipo de Soporte, Detalle, Prioridad, Datos del Técnico y Observaciones).
3. **Modificaciones quirúrgicas:** Cuando se solicite un cambio, editar **únicamente** la sección o el bloque específico solicitado, sin tocar, reescribir ni alterar el resto del archivo funcional.
4. **Proteger el entorno:** Mantener siempre la compatibilidad estricta con PHP 7.3, la estructura de Bootstrap 5 y la arquitectura definida en el proyecto.
5. **Aislamiento de componentes extensos:** Si la lógica de una sección o bloque requiere crecer demasiado, aislarla de forma limpia manteniendo la separación estricta para evitar archivos robustos y facilitar futuros cambios.

---

## 4. Estructura de Directorios

```text
Formulario_Registro/
├── AGENTS.md                          # Este documento de reglas y arquitectura
├── docker-compose.yml                 # Orquestador de contenedores
├── init.sql                           # Script físico DDL para PostgreSQL
│
├── back_form/                         # Backend (PHP 7.3 API)
│   ├── Dockerfile                     # Configuración del contenedor PHP 7.3 con pdo_pgsql
│   ├── index.php                      # Estado del servicio API (Healthcheck)
│   ├── configuracion/
│   │   ├── conexion_bd.php            # Conexión PDO a PostgreSQL
│   │   └── respuestas_api.php         # Respuestas JSON y cabeceras CORS
│   └── api/
│       ├── soporte_tecnico/           # Endpoints del Módulo de Soporte
│       │   ├── crear_ticket.php       # POST: Orquestador registrar ticket
│       │   ├── listar_tickets.php     # GET: Listado con filtros
│       │   ├── obtener_ticket.php     # GET: Detalle de un ticket
│       │   ├── actualizar_ticket.php  # POST: Actualizar atención/resolución
│       │   ├── buscar_equipos.php     # GET: Búsqueda de equipos en catálogo
│       │   └── servicios/             # Submódulos de negocio especializados
│       │       ├── servicio_equipos.php   # Lógica de búsqueda y alta en inventario
│       │       ├── servicio_firmas.php    # Validación de firmas manuscritas (Base64)
│       │       └── servicio_atencion.php  # Registro de atención técnica y cierre
│       │
│       └── mantenimiento_preventivo/  # Endpoints del Módulo de Mantenimiento (PC/Laptop)
│           ├── crear_mantenimiento.php  # POST: Registrar mantenimiento
│           ├── listar_mantenimientos.php# GET: Listado con filtros y métricas
│           ├── obtener_mantenimiento.php# GET: Detalle completo de ficha técnica
│           └── servicios/             # Submódulos de negocio
│               ├── servicio_equipo_mp.php    # Validación y procesamiento de equipo
│               ├── servicio_checklist_mp.php # Normalización de checklists
│               └── servicio_firmas_mp.php    # Validación de doble firma digital
│
└── front_forms/                       # Frontend (PHP 7.3 + Bootstrap)
    ├── Dockerfile                     # Configuración del contenedor PHP 7.3 Apache
    ├── index.php                      # Portal central (Módulos 1 y 2 activos, 3-4 próximos)
    ├── componentes/
    │   ├── encabezado.php             # Head HTML y estilos Bootstrap
    │   ├── barra_navegacion.php       # Barra superior de navegación
    │   └── pie_pagina.php             # Scripts y cierre de documento
    ├── recursos/
    │   ├── css/
    │   │   └── estilos_personalizados.css  # Reglas visuales y diseño
    │   └── js/
    │       ├── cliente_api.js                 # Cliente central de comunicación API
    │       ├── formulario_soporte.js          # Lógica del formulario de soporte
    │       ├── gestion_tickets.js             # Lógica del panel de soporte
    │       ├── formulario_mantenimiento.js    # Lógica de mantenimiento preventivo
    │       └── gestion_mantenimientos.js      # Lógica del panel de mantenimiento
    └── modulos/
        ├── soporte_tecnico/
        │   ├── index.php              # Punto de entrada del módulo
        │   ├── formulario_soporte.php # Formulario orquestador
        │   ├── gestion_tickets.php    # Panel orquestador maestro
        │   ├── secciones/             # Subcomponentes del formulario de registro
        │   └── componentes_gestion/   # Subcomponentes del panel de gestión
        │
        └── mantenimiento_preventivo/
            ├── index.php                       # Redirección a formulario
            ├── formulario_mantenimiento.php    # Formulario orquestador limpio
            ├── gestion_mantenimientos.php      # Panel orquestador maestro
            ├── secciones/                      # Subcomponentes (6 secciones + firmas)
            │   ├── seccion_1_datos_generales.php
            │   ├── seccion_2_informacion_equipo.php
            │   ├── seccion_3_mantenimiento_externo.php
            │   ├── seccion_4_mantenimiento_interno.php
            │   ├── seccion_5_verificacion_funcionamiento.php
            │   ├── seccion_6_observaciones.php
            │   └── seccion_firmas.php
            └── componentes_gestion/            # Subcomponentes del panel de gestión
                ├── panel_encabezado.php        # Cabecera y botón nuevo
                ├── panel_metricas.php          # 4 tarjetas de indicadores KPI
                ├── panel_filtros.php           # Buscador y filtro por tipo
                ├── panel_tabla.php             # Tabla responsiva
                └── modal_detalle.php           # Ficha técnica imprimible y firmas
```

---

## 5. Especificación del Formulario de Soporte Técnico

El formulario consta de las siguientes 6 secciones:

1. **Datos Generales:**
   - Nombre del solicitante (`nombre_solicitante`)
   - Fecha de registro (`fecha_solicitud`)
   - Departamento o área (`departamento_area`)
2. **Tipo de Soporte Requerido (Marcar lo que corresponda):**
   - Hardware: equipo, periférico (`soporte_hardware`)
   - Software: aplicaciones, sistema (`soporte_software`)
3. **Detalle del Problema / Requerimiento:**
   - Descripción detallada (`descripcion_problema`)
   - Equipo afectado (si aplica):
     - Número de serie / inventario (`numero_serie`)
     - Marca / modelo (`marca_modelo`)
     - Sistema operativo (`sistema_operativo`)
4. **Prioridad (A definir por el área de soporte):**
   - Urgente (afecta operaciones críticas)
   - Alta (afecta productividad significativa)
   - Media (molestia operativa pero no detiene trabajo)
   - Baja (requerimiento rutinario/menor)
5. **Datos del Técnico (Para completar por soporte):**
   - Fecha / hora de atención (`fecha_hora_atencion`)
   - Técnico asignado (`tecnico_asignado`)
   - Diagnóstico (`diagnostico`)
   - Solución aplicada (`solucion_aplicada`)
   - Tipo de resolución (`tipo_resolucion`)
6. **Observaciones / Recomendaciones:**
   - Observaciones y sugerencias (`observaciones_recomendacion`)

> **Estructura en Base de Datos (Modelo Relacional):**
> - **Tabla `tickets_soporte`:** Almacena las Secciones 1 a 4 (solicitud, prioridad, estado y auditoría).
> - **Tabla `atenciones_soporte`:** Almacena las Secciones 5 y 6 (datos del técnico, resolución y observaciones) vinculada por `ticket_id` (FK).

---

## 6. Convención de Respuestas JSON de la API

Todas las respuestas del backend deben emitir el encabezado `Content-Type: application/json; charset=utf-8` y seguir la estructura:

---

## 6. Especificación del Formulario de Mantenimiento Preventivo (PC / Laptop)

El formulario consta de las siguientes 6 secciones y firmas:

1. **Datos Generales:**
   - Técnico responsable (`tecnico_responsable`)
   - Fecha de mantenimiento (`fecha_mantenimiento`)
   - Ubicación del equipo (`ubicacion_equipo`)
2. **Información del Equipo:**
   - Tipo de equipo: PC o LAPTOP (`tipo_equipo`)
   - Nombre de equipo / hostname (`nombre_equipo`)
   - Código de activo (`codigo_activo`)
   - Memoria RAM (`memoria_ram`)
   - Tipo de red: LAN o WIFI (`tipo_red`)
   - Marca / modelo (`marca_modelo`)
   - Sistema operativo (`sistema_operativo`)
   - Procesador (`procesador`)
   - Almacenamiento (`almacenamiento`)
   - Dirección IP (`direccion_ip`)
   - Vinculación opcional a catálogo (`equipo_id` -> `equipos_inventario`)
3. **Mantenimiento Externo (Limpieza Física):**
   - Limpieza de carcasa, ventiladores y componentes (`limpieza_carcasa_componentes`)
   - Limpieza de pantalla, teclado y touchpad (`limpieza_pantalla_teclado`)
   - Verificación de conectores USB/HDMI (`verificacion_conectores`)
   - Otros aspectos físicos (`limpieza_otros`)
4. **Mantenimiento Interno (Software / Configuración):**
   - Actualización del sistema operativo (`actualizacion_so`)
   - Eliminación de archivos temporales/caché (`eliminacion_temporales`)
   - Desfragmentación / optimización de unidades (`desfragmentacion_optimizacion`)
   - Escaneo antivirus/anti-malware (`escaneo_antivirus`)
   - Verificación de drivers y actualizaciones (`verificacion_drivers`)
   - Copia de seguridad de datos críticos (`copia_seguridad`)
   - Otros aspectos lógicos (`mantenimiento_interno_otros`)
5. **Verificación de Funcionamiento:**
   - Encendido / apagado correcto (`verificacion_encendido_apagado`)
   - Rendimiento general fluido (`verificacion_rendimiento`)
   - Conectividad Wi-Fi / red funcional (`verificacion_red`)
   - Periféricos operativos (`verificacion_perifericos`)
   - Sin sobrecalentamiento / anomalías térmicas (`verificacion_temperatura_anomalias`)
6. **Observaciones / Incidencias:**
   - Notas y recomendaciones preventivas (`observaciones_incidencias`)
7. **Firmas Digitales (Lienzos Canvas):**
   - Firma del responsable del equipo (`firma_responsable_equipo`)
   - Firma del Dpto. de Sistemas (`firma_sistemas`)

> **Estructura en Base de Datos:** Tabla única `mantenimientos_preventivos` con campos booleanos para cada chequeo e imágenes Base64 de firmas.

---

## 7. Convención de Respuestas JSON de la API

Todas las respuestas del backend deben emitir el encabezado `Content-Type: application/json; charset=utf-8` y seguir la estructura:

```json
{
  "exito": true,
  "mensaje": "Mensaje descriptivo en español",
  "datos": { ... }
}
```

En caso de error:
```json
{
  "exito": false,
  "mensaje": "Descripción clara del error ocurrido",
  "errores": [ ... ]
}
```

---

## 8. Instrucciones para Agregar un Nuevo Formulario

Cuando se implementen los formularios restantes (3 o 4):
1. **Base de Datos:** Añadir la nueva tabla en `init.sql` con prefijo descriptivo (ej. `activos_inventario`).
2. **Backend:** Crear la carpeta en `back_form/api/<nombre_modulo>/` con los scripts correspondientes (`crear_registro.php`, `listar_registros.php`, etc.).
3. **Frontend:** Crear la carpeta en `front_forms/modulos/<nombre_modulo>/` reutilizando `componentes/encabezado.php`, `barra_navegacion.php` y `pie_pagina.php`.
4. **Portal Principal:** Actualizar `front_forms/index.php` cambiando el estado del módulo de "En desarrollo" a "Activo".

