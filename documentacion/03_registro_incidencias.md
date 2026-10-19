# Módulo 3: Formulario de Registro de Incidencias de Sistemas

Guía técnica y funcional del módulo de incidencias de software y plataformas críticas.

---

## 1. Secciones del Formulario

El formulario consta de las siguientes 7 secciones y doble firma digital:

1. **Datos Generales:**
   - Nro. de Incidencia (`nro_incidencia`) -> Ej: `INC-YYYY-XXXX`
   - Fecha y hora del Reporte (`fecha_hora_reporte`)
   - Responsable del Reporte (`responsable_reporte`)
2. **Plataforma / Sistema Afectado:**
   - SISTEMA ERP - SAI (`sistema_erp_sai`)
   - SISTEMA DE COBRANZAS - NETCOB (`sistema_cobranzas_netcob`)
   - OTROS SISTEMAS / COMPLEMENTOS (`sistema_otros` y `sistema_otros_detalle`)
3. **Naturaleza Técnica del Fallo:**
   - Base de Datos (`fallo_base_datos` y `detalle_base_datos`)
   - Infraestructura / Servidor (`fallo_infraestructura_servidor` y `detalle_infraestructura_servidor`)
   - Enlaces / Conectividad (`fallo_enlaces_conectividad` y `detalle_enlaces_conectividad`)
4. **Nivel de Criticidad Institucional (A definir por Sistemas):**
   - Crítica (Nivel 1): Paralización del ciclo de facturación (`critica_nivel_1`)
   - Alta (Nivel 2): Falla en un módulo importante pero permite trabajar (`alta_nivel_2`)
   - Media / Baja (Nivel 3): Error estético, lentitud o cambios menores (`media_baja_nivel_3`)
5. **Descripción Técnica y Logs de Error:**
   - Trazas de logs, excepciones o detalle del comportamiento anómalo (`descripcion_tecnica_logs`)
6. **Solución Aplicada y Tiempos:**
   - Acción Realizada (`accion_realizada`)
   - Detalle Técnico (`detalle_tecnico`)
   - Fecha y Hora de Cierre (`fecha_hora_cierre`)
   - Estado de la Incidencia (`estado`: 'abierta', 'en_atencion', 'resuelta', 'cerrada')
7. **Seguimiento y Recomendaciones:**
   - Medidas preventivas post-incidente (`observaciones_recomendaciones`)
8. **Firmas Digitales (Lienzos Canvas):**
   - Firma del Responsable del Reporte (`firma_responsable_reporte`)
   - Firma del Dpto. de Sistemas (`firma_sistemas`)

---

## 2. Estructura en Base de Datos (PostgreSQL)

- **Tabla `incidencias_sistemas`:** Tabla con índices en `nro_incidencia`, `nivel_criticidad`, `estado`, `fecha_hora_reporte` y `responsable_reporte`.

---

## 3. Endpoints del Backend (`back_form/api/registro_incidencias/`)

- `crear_incidencia.php` (POST): Registrar incidencia con correlativo automático `INC-YYYY-XXXX`.
- `actualizar_incidencia.php` (POST): Registro de solución, cierre técnico y trazabilidad en auditoría.
- `listar_incidencias.php` (GET): Listado con filtros de criticidad, estado y paginación.
- `obtener_incidencia.php` (GET): Detalle completo de la incidencia.
- `servicios/`:
  - `servicio_codigo_incidencia.php`: Correlativo `INC-YYYY-XXXX`.
  - `servicio_validaciones_ri.php`: Reglas de negocio y estados.
  - `servicio_firmas_ri.php`: Procesamiento de firmas canvas.
