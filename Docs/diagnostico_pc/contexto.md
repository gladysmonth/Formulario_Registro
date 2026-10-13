# Módulo de Diagnóstico y Telemetría de Equipos PC
## Documento de Contexto y Alcance del Proyecto

---

### 1. Resumen Ejecutivo y Propósito
El **Módulo de Diagnóstico y Telemetría PC** es una extensión del Sistema de Formularios y Soporte Técnico de **Cosmol**. Su objetivo es automatizar la captura, centralización y análisis del estado técnico (hardware, almacenamiento, rendimiento y seguridad) de las computadoras del personal administrativo y operativo.

Este módulo elimina la incertidumbre técnica durante la atención de incidencias: cuando un usuario reporta una falla, el personal técnico ya cuenta con una **radiografía digital** del equipo (diagnóstico en frío) sin requerir que el usuario realice diagnósticos manuales ni llamadas adicionales.

---

### 2. Problemática Actual vs. Solución Implementada

| Escenario Tradicional (Sin Telemetría) | Nueva Solución (Módulo de Diagnóstico Cosmol) |
| :--- | :--- |
| El usuario reporta: *Mi computadora está lenta* o *Se colgó el sistema*. | El sistema ya sabe si el disco duro C: está al 95% de capacidad, si la RAM está saturada o si el disco presenta fallas SMART. |
| El técnico debe desplazarse físicamente a la oficina a revisar especificaciones básicas. | El técnico abre el ticket en el panel y visualiza el hardware completo (CPU, RAM, discos, temperaturas, adaptadores de red). |
| Diagnósticos globales ambiguos sin saber qué pieza falla. | **Evaluación granular por componente:** Cada pieza (CPU, RAM, cada disco C:, D:, Antivirus, Red) posee su propio semáforo individual de estado (óptimo, ueno, dvertencia, crítico) y motivo de alerta. |
| No hay trazabilidad de qué programas se usaron o qué se descargó previo a la falla. | Se cuenta con un registro forense ligero de las últimas aplicaciones ejecutadas (Windows Prefetch / Recent) y descargas recientes. |
| El formulario de reparación se llena en papel o de manera desconectada. | El formulario de pre-arreglo y servicio técnico se vincula directamente al historial de diagnósticos y genera un reporte imprimible/PDF formal con membrete de Cosmol. |

---

### 3. Principio de Cero Fricción para el Usuario
El diseño sigue el principio de **cero fricción**:
1. **El usuario no ejecuta nada:** El agente corre como una Tarea Programada de Windows en segundo plano silencioso con privilegios del sistema (SYSTEM). No muestra consolas negras ni ventanas emergentes.
2. **El usuario no busca su MAC ni serial:** Al redactar un ticket en el portal de formularios, el sistema asocia de forma transparente el **último diagnóstico registrado** para la PC asignada a ese usuario.

---

### 4. Horarios y Frecuencias de Telemetría
Considerando la jornada laboral institucional (08:00 a 12:00 y 14:00 a 18:00):
* **08:05 AM (Diagnóstico Matutino Completo):** Evaluación profunda de hardware, estado del antivirus, firmas actualizadas, salud SMART de discos y espacio de almacenamiento.
* **Telemetría Periódica (Cada 2 horas: 10:00, 12:00, 14:05, 16:00, 18:05):** Chequeo ligero de consumo de memoria RAM, uso de CPU, últimas aplicaciones activas y eventos de advertencia.
* **Alerta Crítica Inmediata:** Si el almacenamiento disponible en la unidad C: cae por debajo de 10 GB o Windows Defender detecta una amenaza activa, el agente envía un evento de tipo lerta_critica.

---

### 5. Ciclo de Vida del Diagnóstico en el Flujo de Soporte

`
[ PC Cliente (Agente Silencioso) ] 
       │ (JSON periódico con estados por componente)
       ▼
[ Backend (back_form :83 /api/diagnostico/recibir.php) ] 
       │
       ▼
[ BD PostgreSQL (equipos_inventario + historial_diagnosticos) ]
       │
    ┌──┴──────────────────────────────────────────────────────┐
    ▼                                                         ▼
[ Vista de Inventario / HWMonitor ]         [ Creación y Atención de Tickets ]
- Lista de PCs por MAC / Activo             - Se asocia automáticamente el último diagnóstico
- Semáforo individual por pieza             - Ficha técnica adjunta a la solicitud
- Historial temporal de cambios             - Formulario Pre/Post Arreglo e Impresión
`

---

### 6. Consideraciones de Seguridad y Privacidad
* **Aislamiento de Información:** El recolector audita únicamente métricas del sistema operativo, procesos del sistema y nombres de archivos de descargas recientes; **no lee ni accede al contenido interno de documentos personales, correos ni datos privados del usuario**.
* **Comportamiento Antivirus:** Utiliza únicamente interfaces nativas de Windows (WMI, CIM, APIs del registro y PowerShell sin compiladores como PyInstaller), eliminando el riesgo de falsos positivos en Windows Defender y antivirus corporativos.
