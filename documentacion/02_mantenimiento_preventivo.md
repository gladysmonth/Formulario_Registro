# Módulo 2: Formulario de Mantenimiento Preventivo (PC / Laptop)

Guía técnica y funcional del módulo de mantenimiento preventivo institucional.

---

## 1. Secciones del Formulario

El formulario consta de las siguientes 6 secciones y doble firma digital:

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
   - Vinculación a inventario (`equipo_id` -> `equipos_inventario`)
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

---

## 2. Estructura en Base de Datos (PostgreSQL)

- **Tabla `mantenimientos_preventivos`:** Tabla única con campos booleanos para cada verificación del checklist e imágenes Base64 de ambas firmas. Correlativo institucional: `MP-YYYY-XXXX`.

---

## 3. Endpoints del Backend (`back_form/api/mantenimiento_preventivo/`)

- `crear_mantenimiento.php` (POST): Registrar mantenimiento preventivo completo.
- `actualizar_mantenimiento.php` (POST/PUT): Actualizar observaciones técnicas y firmas.
- `listar_mantenimientos.php` (GET): Listado con filtros y KPIs de cumplimiento.
- `obtener_mantenimiento.php` (GET): Ficha técnica completa de mantenimiento.
- `servicios/`:
  - `servicio_codigo_mp.php`: Generación de código `MP-YYYY-XXXX`.
  - `servicio_checklist_mp.php`: Validación de booleanos de checklist.
  - `servicio_equipo_mp.php`: Procesamiento de datos de hardware.
  - `servicio_firmas_mp.php`: Validación de firmas canvas.
