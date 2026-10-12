# Stack Tecnológico y Contratos de Integración
## Módulo de Diagnóstico y Telemetría PC

---

### 1. Resumen de Tecnologías y Versiones

| Capa | Tecnología / Versión | Descripción y Uso |
| :--- | :--- | :--- |
| **Frontend Runtime** | **PHP 7.3-apache** (Docker puerto 82) | Renderizado de vistas modulares mediante componentes (encabezado.php, arra_navegacion.php). |
| **Framework CSS** | **Bootstrap 5.3.3** (CDN) | Rejilla responsiva, componentes de interfaz, tarjetas, tablas y badges. |
| **Iconografía** | **Bootstrap Icons 1.11.3** | Simbología de hardware (i-cpu, i-memory, i-hdd, i-shield-check, etc.). |
| **Tipografía** | **Google Fonts: Inter** (300-700) | Fuente principal institucional para legibilidad técnica. |
| **Frontend JS** | **Vanilla JavaScript (ES6+)** | Comunicación asíncrona mediante etch(), manipulación de DOM sin frameworks pesados. |
| **Backend API** | **PHP 7.3** (Docker puerto 83) | Endpoints REST en ack_form/api/diagnostico/ con PDO PostgreSQL y soporte UTF-8 sin BOM. |
| **Base de Datos** | **PostgreSQL 13-alpine** (Docker puerto 5432) | Almacenamiento relacional y tipos nativos **JSONB** para telemetría detallada. |
| **Agente Cliente (PC)** | **Windows PowerShell 5.1+ Nativo** | Extracción nativa mediante WMI/CIM, Prefetch y Defender. Cero librerías externas. |

---

### 2. Estructura de la Base de Datos (PostgreSQL 13)

Las tablas y esquemas se encuentran aplicados y validados en ormularios_db:

1. **equipos_inventario:**
   * Columnas: direccion_mac, hostname, ip_actual, usuario_actual, 
umero_serie, marca_modelo.
   * El backend autoregistra el equipo en esta tabla al recibir su primer reporte de telemetría.
2. **historial_diagnosticos:**
   * Cabecera y métricas de semáforo: id, equipo_id, direccion_mac, hostname, 	ipo_evento, echa_diagnostico, estado_salud_general, 	emperatura_cpu, uso_cpu_porcentaje, uso_ram_porcentaje, espacio_libre_c_gb, salud_disco_smart, ntivirus_activo.
   * Bloques estructurados **JSONB** (con evaluación granular por componente):
     * detalle_cpu: Modelo, núcleos, hilos, carga %, temperatura, estado (optimo / advertencia / critico) y lerta.
     * detalle_ram: Total GB, usado GB, libre GB, uso %, ranuras, velocidad MHz, estado y lerta.
     * detalle_almacenamiento: Array de unidades con tamaño, libre, % libre, modelo físico, SMART, estado y lerta.
     * detalle_red: Adaptador, MAC, IP, Gateway, DNS, estado y lerta.
     * detalle_placa_bios: Fabricante, modelo, serial.
     * pps_recientes_prefetch: Historial forense de aplicaciones con fecha/hora.
     * descargas_recientes: Archivos descargados en el perfil de usuario con bandera de riesgo.
     * seguridad_antivirus: Estado de Defender, tiempo real, firmas, amenazas, estado y lerta.
     * lertas_detectadas: Lista consolidada de advertencias detectadas.
3. **ista_ultimo_diagnostico_pc:**
   * Vista SQL de alto rendimiento para que el dashboard consulte el estado actual de todas las máquinas en un solo query.

---

### 3. Endpoints de la API REST (ack_form/api/diagnostico/)

#### Endpoint 1: Recepción de Telemetría (Operativo y Validado)
* **Ruta:** POST /api/diagnostico/recibir.php
* **Cabecera:** Content-Type: application/json; charset=utf-8
* **Acción:**
  1. Recibe el JSON del agente y limpia BOM UTF-8.
  2. Autoregistra o actualiza el equipo en equipos_inventario.
  3. Inserta el registro en historial_diagnosticos.
  4. Retorna HTTP 201: {exito: true, datos: {diagnostico_id: 6, direccion_mac: ..., estado_salud: ...}}.

#### Endpoint 2: Consulta del Último Estado (Por integrar)
* **Ruta:** GET /api/diagnostico/ultimo.php?mac={MAC} o ?equipo_id={ID}
* **Acción:** Consulta la vista ista_ultimo_diagnostico_pc y retorna la radiografía técnica completa del equipo.

#### Endpoint 3: Historial Cronológico (Por integrar)
* **Ruta:** GET /api/diagnostico/historial.php?mac={MAC}&limite=20
* **Acción:** Retorna los últimos snapshots para gráficos de evolución temporal (uso de disco, memoria y alertas a lo largo de las semanas).
