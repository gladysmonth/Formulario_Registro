# AGENTS.md - Guía de Desarrollo y Convenciones del Proyecto

Bienvenido al repositorio del **Sistema Modular de Formularios de Registro**.
Este documento define la arquitectura, normas de codificación, estructura de directorios y directrices para agentes de IA y desarrolladores que interactúan con este código.

---

## 1. Visión General del Proyecto

El objetivo del sistema es gestionar múltiples formularios de registro organizacionales de manera modular. Inicialmente el sistema contempla **4 formularios**, comenzando por:

1. **Formulario 1 (Activo): Soporte Técnico**
2. **Formulario 2 (Futuro):** Registro de Permisos / Vacaciones
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
│       └── soporte_tecnico/           # Endpoints del Módulo de Soporte
│           ├── crear_ticket.php       # POST: Registrar ticket
│           ├── listar_tickets.php     # GET: Listado con filtros
│           ├── obtener_ticket.php     # GET: Detalle de un ticket
│           └── actualizar_ticket.php  # POST: Actualizar atención/resolución
│
└── front_forms/                       # Frontend (PHP 7.3 + Bootstrap)
    ├── Dockerfile                     # Configuración del contenedor PHP 7.3 Apache
    ├── index.php                      # Portal central (Módulo 1 activo, 2-4 próximos)
    ├── componentes/
    │   ├── encabezado.php             # Head HTML y estilos Bootstrap
    │   ├── barra_navegacion.php       # Barra superior de navegación
    │   └── pie_pagina.php             # Scripts y cierre de documento
    ├── recursos/
    │   ├── css/
    │   │   └── estilos_personalizados.css  # Reglas visuales y diseño
    │   └── js/
    │       ├── cliente_api.js         # Cliente central de comunicación API
    │       ├── formulario_soporte.js  # Lógica del formulario de soporte
    │       └── gestion_tickets.js     # Lógica del panel de soporte
    └── modulos/
        └── soporte_tecnico/
            ├── index.php              # Punto de entrada del módulo
            ├── formulario_soporte.php # Formulario de 6 secciones
            └── gestion_tickets.php    # Panel de gestión y seguimiento
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

## 7. Instrucciones para Agregar un Nuevo Formulario

Cuando se implementen los formularios restantes (2, 3 o 4):
1. **Base de Datos:** Añadir la nueva tabla en `init.sql` con prefijo descriptivo (ej. `vacaciones_permisos`).
2. **Backend:** Crear la carpeta en `back_form/api/<nombre_modulo>/` con los scripts correspondientes (`crear_registro.php`, `listar_registros.php`, etc.).
3. **Frontend:** Crear la carpeta en `front_forms/modulos/<nombre_modulo>/` reutilizando `componentes/encabezado.php`, `barra_navegacion.php` y `pie_pagina.php`.
4. **Portal Principal:** Actualizar `front_forms/index.php` cambiando el estado del módulo de "En desarrollo" a "Activo".
