# Plan de Integración de Vistas en el Sistema de Formularios
## Módulo de Diagnóstico y Telemetría PC

> [!NOTE]
> **ESTADO DE LA IMPLEMENTACIÓN: MÓDULO MODULAR IMPLEMENTADO (OPCIÓN A - RUTA DIRECTA)**
> Siguiendo la estrategia de cero colisión con la reorganización en curso, las vistas se implementaron como un módulo 100% independiente en modulos/diagnostico/. No se tocó ninguna línea de arra_navegacion.php ni de los formularios existentes.

---

### 1. Ubicación de los Archivos Implementados

`
front_forms/
└── modulos/
    └── diagnostico/                     <-- [Módulo Independiente]
        ├── index.php                    <-- Redirección a monitoreo_equipos.php
        ├── monitoreo_equipos.php        <-- Panel general de equipos (KPIs + Tabla de salud)
        └── detalle_equipo.php           <-- Ficha técnica HWMonitor + Forense + Historial

back_form/
└── api/
    └── diagnostico/                     <-- [API REST de Diagnóstico]
        ├── recibir.php                  <-- POST: Recepción de telemetría desde las PCs
        ├── listar.php                   <-- GET: Lista de equipos y estado actual
        └── detalle.php                  <-- GET: Ficha completa e historial (?mac=...)
`

---

### 2. URLs de Acceso Directo (Navegador y Tablet)

* **Panel General de Monitoreo:**
  👉 http://[IP_SERVIDOR]:82/modulos/diagnostico/monitoreo_equipos.php
  *(En tu red local actual: http://192.168.11.75:82/modulos/diagnostico/monitoreo_equipos.php)*

* **Ficha Técnica Detallada de una PC:**
  👉 http://[IP_SERVIDOR]:82/modulos/diagnostico/detalle_equipo.php?mac={MAC}
  *(O haciendo clic en el botón Ficha HW de cualquier fila en el panel de monitoreo)*

---

### 3. Notas para Futura Reorganización o Enlace al Menú

Cuando el desarrollador encargado finalice la reorganización de la barra de navegación:
1. **Para agregar el botón en el menú lateral desplegable:**
   * Abrir ront_forms/componentes/barra_navegacion.php.
   * En el array $modulos_sistema o en el bloque del offcanvas lateral #menuFormulariosLateral, simplemente enlazar a:
     $ruta_base . 'modulos/diagnostico/monitoreo_equipos.php'.
2. **Si deciden mover la carpeta modulos/diagnostico/:**
   * La variable $nivel_ruta en monitoreo_equipos.php y detalle_equipo.php controla las rutas relativas a encabezado.php y arra_navegacion.php (actualmente está configurada como '../../').
   * Los endpoints JavaScript llaman a window.URL_BACKEND + '/api/diagnostico/...', por lo que son completamente independientes de la ubicación del archivo en el frontend.
